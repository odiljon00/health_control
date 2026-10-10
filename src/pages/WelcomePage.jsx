import { useState } from 'react';
import { Link } from 'react-router-dom';
import { useForm } from 'react-hook-form';
import {
  ArrowRight,
  Activity,
  CalendarCheck2,
  ChartNoAxesCombined,
  HeartPulse,
  Mail,
  Send,
  ShieldCheck,
  UsersRound,
} from 'lucide-react';
import { Button, Field, TextAreaField } from '../components/ui.jsx';
import { requireSupabase } from '../lib/supabase.js';

const features = [
  {
    icon: UsersRound,
    color: 'bg-green/10 text-green',
    title: 'O‘quvchilar',
    description: 'O‘quvchilar ma’lumotlarini va sinflar bo‘yicha ro‘yxatni bir joyda boshqaring.',
  },
  {
    icon: CalendarCheck2,
    color: 'bg-orange/15 text-amber-700',
    title: 'Kundalik davomat',
    description: 'Davomatni belgilab, tez-tez dars qoldirish holatlariga vaqtida e’tibor qarating.',
  },
  {
    icon: ShieldCheck,
    color: 'bg-red/10 text-red',
    title: 'Xavf holatlari',
    description: 'Qo‘shimcha ko‘makka muhtoj o‘quvchilar holatini kuzatib, chora-tadbirlarni qayd eting.',
  },
  {
    icon: ChartNoAxesCombined,
    color: 'bg-navy/10 text-navy',
    title: 'Aniq statistika',
    description: 'Davomat, sinflar va holatlar bo‘yicha maktab ko‘rsatkichlarini tahlil qiling.',
  },
];

export default function WelcomePage() {
  const [sending, setSending] = useState(false);
  const [feedback, setFeedback] = useState(null);
  const { register, handleSubmit, reset, formState: { errors } } = useForm();

  async function submitMessage(values) {
    setSending(true);
    setFeedback(null);
    try {
      const { error } = await requireSupabase().rpc('submit_contact_message', {
        p_name: values.name.trim(),
        p_email: values.email.trim(),
        p_phone: values.phone.trim() || null,
        p_subject: values.subject.trim() || null,
        p_message: values.message.trim(),
      });
      if (error) throw error;
      reset();
      setFeedback({ type: 'success', text: 'Xabaringiz muvaffaqiyatli yuborildi. Tez orada siz bilan bog‘lanamiz.' });
    } catch (error) {
      setFeedback({ type: 'error', text: `Xabar yuborilmadi: ${error.message}` });
    } finally {
      setSending(false);
    }
  }

  return (
    <main className="min-h-screen overflow-hidden bg-white text-navy">
      <header className="relative z-10 border-b border-slate-100 bg-white/90">
        <div className="mx-auto flex max-w-7xl items-center justify-between gap-4 px-5 py-4 sm:px-8">
          <Link to="/" className="flex items-center gap-3">
            <span className="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-green to-teal-400 text-white shadow-lg shadow-green/20">
              <HeartPulse size={23} />
            </span>
            <span>
              <span className="block text-sm font-black tracking-wide">SOG‘LOM AVLOD</span>
              <span className="text-[10px] font-bold uppercase tracking-[0.18em] text-slate-400">Salomatlik platformasi</span>
            </span>
          </Link>
          <nav className="hidden items-center gap-7 text-sm font-extrabold text-slate-500 md:flex">
            <a className="transition hover:text-green" href="#imkoniyatlar">Imkoniyatlar</a>
            <a className="transition hover:text-green" href="#haqida">Loyiha haqida</a>
            <a className="transition hover:text-green" href="#xabar-yuborish">Xabar yuborish</a>
          </nav>
          <Link to="/login" className="inline-flex min-h-10 items-center gap-2 rounded-xl bg-green px-4 text-sm font-extrabold text-white shadow-lg shadow-green/15 transition hover:bg-green/90 sm:px-5">
            Tizimga kirish <ArrowRight size={16} />
          </Link>
        </div>
      </header>

      <section className="welcome-hero relative">
        <div className="pointer-events-none absolute -right-36 -top-44 h-[520px] w-[520px] rounded-full border-[1px] border-green/10" />
        <div className="pointer-events-none absolute -right-16 -top-24 h-[360px] w-[360px] rounded-full border-[55px] border-green/[0.035]" />
        <div className="relative mx-auto grid max-w-7xl items-center gap-12 px-5 py-16 sm:px-8 sm:py-24 lg:grid-cols-[1.1fr_.9fr] lg:py-28">
          <div>
            <span className="inline-flex items-center gap-2 rounded-full border border-green/15 bg-white/80 px-3.5 py-2 text-xs font-extrabold text-green shadow-sm">
              <span className="h-2 w-2 rounded-full bg-green" />
              Har bir bola uchun e’tibor va g‘amxo‘rlik
            </span>
            <h1 className="mt-6 max-w-2xl text-[42px] font-black leading-[1.08] tracking-tight text-navy sm:text-6xl">
              Sog‘lom avlod — <span className="text-green">yorqin kelajak</span>
            </h1>
            <p className="mt-6 max-w-xl text-base leading-7 text-slate-600 sm:text-lg sm:leading-8">
              Maktab o‘quvchilarining salomatligi, davomat va xavfsizligini boshqarish uchun yagona, qulay platforma.
            </p>
            <div className="mt-8 flex flex-wrap items-center gap-3">
              <Link to="/login" className="inline-flex min-h-12 items-center gap-2 rounded-xl bg-green px-6 text-sm font-black text-white shadow-xl shadow-green/20 transition hover:-translate-y-0.5 hover:bg-green/90">
                Platformaga kirish <ArrowRight size={17} />
              </Link>
              <a href="#imkoniyatlar" className="inline-flex min-h-12 items-center rounded-xl border border-slate-200 bg-white px-5 text-sm font-extrabold text-navy transition hover:border-green/30 hover:text-green">
                Imkoniyatlar bilan tanishish
              </a>
              <a href="#xabar-yuborish" className="inline-flex min-h-12 items-center gap-2 rounded-xl border border-slate-200 bg-white px-5 text-sm font-extrabold text-navy transition hover:border-green/30 hover:text-green">
                <Mail size={16} /> Xabar yuborish
              </a>
            </div>
            <div className="mt-9 flex flex-wrap gap-x-6 gap-y-3 text-xs font-bold text-slate-500">
              <span className="inline-flex items-center gap-2"><ShieldCheck size={16} className="text-green" /> Himoyalangan ma’lumotlar</span>
              <span className="inline-flex items-center gap-2"><Activity size={16} className="text-green" /> Jonli yangilanishlar</span>
            </div>
          </div>

          <div className="relative mx-auto w-full max-w-[480px]" id="haqida">
            <div className="absolute -inset-5 rounded-[36px] bg-gradient-to-br from-green/10 via-transparent to-orange/15 blur-2xl" />
            <div className="relative rounded-[28px] border border-white bg-white p-4 shadow-[0_28px_90px_rgba(10,37,64,.13)] sm:p-6">
              <div className="flex items-center justify-between border-b border-slate-100 pb-4">
                <div className="flex items-center gap-3">
                  <span className="flex h-10 w-10 items-center justify-center rounded-xl bg-green/10 text-green"><HeartPulse size={20} /></span>
                  <div><p className="text-sm font-black text-navy">Salomatlik markazi</p><p className="text-xs text-slate-400">Maktab boshqaruvi</p></div>
                </div>
                <span className="flex items-center gap-1.5 rounded-full bg-green/10 px-2.5 py-1 text-[10px] font-extrabold text-green"><span className="h-1.5 w-1.5 rounded-full bg-green" /> Tizim faol</span>
              </div>
              <div className="mt-5 grid grid-cols-2 gap-3">
                <div className="rounded-2xl bg-canvas p-4"><span className="flex h-9 w-9 items-center justify-center rounded-xl bg-green/10 text-green"><UsersRound size={18} /></span><p className="mt-4 text-xs font-bold text-slate-500">O‘quvchilar</p><p className="mt-1 text-lg font-black text-navy">Yagona ro‘yxat</p></div>
                <div className="rounded-2xl bg-canvas p-4"><span className="flex h-9 w-9 items-center justify-center rounded-xl bg-orange/15 text-amber-700"><CalendarCheck2 size={18} /></span><p className="mt-4 text-xs font-bold text-slate-500">Davomat</p><p className="mt-1 text-lg font-black text-navy">Kunlik nazorat</p></div>
              </div>
              <div className="mt-3 rounded-2xl bg-gradient-to-r from-navy to-[#16435f] p-4 text-white">
                <div className="flex items-center justify-between gap-3"><div><p className="text-xs font-bold text-white/65">Birgalikdagi maqsad</p><p className="mt-1 text-lg font-black">Har bir bola e’tiborda</p></div><span className="flex h-11 w-11 items-center justify-center rounded-xl bg-white/10"><ShieldCheck size={22} /></span></div>
                <div className="mt-4 h-1.5 overflow-hidden rounded-full bg-white/15"><div className="h-full w-4/5 rounded-full bg-teal-300" /></div>
              </div>
              <p className="px-1 pt-4 text-center text-xs font-bold text-slate-400">Salomatlik · Davomat · Xavfsizlik · Hamkorlik</p>
            </div>
          </div>
        </div>
      </section>

      <section id="imkoniyatlar" className="bg-canvas px-5 py-16 sm:px-8 sm:py-20">
        <div className="mx-auto max-w-7xl">
          <div className="mx-auto max-w-2xl text-center">
            <p className="text-xs font-black uppercase tracking-[0.18em] text-green">Platforma imkoniyatlari</p>
            <h2 className="mt-3 text-3xl font-black tracking-tight text-navy sm:text-4xl">Maktab jamoasi uchun kerakli vositalar</h2>
            <p className="mt-4 text-sm leading-6 text-slate-500 sm:text-base">Muhim ma’lumotlar bir joyda — o‘quvchilarga o‘z vaqtida ko‘mak berish uchun.</p>
          </div>
          <div className="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            {features.map(({ icon: Icon, color, title, description }) => (
              <article key={title} className="rounded-2xl border border-slate-100 bg-white p-5 shadow-card transition hover:-translate-y-1 hover:shadow-lg">
                <span className={`flex h-12 w-12 items-center justify-center rounded-2xl ${color}`}><Icon size={22} /></span>
                <h3 className="mt-5 font-black text-navy">{title}</h3>
                <p className="mt-2 text-sm leading-6 text-slate-500">{description}</p>
              </article>
            ))}
          </div>
        </div>
      </section>

      <section id="xabar-yuborish" className="px-5 py-16 sm:px-8 sm:py-20">
        <div className="mx-auto grid max-w-7xl gap-10 lg:grid-cols-[.8fr_1.2fr] lg:items-start">
          <div className="max-w-xl">
            <span className="flex h-12 w-12 items-center justify-center rounded-2xl bg-green/10 text-green"><Mail size={22} /></span>
            <p className="mt-5 text-xs font-black uppercase tracking-[0.18em] text-green">Biz bilan bog‘laning</p>
            <h2 className="mt-3 text-3xl font-black tracking-tight text-navy sm:text-4xl">Xabaringiz bormi?</h2>
            <p className="mt-4 text-sm leading-6 text-slate-500 sm:text-base">Savol yoki taklifingizni yuboring. Murojaatingiz administratorlarga yetkaziladi.</p>
          </div>
          <form className="space-y-4 rounded-2xl border border-slate-100 bg-white p-5 shadow-card sm:p-7" onSubmit={handleSubmit(submitMessage)}>
            <div className="grid gap-4 sm:grid-cols-2">
              <Field label="Ism-familiya" autoComplete="name" maxLength={100} error={errors.name?.message} {...register('name', { required: 'Ismingizni kiriting.', validate: (value) => value.trim().length >= 2 || 'Kamida 2 ta belgi kiriting.', maxLength: { value: 100, message: '100 ta belgidan oshmasin.' } })} />
              <Field label="Elektron pochta" type="email" autoComplete="email" maxLength={254} error={errors.email?.message} {...register('email', { required: 'Elektron pochtangizni kiriting.', maxLength: { value: 254, message: 'Elektron pochta juda uzun.' } })} />
              <Field label="Telefon (ixtiyoriy)" type="tel" autoComplete="tel" maxLength={30} error={errors.phone?.message} {...register('phone', { validate: (value) => !value.trim() || value.trim().length >= 7 || 'Telefon raqamini to‘liq kiriting.', maxLength: { value: 30, message: 'Telefon raqami juda uzun.' } })} />
              <Field label="Mavzu (ixtiyoriy)" maxLength={160} error={errors.subject?.message} {...register('subject', { maxLength: { value: 160, message: 'Mavzu 160 ta belgidan oshmasin.' } })} />
            </div>
            <TextAreaField label="Xabar" rows={5} maxLength={5000} error={errors.message?.message} {...register('message', { required: 'Xabar matnini kiriting.', validate: (value) => value.trim().length >= 10 || 'Xabar kamida 10 ta belgidan iborat bo‘lsin.', maxLength: { value: 5000, message: 'Xabar 5000 ta belgidan oshmasin.' } })} />
            {feedback && <p role={feedback.type === 'error' ? 'alert' : 'status'} className={`rounded-xl p-3 text-sm font-bold ${feedback.type === 'error' ? 'bg-red/5 text-red' : 'bg-green/10 text-green'}`}>{feedback.text}</p>}
            <div className="flex flex-wrap items-center justify-between gap-3">
              <p className="text-xs leading-5 text-slate-400">Yuborilgan murojaatni faqat administrator ko‘radi.</p>
              <Button type="submit" disabled={sending}>{sending ? 'Yuborilmoqda...' : 'Xabar yuborish'} <Send size={16} /></Button>
            </div>
          </form>
        </div>
      </section>

      <section className="px-5 py-14 sm:px-8 sm:py-16">
        <div className="mx-auto flex max-w-7xl flex-col items-start justify-between gap-6 rounded-[28px] bg-gradient-to-r from-navy to-[#16435f] p-7 text-white sm:flex-row sm:items-center sm:p-10">
          <div><p className="text-xs font-black uppercase tracking-[0.18em] text-teal-200">Sog‘lom kelajak sari</p><h2 className="mt-2 text-2xl font-black sm:text-3xl">O‘quvchilar salomatligini birga asraylik</h2><p className="mt-2 max-w-2xl text-sm leading-6 text-white/65">Hisobingiz orqali platformaga kiring va maktabingizdagi ishlarni boshqarishni boshlang.</p></div>
          <Link to="/login" className="inline-flex min-h-12 shrink-0 items-center gap-2 rounded-xl bg-white px-5 text-sm font-black text-navy transition hover:bg-teal-50">Tizimga kirish <ArrowRight size={16} /></Link>
        </div>
      </section>

      <footer className="border-t border-slate-100 px-5 py-6 sm:px-8">
        <div className="mx-auto flex max-w-7xl flex-col items-center justify-between gap-3 text-center text-xs text-slate-500 sm:flex-row sm:text-left">
          <Link to="/" className="inline-flex items-center gap-2 font-black text-navy"><HeartPulse size={16} className="text-green" /> SOG‘LOM AVLOD</Link>
          <span>© {new Date().getFullYear()} Sog‘lom Avlod · Sog‘lom kelajak uchun</span>
          <Link to="/login" className="font-extrabold text-green hover:underline">Xodimlar uchun kirish</Link>
        </div>
      </footer>
    </main>
  );
}
