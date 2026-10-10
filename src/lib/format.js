export function formatDate(value, options = { day: 'numeric', month: 'short', year: 'numeric' }) {
  if (!value) return '—';
  const date = value instanceof Date
    ? value
    : new Date(`${value}`.includes('T') ? value : `${value}T12:00:00`);
  return new Intl.DateTimeFormat('uz-UZ', options).format(date);
}

export function formatNumber(value) {
  return new Intl.NumberFormat('uz-UZ').format(value ?? 0);
}

export function fullName(person) {
  return [person?.last_name, person?.first_name, person?.middle_name].filter(Boolean).join(' ') || '—';
}

export function initials(name = '') {
  return name.split(/\s+/).filter(Boolean).slice(0, 2).map((part) => part[0]).join('').toUpperCase() || 'SA';
}

export function errorMessage(error) {
  return (typeof error === 'string' ? error : error?.message) || 'Kutilmagan xatolik yuz berdi. Qayta urinib ko‘ring.';
}
