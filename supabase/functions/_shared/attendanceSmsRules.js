export function attendanceSmsAudience(streakDays) {
  if (streakDays === 3) return 'parent';
  if (streakDays === 4 || streakDays === 5) return 'iio';
  return null;
}
