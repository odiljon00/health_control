import { ExternalLink, HeartPulse } from 'lucide-react';

export default function ConfigScreen() {
  return (
    <main className="flex min-h-screen items-center justify-center bg-canvas px-5 py-12">
      <section className="w-full max-w-xl rounded-3xl border border-slate-100 bg-white p-8 shadow-card sm:p-10">
        <div className="mb-6 flex h-14 w-14 items-center justify-center rounded-2xl bg-green/10 text-green"><HeartPulse size={28} /></div>
        <p className="text-xs font-extrabold uppercase tracking-[0.18em] text-green">Sog‘lom Avlod</p>
        <h1 className="mt-2 text-2xl font-extrabold text-navy">Supabase ulanishini sozlang</h1>
        <p className="mt-3 text-sm leading-6 text-slate-600">Ilovani ishga tushirishdan oldin Supabase loyiha URL manzili va xavfsiz anon kalitini muhit o‘zgaruvchilariga qo‘shing.</p>
        <ol className="mt-6 list-inside list-decimal space-y-2 rounded-2xl bg-canvas p-5 text-sm font-semibold text-slate-700">
          <li><code>.env.example</code> faylidan nusxa olib <code>.env.local</code> yarating.</li>
          <li><code>VITE_SUPABASE_URL</code> va <code>VITE_SUPABASE_ANON_KEY</code> ni to‘ldiring.</li>
          <li>Supabase SQL Editor’da <code>supabase/migrations</code> migratsiyasini bajaring.</li>
        </ol>
        <a className="mt-6 inline-flex items-center gap-2 text-sm font-extrabold text-green hover:underline" href="https://supabase.com/dashboard" target="_blank" rel="noreferrer">Supabase boshqaruv paneli <ExternalLink size={15} /></a>
      </section>
    </main>
  );
}
