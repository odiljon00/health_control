import { createClient } from '@supabase/supabase-js';

const supabaseUrl = import.meta.env.VITE_SUPABASE_URL?.trim();
const supabaseAnonKey = import.meta.env.VITE_SUPABASE_ANON_KEY?.trim();

export const isSupabaseConfigured = Boolean(supabaseUrl && supabaseAnonKey);

async function fetchWithTimeout(input, init = {}) {
  const controller = new AbortController();
  const timeoutId = window.setTimeout(() => {
    controller.abort(new Error('Supabase request timed out after 30 seconds.'));
  }, 30_000);
  const abortFromCaller = () => controller.abort(init.signal?.reason);

  if (init.signal?.aborted) abortFromCaller();
  else init.signal?.addEventListener('abort', abortFromCaller, { once: true });

  try {
    return await fetch(input, { ...init, signal: controller.signal });
  } finally {
    window.clearTimeout(timeoutId);
    init.signal?.removeEventListener('abort', abortFromCaller);
  }
}

export const supabase = isSupabaseConfigured
  ? createClient(supabaseUrl, supabaseAnonKey, {
      auth: {
        autoRefreshToken: true,
        persistSession: true,
        detectSessionInUrl: true,
      },
      realtime: {
        params: { eventsPerSecond: 5 },
      },
      global: {
        fetch: fetchWithTimeout,
      },
    })
  : null;

export function withTimeout(promise, timeoutMs, message) {
  let timeoutId;
  const timeout = new Promise((_, reject) => {
    timeoutId = window.setTimeout(() => reject(new Error(message)), timeoutMs);
  });

  return Promise.race([promise, timeout]).finally(() => window.clearTimeout(timeoutId));
}

export function requireSupabase() {
  if (!supabase) {
    throw new Error('Supabase is not configured. Add the project URL and anon key to your environment.');
  }
  return supabase;
}

export function getStorageUrl(bucket, path) {
  if (!path) return Promise.resolve(null);
  return requireSupabase()
    .storage.from(bucket)
    .createSignedUrl(path, 60 * 60)
    .then(({ data, error }) => {
      if (error) throw error;
      return data.signedUrl;
    });
}
