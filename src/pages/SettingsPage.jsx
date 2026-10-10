import { useEffect, useState } from 'react';
import { useForm } from 'react-hook-form';
import { BellRing, Camera, Check, KeyRound, Save, UserRound } from 'lucide-react';
import { useAuth } from '../auth/AuthProvider.jsx';
import { Badge, Button, Card, ErrorMessage, Field, PageTitle, SelectField } from '../components/ui.jsx';
import { getStorageUrl, requireSupabase } from '../lib/supabase.js';
import { errorMessage, initials } from '../lib/format.js';

function isMissingSchoolSettingsTable(error) {
  return error?.code === '42P01'
    || error?.code === 'PGRST205'
    || /school_settings.*(not found|does not exist)|relation .*school_settings/i.test(error?.message || '');
}

function ProfileSettings({ profile, user, setProfile }) {
  const [saving, setSaving] = useState(false);
  const [problem, setProblem] = useState('');
  const [success, setSuccess] = useState('');
  const [photoFile, setPhotoFile] = useState(null);
  const [avatarUrl, setAvatarUrl] = useState('');
  const [schools, setSchools] = useState([]);
  const [defaultSchoolId, setDefaultSchoolId] = useState('');
  const [schoolsLoading, setSchoolsLoading] = useState(profile.role === 'admin');
  const { register, handleSubmit, reset, formState: { errors } } = useForm({
    defaultValues: { name: profile.name, email: profile.email || user.email, phone: profile.phone || '', position: profile.position || '' },
  });

  useEffect(() => {
    let alive = true;
    if (profile.avatar) getStorageUrl('profiles-avatars', profile.avatar)
      .then((url) => { if (alive) setAvatarUrl(url); })
      .catch((error) => { if (alive) setProblem(error.message); });
    if (profile.role === 'admin') {
      const client = requireSupabase();
      Promise.all([
        client.from('schools').select('id,name').order('name'),
        client.from('school_settings').select('default_school_id').eq('id', true).single(),
      ])
        .then(([schoolsResult, settingsResult]) => {
          if (!alive) return;
          if (schoolsResult.error) throw schoolsResult.error;
        if (settingsResult.error && !isMissingSchoolSettingsTable(settingsResult.error)) throw settingsResult.error;
          setSchools(schoolsResult.data || []);
        const selectedSchoolId = settingsResult.error
          ? profile.school_id || (schoolsResult.data?.length === 1 ? schoolsResult.data[0].id : null)
          : settingsResult.data.default_school_id;
        setDefaultSchoolId(selectedSchoolId ? String(selectedSchoolId) : '');
        setSchoolsLoading(false);
        })
        .catch((error) => {
          if (alive) {
            setProblem(error.message);
            setSchoolsLoading(false);
          }
        });
    }
    return () => { alive = false; };
  }, [profile.avatar, profile.role]);

  async function submit(values) {
    setSaving(true);
    setProblem('');
    setSuccess('');
    const client = requireSupabase();
    let newPhotoPath = null;
    try {
      if (photoFile) {
        const safeName = photoFile.name.toLowerCase().replace(/[^a-z0-9.-]+/g, '-');
        newPhotoPath = `${user.id}/${crypto.randomUUID()}-${safeName}`;
        const { error: uploadError } = await client.storage.from('profiles-avatars').upload(newPhotoPath, photoFile, { contentType: photoFile.type, upsert: false });
        if (uploadError) throw uploadError;
      }
      const updatedValues = {
        name: values.name.trim(),
        phone: values.phone.trim() || null,
        position: values.position.trim() || null,
        ...(profile.role === 'admin' ? { school_id: defaultSchoolId ? Number(defaultSchoolId) : null } : {}),
        ...(newPhotoPath ? { avatar: newPhotoPath } : {}),
      };
      const { data, error } = await client.from('profiles').update(updatedValues).eq('id', user.id).select('*').single();
      if (error) {
        if (newPhotoPath) {
          const { error: cleanupError } = await client.storage.from('profiles-avatars').remove([newPhotoPath]);
          if (cleanupError) throw new Error(`Profilni yangilash amalga oshmadi: ${error.message}. Yuklangan suratni tozalash ham amalga oshmadi: ${cleanupError.message}`);
        }
        throw error;
      }
      setProfile(data);
      reset({ ...values, email: user.email });
      setPhotoFile(null);
      if (newPhotoPath) {
        const url = await getStorageUrl('profiles-avatars', newPhotoPath);
        setAvatarUrl(url);
        if (profile.avatar) {
          const { error: removeError } = await client.storage.from('profiles-avatars').remove([profile.avatar]);
          if (removeError) throw new Error(`Profil saqlandi, ammo eski suratni o‘chirish amalga oshmadi: ${removeError.message}`);
        }
      }
      if (profile.role === 'admin') {
        const selectedSchoolId = defaultSchoolId ? Number(defaultSchoolId) : null;
        const { error: settingsError } = await client.from('school_settings').upsert({
          id: true,
          default_school_id: selectedSchoolId,
        });
        if (settingsError && !isMissingSchoolSettingsTable(settingsError)) {
          throw new Error(`Profil saqlandi, ammo asosiy maktab sozlamasi saqlanmadi: ${settingsError.message}`);
        }
        if (selectedSchoolId) {
          const { error: staffError } = await client.from('profiles')
            .update({ school_id: selectedSchoolId })
            .is('school_id', null)
            .in('role', ['teacher', 'psychologist', 'school_admin', 'iio']);
          if (staffError) {
            throw new Error(`Asosiy maktab tanlandi, ammo maktab biriktirilmagan xodimlarga biriktirilmadi: ${staffError.message}`);
          }
        }
      }
      setSuccess(profile.role === 'admin'
        ? 'Profil va asosiy maktab saqlandi. Maktabsiz xodimlarga ham asosiy maktab biriktirildi.'
        : 'Profil ma’lumotlari saqlandi.');
    } catch (error) {
      setProblem(errorMessage(error));
    } finally {
      setSaving(false);
    }
  }

  return (
    <Card>
      <div className="mb-5 flex items-center gap-3"><span className="flex h-10 w-10 items-center justify-center rounded-xl bg-green/10 text-green"><UserRound size={19} /></span><div><h2 className="font-extrabold text-navy">Shaxsiy ma’lumotlar</h2><p className="text-xs text-slate-500">Profilingiz va ish ma’lumotlari</p></div></div>
      {problem && <div className="mb-4"><ErrorMessage error={problem} /></div>}{success && <p role="status" className="mb-4 rounded-xl bg-green/5 p-3 text-sm font-bold text-green">{success}</p>}
      <form className="space-y-4" onSubmit={handleSubmit(submit)}>
        <div className="flex items-center gap-4"><label className="relative flex h-16 w-16 cursor-pointer items-center justify-center overflow-hidden rounded-full bg-green/10 text-green">{avatarUrl ? <img className="h-full w-full object-cover" src={avatarUrl} alt="Profil surati" /> : <span className="text-sm font-black">{initials(profile.name)}</span>}<span className="absolute inset-x-0 bottom-0 flex justify-center bg-navy/60 py-1 text-white"><Camera size={13} /></span><input className="sr-only" type="file" accept="image/png,image/jpeg,image/webp" onChange={(event) => setPhotoFile(event.target.files?.[0] || null)} /></label><div><p className="text-sm font-extrabold text-navy">Profil surati</p><p className="text-xs text-slate-500">{photoFile?.name || 'PNG, JPEG yoki WebP · 5 MB gacha'}</p></div></div>
        <div className="grid gap-4 sm:grid-cols-2"><Field label="To‘liq ism" error={errors.name?.message} {...register('name', { required: 'Ismni kiriting.' })} /><Field label="Email" type="email" readOnly className="opacity-70" {...register('email')} /><Field label="Telefon" type="tel" placeholder="+998 90 000 00 00" {...register('phone')} /><Field label="Lavozim" placeholder="Masalan, sinf rahbari" {...register('position')} /></div>
        {profile.role === 'admin' && <div className="rounded-2xl bg-canvas p-4">
          <SelectField label="O‘quvchilar uchun asosiy maktab" value={defaultSchoolId} disabled={schoolsLoading} onChange={(event) => setDefaultSchoolId(event.target.value)}>
            <option value="">{schoolsLoading ? 'Maktablar yuklanmoqda...' : 'Maktab tanlang'}</option>
            {schools.map((school) => <option key={school.id} value={school.id}>{school.name}</option>)}
          </SelectField>
          <p className="mt-2 text-xs leading-5 text-slate-500">
            {schoolsLoading
              ? 'Maktab sozlamalari yuklanmoqda.'
              : schools.length
                ? 'Bu maktab biriktirilmagan o‘qituvchilar va yangi o‘quvchilarga avtomatik biriktiriladi.'
                : 'Maktab topilmadi. Avval Supabase’da school yozuvini yarating.'}
          </p>
        </div>}
        <div className="flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 pt-4"><span className="text-xs text-slate-500">Hisob turi: <Badge tone="navy">{profile.role}</Badge></span><Button type="submit" disabled={saving}><Save size={16} />{saving ? 'Saqlanmoqda...' : 'Profilni saqlash'}</Button></div>
      </form>
    </Card>
  );
}

function NotificationSettings({ profile, setProfile }) {
  const [values, setValues] = useState({
    notify_email: profile.notify_email,
    notify_sms: profile.notify_sms,
    notify_risk_cases: profile.notify_risk_cases,
    notify_attendance: profile.notify_attendance,
  });
  const [saving, setSaving] = useState(false);
  const [feedback, setFeedback] = useState('');
  useEffect(() => setValues({
    notify_email: profile.notify_email,
    notify_sms: profile.notify_sms,
    notify_risk_cases: profile.notify_risk_cases,
    notify_attendance: profile.notify_attendance,
  }), [profile.notify_email, profile.notify_sms, profile.notify_risk_cases, profile.notify_attendance]);

  async function save() {
    setSaving(true);
    setFeedback('');
    try {
      const { data, error } = await requireSupabase().from('profiles').update(values).eq('id', profile.id).select('*').single();
      if (error) throw error;
      setProfile(data);
      setFeedback('Bildirishnoma sozlamalari saqlandi.');
    } catch (error) {
      setFeedback(errorMessage(error));
    } finally { setSaving(false); }
  }

  const options = [
    ['notify_email', 'Email bildirishnomalari', 'Muhim yangiliklarni elektron pochta orqali olish'],
    ['notify_sms', 'SMS bildirishnomalari', 'Zarur xabarlarni SMS orqali olish'],
    ['notify_risk_cases', 'Xavf holatlari', 'Yangi yoki o‘zgargan xavf holatlari haqida'],
    ['notify_attendance', 'Davomat ogohlantirishlari', 'Davomatdagi muhim o‘zgarishlar haqida'],
  ];
  return <Card><div className="mb-5 flex items-center gap-3"><span className="flex h-10 w-10 items-center justify-center rounded-xl bg-orange/15 text-amber-700"><BellRing size={19} /></span><div><h2 className="font-extrabold text-navy">Bildirishnomalar</h2><p className="text-xs text-slate-500">Qaysi yangiliklarni olishni tanlang</p></div></div><div className="divide-y divide-slate-100">{options.map(([key, title, hint]) => <label key={key} className="flex cursor-pointer items-center justify-between gap-4 py-4"><span><span className="block text-sm font-extrabold text-navy">{title}</span><span className="text-xs text-slate-500">{hint}</span></span><input className="toggle-control" type="checkbox" checked={Boolean(values[key])} onChange={(event) => setValues((current) => ({ ...current, [key]: event.target.checked }))} /></label>)}</div>{feedback && <p role="status" className={`mt-3 text-sm font-bold ${feedback.includes('saqlandi') ? 'text-green' : 'text-red'}`}>{feedback}</p>}<div className="mt-4 flex justify-end"><Button onClick={save} disabled={saving}><Save size={16} />{saving ? 'Saqlanmoqda...' : 'Sozlamalarni saqlash'}</Button></div></Card>;
}

function PasswordSettings() {
  const [password, setPassword] = useState('');
  const [confirm, setConfirm] = useState('');
  const [saving, setSaving] = useState(false);
  const [feedback, setFeedback] = useState('');
  async function changePassword(event) {
    event.preventDefault();
    setFeedback('');
    if (password.length < 8) { setFeedback('Yangi parol kamida 8 ta belgidan iborat bo‘lishi kerak.'); return; }
    if (password !== confirm) { setFeedback('Parollar mos kelmadi.'); return; }
    setSaving(true);
    try {
      const { error } = await requireSupabase().auth.updateUser({ password });
      if (error) throw error;
      setPassword('');
      setConfirm('');
      setFeedback('Parol muvaffaqiyatli yangilandi.');
    } catch (error) {
      setFeedback(errorMessage(error));
    } finally { setSaving(false); }
  }
  return <Card><div className="mb-5 flex items-center gap-3"><span className="flex h-10 w-10 items-center justify-center rounded-xl bg-navy/10 text-navy"><KeyRound size={19} /></span><div><h2 className="font-extrabold text-navy">Xavfsizlik</h2><p className="text-xs text-slate-500">Hisob parolingizni yangilang</p></div></div><form className="space-y-4" onSubmit={changePassword}><div className="grid gap-4 sm:grid-cols-2"><Field label="Yangi parol" type="password" minLength={8} autoComplete="new-password" value={password} onChange={(event) => setPassword(event.target.value)} required /><Field label="Parolni tasdiqlang" type="password" autoComplete="new-password" value={confirm} onChange={(event) => setConfirm(event.target.value)} required /></div>{feedback && <p role="status" className={`text-sm font-bold ${feedback.includes('muvaffaqiyatli') ? 'text-green' : 'text-red'}`}>{feedback}</p>}<div className="flex justify-end"><Button type="submit" disabled={saving}>{saving ? 'Yangilanmoqda...' : 'Parolni yangilash'}</Button></div></form></Card>;
}

export default function SettingsPage() {
  const { profile, user, setProfile } = useAuth();
  return <><PageTitle eyebrow="Hisob" title="Sozlamalar" description="Profil, xavfsizlik va bildirishnomalarni boshqaring." /><div className="grid items-start gap-5 xl:grid-cols-2"><ProfileSettings profile={profile} user={user} setProfile={setProfile} /><NotificationSettings profile={profile} setProfile={setProfile} /><div className="xl:col-span-2"><PasswordSettings /></div></div></>;
}
