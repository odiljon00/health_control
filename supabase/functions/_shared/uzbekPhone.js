export function normalizeUzbekPhone(phone) {
  const digits = (phone || '').replace(/\D/g, '').replace(/^00/, '');
  const normalized = digits.length === 9 ? `998${digits}` : digits;
  return /^998\d{9}$/.test(normalized) ? normalized : null;
}
