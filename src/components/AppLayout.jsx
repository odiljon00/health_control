import { useCallback, useEffect, useState } from 'react';
import { Link, NavLink, Outlet, useLocation, useNavigate } from 'react-router-dom';
import {
  Activity, Bell, BookUser, ChartNoAxesCombined, ChevronDown, ClipboardCheck,
  HeartPulse, LayoutDashboard, LogOut, Menu, MessageSquareText, Moon, Settings,
  Sun, X, ShieldAlert,
} from 'lucide-react';
import { useAuth } from '../auth/AuthProvider.jsx';
import { getStorageUrl, requireSupabase } from '../lib/supabase.js';
import { initials } from '../lib/format.js';
import { useRealtimeRefresh } from '../lib/useRealtime.js';
import { Badge } from './ui.jsx';

const links = [
  { to: '/dashboard', label: 'Boshqaruv paneli', icon: LayoutDashboard, roles: ['admin', 'teacher', 'psychologist', 'school_admin', 'iio', 'parent'] },
  { to: '/students', label: 'O‘quvchilar', icon: BookUser, roles: ['admin', 'teacher', 'psychologist', 'school_admin'] },
  { to: '/attendance', label: 'Davomat', icon: ClipboardCheck, roles: ['admin', 'teacher', 'school_admin'] },
  { to: '/cases', label: 'Xavf holatlari', icon: ShieldAlert, roles: ['admin', 'teacher', 'psychologist', 'school_admin', 'iio', 'parent'] },
  { to: '/statistics', label: 'Statistika', icon: ChartNoAxesCombined, roles: ['admin', 'teacher', 'psychologist', 'school_admin'] },
  { to: '/messages', label: 'Xabarlar', icon: MessageSquareText, roles: ['admin'] },
  { to: '/settings', label: 'Sozlamalar', icon: Settings, roles: ['admin', 'teacher', 'psychologist', 'school_admin', 'iio', 'parent'] },
];

const roleLabels = {
  admin: 'Administrator',
  teacher: 'O‘qituvchi',
  psychologist: 'Psixolog',
  school_admin: 'Maktab ma’muri',
  parent: 'Ota-ona',
  iio: 'IIO mutaxassisi',
};

function Sidebar({ open, onClose }) {
  const { profile } = useAuth();
  const location = useLocation();
  return (
    <>
      {open && <button aria-label="Menyuni yopish" className="fixed inset-0 z-40 bg-navy/35 lg:hidden" onClick={onClose} />}
      <aside className={`fixed bottom-0 left-0 top-0 z-50 flex w-[264px] flex-col border-r border-slate-100 bg-white px-4 pb-5 pt-5 transition-transform lg:translate-x-0 ${open ? 'translate-x-0' : '-translate-x-full'}`}>
        <div className="mb-7 flex items-center gap-3 px-2">
          <Link to="/dashboard" className="flex min-w-0 items-center gap-3" onClick={onClose}>
            <span className="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-green to-teal-300 text-white shadow-lg shadow-green/20"><HeartPulse size={22} /></span>
            <span><span className="block text-[15px] font-black leading-tight text-navy">SOG‘LOM AVLOD</span><span className="text-[10px] font-bold uppercase tracking-[0.18em] text-slate-400">Salomatlik platformasi</span></span>
          </Link>
          <button className="ml-auto rounded-lg p-2 text-slate-400 lg:hidden" onClick={onClose} aria-label="Menyuni yopish"><X size={18} /></button>
        </div>
        <p className="mb-2 px-3 text-[10px] font-black uppercase tracking-[0.18em] text-slate-400">Asosiy menyu</p>
        <nav className="space-y-1">
          {links.filter((item) => item.roles.includes(profile?.role)).map(({ to, label, icon: Icon }) => (
            <NavLink key={to} to={to} onClick={onClose} className={({ isActive }) => `nav-link ${isActive ? 'nav-link-active' : ''}`}>
              <Icon size={18} strokeWidth={isActiveRoute(location.pathname, to) ? 2.4 : 2} />
              <span>{label}</span>
              {to === '/cases' && <span className="ml-auto h-2 w-2 rounded-full bg-orange" />}
            </NavLink>
          ))}
        </nav>
        <div className="mt-auto rounded-2xl bg-gradient-to-br from-navy to-[#16435f] p-4 text-white">
          <div className="mb-3 flex h-9 w-9 items-center justify-center rounded-xl bg-white/10"><Activity size={18} /></div>
          <p className="text-sm font-extrabold">Sog‘lom avlod — xavfsiz kelajak</p>
          <p className="mt-1 text-xs leading-5 text-white/65">Har bir bola salomatligi biz uchun muhim.</p>
        </div>
      </aside>
    </>
  );
}

function isActiveRoute(path, to) {
  return path === to || (to !== '/dashboard' && path.startsWith(`${to}/`));
}

function Header({ onOpenMenu }) {
  const { profile, user, setProfile, signOut } = useAuth();
  const navigate = useNavigate();
  const location = useLocation();
  const [menuOpen, setMenuOpen] = useState(false);
  const [noticeOpen, setNoticeOpen] = useState(false);
  const [notices, setNotices] = useState(0);
  const [noticeError, setNoticeError] = useState('');
  const [avatarUrl, setAvatarUrl] = useState('');
  const [refreshKey, setRefreshKey] = useState(0);
  const refreshNotices = useCallback(() => setRefreshKey((value) => value + 1), []);
  useRealtimeRefresh(['attendances'], refreshNotices);

  useEffect(() => {
    let alive = true;
    async function load() {
      const client = requireSupabase();
      if (!profile?.notify_attendance) {
        if (alive) setNotices(0);
        return;
      }
      const today = new Date().toLocaleDateString('en-CA', { timeZone: profile?.timezone || 'Asia/Tashkent' });
      let query = client.from('attendances').select('id', { count: 'exact', head: true }).eq('attendance_date', today).eq('is_alert', true);
      if (profile?.school_id) query = query.eq('school_id', profile.school_id);
      const { count, error } = await query;
      if (error) throw error;
      if (alive) setNotices(count ?? 0);
    }
    load().catch((error) => {
      if (alive) setNoticeError(error.message);
    });
    return () => { alive = false; };
  }, [profile?.school_id, profile?.role, profile?.timezone, profile?.notify_attendance, refreshKey]);

  useEffect(() => {
    let alive = true;
    if (!profile?.avatar) {
      setAvatarUrl('');
      return undefined;
    }
    getStorageUrl('profiles-avatars', profile.avatar)
      .then((url) => { if (alive) setAvatarUrl(url); })
      .catch((error) => { if (alive) setNoticeError(error.message); });
    return () => { alive = false; };
  }, [profile?.avatar]);

  const pageNames = {
    '/dashboard': 'Boshqaruv paneli',
    '/students': 'O‘quvchilar',
    '/attendance': 'Davomat',
    '/cases': 'Xavf holatlari',
    '/statistics': 'Statistika',
    '/messages': 'Xabarlar',
    '/settings': 'Sozlamalar',
  };

  async function toggleTheme() {
    const theme = profile.theme === 'dark' ? 'light' : 'dark';
    const { data, error } = await requireSupabase().from('profiles').update({ theme }).eq('id', user.id).select('*').single();
    if (error) {
      setNoticeError(error.message);
      return;
    }
    setProfile(data);
  }

  async function logOut() {
    try {
      await signOut();
      navigate('/', { replace: true });
    } catch (error) {
      setNoticeError(error.message);
    }
  }

  return (
    <header className="sticky top-0 z-30 flex h-[72px] items-center gap-3 border-b border-slate-100 bg-white/95 px-4 backdrop-blur sm:px-7">
      <button className="rounded-xl p-2 text-navy hover:bg-canvas lg:hidden" aria-label="Menyuni ochish" onClick={onOpenMenu}><Menu size={21} /></button>
      <div className="hidden min-w-0 items-center gap-2 text-sm text-slate-400 sm:flex"><span className="font-semibold">Sog‘lom Avlod</span><span>/</span><span className="truncate font-extrabold text-navy">{pageNames[location.pathname] || 'Boshqaruv paneli'}</span></div>
      <div className="ml-auto flex items-center gap-2 sm:gap-3">
        <div className="relative">
          <button className="relative rounded-xl p-2.5 text-slate-500 hover:bg-canvas hover:text-navy" title={noticeError || `${notices} yangi davomat ogohlantirishi`} aria-label="Ogohlantirishlar" aria-expanded={noticeOpen} onClick={() => setNoticeOpen((value) => !value)}><Bell size={19} />{notices > 0 && <span className="absolute right-1.5 top-1.5 h-2 w-2 rounded-full bg-red ring-2 ring-white" />}</button>
          {noticeOpen && <><button aria-label="Ogohlantirishlarni yopish" className="fixed inset-0 z-10 cursor-default" onClick={() => setNoticeOpen(false)} /><div className="absolute right-0 z-20 mt-2 w-72 rounded-2xl border border-slate-100 bg-white p-4 shadow-xl"><div className="flex items-center justify-between gap-2"><p className="text-sm font-extrabold text-navy">Ogohlantirishlar</p><Badge tone={notices ? 'red' : 'green'}>{notices}</Badge></div><p className="mt-2 text-xs leading-5 text-slate-500">{noticeError || (notices ? `${notices} nafar o‘quvchi bo‘yicha davomat ogohlantirishi mavjud.` : 'Hozircha yangi davomat ogohlantirishlari yo‘q.')}</p><Link to={['admin', 'teacher', 'school_admin'].includes(profile?.role) ? '/attendance' : '/cases'} onClick={() => setNoticeOpen(false)} className="mt-3 inline-flex text-xs font-extrabold text-green hover:underline">{['admin', 'teacher', 'school_admin'].includes(profile?.role) ? 'Davomatni ko‘rish' : 'Holatlarni ko‘rish'}</Link></div></>}
        </div>
        <button className="rounded-xl p-2.5 text-slate-500 hover:bg-canvas hover:text-navy" aria-label="Mavzuni almashtirish" title="Mavzuni almashtirish" onClick={toggleTheme}>{profile?.theme === 'dark' ? <Sun size={19} /> : <Moon size={19} />}</button>
        <div className="relative">
          <button className="flex items-center gap-2 rounded-xl p-1.5 pr-2 text-left hover:bg-canvas" onClick={() => setMenuOpen((value) => !value)} aria-expanded={menuOpen}>
            {avatarUrl ? <img src={avatarUrl} alt="" className="h-9 w-9 rounded-full object-cover" /> : <span className="flex h-9 w-9 items-center justify-center rounded-full bg-green/10 text-xs font-black text-green">{initials(profile?.name)}</span>}
            <span className="hidden max-w-36 sm:block"><span className="block truncate text-sm font-extrabold leading-tight text-navy">{profile?.name || user?.email}</span><span className="text-[11px] text-slate-500">{roleLabels[profile?.role] || 'Foydalanuvchi'}</span></span>
            <ChevronDown size={15} className="hidden text-slate-400 sm:block" />
          </button>
          {menuOpen && <><button aria-label="Menyuni yopish" className="fixed inset-0 z-10 cursor-default" onClick={() => setMenuOpen(false)} /><div className="absolute right-0 z-20 mt-2 w-52 rounded-2xl border border-slate-100 bg-white p-2 shadow-xl"><div className="border-b border-slate-100 px-3 py-2"><p className="truncate text-sm font-extrabold text-navy">{profile?.name}</p><p className="truncate text-xs text-slate-500">{user?.email}</p></div><Link to="/settings" className="mt-1 flex w-full items-center gap-2 rounded-xl px-3 py-2.5 text-sm font-bold text-slate-600 hover:bg-slate-50" onClick={() => setMenuOpen(false)}><Settings size={16} /> Sozlamalar</Link><button className="flex w-full items-center gap-2 rounded-xl px-3 py-2.5 text-sm font-bold text-red hover:bg-red/5" onClick={logOut}><LogOut size={16} /> Chiqish</button></div></>}
        </div>
      </div>
    </header>
  );
}

export default function AppLayout() {
  const [sidebarOpen, setSidebarOpen] = useState(false);
  const { profile } = useAuth();
  useEffect(() => {
    document.documentElement.dataset.theme = profile?.theme || 'light';
    return () => { delete document.documentElement.dataset.theme; };
  }, [profile?.theme]);
  return (
    <div className="min-h-screen bg-canvas">
      <Sidebar open={sidebarOpen} onClose={() => setSidebarOpen(false)} />
      <div className="min-h-screen lg:pl-[264px]">
        <Header onOpenMenu={() => setSidebarOpen(true)} />
        <main className="mx-auto min-h-[calc(100vh-136px)] max-w-[1600px] px-4 py-7 sm:px-7 lg:px-9"><Outlet /></main>
        <footer className="border-t border-slate-100 bg-white px-5 py-4 text-center text-xs text-slate-500 sm:flex sm:items-center sm:justify-between sm:px-9 sm:text-left"><span>© {new Date().getFullYear()} Sog‘lom Avlod · Yaratgan: Sog‘lom Avlod jamoasi</span><span className="mt-1 inline-flex items-center justify-center gap-3 sm:mt-0"><Link className="font-bold text-navy hover:text-green" to="/dashboard">Bosh sahifa</Link><Link className="font-bold text-navy hover:text-green" to="/cases">Holatlar</Link><Link className="font-bold text-navy hover:text-green" to="/settings">Sozlamalar</Link></span></footer>
      </div>
    </div>
  );
}
