import { useState } from 'react';
import { useForm } from 'react-hook-form';
import { HeartPulse, Eye, EyeOff, ArrowRight, LoaderCircle } from 'lucide-react';
import { useLocation, useNavigate } from 'react-router-dom';
import { useAuth } from '../auth/AuthProvider.jsx';
import { requireSupabase, withTimeout } from '../lib/supabase.js';
import { errorMessage } from '../lib/format.js';

export default function LoginPage() {
  const [busy, setBusy] = useState(false);
  const [showPassword, setShowPassword] = useState(false);
  const [notice, setNotice] = useState('');
  const [problem, setProblem] = useState('');
  const location = useLocation();
  const navigate = useNavigate();
  const { authError } = useAuth();
  const { register, handleSubmit, getValues, formState: { errors } } = useForm();

  async function onSubmit(values) {
    setBusy(true);
    setProblem('');
    setNotice('');
    try {
      const { error } = await withTimeout(
        requireSupabase().auth.signInWithPassword({
          email: values.email.trim(),
          password: values.password,
        }),
        15_000,
        'Kirish 15 soniyadan ko‘proq davom etdi. Ulanishni tekshirib qayta urinib ko‘ring.',
      );
      if (error) throw error;
      navigate(location.state?.from?.pathname || '/dashboard', { replace: true });
    } catch (error) {
      setProblem(errorMessage(error));
    } finally {
      setBusy(false);
    }
  }

  async function sendReset() {
    const email = getValues('email')?.trim();
    if (!email) {
      setProblem('Avval elektron pochta manzilingizni kiriting.');
      return;
    }
    setBusy(true);
    setProblem('');
    try {
      const { error } = await withTimeout(
        requireSupabase().auth.resetPasswordForEmail(email, {
          redirectTo: `${window.location.origin}/settings`,
        }),
        15_000,
        'Parolni tiklash so‘rovi juda uzoq davom etdi. Ulanishni tekshirib qayta urinib ko‘ring.',
      );
      if (error) throw error;
      setNotice('Parolni tiklash havolasi elektron pochtangizga yuborildi.');
    } catch (error) {
      setProblem(errorMessage(error));
    } finally {
      setBusy(false);
    }
  }

  return (
    <main className="login-bg flex min-h-screen items-center justify-center px-4 py-10 sm:px-6">
      <div className="grid w-full max-w-[980px] overflow-hidden rounded-[28px] border border-white/60 bg-white shadow-[0_28px_90px_rgba(10,37,64,.14)] md:grid-cols-[1fr_1fr]">
        <aside className="relative hidden min-h-[580px] overflow-hidden bg-gradient-to-br from-navy via-[#123e5d] to-green p-10 text-white md:flex md:flex-col md:justify-between">
          <div className="absolute -right-24 -top-24 h-72 w-72 rounded-full border-[42px] border-white/5" />
          <div className="absolute -bottom-28 -left-20 h-72 w-72 rounded-full border-[54px] border-white/5" />
          <div className="relative z-10 flex items-center gap-3">
            <span className="flex h-11 w-11 items-center justify-center rounded-2xl bg-white/15"><HeartPulse size={25} /></span>
            <span className="text-sm font-black tracking-wide">SOG‘LOM AVLOD</span>
          </div>
          <div className="relative z-10">
            <p className="mb-3 text-xs font-black uppercase tracking-[0.2em] text-teal-200">Salomatlik. Xavfsizlik. E’tibor.</p>
            <h1 className="max-w-sm text-[38px] font-black leading-[1.15]">Har bir bola uchun sog‘lom kelajak.</h1>
            <p className="mt-4 max-w-sm text-sm leading-6 text-white/70">O‘quvchilar salomatligi, davomat va qo‘llab-quvvatlashni bitta ishonchli tizimda boshqaring.</p>
          </div>
          <p className="relative z-10 text-xs text-white/55">© {new Date().getFullYear()} Sog‘lom Avlod</p>
        </aside>

        <section className="flex min-h-[580px] flex-col justify-center p-7 sm:p-11">
          <div className="mb-8 flex items-center gap-3 md:hidden"><span className="flex h-11 w-11 items-center justify-center rounded-2xl bg-green text-white"><HeartPulse size={24} /></span><span className="font-black text-navy">SOG‘LOM AVLOD</span></div>
          <p className="text-xs font-black uppercase tracking-[0.17em] text-green">Xush kelibsiz</p>
          <h2 className="mt-2 text-3xl font-black tracking-tight text-navy">Tizimga kirish</h2>
          <p className="mt-2 text-sm text-slate-500">Hisobingizga kirish uchun ma’lumotlarni kiriting.</p>
          <form className="mt-8 space-y-5" onSubmit={handleSubmit(onSubmit)}>
            <label className="block">
              <span className="mb-1.5 block text-sm font-bold text-navy">Elektron pochta</span>
              <input id="email" type="email" autoComplete="username" className="field" placeholder="ism@maktab.uz" {...register('email', { required: 'Elektron pochta manzili majburiy.' })} />
              {errors.email && <span className="mt-1 block text-xs font-bold text-red">{errors.email.message}</span>}
            </label>
            <label className="block">
              <span className="mb-1.5 block text-sm font-bold text-navy">Parol</span>
              <span className="relative block">
                <input type={showPassword ? 'text' : 'password'} autoComplete="current-password" className="field pr-12" placeholder="Parolingizni kiriting" {...register('password', { required: 'Parol majburiy.' })} />
                <button type="button" aria-label={showPassword ? 'Parolni yashirish' : 'Parolni ko‘rsatish'} className="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-navy" onClick={() => setShowPassword((value) => !value)}>{showPassword ? <EyeOff size={18} /> : <Eye size={18} />}</button>
              </span>
              {errors.password && <span className="mt-1 block text-xs font-bold text-red">{errors.password.message}</span>}
            </label>
            {(problem || authError) && <div role="alert" className="flex flex-wrap items-center justify-between gap-3 rounded-xl bg-red/5 px-4 py-3 text-sm font-bold text-red"><span>{problem || authError}</span>{authError && <button type="button" className="shrink-0 underline" onClick={() => window.location.reload()}>Qayta urinish</button>}</div>}
            {notice && <p role="status" className="rounded-xl bg-green/10 px-4 py-3 text-sm font-bold text-green">{notice}</p>}
            <button disabled={busy} className="flex min-h-12 w-full items-center justify-center gap-2 rounded-xl bg-green px-4 text-sm font-black text-white shadow-lg shadow-green/20 transition hover:bg-green/90 disabled:opacity-60">
              {busy ? <LoaderCircle size={18} className="animate-spin" /> : <>Kirish <ArrowRight size={17} /></>}
            </button>
          </form>
          <button disabled={busy} className="mt-5 self-center text-sm font-bold text-slate-500 hover:text-green disabled:opacity-50" onClick={sendReset}>Parolni unutdingizmi?</button>
          <p className="mt-9 text-center text-xs leading-5 text-slate-400">Hisob ochish uchun tizim administratoriga murojaat qiling.</p>
        </section>
      </div>
    </main>
  );
}
