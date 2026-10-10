import { createClient } from 'https://esm.sh/@supabase/supabase-js@2';
import { attendanceSmsAudience } from '../_shared/attendanceSmsRules.js';
import { normalizeUzbekPhone } from '../_shared/uzbekPhone.js';

const corsHeaders = {
  'Access-Control-Allow-Origin': '*',
  'Access-Control-Allow-Headers': 'authorization, x-client-info, apikey, content-type',
  'Access-Control-Allow-Methods': 'POST, OPTIONS',
};

type Alert = {
  student_id: number;
  student_name: string;
  class_name: string;
  parent_id: string | null;
  parent_phone: string | null;
  absence_streak: number;
};

type Recipient = {
  id: string | null;
  name: string;
  phone: string | null;
  notify_sms: boolean;
  is_active: boolean;
  school_id: number | null;
};

type SmsJob = {
  student_id: number;
  student_name: string;
  class_name: string;
  school_name: string;
  school_id: number;
  attendance_date: string;
  streak_days: number;
  recipient_profile_id: string | null;
  phone: string;
  message: string;
  delivery_id?: string;
};

function jsonResponse(body: unknown, status = 200) {
  return new Response(JSON.stringify(body), {
    status,
    headers: { ...corsHeaders, 'Content-Type': 'application/json' },
  });
}

function isCalendarDate(value: unknown): value is string {
  if (typeof value !== 'string' || !/^\d{4}-\d{2}-\d{2}$/.test(value)) return false;
  const parsed = new Date(`${value}T00:00:00.000Z`);
  return !Number.isNaN(parsed.getTime()) && parsed.toISOString().slice(0, 10) === value;
}

function todayInTashkent() {
  return new Intl.DateTimeFormat('en-CA', {
    timeZone: 'Asia/Tashkent',
    year: 'numeric',
    month: '2-digit',
    day: '2-digit',
  }).format(new Date());
}

function getSmsText(job: Omit<SmsJob, 'delivery_id' | 'recipient' | 'phone' | 'message'>) {
  if (job.streak_days === 3) {
    return `Hurmatli ota-ona, farzandingiz ${job.student_name} (${job.class_name}-sinf) ketma-ket 3 ta o‘qish kuni sababsiz dars qoldirdi. Iltimos, maktab bilan bog‘laning.`;
  }
  return `Diqqat: ${job.student_name} (${job.class_name}-sinf) ketma-ket ${job.streak_days} ta o‘qish kuni sababsiz dars qoldirdi. Maktab: ${job.school_name}.`;
}

async function getEskizToken(email: string, password: string) {
  const response = await fetch('https://notify.eskiz.uz/api/auth/login', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ email, password }),
  });
  const result = await response.json().catch(() => null);
  const token = result?.data?.token;
  if (!response.ok || typeof token !== 'string' || !token) {
    throw new Error(`Eskiz tizimiga kirish amalga oshmadi (HTTP ${response.status}). Login va parolni tekshiring.`);
  }
  return token;
}

async function sendEskizSms(token: string, phone: string, message: string, sender: string) {
  const body = new FormData();
  body.set('mobile_phone', phone);
  body.set('message', message);
  body.set('from', sender);
  const response = await fetch('https://notify.eskiz.uz/api/message/sms/send', {
    method: 'POST',
    headers: { Authorization: `Bearer ${token}` },
    body,
  });
  const result = await response.json().catch(() => null);
  if (!response.ok || result?.status === 'error') {
    throw new Error(`Eskiz SMS yuborishni rad etdi (HTTP ${response.status}).`);
  }
  return String(result?.data?.id ?? result?.id ?? '');
}

Deno.serve(async (request) => {
  if (request.method === 'OPTIONS') return new Response('ok', { headers: corsHeaders });
  if (request.method !== 'POST') return jsonResponse({ error: 'Faqat POST so‘rovi qabul qilinadi.' }, 405);

  const claimedDeliveryIds: string[] = [];
  let serviceClient: ReturnType<typeof createClient> | null = null;

  try {
    const supabaseUrl = Deno.env.get('SUPABASE_URL');
    const anonKey = Deno.env.get('SUPABASE_ANON_KEY');
    const serviceRoleKey = Deno.env.get('SUPABASE_SERVICE_ROLE_KEY');
    if (!supabaseUrl || !anonKey || !serviceRoleKey) {
      throw new Error('Supabase server sozlamalari to‘liq emas.');
    }

    const authorization = request.headers.get('Authorization');
    if (!authorization?.startsWith('Bearer ')) {
      return jsonResponse({ error: 'Tizimga qayta kiring.' }, 401);
    }

    const userClient = createClient(supabaseUrl, anonKey, {
      auth: { persistSession: false, autoRefreshToken: false },
      global: { headers: { Authorization: authorization } },
    });
    const { data: userResult, error: userError } = await userClient.auth.getUser(authorization.slice('Bearer '.length));
    if (userError || !userResult.user) return jsonResponse({ error: 'Tizimga qayta kiring.' }, 401);

    serviceClient = createClient(supabaseUrl, serviceRoleKey, {
      auth: { persistSession: false, autoRefreshToken: false },
    });
    const { data: profile, error: profileError } = await serviceClient.from('profiles')
      .select('role,school_id,is_active')
      .eq('id', userResult.user.id)
      .maybeSingle();
    if (profileError) throw new Error(`Foydalanuvchi profilini tekshirib bo‘lmadi: ${profileError.message}`);
    if (!profile?.is_active || !['admin', 'teacher', 'school_admin'].includes(profile.role)) {
      return jsonResponse({ error: 'Bu amal uchun ruxsat yo‘q.' }, 403);
    }

    const body = await request.json();
    const attendanceDate = body?.attendance_date;
    if (!isCalendarDate(attendanceDate) || attendanceDate > todayInTashkent()) {
      return jsonResponse({ error: 'Davomat sanasi noto‘g‘ri.' }, 400);
    }

    let assignedSchoolId = profile.school_id as number | null;
    if (!assignedSchoolId && profile.role !== 'admin') {
      const { data: schoolSetting, error: settingError } = await serviceClient.from('school_settings')
        .select('default_school_id')
        .eq('id', true)
        .maybeSingle();
      if (settingError) throw new Error(`Maktab sozlamasini o‘qib bo‘lmadi: ${settingError.message}`);
      assignedSchoolId = schoolSetting?.default_school_id ?? null;
    }
    if (profile.role !== 'admin' && !assignedSchoolId) {
      return jsonResponse({ error: 'Profilingizga maktab biriktirilmagan.' }, 403);
    }

    let dailyQuery = serviceClient.from('attendances').select('school_id').eq('attendance_date', attendanceDate);
    if (assignedSchoolId) dailyQuery = dailyQuery.eq('school_id', assignedSchoolId);
    const { data: dailyRows, error: dailyError } = await dailyQuery;
    if (dailyError) throw new Error(`Davomat yozuvlarini tekshirib bo‘lmadi: ${dailyError.message}`);
    const schoolIds = [...new Set((dailyRows || []).map((row) => Number(row.school_id)))];
    if (!schoolIds.length) return jsonResponse({ sent: 0, failed: 0, skipped: 0, already_sent: 0 });

    const { data: schools, error: schoolsError } = await serviceClient.from('schools')
      .select('id,name')
      .in('id', schoolIds);
    if (schoolsError) throw new Error(`Maktab ma’lumotlarini o‘qib bo‘lmadi: ${schoolsError.message}`);
    const schoolNames = new Map((schools || []).map((school) => [Number(school.id), school.name]));

    const alerts: Array<Alert & { school_id: number }> = [];
    for (const schoolId of schoolIds) {
      const { data, error } = await serviceClient.rpc('attendance_absence_alerts', {
        p_school_id: schoolId,
        p_attendance_date: attendanceDate,
      });
      if (error) throw new Error(`Davomat ogohlantirishlarini hisoblab bo‘lmadi: ${error.message}`);
      const alertStudentIds = (data || []).map((alert: Alert) => alert.student_id);
      const { error: clearAlertsError } = await serviceClient.from('attendances')
        .update({ is_alert: false })
        .eq('school_id', schoolId)
        .eq('attendance_date', attendanceDate)
        .eq('is_alert', true);
      if (clearAlertsError) throw new Error(`Davomat ogohlantirish belgilarini tozalab bo‘lmadi: ${clearAlertsError.message}`);
      if (alertStudentIds.length) {
        const { error: setAlertsError } = await serviceClient.from('attendances')
          .update({ is_alert: true })
          .eq('school_id', schoolId)
          .eq('attendance_date', attendanceDate)
          .in('student_id', alertStudentIds);
        if (setAlertsError) throw new Error(`Davomat ogohlantirish belgilarini saqlab bo‘lmadi: ${setAlertsError.message}`);
      }
      for (const alert of (data || []) as Alert[]) alerts.push({ ...alert, school_id: schoolId });
    }

    const iioSchoolIds = [...new Set(alerts.filter((alert) => attendanceSmsAudience(alert.absence_streak) === 'iio').map((alert) => alert.school_id))];

    let iioStaff: Recipient[] = [];
    if (iioSchoolIds.length) {
      const { data, error } = await serviceClient.from('profiles')
        .select('id,name,phone,notify_sms,is_active,school_id')
        .eq('role', 'iio')
        .eq('is_active', true)
        .eq('notify_sms', true)
        .in('school_id', iioSchoolIds);
      if (error) throw new Error(`IIO telefonlarini o‘qib bo‘lmadi: ${error.message}`);
      iioStaff = (data || []) as Recipient[];
    }

    let skipped = 0;
    const jobs: SmsJob[] = [];
    for (const alert of alerts) {
      const audience = attendanceSmsAudience(alert.absence_streak);
      if (!audience) continue;
      const schoolName = schoolNames.get(alert.school_id) || 'Maktab';
      const recipients = audience === 'parent'
        ? alert.parent_phone
          ? [{ id: null, name: 'Ota-ona', phone: alert.parent_phone, notify_sms: true, is_active: true, school_id: alert.school_id }]
          : []
        : iioStaff.filter((staff) => Number(staff.school_id) === alert.school_id);
      if (!recipients.length) {
        skipped += 1;
        continue;
      }
      for (const recipient of recipients) {
        const phone = normalizeUzbekPhone(recipient.phone);
        if (!phone) {
          skipped += 1;
          continue;
        }
        const jobBase = {
          student_id: alert.student_id,
          student_name: alert.student_name,
          class_name: alert.class_name,
          school_name: schoolName,
          school_id: alert.school_id,
          attendance_date: attendanceDate,
          streak_days: alert.absence_streak,
        };
        jobs.push({
          ...jobBase,
          recipient_profile_id: recipient.id,
          phone,
          message: getSmsText(jobBase),
        });
      }
    }

    let alreadySent = 0;
    const claimedJobs: SmsJob[] = [];
    for (const job of jobs) {
      const { data: deliveryId, error } = await serviceClient.rpc('claim_attendance_sms_delivery', {
        p_student_id: job.student_id,
        p_attendance_date: job.attendance_date,
        p_streak_days: job.streak_days,
        p_recipient_profile_id: job.recipient_profile_id,
        p_recipient_phone: job.phone,
      });
      if (error) throw new Error(`SMS yuborish qaydini yaratib bo‘lmadi: ${error.message}`);
      if (!deliveryId) {
        alreadySent += 1;
        continue;
      }
      job.delivery_id = deliveryId as string;
      claimedDeliveryIds.push(job.delivery_id);
      claimedJobs.push(job);
    }

    if (!claimedJobs.length) {
      return jsonResponse({ sent: 0, failed: 0, skipped, already_sent: alreadySent });
    }

    const eskizEmail = Deno.env.get('ESKIZ_EMAIL');
    const eskizPassword = Deno.env.get('ESKIZ_PASSWORD');
    if (!eskizEmail || !eskizPassword) {
      throw new Error('Eskiz login/parol sozlanmagan. Supabase Edge Function secrets’ni tekshiring.');
    }
    const sender = Deno.env.get('ESKIZ_FROM') || '4546';
    const token = await getEskizToken(eskizEmail, eskizPassword);
    let sent = 0;
    let failed = 0;

    for (const job of claimedJobs) {
      try {
        const providerMessageId = await sendEskizSms(token, job.phone, job.message, sender);
        const { error } = await serviceClient.from('attendance_sms_deliveries')
          .update({ status: 'sent', provider_message_id: providerMessageId || null, error_message: null })
          .eq('id', job.delivery_id);
        if (error) throw new Error(`SMS natijasini saqlab bo‘lmadi: ${error.message}`);
        sent += 1;
      } catch (error) {
        const message = error instanceof Error ? error.message : 'Noma’lum SMS xatosi.';
        const { error: updateError } = await serviceClient.from('attendance_sms_deliveries')
          .update({ status: 'failed', error_message: message.slice(0, 500) })
          .eq('id', job.delivery_id);
        if (updateError) throw new Error(`SMS xatosi qayd etilmadi: ${updateError.message}`);
        failed += 1;
      }
    }

    return jsonResponse({ sent, failed, skipped, already_sent: alreadySent });
  } catch (error) {
    const message = error instanceof Error ? error.message : 'Noma’lum server xatosi.';
    if (serviceClient && claimedDeliveryIds.length) {
      const { error: updateError } = await serviceClient.from('attendance_sms_deliveries')
        .update({ status: 'failed', error_message: message.slice(0, 500) })
        .in('id', claimedDeliveryIds)
        .eq('status', 'sending');
      if (updateError) console.error('Failed to update SMS delivery status:', updateError.message);
    }
    console.error('Attendance SMS request failed:', message);
    return jsonResponse({ error: message }, 500);
  }
});
