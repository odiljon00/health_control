create table public.school_settings (
  id boolean primary key default true check (id),
  default_school_id bigint references public.schools (id) on delete set null,
  updated_at timestamptz not null default now()
);

insert into public.school_settings (id, default_school_id)
select
  true,
  coalesce(
    (
      select p.school_id
      from public.profiles p
      where p.role = 'admin' and p.school_id is not null
      order by p.created_at
      limit 1
    ),
    case when count(*) = 1 then min(s.id) else null end
  )
from public.schools s
on conflict (id) do nothing;

create trigger school_settings_set_updated_at
before update on public.school_settings
for each row execute function public.set_updated_at();

alter table public.school_settings enable row level security;

create policy "Authenticated users read default school"
on public.school_settings for select to authenticated
using (true);

create policy "Admins manage default school"
on public.school_settings for all to authenticated
using (public.is_active_admin())
with check (public.is_active_admin());

revoke all on public.school_settings from anon;
grant select, insert, update, delete on public.school_settings to authenticated;

create or replace function public.current_school_id()
returns bigint
language sql
stable
security definer
set search_path = ''
as $$
  select coalesce(
    p.school_id,
    case
      when p.role in ('admin', 'teacher', 'psychologist', 'school_admin', 'iio')
        then (select ss.default_school_id from public.school_settings ss where ss.id)
      else null
    end
  )
  from public.profiles p
  where p.id = (select auth.uid()) and p.is_active;
$$;
