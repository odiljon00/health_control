import { useCallback, useEffect, useMemo, useState } from 'react';
import { CalendarDays, Check, CheckCheck, ClipboardCheck, Save } from 'lucide-react';
import { useAuth } from '../auth/AuthProvider.jsx';
import { Badge, Button, Card, EmptyState, ErrorMessage, Loading, PageTitle, SearchBox } from '../components/ui.jsx';
import { fullName } from '../lib/format.js';
import { requireSupabase } from '../lib/supabase.js';
import { useRealtimeRefresh } from '../lib/useRealtime.js';

const statusLabels = { present: 'Keldi', absent: 'Kelmadi', late: 'Kechikdi', excused: 'Sababli' };
const statusStyles = {
  present: 'border-green bg-green text-white',
  absent: 'border-red bg-red text-white',
  late: 'border-orange bg-orange text-white',
  excused: 'border-navy bg-navy text-white',
};
const dateInTashkent = () => new Intl.DateTimeFormat('en-CA', { timeZone: 'Asia/Tashkent', year: 'numeric', month: '2-digit', day: '2-digit' }).format(new Date());

export default function AttendancePage() {
  const { profile, user } = useAuth();
  const [date, setDate] = useState(dateInTashkent());
  const [students, setStudents] = useState([]);
  const [attendance, setAttendance] = useState({});
  const [search, setSearch] = useState('');
  const [error, setError] = useState('');
  const [smsNotice, setSmsNotice] = useState('');
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
  const [saved, setSaved] = useState(false);
  const [reloadKey, setReloadKey] = useState(0);
  const reload = useCallback(() => setReloadKey((value) => value + 1), []);
  useRealtimeRefresh(['students', 'attendances'], reload);

  useEffect(() => {
    let alive = true;
    async function load() {
      setLoading(true);
      setError('');
      const client = requireSupabase();
      const [studentResult, attendanceResult] = await Promise.all([
        client.from('students').select('id,first_name,last_name,middle_name,class_name,school_id').eq('is_active', true).order('class_name').order('last_name'),
        client.from('attendances').select('student_id,status,is_excused,reason,note').eq('attendance_date', date),
      ]);
      if (studentResult.error) throw studentResult.error;
      if (attendanceResult.error) throw attendanceResult.error;
      const statusMap = Object.fromEntries(attendanceResult.data.map((row) => [row.student_id, {
        status: row.status,
        is_excused: row.is_excused,
        reason: row.reason || '',
        note: row.note || '',
      }]));
      if (alive) {
        setStudents(studentResult.data);
        setAttendance(statusMap);
      }
    }
    load().catch((reason) => { if (alive) setError(reason.message); }).finally(() => { if (alive) setLoading(false); });
    return () => { alive = false; };
  }, [date, reloadKey]);

  const visibleStudents = useMemo(() => students.filter((student) => `${fullName(student)} ${student.class_name}`.toLocaleLowerCase().includes(search.trim().toLocaleLowerCase())), [students, search]);
  const markedCount = students.filter((student) => Boolean(attendance[student.id]?.status)).length;
  const counts = students.reduce((result, student) => {
    const status = attendance[student.id]?.status;
    if (status) result[status] += 1;
    return result;
  }, { present: 0, absent: 0, late: 0, excused: 0 });

  function setStatus(studentId, status) {
    setSaved(false);
    setAttendance((current) => ({ ...current, [studentId]: { ...current[studentId], status, is_excused: status === 'excused' } }));
  }

  async function saveAttendance() {
    const unmarked = students.filter((student) => !attendance[student.id]?.status);
    if (unmarked.length) {
      setError(`${unmarked.length} nafar o‘quvchi uchun davomat belgilanmagan.`);
      return;
    }
    setSaving(true);
    setError('');
    setSmsNotice('');
    setSaved(false);
    try {
      const client = requireSupabase();
      const payload = students.map((student) => {
        const current = attendance[student.id];
        return {
          student_id: student.id,
          school_id: student.school_id,
          marked_by: user.id,
          attendance_date: date,
          status: current.status,
          is_excused: current.status === 'excused' || current.is_excused,
          reason: current.reason || null,
          note: current.note || null,
          is_alert: false,
        };
      });
      const { error: saveError } = await client.from('attendances').upsert(payload, { onConflict: 'student_id,attendance_date' });
      if (saveError) throw saveError;
      setSaved(true);
      const { data: smsResult, error: smsError } = await client.functions.invoke('send-attendance-sms', {
        body: { attendance_date: date },
      });
      if (smsError) {
        setSmsNotice(`Davomat saqlandi, lekin SMS ogohlantirishlari yuborilmadi. Qayta urinib ko‘ring: ${smsError.message}`);
      } else if (smsResult?.failed) {
        setSmsNotice(`Davomat saqlandi. ${smsResult.sent} ta SMS yuborildi, ${smsResult.failed} tasini yuborib bo‘lmadi. Davomatni qayta saqlab urinib ko‘ring.`);
      } else if (smsResult?.skipped) {
        setSmsNotice(`Davomat saqlandi. ${smsResult.sent} ta SMS yuborildi. ${smsResult.skipped} ta ogohlantirish uchun ota-ona yoki IIO telefon raqami topilmadi yoki SMS o‘chirilgan.`);
      } else if (smsResult?.sent) {
        setSmsNotice(`${smsResult.sent} ta SMS ogohlantirish yuborildi.`);
      }
    } catch (reason) {
      setError(reason.message);
    } finally {
      setSaving(false);
    }
  }

  return (
    <>
      <PageTitle eyebrow="Davomat" title="Kundalik davomat" description="O‘quvchilarning kelganligi va sababsiz qoldirishini qayd eting." action={<Button onClick={saveAttendance} disabled={saving || !students.length}><Save size={16} />{saving ? 'Saqlanmoqda...' : 'Davomatni saqlash'}</Button>} />
      <ErrorMessage error={error} onRetry={reload} />
      {saved && <div role="status" className="mb-5 rounded-xl border border-green/20 bg-green/5 px-4 py-3 text-sm font-bold text-green">Davomat muvaffaqiyatli saqlandi.</div>}
      {smsNotice && <div role="status" className="mb-5 rounded-xl border border-orange/20 bg-orange/10 px-4 py-3 text-sm font-bold text-amber-800">{smsNotice}</div>}
      <div className="mb-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
        {[['Jami o‘quvchi', students.length, 'text-navy'], ['Kelgan', counts.present, 'text-green'], ['Kelmagan', counts.absent, 'text-red'], ['Sababli / kechikkan', counts.excused + counts.late, 'text-amber-700']].map(([label, count, tone]) => <Card key={label} className="!p-4"><p className="text-xs font-bold text-slate-500">{label}</p><p className={`mt-1 text-2xl font-black ${tone}`}>{count}</p></Card>)}
      </div>
      <Card className="!p-0">
        <div className="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 p-4 sm:p-5">
          <label className="relative"><CalendarDays size={17} className="absolute left-3 top-1/2 -translate-y-1/2 text-green" /><input aria-label="Davomat sanasi" type="date" className="field min-w-44 pl-10" value={date} max={dateInTashkent()} onChange={(event) => { setDate(event.target.value); setSaved(false); }} /></label>
          <div className="flex flex-wrap items-center gap-2"><span className="text-xs font-bold text-slate-500">{markedCount}/{students.length} belgilandi</span><Button variant="secondary" className="!min-h-9 !px-3" onClick={() => { setAttendance(Object.fromEntries(students.map((student) => [student.id, { ...attendance[student.id], status: 'present', is_excused: false }]))); setSaved(false); }}><CheckCheck size={15} /> Barchasini keldi</Button><SearchBox value={search} onChange={setSearch} placeholder="O‘quvchi qidirish..." /></div>
        </div>
        {loading && !students.length ? <Loading /> : <div className="overflow-x-auto"><table className="data-table min-w-[760px]"><thead><tr><th>O‘quvchi</th><th>Sinf</th><th>Davomat holati</th><th>Belgisi</th></tr></thead><tbody>
          {visibleStudents.length ? visibleStudents.map((student) => {
            const currentStatus = attendance[student.id]?.status;
            return <tr key={student.id}><td className="font-extrabold text-navy">{fullName(student)}</td><td>{student.class_name}</td><td><div className="flex flex-wrap gap-1.5">{Object.entries(statusLabels).map(([status, label]) => <button key={status} onClick={() => setStatus(student.id, status)} className={`rounded-lg border px-2.5 py-1.5 text-xs font-extrabold transition ${currentStatus === status ? statusStyles[status] : 'border-slate-200 bg-white text-slate-500 hover:border-slate-300'}`}>{label}</button>)}</div></td><td>{currentStatus ? <Badge tone={currentStatus === 'present' ? 'green' : currentStatus === 'absent' ? 'red' : currentStatus === 'late' ? 'orange' : 'navy'}><Check size={12} className="mr-1" />{statusLabels[currentStatus]}</Badge> : <Badge>Belgilanmagan</Badge>}</td></tr>;
          }) : <tr><td colSpan="4"><EmptyState title={search ? 'O‘quvchi topilmadi' : 'Faol o‘quvchilar topilmadi'} description="Tanlangan sana uchun davomatni belgilashingiz mumkin." /></td></tr>}
        </tbody></table></div>}
      </Card>
      {profile?.role === 'parent' && <p className="mt-3 text-xs text-slate-500">Ota-onalar bu sahifada faqat o‘z farzandining davomatini ko‘ra oladi; belgilash faqat xodimlar uchun.</p>}
    </>
  );
}
