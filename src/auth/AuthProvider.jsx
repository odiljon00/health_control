import { createContext, useCallback, useContext, useEffect, useMemo, useRef, useState } from 'react';
import { isSupabaseConfigured, requireSupabase, withTimeout } from '../lib/supabase.js';
import { shouldLoadProfileForSession } from './authState.js';

const AuthContext = createContext(null);

export function AuthProvider({ children }) {
  const [session, setSession] = useState(null);
  const [profile, setProfile] = useState(null);
  const [loading, setLoading] = useState(isSupabaseConfigured);
  const [authError, setAuthError] = useState('');
  const sessionUserId = useRef(null);
  const authEventSeen = useRef(false);

  const loadProfile = useCallback(async (user) => {
    const client = requireSupabase();
    const { data, error } = await withTimeout(
      client.from('profiles').select('*').eq('id', user.id).maybeSingle(),
      15_000,
      'Profil yuklanishi 15 soniyadan ko‘proq vaqt oldi. Internet va Supabase ulanishini tekshirib, qayta urinib ko‘ring.',
    );
    if (error) throw error;
    if (!data) throw new Error('Foydalanuvchi profili topilmadi. Administrator hisobni sozlashi kerak.');
    if (!data.is_active) {
      await client.auth.signOut();
      throw new Error('Hisobingiz vaqtincha faolsizlantirilgan. Administrator bilan bog‘laning.');
    }
    return data;
  }, []);

  useEffect(() => {
    if (!isSupabaseConfigured) return undefined;
    const client = requireSupabase();
    let alive = true;
    withTimeout(
      client.auth.getSession(),
      15_000,
      'Supabase hisobiga ulanish 15 soniyadan ko‘proq vaqt oldi. Internet va loyiha URL manzilini tekshiring.',
    ).then(({ data, error }) => {
      if (error) throw error;
      if (!alive || authEventSeen.current) return;
      sessionUserId.current = data.session?.user?.id ?? null;
      setSession(data.session);
      if (!data.session) setLoading(false);
    }).catch((error) => {
      if (alive && !authEventSeen.current) {
        setAuthError(error.message);
        setLoading(false);
      }
    });
    const { data: listener } = client.auth.onAuthStateChange((_event, nextSession) => {
      if (!alive) return;
      authEventSeen.current = true;
      setSession(nextSession);
      if (!nextSession) {
        sessionUserId.current = null;
        setProfile(null);
        setAuthError('');
        setLoading(false);
      } else if (shouldLoadProfileForSession(sessionUserId.current, nextSession)) {
        sessionUserId.current = nextSession.user.id;
        setProfile(null);
        setLoading(true);
      }
    });
    return () => {
      alive = false;
      listener.subscription.unsubscribe();
    };
  }, []);

  useEffect(() => {
    if (!loading) return undefined;
    const timeoutId = window.setTimeout(() => {
      setAuthError('Tekshirish juda uzoq davom etmoqda. Supabase ulanishini tekshirib, qayta urinib ko‘ring.');
      setLoading(false);
    }, 18_000);
    return () => window.clearTimeout(timeoutId);
  }, [loading]);

  useEffect(() => {
    if (!isSupabaseConfigured || !session?.user) return undefined;
    let alive = true;
    setLoading(true);
    loadProfile(session.user).then((data) => {
      if (alive) {
        setProfile(data);
        setAuthError('');
      }
    }).catch((error) => {
      if (alive) {
        setProfile(null);
        setAuthError(error.message);
      }
    }).finally(() => {
      if (alive) setLoading(false);
    });
    return () => { alive = false; };
  }, [loadProfile, session?.user?.id]);

  const refreshProfile = useCallback(async () => {
    if (!session?.user) return null;
    const freshProfile = await loadProfile(session.user);
    setProfile(freshProfile);
    setAuthError('');
    return freshProfile;
  }, [loadProfile, session]);

  const signOut = useCallback(async () => {
    const { error } = await requireSupabase().auth.signOut();
    if (error) throw error;
    sessionUserId.current = null;
    setSession(null);
    setProfile(null);
  }, []);

  const value = useMemo(() => ({
    session,
    user: session?.user ?? null,
    profile,
    setProfile,
    loading,
    authError,
    refreshProfile,
    signOut,
  }), [session, profile, loading, authError, refreshProfile, signOut]);

  return <AuthContext.Provider value={value}>{children}</AuthContext.Provider>;
}

export function useAuth() {
  const context = useContext(AuthContext);
  if (!context) throw new Error('useAuth must be used within AuthProvider');
  return context;
}
