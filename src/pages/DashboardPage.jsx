import { useCallback, useEffect, useMemo, useState } from 'react';
import { Link } from 'react-router-dom';
import { Activity, ArrowUpRight, CalendarCheck2, CircleAlert, GraduationCap, ShieldCheck, UsersRound } from 'lucide-react';
import { Area, AreaChart, CartesianGrid, ResponsiveContainer, Tooltip, XAxis, YAxis } from 'recharts';
import { useAuth } from '../auth/AuthProvider.jsx';
import { Badge, Card, EmptyState, ErrorMessage, Loading, PageTitle } from '../components/ui.jsx';
import { formatDate, formatNumber, fullName } from '../lib/format.js';
import { requireSupabase } from '../lib/supabase.js';
import { useRealtimeRefresh } from '../lib/useRealtime.js';

function MetricCard({ icon: Icon, label, value, detail, tone }) {
  const colors = { green: 'bg-green/10 text-green', red: 'bg-red/10 text-red', orange: 'bg-orange/15 text-amber-700', navy: 'bg-navy/10 text-navy' };
  return (
    <Card className="relative overflow-hidden">
      <div className="flex items-start justify-between gap-3">
        <div><p className="text-sm font-bold text-slate-500">{label}</p><p className="mt-2 text-3xl font-black tracking-tight text-navy">{formatNumber(value)}</p></div>
        <span className={`flex h-11 w-11 items-center justify-center rounded-2xl ${colors[tone]}`}><Icon size={21} /></span>
      </div>
      <p className="mt-4 flex items-center gap-1 text-xs font-bold text-slate-400"><ArrowUpRight size={14} className="text-green" />{detail}</p>
    </Card>
  );
}

export default function DashboardPage() {
  const { profile } = useAuth();
  const isParent = profile?.role === 'parent';
  const [data, setData] = useState(null);
  const [error, setError] = useState('');
  const [loading, setLoading] = useState(true);
  const [reloadKey, setReloadKey] = useState(0);
  const reload = useCallback(() => setReloadKey((value) => value + 1), []);
  useRealtimeRefresh(['students', 'attendances', 'risk_cases'], reload);

  useEffect(() => {
    let alive = true;
    async function load() {
      setLoading(true);
      setError('');
      const client = requireSupabase();
      const today = new Date().toLocaleDateString('en-CA', { timeZone: profile?.timezone || 'Asia/Tashkent' });
      const from = new Date(`${today}T00:00:00`);
      from.setDate(from.getDate() - 6);
      const weekStart = from.toLocaleDateString('en-CA', { timeZone: profile?.timezone || 'Asia/Tashkent' });
      const casesTable = isParent ? 'parent_visible_risk_cases' : 'risk_cases';
      const recentCasesQuery = isParent
        ? client.from(casesTable).select('id,risk_type,risk_level,status,created_at,first_name,last_name,middle_name,class_name').order('created_at', { ascending: false }).limit(5)
        : client.from(casesTable).select('id,risk_type,risk_level,status,created_at,student:students(first_name,last_name,middle_name,class_name)').order('created_at', { ascending: false }).limit(5);
      const [
        studentsResult,
        casesResult,
        redCasesResult,
        newCasesResult,
        attendanceResult,
        absentResult,
        recentResult,
        weekResult,
      ] = await Promise.all([
        client.from('students').select('id', { count: 'exact', head: true }).eq('is_active', true),
        client.from(casesTable).select('id', { count: 'exact', head: true }).neq('status', 'closed'),
        client.from(casesTable).select('id', { count: 'exact', head: true }).eq('risk_level', 'red').neq('status', 'closed'),
        client.from(casesTable).select('id', { count: 'exact', head: true }).eq('status', 'new'),
        client.from('attendances').select('id', { count: 'exact', head: true }).eq('attendance_date', today).in('status', ['present', 'late']),
        client.from('attendances').select('id', { count: 'exact', head: true }).eq('attendance_date', today).eq('status', 'absent').eq('is_excused', false),
        recentCasesQuery,
        client.from('attendances').select('attendance_date,status').gte('attendance_date', weekStart).lte('attendance_date', today),
      ]);
      const resultErrors = [studentsResult, casesResult, redCasesResult, newCasesResult, attendanceResult, absentResult, recentResult, weekResult].filter((result) => result.error);
      if (resultErrors.length) throw resultErrors[0].error;
      const attendanceByDate = new Map();
      weekResult.data.forEach((row) => {
        const entry = attendanceByDate.get(row.attendance_date) || { present: 0, absent: 0 };
        if (row.status === 'present' || row.status === 'late') entry.present += 1;
        if (row.status === 'absent') entry.absent += 1;
        attendanceByDate.set(row.attendance_date, entry);
      });
      const chart = Array.from({ length: 7 }, (_, index) => {
        const date = new Date(`${weekStart}T12:00:00`);
        date.setDate(date.getDate() + index);
        const key = date.toLocaleDateString('en-CA');
        const counts = attendanceByDate.get(key) || { present: 0, absent: 0 };
        return { day: new Intl.DateTimeFormat('uz-UZ', { weekday: 'short' }).format(date), ...counts };
      });
      if (alive) {
        setData({
          students: studentsResult.count ?? 0,
          openCases: casesResult.count ?? 0,
          redCases: redCasesResult.count ?? 0,
          newCases: newCasesResult.count ?? 0,
          present: attendanceResult.count ?? 0,
          absent: absentResult.count ?? 0,
          latest: recentResult.data.map((item) => isParent
            ? { ...item, student: { first_name: item.first_name, last_name: item.last_name, middle_name: item.middle_name, class_name: item.class_name } }
            : item),
          chart,
        });
      }
    }
    load().catch((reason) => {
      if (alive) setError(reason.message);
    }).finally(() => {
      if (alive) setLoading(false);
    });
    return () => { alive = false; };
  }, [profile?.timezone, isParent, reloadKey]);

  const attendancePercent = useMemo(() => {
    const total = (data?.present ?? 0) + (data?.absent ?? 0);
    return total ? Math.round((data.present / total) * 100) : 0;
  }, [data]);

  return (
    <>
      <PageTitle eyebrow="Xayrli kun" title={`Assalomu alaykum, ${profile?.name?.split(' ')[0] || 'foydalanuvchi'}!`} description="Bugungi maktab salomatligi va xavfsizligi bo‘yicha qisqacha ma’lumot." />
      <ErrorMessage error={error} onRetry={reload} />
      {loading && !data ? <Loading /> : data && <>
        <div className="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
          <MetricCard icon={GraduationCap} label="Faol o‘quvchilar" value={data.students} detail="Tizimdagi jami o‘quvchilar" tone="green" />
          <MetricCard icon={CircleAlert} label="Ochiq holatlar" value={data.openCases} detail={`${data.newCases} yangi ko‘rib chiqilmoqda`} tone="orange" />
          <MetricCard icon={ShieldCheck} label="Yuqori xavf" value={data.redCases} detail="Tezkor e’tibor talab qiladi" tone="red" />
          <MetricCard icon={CalendarCheck2} label="Bugungi davomat" value={attendancePercent ? `${attendancePercent}%` : '—'} detail={`${formatNumber(data.present)} kelgan · ${formatNumber(data.absent)} kelmagan`} tone="navy" />
        </div>

        <div className="mt-5 grid gap-5 xl:grid-cols-[1.55fr_1fr]">
          <Card>
            <div className="mb-5 flex flex-wrap items-center justify-between gap-3">
              <div><h2 className="font-extrabold text-navy">Davomat dinamikasi</h2><p className="mt-1 text-xs text-slate-500">So‘nggi 7 kunlik ko‘rsatkich</p></div>
              <div className="flex gap-4 text-xs font-bold"><span className="flex items-center gap-1.5 text-green"><i className="h-2 w-2 rounded-full bg-green" />Kelgan</span><span className="flex items-center gap-1.5 text-red"><i className="h-2 w-2 rounded-full bg-red" />Kelmagan</span></div>
            </div>
            <div className="h-[260px]">
              <ResponsiveContainer width="100%" height="100%">
                <AreaChart data={data.chart} margin={{ top: 8, right: 4, left: -20, bottom: 0 }}>
                  <defs><linearGradient id="presentFill" x1="0" y1="0" x2="0" y2="1"><stop offset="0%" stopColor="#2A9D8F" stopOpacity={0.2} /><stop offset="95%" stopColor="#2A9D8F" stopOpacity={0} /></linearGradient><linearGradient id="absentFill" x1="0" y1="0" x2="0" y2="1"><stop offset="0%" stopColor="#E63946" stopOpacity={0.16} /><stop offset="95%" stopColor="#E63946" stopOpacity={0} /></linearGradient></defs>
                  <CartesianGrid stroke="#edf1f5" vertical={false} />
                  <XAxis dataKey="day" axisLine={false} tickLine={false} tick={{ fill: '#94a3b8', fontSize: 11, fontWeight: 700 }} dy={9} />
                  <YAxis axisLine={false} tickLine={false} tick={{ fill: '#94a3b8', fontSize: 11 }} allowDecimals={false} />
                  <Tooltip contentStyle={{ border: '1px solid #edf1f5', borderRadius: 12, boxShadow: '0 8px 24px rgba(10,37,64,.08)', fontSize: 12 }} />
                  <Area type="monotone" dataKey="present" name="Kelgan" stroke="#2A9D8F" strokeWidth={2.5} fill="url(#presentFill)" />
                  <Area type="monotone" dataKey="absent" name="Kelmagan" stroke="#E63946" strokeWidth={2} fill="url(#absentFill)" />
                </AreaChart>
              </ResponsiveContainer>
            </div>
          </Card>
          <Card>
            <div className="mb-4 flex items-center justify-between"><div><h2 className="font-extrabold text-navy">Bugungi holat</h2><p className="mt-1 text-xs text-slate-500">{formatDate(new Date())}</p></div><Activity size={19} className="text-green" /></div>
            <div className="grid grid-cols-2 gap-3">
              <div className="rounded-2xl bg-green/5 p-4"><p className="text-xs font-bold text-slate-500">Kelgan</p><p className="mt-1 text-2xl font-black text-green">{formatNumber(data.present)}</p></div>
              <div className="rounded-2xl bg-red/5 p-4"><p className="text-xs font-bold text-slate-500">Kelmagan</p><p className="mt-1 text-2xl font-black text-red">{formatNumber(data.absent)}</p></div>
            </div>
            <div className="mt-5 flex items-center justify-between text-xs font-bold text-slate-500"><span>Maktab davomat ko‘rsatkichi</span><span className="text-navy">{attendancePercent}%</span></div>
            <div className="mt-2 h-2 overflow-hidden rounded-full bg-slate-100"><div className="h-full rounded-full bg-green transition-all" style={{ width: `${attendancePercent}%` }} /></div>
            <p className="mt-5 rounded-xl bg-orange/10 px-3 py-2.5 text-xs font-bold leading-5 text-amber-800"><UsersRound size={14} className="mr-1 inline" />Davomatni muntazam belgilang — o‘zgarishlar jonli ko‘rinadi.</p>
          </Card>
        </div>

        <Card className="mt-5 overflow-hidden !p-0">
          <div className="flex items-center justify-between border-b border-slate-100 px-5 py-4 sm:px-6"><div><h2 className="font-extrabold text-navy">So‘nggi xavf holatlari</h2><p className="mt-1 text-xs text-slate-500">Yaqinda ro‘yxatga olingan holatlar</p></div><Link to="/cases" className="text-xs font-extrabold text-green hover:underline">Barchasini ko‘rish</Link></div>
          <div className="overflow-x-auto">
            <table className="data-table min-w-[620px]"><thead><tr><th>O‘quvchi</th><th>Holat turi</th><th>Sinf</th><th>Daraja</th><th>Sana</th></tr></thead>
              <tbody>{data.latest.length ? data.latest.map((item) => <tr key={item.id}><td className="font-extrabold text-navy">{fullName(item.student)}</td><td>{item.risk_type}</td><td>{item.student?.class_name || '—'}</td><td><Badge tone={item.risk_level === 'red' ? 'red' : item.risk_level === 'yellow' ? 'orange' : 'green'}>{item.risk_level === 'red' ? 'Yuqori' : item.risk_level === 'yellow' ? 'O‘rta' : 'Past'}</Badge></td><td>{formatDate(item.created_at)}</td></tr>) : <tr><td colSpan="5"><EmptyState title="Hozircha xavf holatlari yo‘q" description="Yangi holatlar bu yerda ko‘rinadi." /></td></tr>}</tbody>
            </table>
          </div>
        </Card>
      </>}
    </>
  );
}
