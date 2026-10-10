import { useCallback, useEffect, useState } from 'react';
import { useForm } from 'react-hook-form';
import { Mail, Star, Trash2 } from 'lucide-react';
import { Badge, Button, Card, EmptyState, ErrorMessage, Loading, Modal, PageTitle, SelectField, TextAreaField } from '../components/ui.jsx';
import { formatDate } from '../lib/format.js';
import { requireSupabase } from '../lib/supabase.js';
import { useRealtimeRefresh } from '../lib/useRealtime.js';

const messageStatuses = [['new', 'Yangi'], ['read', 'O‘qildi'], ['replied', 'Javob berildi'], ['archived', 'Arxiv']];
const messageStatusLabels = Object.fromEntries(messageStatuses);

function MessageDialog({ message, onClose, onSaved }) {
  const [saving, setSaving] = useState(false);
  const [problem, setProblem] = useState('');
  const { register, handleSubmit } = useForm({ defaultValues: { status: message.status, admin_note: message.admin_note || '' } });

  async function submit(values) {
    setSaving(true);
    setProblem('');
    const now = new Date().toISOString();
    const payload = {
      status: values.status,
      admin_note: values.admin_note.trim() || null,
      read_at: values.status === 'new' ? null : message.read_at || now,
      replied_at: values.status === 'replied' ? message.replied_at || now : message.replied_at,
    };
    try {
      const { error } = await requireSupabase().from('messages').update(payload).eq('id', message.id);
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
    <Modal title={message.subject || 'Xabar tafsilotlari'} onClose={onClose} className="max-w-2xl">
      <div className="mb-5 grid gap-3 rounded-2xl bg-canvas p-4 text-sm sm:grid-cols-2">
        <p><span className="text-slate-500">Yuboruvchi:</span> <strong className="text-navy">{message.name}</strong></p>
        <p><span className="text-slate-500">Sana:</span> <strong className="text-navy">{formatDate(message.created_at)}</strong></p>
        <p><span className="text-slate-500">Email:</span> <a className="font-bold text-green hover:underline" href={`mailto:${message.email}`}>{message.email}</a></p>
        <p><span className="text-slate-500">Telefon:</span> <strong className="text-navy">{message.phone || '—'}</strong></p>
      </div>
      <div className="mb-5 whitespace-pre-wrap rounded-2xl border border-slate-100 p-4 text-sm leading-6 text-slate-700">{message.message}</div>
      <form className="space-y-4" onSubmit={handleSubmit(submit)}>
        {problem && <p role="alert" className="rounded-xl bg-red/5 p-3 text-sm font-bold text-red">{problem}</p>}
        <SelectField label="Xabar holati" {...register('status')}>{messageStatuses.map(([value, label]) => <option value={value} key={value}>{label}</option>)}</SelectField>
        <TextAreaField label="Ichki izoh" placeholder="Faqat administratorlar ko‘ra oladi" {...register('admin_note')} />
        <div className="flex justify-end gap-2 border-t border-slate-100 pt-4"><Button type="button" variant="secondary" onClick={onClose}>Yopish</Button><Button type="submit" disabled={saving}>{saving ? 'Saqlanmoqda...' : 'Saqlash'}</Button></div>
      </form>
    </Modal>
  );
}

export default function MessagesPage() {
  const [messages, setMessages] = useState([]);
  const [selected, setSelected] = useState(null);
  const [statusFilter, setStatusFilter] = useState('all');
  const [error, setError] = useState('');
  const [loading, setLoading] = useState(true);
  const [reloadKey, setReloadKey] = useState(0);
  const [pendingId, setPendingId] = useState(null);
  const reload = useCallback(() => setReloadKey((value) => value + 1), []);
  useRealtimeRefresh(['messages'], reload);

  useEffect(() => {
    let alive = true;
    async function load() {
      setLoading(true);
      setError('');
      const { data, error: queryError } = await requireSupabase().from('messages')
        .select('id,name,email,phone,subject,message,status,is_starred,admin_note,read_at,replied_at,created_at')
        .order('is_starred', { ascending: false })
        .order('created_at', { ascending: false });
      if (queryError) throw queryError;
      if (alive) setMessages(data);
    }
    load().catch((reason) => { if (alive) setError(reason.message); }).finally(() => { if (alive) setLoading(false); });
    return () => { alive = false; };
  }, [reloadKey]);

  async function toggleStar(message) {
    setPendingId(message.id);
    try {
      const { error: updateError } = await requireSupabase().from('messages').update({ is_starred: !message.is_starred }).eq('id', message.id);
      if (updateError) throw updateError;
      reload();
    } catch (reason) { setError(reason.message); }
    finally { setPendingId(null); }
  }

  async function deleteMessage(message) {
    if (!window.confirm(`“${message.subject || 'Xabar'}”ni butunlay o‘chirishni xohlaysizmi?`)) return;
    setPendingId(message.id);
    try {
      const { error: deleteError } = await requireSupabase().from('messages').delete().eq('id', message.id);
      if (deleteError) throw deleteError;
      reload();
    } catch (reason) { setError(reason.message); }
    finally { setPendingId(null); }
  }

  const visible = messages.filter((message) => statusFilter === 'all' || message.status === statusFilter);
  const unread = messages.filter((message) => message.status === 'new').length;

  return (
    <>
      <PageTitle eyebrow="Aloqa" title="Xabarlar" description="Sayt orqali yuborilgan murojaatlarni ko‘rib chiqing." />
      <ErrorMessage error={error} onRetry={reload} />
      <Card className="!p-0">
        <div className="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 p-4 sm:p-5">
          <div className="flex items-center gap-2"><span className="flex h-10 w-10 items-center justify-center rounded-xl bg-green/10 text-green"><Mail size={19} /></span><div><p className="text-sm font-extrabold text-navy">Kelib tushgan xabarlar</p><p className="text-xs text-slate-500">{unread} ta yangi xabar</p></div></div>
          <select className="field w-auto min-w-36" value={statusFilter} onChange={(event) => setStatusFilter(event.target.value)}><option value="all">Barcha xabarlar</option>{messageStatuses.map(([value, label]) => <option key={value} value={value}>{label}</option>)}</select>
        </div>
        {loading && !messages.length ? <Loading /> : <div className="overflow-x-auto"><table className="data-table min-w-[800px]"><thead><tr><th></th><th>Yuboruvchi</th><th>Mavzu</th><th>Xabar</th><th>Sana</th><th>Holati</th><th>Amallar</th></tr></thead><tbody>
          {visible.length ? visible.map((message) => <tr key={message.id} className={message.status === 'new' ? 'bg-green/[0.025]' : ''}><td><button disabled={pendingId === message.id} className={`rounded-lg p-2 ${message.is_starred ? 'text-orange' : 'text-slate-300 hover:text-orange'}`} title={message.is_starred ? 'Muhimdan olish' : 'Muhim deb belgilash'} aria-label="Muhim deb belgilash" onClick={() => toggleStar(message)}><Star size={16} fill={message.is_starred ? 'currentColor' : 'none'} /></button></td><td><p className="font-extrabold text-navy">{message.name}</p><p className="text-xs text-slate-500">{message.email}</p></td><td className="max-w-40 truncate font-bold text-navy">{message.subject || 'Mavzusiz'}</td><td className="max-w-56 truncate">{message.message}</td><td>{formatDate(message.created_at)}</td><td><Badge tone={message.status === 'new' ? 'orange' : message.status === 'replied' ? 'green' : 'slate'}>{messageStatusLabels[message.status]}</Badge></td><td><div className="flex gap-1"><Button variant="secondary" className="!min-h-8 !px-2.5 !py-1 text-xs" onClick={() => setSelected(message)}>Ko‘rish</Button><button disabled={pendingId === message.id} className="icon-button text-red hover:bg-red/5" aria-label="Xabarni o‘chirish" onClick={() => deleteMessage(message)}><Trash2 size={15} /></button></div></td></tr>) : <tr><td colSpan="7"><EmptyState title="Xabarlar topilmadi" description="Tanlangan holatda xabarlar yo‘q." /></td></tr>}
        </tbody></table></div>}
      </Card>
      {selected && <MessageDialog key={selected.id} message={selected} onClose={() => setSelected(null)} onSaved={reload} />}
    </>
  );
}
