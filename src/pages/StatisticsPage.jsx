import { useCallback, useEffect, useMemo, useState } from 'react';
import { Bar, BarChart, CartesianGrid, Cell, Pie, PieChart, ResponsiveContainer, Tooltip, XAxis, YAxis } from 'recharts';
import { BarChart3, CalendarClock, ShieldAlert, UsersRound } from 'lucide-react';
import { Badge, Card, EmptyState, ErrorMessage, Loading, PageTitle } from '../components/ui.jsx';
import { formatNumber } from '../lib/format.js';
import { requireSupabase } from '../lib/supabase.js';
import { useRealtimeRefresh } from '../lib/useRealtime.js';

const riskColors = { red: '#E63946', yellow: '#F4A261', green: '#2A9D8F' };
const riskLabels = { red: 'Yuqori', yellow: 'O‘rta', green: 'Past' };

export default function StatisticsPage() {
  const [dataset, setDataset] = useState(null);
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
      const [studentResult, attendanceResult, caseResult] = await Promise.all([
        client.from('students').select('id,class_name,is_active'),
        client.from('attendances').select('attendance_date,status,is_excused'),
        client.from('risk_cases').select('id,risk_level,status,created_at'),
      ]);
      const failure = [studentResult, attendanceResult, caseResult].find((result) => result.error);
      if (failure) throw failure.error;
      const classes = new Map();
      studentResult.data.filter((student) => student.is_active).forEach((student) => {
        const current = classes.get(student.class_name) || 0;
        classes.set(student.class_name, current + 1);
      });
      const today = new Date().toLocaleDateString('en-CA', { timeZone: 'Asia/Tashkent' });
      const weekStartDate = new Date(`${today}T12:00:00`);
      weekStartDate.setDate(weekStartDate.getDate() - 6);
      const weekStart = weekStartDate.toLocaleDateString('en-CA');
      const week = new Map();
      attendanceResult.data.filter((row) => row.attendance_date >= weekStart && row.attendance_date <= today).forEach((row) => {
        const current = week.get(row.attendance_date) || { present: 0, absent: 0 };
        if (row.status === 'present' || row.status === 'late') current.present += 1;
        if (row.status === 'absent' && !row.is_excused) current.absent += 1;
        week.set(row.attendance_date, current);
      });
      const attendance = Array.from({ length: 7 }, (_, index) => {
        const date = new Date(`${weekStart}T12:00:00`);
        date.setDate(date.getDate() + index);
        const key = date.toLocaleDateString('en-CA');
        return { day: new Intl.DateTimeFormat('uz-UZ', { weekday: 'short' }).format(date), ...(week.get(key) || { present: 0, absent: 0 }) };
      });
      const risks = ['red', 'yellow', 'green'].map((level) => ({
        name: riskLabels[level],
        value: caseResult.data.filter((item) => item.risk_level === level && item.status !== 'closed').length,
        color: riskColors[level],
      }));
      const activeCases = caseResult.data.filter((item) => item.status !== 'closed').length;
      if (alive) setDataset({
        studentCount: studentResult.data.filter((student) => student.is_active).length,
        classCount: [...classes].map(([name, count]) => ({ name, count })).sort((a, b) => a.name.localeCompare(b.name, 'uz')),
        attendance,
        risks,
        activeCases,
        absent: attendanceResult.data.filter((row) => row.attendance_date === today && row.status === 'absent' && !row.is_excused).length,
      });
    }
    load().catch((reason) => { if (alive) setError(reason.message); }).finally(() => { if (alive) setLoading(false); });
    return () => { alive = false; };
  }, [reloadKey]);

  const attendanceTotal = useMemo(() => dataset?.attendance.reduce((sum, row) => sum + row.present + row.absent, 0) || 0, [dataset]);
  return (
    <>
      <PageTitle eyebrow="Tahlil" title="Statistika va hisobotlar" description="Davomat, o‘quvchilar va xavf holatlari bo‘yicha ko‘rsatkichlar." />
      <ErrorMessage error={error} onRetry={reload} />
      {loading && !dataset ? <Loading /> : dataset && <>
        <div className="mb-5 grid gap-4 sm:grid-cols-3">
          <Card className="flex items-center gap-4"><span className="flex h-11 w-11 items-center justify-center rounded-xl bg-green/10 text-green"><UsersRound size={21} /></span><div><p className="text-xs font-bold text-slate-500">Faol o‘quvchilar</p><p className="text-2xl font-black text-navy">{formatNumber(dataset.studentCount)}</p></div></Card>
          <Card className="flex items-center gap-4"><span className="flex h-11 w-11 items-center justify-center rounded-xl bg-orange/15 text-amber-700"><ShieldAlert size={21} /></span><div><p className="text-xs font-bold text-slate-500">Ochiq xavf holatlari</p><p className="text-2xl font-black text-navy">{formatNumber(dataset.activeCases)}</p></div></Card>
          <Card className="flex items-center gap-4"><span className="flex h-11 w-11 items-center justify-center rounded-xl bg-red/10 text-red"><CalendarClock size={21} /></span><div><p className="text-xs font-bold text-slate-500">Bugun kelmagan</p><p className="text-2xl font-black text-navy">{formatNumber(dataset.absent)}</p></div></Card>
        </div>
        <div className="grid gap-5 xl:grid-cols-[1.35fr_1fr]">
          <Card><div className="mb-5 flex items-center gap-2"><BarChart3 size={18} className="text-green" /><div><h2 className="font-extrabold text-navy">Sinf kesimida o‘quvchilar</h2><p className="mt-1 text-xs text-slate-500">Faol ro‘yxatdagi o‘quvchilar</p></div></div>
            {dataset.classCount.length ? <div className="h-[320px]"><ResponsiveContainer width="100%" height="100%"><BarChart data={dataset.classCount} margin={{ left: -20, right: 8, top: 5, bottom: 5 }}><CartesianGrid stroke="#edf1f5" vertical={false} /><XAxis dataKey="name" axisLine={false} tickLine={false} tick={{ fill: '#64748b', fontSize: 11 }} /><YAxis axisLine={false} tickLine={false} allowDecimals={false} tick={{ fill: '#94a3b8', fontSize: 11 }} /><Tooltip contentStyle={{ border: '1px solid #edf1f5', borderRadius: 12, fontSize: 12 }} /><Bar dataKey="count" name="O‘quvchilar" fill="#2A9D8F" radius={[6, 6, 0, 0]} maxBarSize={42} /></BarChart></ResponsiveContainer></div> : <EmptyState title="Sinf ma’lumotlari mavjud emas" />}</Card>
          <Card><div className="mb-3"><h2 className="font-extrabold text-navy">Xavf darajalari</h2><p className="mt-1 text-xs text-slate-500">Yopilmagan holatlar</p></div>
            {dataset.activeCases ? <div className="h-[250px]"><ResponsiveContainer width="100%" height="100%"><PieChart><Pie data={dataset.risks} dataKey="value" nameKey="name" innerRadius={62} outerRadius={95} paddingAngle={4} stroke="none">{dataset.risks.map((entry) => <Cell key={entry.name} fill={entry.color} />)}</Pie><Tooltip contentStyle={{ border: '1px solid #edf1f5', borderRadius: 12, fontSize: 12 }} /></PieChart></ResponsiveContainer></div> : <EmptyState title="Faol holatlar mavjud emas" />}
            <div className="grid grid-cols-3 gap-2">{dataset.risks.map((item) => <div key={item.name} className="rounded-xl bg-canvas p-3 text-center"><p className="text-xs font-bold text-slate-500">{item.name}</p><p className="mt-1 text-lg font-black text-navy">{item.value}</p><span className="mx-auto mt-1 block h-1 w-7 rounded-full" style={{ backgroundColor: item.color }} /></div>)}</div>
          </Card>
          <Card className="xl:col-span-2"><div className="mb-4"><h2 className="font-extrabold text-navy">Haftalik davomat</h2><p className="mt-1 text-xs text-slate-500">{formatNumber(attendanceTotal)} ta qayd so‘nggi 7 kun ichida</p></div>
            <div className="h-[280px]"><ResponsiveContainer width="100%" height="100%"><BarChart data={dataset.attendance} margin={{ left: -20, right: 8, top: 5, bottom: 5 }}><CartesianGrid stroke="#edf1f5" vertical={false} /><XAxis dataKey="day" axisLine={false} tickLine={false} tick={{ fill: '#64748b', fontSize: 11 }} /><YAxis axisLine={false} tickLine={false} allowDecimals={false} tick={{ fill: '#94a3b8', fontSize: 11 }} /><Tooltip contentStyle={{ border: '1px solid #edf1f5', borderRadius: 12, fontSize: 12 }} /><Bar dataKey="present" name="Kelgan" fill="#2A9D8F" radius={[5, 5, 0, 0]} /><Bar dataKey="absent" name="Kelmagan" fill="#E63946" radius={[5, 5, 0, 0]} /></BarChart></ResponsiveContainer></div>
          </Card>
        </div>
      </>}
    </>
  );
}
