import { useCallback, useEffect, useMemo, useState } from 'react';
import { useForm } from 'react-hook-form';
import { Plus, ShieldAlert } from 'lucide-react';
import { useAuth } from '../auth/AuthProvider.jsx';
import { Badge, Button, Card, EmptyState, ErrorMessage, Field, Loading, Modal, PageTitle, SearchBox, SelectField, TextAreaField } from '../components/ui.jsx';
import { formatDate, fullName } from '../lib/format.js';
import { requireSupabase } from '../lib/supabase.js';
import { useRealtimeRefresh } from '../lib/useRealtime.js';

const caseStatuses = [
  ['new', 'Yangi'],
  ['in_progress', 'Ko‘rib chiqilmoqda'],
  ['ongoing', 'Davom etmoqda'],
  ['improved', 'Yaxshilandi'],
  ['severe', 'Jiddiy'],
  ['closed', 'Yopildi'],
];
const statusLabels = Object.fromEntries(caseStatuses);

function CaseDialog({ students, profile, user, onClose, onSaved }) {
  const [saving, setSaving] = useState(false);
  const [problem, setProblem] = useState('');
  const { register, handleSubmit, watch, formState: { errors } } = useForm({
    defaultValues: { student_id: '', risk_type: '', risk_score: 20, description: '', notify_parent: true, notify_iio: false },
  });
  const score = Number(watch('risk_score') || 0);
  const level = score >= 70 ? 'red' : score >= 40 ? 'yellow' : 'green';
  async function submit(values) {
    setSaving(true);
    setProblem('');
    try {
      const student = students.find((item) => String(item.id) === String(values.student_id));
      if (!student) throw new Error('O‘quvchini tanlang.');
      const { error } = await requireSupabase().from('risk_cases').insert({
        student_id: student.id,
        reported_by: user.id,
        school_id: student.school_id,
        risk_type: values.risk_type.trim(),
        risk_score: Number(values.risk_score),
        risk_level: level,
        description: values.description.trim() || null,
        status: 'new',
        notify_parent: values.notify_parent,
        notify_iio: values.notify_iio,
      });
      if (error) throw error;
      onSaved();
      onClose();
    } catch (error) {
      setProblem(error.message);
    } finally {
      setSaving(false);
    }
  }
  return (
    <Modal title="Yangi xavf holati" onClose={onClose}>
      <form className="space-y-4" onSubmit={handleSubmit(submit)}>
        {problem && <p role="alert" className="rounded-xl bg-red/5 p-3 text-sm font-bold text-red">{problem}</p>}
        <SelectField label="O‘quvchi" error={errors.student_id?.message} {...register('student_id', { required: 'O‘quvchini tanlang.' })}><option value="">O‘quvchini tanlang</option>{students.map((student) => <option key={student.id} value={student.id}>{fullName(student)} · {student.class_name}</option>)}</SelectField>
        <Field label="Holat turi" placeholder="Masalan, ruhiy salomatlik" error={errors.risk_type?.message} {...register('risk_type', { required: 'Holat turini kiriting.' })} />
        <div className="grid gap-4 sm:grid-cols-2"><Field label="Xavf bali (0–100)" type="number" min="0" max="100" error={errors.risk_score?.message} {...register('risk_score', { required: true, min: 0, max: 100, valueAsNumber: true })} /><div><span className="mb-1.5 block text-sm font-bold text-navy">Xavf darajasi</span><Badge tone={level === 'red' ? 'red' : level === 'yellow' ? 'orange' : 'green'}>{level === 'red' ? 'Yuqori' : level === 'yellow' ? 'O‘rta' : 'Past'} · {score}</Badge></div></div>
        <TextAreaField label="Tavsif" placeholder="Holat tafsilotlarini yozing" {...register('description')} />
        <div className="flex flex-wrap gap-x-5 gap-y-2 text-sm font-bold text-slate-600"><label className="flex items-center gap-2"><input className="accent-green" type="checkbox" {...register('notify_parent')} /> Ota-onani xabardor qilish</label><label className="flex items-center gap-2"><input className="accent-green" type="checkbox" {...register('notify_iio')} /> IIOga yo‘naltirish</label></div>
        <div className="flex justify-end gap-2 border-t border-slate-100 pt-4"><Button type="button" variant="secondary" onClick={onClose}>Bekor qilish</Button><Button type="submit" disabled={saving}>{saving ? 'Saqlanmoqda...' : 'Holatni yaratish'}</Button></div>
      </form>
    </Modal>
  );
}

export default function CasesPage() {
  const { profile, user } = useAuth();
  const [cases, setCases] = useState([]);
  const [students, setStudents] = useState([]);
  const [search, setSearch] = useState('');
  const [statusFilter, setStatusFilter] = useState('all');
  const [levelFilter, setLevelFilter] = useState('all');
  const [dialogOpen, setDialogOpen] = useState(false);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');
  const [reloadKey, setReloadKey] = useState(0);
  const [savingId, setSavingId] = useState(null);
  const reload = useCallback(() => setReloadKey((value) => value + 1), []);
  useRealtimeRefresh(['risk_cases', 'students', 'case_actions'], reload);

  useEffect(() => {
    let alive = true;
    async function load() {
      setLoading(true);
      setError('');
      const client = requireSupabase();
      const casesQuery = profile?.role === 'parent'
        ? client.from('parent_visible_risk_cases').select('id,student_id,risk_type,risk_score,risk_level,description,status,created_at,first_name,last_name,middle_name,class_name').order('created_at', { ascending: false })
        : client.from('risk_cases').select('id,student_id,reported_by,school_id,risk_type,risk_score,risk_level,description,status,notify_parent,notify_iio,created_at,student:students(first_name,last_name,middle_name,class_name)').order('created_at', { ascending: false });
      const [casesResult, studentsResult] = await Promise.all([
        casesQuery,
        client.from('students').select('id,first_name,last_name,middle_name,class_name,school_id').eq('is_active', true).order('last_name'),
      ]);
      if (casesResult.error) throw casesResult.error;
      if (studentsResult.error) throw studentsResult.error;
      const visibleCases = profile?.role === 'parent'
        ? casesResult.data.map((item) => ({ ...item, student: { first_name: item.first_name, last_name: item.last_name, middle_name: item.middle_name, class_name: item.class_name } }))
        : casesResult.data;
      if (alive) { setCases(visibleCases); setStudents(studentsResult.data); }
    }
    load().catch((reason) => { if (alive) setError(reason.message); }).finally(() => { if (alive) setLoading(false); });
    return () => { alive = false; };
  }, [profile?.role, reloadKey]);

  const canManage = ['admin', 'teacher', 'psychologist', 'school_admin'].includes(profile?.role);
  const visibleCases = useMemo(() => cases.filter((item) => {
    const term = search.trim().toLocaleLowerCase();
    return (!term || `${fullName(item.student)} ${item.risk_type} ${item.student?.class_name || ''}`.toLocaleLowerCase().includes(term))
      && (statusFilter === 'all' || item.status === statusFilter)
      && (levelFilter === 'all' || item.risk_level === levelFilter);
  }), [cases, search, statusFilter, levelFilter]);

  async function updateStatus(item, nextStatus) {
    if (item.status === nextStatus) return;
    setSavingId(item.id);
    setError('');
    const client = requireSupabase();
    try {
      const { error: statusError } = await client.from('risk_cases').update({
        status: nextStatus,
        closed_at: nextStatus === 'closed' ? new Date().toISOString() : null,
      }).eq('id', item.id);
      if (statusError) throw statusError;
      const { error: actionError } = await client.from('case_actions').insert({
        risk_case_id: item.id,
        user_id: user.id,
        action_type: 'status_changed',
        note: `Holat "${statusLabels[item.status]}" dan "${statusLabels[nextStatus]}" ga o‘zgartirildi.`,
        result: nextStatus,
      });
      if (actionError) throw new Error(`Holat yangilandi, ammo faoliyat tarixiga yozilmadi: ${actionError.message}`);
      reload();
    } catch (reason) {
      setError(reason.message);
      reload();
    } finally {
      setSavingId(null);
    }
  }

  return (
    <>
      <PageTitle eyebrow="Monitoring" title="Xavf holatlari" description="O‘quvchilar uchun qo‘shimcha e’tibor talab qiladigan holatlarni kuzating." action={canManage && <Button onClick={() => setDialogOpen(true)}><Plus size={17} /> Holat qo‘shish</Button>} />
      <ErrorMessage error={error} onRetry={reload} />
      <div className="mb-5 grid gap-3 sm:grid-cols-3">
        {[['Barcha ochiq holatlar', cases.filter((item) => item.status !== 'closed').length, 'navy'], ['Yuqori xavf', cases.filter((item) => item.risk_level === 'red' && item.status !== 'closed').length, 'red'], ['Yangi holatlar', cases.filter((item) => item.status === 'new').length, 'orange']].map(([label, value, tone]) => <Card key={label} className="!p-4"><p className="text-xs font-bold text-slate-500">{label}</p><p className={`mt-1 text-2xl font-black ${tone === 'red' ? 'text-red' : tone === 'orange' ? 'text-amber-700' : 'text-navy'}`}>{value}</p></Card>)}
      </div>
      <Card className="!p-0">
        <div className="flex flex-wrap items-center gap-2 border-b border-slate-100 p-4 sm:p-5"><SearchBox value={search} onChange={setSearch} placeholder="O‘quvchi yoki holat..." /><select className="field w-auto min-w-36" value={statusFilter} onChange={(event) => setStatusFilter(event.target.value)}><option value="all">Barcha holatlar</option>{caseStatuses.map(([value, label]) => <option key={value} value={value}>{label}</option>)}</select><select className="field w-auto min-w-32" value={levelFilter} onChange={(event) => setLevelFilter(event.target.value)}><option value="all">Barcha daraja</option><option value="red">Yuqori xavf</option><option value="yellow">O‘rta xavf</option><option value="green">Past xavf</option></select></div>
        {loading && !cases.length ? <Loading /> : <div className="overflow-x-auto"><table className="data-table min-w-[900px]"><thead><tr><th>O‘quvchi</th><th>Holat</th><th>Sinf</th><th>Xavf darajasi</th><th>Sana</th><th>Jarayon</th></tr></thead><tbody>
          {visibleCases.length ? visibleCases.map((item) => <tr key={item.id}><td className="font-extrabold text-navy">{fullName(item.student)}</td><td>{item.risk_type}<p className="mt-1 max-w-[250px] truncate text-xs text-slate-400">{item.description}</p></td><td>{item.student?.class_name || '—'}</td><td><Badge tone={item.risk_level === 'red' ? 'red' : item.risk_level === 'yellow' ? 'orange' : 'green'}><ShieldAlert size={12} className="mr-1" />{item.risk_level === 'red' ? 'Yuqori' : item.risk_level === 'yellow' ? 'O‘rta' : 'Past'} · {item.risk_score}</Badge></td><td>{formatDate(item.created_at)}</td><td>{canManage ? <select aria-label={`${fullName(item.student)} holati`} className="field min-w-40 !py-2 text-xs" disabled={savingId === item.id} value={item.status} onChange={(event) => updateStatus(item, event.target.value)}>{caseStatuses.map(([value, label]) => <option key={value} value={value}>{label}</option>)}</select> : <Badge tone={item.status === 'closed' ? 'green' : item.status === 'new' ? 'orange' : 'navy'}>{statusLabels[item.status]}</Badge>}</td></tr>) : <tr><td colSpan="6"><EmptyState title="Xavf holatlari topilmadi" description="Filtrlarni o‘zgartiring yoki yangi holat yarating." /></td></tr>}
        </tbody></table></div>}
      </Card>
      {dialogOpen && <CaseDialog students={students} profile={profile} user={user} onClose={() => setDialogOpen(false)} onSaved={reload} />}
    </>
  );
}
