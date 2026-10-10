import { useEffect, useRef } from 'react';
import { requireSupabase } from './supabase.js';

export function useRealtimeRefresh(tables, onChange) {
  const callback = useRef(onChange);
  callback.current = onChange;
  const tableKey = tables.join(',');

  useEffect(() => {
    const client = requireSupabase();
    const channel = client.channel(`live:${tableKey}:${crypto.randomUUID()}`);
    tables.forEach((table) => {
      channel.on('postgres_changes', { event: '*', schema: 'public', table }, () => callback.current());
    });
    channel.subscribe((status) => {
      if (status === 'CHANNEL_ERROR' || status === 'TIMED_OUT') {
        console.error(`Realtime subscription failed for ${tableKey}: ${status}`);
      }
    });
    return () => {
      client.removeChannel(channel);
    };
  }, [tableKey]);
}
