export function shouldLoadProfileForSession(currentSessionUserId, nextSession) {
  const nextUserId = nextSession?.user?.id;
  return Boolean(nextUserId && nextUserId !== currentSessionUserId);
}
