import { useCallback, useEffect, useMemo, useState } from 'react';
import { useForm } from 'react-hook-form';
import { Link } from 'react-router-dom';
import { Edit3, Plus, Trash2, UserRound, UsersRound } from 'lucide-react';
import { useAuth } from '../auth/AuthProvider.jsx';
import { Badge, Button, Card, EmptyState, ErrorMessage, Field, Loading, Modal, PageTitle, SearchBox, SelectField } from '../components/ui.jsx';
import { errorMessage, fullName } from '../lib/format.js';
import { getStorageUrl, requireSupabase } from '../lib/supabase.js';
import { useRealtimeRefresh } from '../lib/useRealtime.js';
import { resolveStudentSchool } from './studentSchoolAssignment.js';

function StudentDialog({ student, schools, profile, onClose, onSaved }) {
  const [saving, setSaving] = useState(false);
  const [formError, setFormError] = useState('');
  const [photo, setPhoto] = useState(null);
  const schoolAssignment = resolveStudentSchool({ profile, student, schools });
  const { register, handleSubmit, formState: { errors } } = useForm({
    defaultValues: student
      ? { ...student, parent_phone: student.parent_phone || '' }
      : {
        first_name: '', last_name: '', middle_name: '', birth_date: '', class_name: '',
        gender: '', parent_phone: '', is_active: true,
      },
  });

  async function onSubmit(values) {
    setSaving(true);
    setFormError('');
    const client = requireSupabase();
    let savedStudent;
    try {
      const payload = {
        first_name: values.first_name.trim(),
        last_name: values.last_name.trim(),
        middle_name: values.middle_name.trim() || null,
        parent_phone: values.parent_phone.trim(),
        birth_date: values.birth_date || null,
        class_name: values.class_name.trim(),
        school_id: Number(schoolAssignment.schoolId),
        gender: values.gender || null,
        is_active: values.is_active === true || values.is_active === 'true',
      };
      if (!schoolAssignment.canAssignSchool) {
        throw new Error(profile.role === 'admin'
          ? 'Avval Sozlamalar sahifasida o‘quvchilar uchun asosiy maktabni tanlab saqlang.'
          : 'Sizning profilingizga maktab biriktirilmagan. Administrator asosiy maktabni tanlab saqlagach, sahifani yangilang yoki qayta kiring.');
      }
      const result = student
        ? await client.from('students').update(payload).eq('id', student.id).select().single()
        : await client.from('students').insert(payload).select().single();
      if (result.error) throw result.error;
      savedStudent = result.data;
      if (photo) {
        const safeName = photo.name.toLowerCase().replace(/[^a-z0-9.-]+/g, '-');
        const photoPath = `${savedStudent.school_id}/${profile.id}/${crypto.randomUUID()}-${safeName}`;
        const { error: uploadError } = await client.storage.from('student-photos').upload(photoPath, photo, { contentType: photo.type, upsert: false });
        if (uploadError) throw uploadError;
        const { error: updateError } = await client.from('students').update({ photo: photoPath }).eq('id', savedStudent.id);
        if (updateError) {
          const { error: cleanupError } = await client.storage.from('student-photos').remove([photoPath]);
          if (cleanupError) throw new Error(`Surat yuklandi, ammo o‘quvchi ma’lumotlariga biriktirilmadi: ${updateError.message}. Yuklangan suratni tozalash ham amalga oshmadi: ${cleanupError.message}`);
          throw updateError;
        }
        if (student?.photo) {
          const { error: removeError } = await client.storage.from('student-photos').remove([student.photo]);
          if (removeError) throw new Error(`O‘quvchi saqlandi, ammo eski suratni o‘chirish amalga oshmadi: ${removeError.message}`);
        }
      }
      onSaved();
      onClose();
    } catch (error) {
      setFormError(errorMessage(error));
    } finally {
      setSaving(false);
    }
  }

  return (
    <Modal title={student ? 'O‘quvchini tahrirlash' : 'Yangi o‘quvchi'} onClose={onClose}>
      <form className="space-y-4" onSubmit={handleSubmit(onSubmit)}>
        {formError && <div role="alert" className="rounded-xl bg-red/5 p-3 text-sm font-bold text-red">{formError}</div>}
        <div className="grid gap-4 sm:grid-cols-2">
          <Field label="Ism" placeholder="Ismi" error={errors.first_name?.message} {...register('first_name', { required: 'Ismni kiriting.' })} />
          <Field label="Familiya" placeholder="Familiyasi" error={errors.last_name?.message} {...register('last_name', { required: 'Familiyani kiriting.' })} />
          <Field label="Otasining ismi" placeholder="Ixtiyoriy" {...register('middle_name')} />
          <Field
            label="Ota-onaning telefon raqami"
            type="tel"
            placeholder="+998 90 123 45 67"
            autoComplete="tel"
            maxLength={30}
            error={errors.parent_phone?.message}
            {...register('parent_phone', {
              required: 'SMS uchun ota-onaning telefon raqamini kiriting.',
              validate: (value) => {
                const digits = value.replace(/\D/g, '');
                return /^998\d{9}$/.test(digits) || /^9\d{8}$/.test(digits)
                  || 'O‘zbekiston raqamini +998 XX XXX XX XX ko‘rinishida kiriting.';
              },
            })}
          />
          <Field label="Tug‘ilgan sana" type="date" {...register('birth_date')} />
          <Field label="Sinf" placeholder="Masalan, 5-A" error={errors.class_name?.message} {...register('class_name', { required: 'Sinfni kiriting.' })} />
          <SelectField label="Jinsi" {...register('gender')}><option value="">Tanlanmagan</option><option value="female">Qiz</option><option value="male">O‘g‘il</option></SelectField>
          <div className="flex items-center gap-2 rounded-xl bg-green/5 px-3 py-2.5 text-xs font-bold text-green sm:col-span-2">
            <UserRound size={15} />
            {schoolAssignment.canAssignSchool
              ? schoolAssignment.schoolName || 'Maktab foydalanuvchi hisobidan avtomatik biriktiriladi.'
              : profile?.role === 'admin'
                ? 'O‘quvchi qo‘shish uchun avval sozlamalarda asosiy maktabni tanlang.'
                : 'Administrator hali asosiy maktabni sozlamagan. Undan asosiy maktabni tanlashini so‘rang.'}
          </div>
          {!schoolAssignment.canAssignSchool && profile?.role === 'admin' && <Link className="text-sm font-extrabold text-green hover:underline sm:col-span-2" to="/settings" onClick={onClose}>Asosiy maktabni sozlash →</Link>}
          <label className="block sm:col-span-2"><span className="mb-1.5 block text-sm font-bold text-navy">O‘quvchi surati</span><input type="file" accept="image/png,image/jpeg,image/webp" className="field file:mr-3 file:rounded-lg file:border-0 file:bg-green/10 file:px-3 file:py-1.5 file:text-xs file:font-extrabold file:text-green" onChange={(event) => setPhoto(event.target.files?.[0] || null)} /><span className="mt-1 block text-xs text-slate-400">PNG, JPEG yoki WebP · 5 MB gacha</span></label>
        </div>
        <label className="flex items-center gap-2 text-sm font-bold text-slate-600"><input type="checkbox" className="accent-green" {...register('is_active')} /> Faol o‘quvchi</label>
        <div className="flex justify-end gap-2 border-t border-slate-100 pt-4"><Button type="button" variant="secondary" onClick={onClose}>Bekor qilish</Button><Button type="submit" disabled={saving}>{saving ? 'Saqlanmoqda...' : student ? 'O‘zgarishlarni saqlash' : 'O‘quvchini qo‘shish'}</Button></div>
      </form>
    </Modal>
  );
}

export default function StudentsPage() {
  const { profile } = useAuth();
  const [students, setStudents] = useState([]);
  const [schools, setSchools] = useState([]);
  const [effectiveSchoolId, setEffectiveSchoolId] = useState(null);
  const [search, setSearch] = useState('');
  const [filter, setFilter] = useState('active');
  const [selected, setSelected] = useState(null);
  const [dialogOpen, setDialogOpen] = useState(false);
  const [error, setError] = useState('');
  const [loading, setLoading] = useState(true);
  const [reloadKey, setReloadKey] = useState(0);
  const [deletingId, setDeletingId] = useState(null);
  const reload = useCallback(() => setReloadKey((value) => value + 1), []);
  useRealtimeRefresh(['students'], reload);

  useEffect(() => {
    let alive = true;
    async function load() {
      setLoading(true);
      setError('');
      const client = requireSupabase();
      const [studentsResult, schoolsResult, schoolIdResult] = await Promise.all([
        client.from('students').select('id,first_name,last_name,middle_name,parent_phone,birth_date,class_name,school_id,gender,photo,is_active,created_at').order('last_name'),
        client.from('schools').select('id,name').order('name'),
        client.rpc('current_school_id'),
      ]);
      if (studentsResult.error) throw studentsResult.error;
      if (schoolsResult.error) throw schoolsResult.error;
      if (schoolIdResult.error) throw schoolIdResult.error;
      const withPhotos = await Promise.all(studentsResult.data.map(async (student) => ({
        ...student,
        photoUrl: student.photo ? await getStorageUrl('student-photos', student.photo) : null,
      })));
      if (alive) {
        setStudents(withPhotos);
        setSchools(schoolsResult.data || []);
        setEffectiveSchoolId(schoolIdResult.data);
      }
    }
    load().catch((reason) => { if (alive) setError(reason.message); }).finally(() => { if (alive) setLoading(false); });
    return () => { alive = false; };
  }, [profile?.role, reloadKey]);

  const shownStudents = useMemo(() => students.filter((student) => {
    const query = search.trim().toLocaleLowerCase();
    const matchesSearch = !query || `${fullName(student)} ${student.class_name}`.toLocaleLowerCase().includes(query);
    const matchesStatus = filter === 'all' || (filter === 'active' ? student.is_active : !student.is_active);
    return matchesSearch && matchesStatus;
  }), [students, search, filter]);

  async function removeStudent(student) {
    if (!window.confirm(`${fullName(student)} ma’lumotlarini o‘chirishni xohlaysizmi?`)) return;
    setDeletingId(student.id);
    setError('');
    const client = requireSupabase();
    try {
      const { error: deleteError } = await client.from('students').delete().eq('id', student.id);
      if (deleteError) throw deleteError;
      if (student.photo) {
        const { error: photoError } = await client.storage.from('student-photos').remove([student.photo]);
        if (photoError) throw new Error(`O‘quvchi o‘chirildi, ammo suratni o‘chirish amalga oshmadi: ${photoError.message}`);
      }
      reload();
    } catch (reason) {
      setError(reason.message);
    } finally {
      setDeletingId(null);
    }
  }

  return (
    <>
      <PageTitle eyebrow="O‘quvchilar" title="O‘quvchilar ro‘yxati" description="Maktab o‘quvchilarini boshqaring, ma’lumotlarini yangilang." action={<Button onClick={() => { setSelected(null); setDialogOpen(true); }}><Plus size={17} /> O‘quvchi qo‘shish</Button>} />
      <ErrorMessage error={error} onRetry={reload} />
      <Card className="!p-0">
        <div className="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 p-4 sm:p-5">
          <div className="flex items-center gap-2 rounded-xl bg-green/5 px-3 py-2 text-sm font-extrabold text-green"><UsersRound size={17} />{shownStudents.length} nafar o‘quvchi</div>
          <div className="flex flex-wrap gap-2"><SearchBox value={search} onChange={setSearch} placeholder="Ism yoki sinf..." /><select className="field w-auto min-w-32" value={filter} onChange={(event) => setFilter(event.target.value)}><option value="active">Faol</option><option value="inactive">Faol emas</option><option value="all">Barchasi</option></select></div>
        </div>
        {loading && !students.length ? <Loading /> : <div className="overflow-x-auto"><table className="data-table min-w-[760px]"><thead><tr><th>O‘quvchi</th><th>Sinf</th><th>Tug‘ilgan sana</th><th>Jinsi</th><th>Holati</th><th className="text-right">Amallar</th></tr></thead><tbody>
          {shownStudents.length ? shownStudents.map((student) => <tr key={student.id}>
            <td><div className="flex items-center gap-3">{student.photoUrl ? <img className="h-10 w-10 rounded-full object-cover" src={student.photoUrl} alt="" /> : <span className="flex h-10 w-10 items-center justify-center rounded-full bg-green/10 text-green"><UserRound size={18} /></span>}<span className="font-extrabold text-navy">{fullName(student)}</span></div></td>
            <td><Badge tone="navy">{student.class_name}</Badge></td><td>{student.birth_date || '—'}</td><td>{student.gender === 'female' ? 'Qiz' : student.gender === 'male' ? 'O‘g‘il' : '—'}</td><td><Badge tone={student.is_active ? 'green' : 'slate'}>{student.is_active ? 'Faol' : 'Faol emas'}</Badge></td>
            <td><div className="flex justify-end gap-1"><button className="icon-button" aria-label={`${fullName(student)} ni tahrirlash`} title="Tahrirlash" onClick={() => { setSelected(student); setDialogOpen(true); }}><Edit3 size={16} /></button><button disabled={deletingId === student.id} className="icon-button text-red hover:bg-red/5" aria-label={`${fullName(student)} ni o‘chirish`} title="O‘chirish" onClick={() => removeStudent(student)}><Trash2 size={16} /></button></div></td>
          </tr>) : <tr><td colSpan="6"><EmptyState title={search ? 'Qidiruv bo‘yicha natija topilmadi' : 'O‘quvchilar hozircha yo‘q'} description="Yangi o‘quvchini qo‘shish uchun yuqoridagi tugmadan foydalaning." /></td></tr>}
        </tbody></table></div>}
      </Card>
      {dialogOpen && <StudentDialog key={selected?.id || 'new'} student={selected} schools={schools} profile={{ ...profile, school_id: effectiveSchoolId ?? profile.school_id }} onClose={() => setDialogOpen(false)} onSaved={reload} />}
    </>
  );
}
