import { lazy, Suspense, useState } from 'react';
import { Navigate, Route, Routes, useLocation } from 'react-router-dom';
import { LogOut, RefreshCw } from 'lucide-react';
import { useAuth } from './auth/AuthProvider.jsx';
import ConfigScreen from './components/ConfigScreen.jsx';
import AppLayout from './components/AppLayout.jsx';
import { isSupabaseConfigured } from './lib/supabase.js';
import { Loading } from './components/ui.jsx';

const LoginPage = lazy(() => import('./pages/LoginPage.jsx'));
const DashboardPage = lazy(() => import('./pages/DashboardPage.jsx'));
const StudentsPage = lazy(() => import('./pages/StudentsPage.jsx'));
const AttendancePage = lazy(() => import('./pages/AttendancePage.jsx'));
const CasesPage = lazy(() => import('./pages/CasesPage.jsx'));
const StatisticsPage = lazy(() => import('./pages/StatisticsPage.jsx'));
const MessagesPage = lazy(() => import('./pages/MessagesPage.jsx'));
const SettingsPage = lazy(() => import('./pages/SettingsPage.jsx'));
const WelcomePage = lazy(() => import('./pages/WelcomePage.jsx'));

function RequireAuth({ children }) {
  const { session, loading, authError, signOut } = useAuth();
  const location = useLocation();
  const [signOutError, setSignOutError] = useState('');
  if (loading) return <Loading label="Hisob tekshirilmoqda..." />;
  if (!session) return <Navigate to="/login" replace state={{ from: location }} />;
  if (authError) return (
    <main role="alert" className="mx-auto mt-20 max-w-xl rounded-2xl border border-red/20 bg-white p-7 text-center shadow-card">
      <h1 className="text-lg font-extrabold text-navy">Tizimga ulanish amalga oshmadi</h1>
      <p className="mt-2 text-sm leading-6 text-red">{signOutError || authError}</p>
      <div className="mt-5 flex flex-wrap justify-center gap-2">
        <button className="inline-flex min-h-10 items-center gap-2 rounded-xl bg-green px-4 text-sm font-extrabold text-white" onClick={() => window.location.reload()}><RefreshCw size={16} /> Qayta urinish</button>
        <button className="inline-flex min-h-10 items-center gap-2 rounded-xl border border-slate-200 px-4 text-sm font-extrabold text-navy" onClick={async () => {
          try {
            await signOut();
          } catch (error) {
            setSignOutError(`Hisobdan chiqib bo‘lmadi: ${error.message}`);
          }
        }}><LogOut size={16} /> Hisobdan chiqish</button>
      </div>
    </main>
  );
  return children;
}

function AdminOnly({ children }) {
  const { profile } = useAuth();
  if (profile?.role !== 'admin') return <Navigate to="/dashboard" replace />;
  return children;
}

function RoleAccess({ roles, children }) {
  const { profile } = useAuth();
  if (!roles.includes(profile?.role)) return <Navigate to="/dashboard" replace />;
  return children;
}

export default function App() {
  const { session } = useAuth();
  if (!isSupabaseConfigured) return <ConfigScreen />;
  return (
    <Suspense fallback={<Loading />}>
      <Routes>
        <Route path="/" element={session ? <Navigate to="/dashboard" replace /> : <WelcomePage />} />
        <Route path="/login" element={session ? <Navigate to="/dashboard" replace /> : <LoginPage />} />
        <Route element={<RequireAuth><AppLayout /></RequireAuth>}>
          <Route path="/dashboard" element={<DashboardPage />} />
          <Route path="/students" element={<RoleAccess roles={['admin', 'teacher', 'psychologist', 'school_admin']}><StudentsPage /></RoleAccess>} />
          <Route path="/attendance" element={<RoleAccess roles={['admin', 'teacher', 'school_admin']}><AttendancePage /></RoleAccess>} />
          <Route path="/cases" element={<CasesPage />} />
          <Route path="/statistics" element={<RoleAccess roles={['admin', 'teacher', 'psychologist', 'school_admin']}><StatisticsPage /></RoleAccess>} />
          <Route path="/messages" element={<AdminOnly><MessagesPage /></AdminOnly>} />
          <Route path="/settings" element={<SettingsPage />} />
          <Route path="*" element={<Navigate to={session ? '/dashboard' : '/'} replace />} />
        </Route>
      </Routes>
    </Suspense>
  );
}
