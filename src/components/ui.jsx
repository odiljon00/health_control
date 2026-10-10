import { forwardRef } from 'react';
import { LoaderCircle, Search } from 'lucide-react';
import { errorMessage } from '../lib/format.js';

export function PageTitle({ eyebrow, title, description, action }) {
  return (
    <div className="mb-7 flex flex-wrap items-end justify-between gap-4">
      <div>
        {eyebrow && <p className="mb-1 text-xs font-extrabold uppercase tracking-[0.16em] text-green">{eyebrow}</p>}
        <h1 className="text-2xl font-extrabold tracking-tight text-navy sm:text-[28px]">{title}</h1>
        {description && <p className="mt-1 text-sm text-slate-500">{description}</p>}
      </div>
      {action}
    </div>
  );
}

export function Card({ children, className = '' }) {
  return <section className={`rounded-2xl border border-slate-100 bg-white p-5 shadow-card sm:p-6 ${className}`}>{children}</section>;
}

export function Button({ children, className = '', variant = 'primary', ...props }) {
  const styles = {
    primary: 'bg-green text-white hover:bg-green/90',
    secondary: 'border border-slate-200 bg-white text-navy hover:bg-slate-50',
    danger: 'bg-red/10 text-red hover:bg-red/15',
    ghost: 'text-slate-500 hover:bg-slate-100 hover:text-navy',
  };
  return <button className={`inline-flex min-h-10 items-center justify-center gap-2 rounded-xl px-4 py-2 text-sm font-extrabold transition disabled:cursor-not-allowed disabled:opacity-50 ${styles[variant]} ${className}`} {...props}>{children}</button>;
}

export const Field = forwardRef(function Field({ label, error, className = '', ...props }, ref) {
  return (
    <label className={`block ${className}`}>
      {label && <span className="mb-1.5 block text-sm font-bold text-navy">{label}</span>}
      <input ref={ref} className="field" {...props} />
      {error && <span className="mt-1 block text-xs font-bold text-red">{error}</span>}
    </label>
  );
});

export const SelectField = forwardRef(function SelectField({ label, error, children, className = '', ...props }, ref) {
  return (
    <label className={`block ${className}`}>
      {label && <span className="mb-1.5 block text-sm font-bold text-navy">{label}</span>}
      <select ref={ref} className="field" {...props}>{children}</select>
      {error && <span className="mt-1 block text-xs font-bold text-red">{error}</span>}
    </label>
  );
});

export const TextAreaField = forwardRef(function TextAreaField({ label, error, className = '', ...props }, ref) {
  return (
    <label className={`block ${className}`}>
      {label && <span className="mb-1.5 block text-sm font-bold text-navy">{label}</span>}
      <textarea ref={ref} className="field min-h-28 resize-y" {...props} />
      {error && <span className="mt-1 block text-xs font-bold text-red">{error}</span>}
    </label>
  );
});

export function SearchBox({ value, onChange, placeholder = 'Qidirish...' }) {
  return (
    <label className="relative block min-w-[200px]">
      <Search size={17} className="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" />
      <input className="field pl-10" value={value} onChange={(event) => onChange(event.target.value)} placeholder={placeholder} />
    </label>
  );
}

export function Loading({ label = 'Yuklanmoqda...' }) {
  return <div className="flex min-h-44 items-center justify-center gap-2 text-sm font-bold text-slate-500"><LoaderCircle size={19} className="animate-spin text-green" />{label}</div>;
}

export function ErrorMessage({ error, onRetry }) {
  if (!error) return null;
  return <div role="alert" className="mb-5 flex flex-wrap items-center justify-between gap-3 rounded-xl border border-red/20 bg-red/5 px-4 py-3 text-sm font-bold text-red"><span>{errorMessage(error)}</span>{onRetry && <button className="underline" onClick={onRetry}>Qayta yuklash</button>}</div>;
}

export function EmptyState({ title = 'Hozircha ma’lumot yo‘q', description }) {
  return <div className="px-5 py-12 text-center"><p className="font-extrabold text-navy">{title}</p>{description && <p className="mt-1 text-sm text-slate-500">{description}</p>}</div>;
}

export function Badge({ children, tone = 'slate' }) {
  const tones = {
    green: 'bg-green/10 text-green',
    red: 'bg-red/10 text-red',
    orange: 'bg-orange/15 text-amber-700',
    navy: 'bg-navy/10 text-navy',
    slate: 'bg-slate-100 text-slate-600',
  };
  return <span className={`inline-flex items-center rounded-full px-2.5 py-1 text-xs font-extrabold ${tones[tone]}`}>{children}</span>;
}

export function Modal({ title, onClose, children, className = '' }) {
  return (
    <div className="modal-backdrop" role="presentation" onMouseDown={(event) => event.target === event.currentTarget && onClose()}>
      <section role="dialog" aria-modal="true" aria-labelledby="modal-title" className={`modal-panel ${className}`}>
        <header className="mb-5 flex items-center justify-between gap-3">
          <h2 id="modal-title" className="text-lg font-extrabold text-navy">{title}</h2>
          <button type="button" aria-label="Yopish" className="rounded-lg p-2 text-slate-400 hover:bg-slate-100 hover:text-navy" onClick={onClose}>×</button>
        </header>
        {children}
      </section>
    </div>
  );
}
