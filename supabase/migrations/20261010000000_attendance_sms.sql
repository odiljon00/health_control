create table public.attendance_sms_deliveries (
  id uuid primary key default gen_random_uuid(),
  student_id bigint not null references public.students (id) on delete cascade,
  attendance_date date not null,
  streak_days smallint not null check (streak_days in (3, 4, 5)),
  recipient_profile_id uuid not null references public.profiles (id) on delete cascade,
  recipient_phone text not null,
  status text not null default 'sending' check (status in ('sending', 'sent', 'failed')),
  provider_message_id text,
  error_message text,
  created_at timestamptz not null default now(),
  updated_at timestamptz not null default now(),
  constraint attendance_sms_deliveries_event_unique
    unique (student_id, attendance_date, streak_days, recipient_profile_id)
);

create index attendance_sms_deliveries_status_idx
  on public.attendance_sms_deliveries (status, updated_at);

create trigger attendance_sms_deliveries_set_updated_at
before update on public.attendance_sms_deliveries
for each row execute function public.set_updated_at();

alter table public.attendance_sms_deliveries enable row level security;
revoke all on public.attendance_sms_deliveries from anon, authenticated;
grant select, insert, update on public.attendance_sms_deliveries to service_role;

create or replace function public.attendance_absence_alerts(
  p_school_id bigint,
  p_attendance_date date
)
returns table (
  student_id bigint,
  student_name text,
  class_name text,
  parent_id uuid,
  absence_streak integer
)
language sql
stable
security definer
set search_path = ''
as $$
  with recent_attendance as (
    select
      s.id as student_id,
      s.first_name,
      s.last_name,
      s.middle_name,
      s.class_name,
      s.parent_id,
      a.attendance_date,
      a.status,
      a.is_excused,
      row_number() over (partition by s.id order by a.attendance_date desc) as day_number
    from public.students s
    cross join lateral (
      select a.attendance_date, a.status, a.is_excused
      from public.attendances a
      where a.student_id = s.id
        and a.school_id = p_school_id
        and a.attendance_date <= p_attendance_date
      order by a.attendance_date desc
      limit 6
    ) a
    where s.school_id = p_school_id
      and s.is_active
  ),
  streaks as (
    select
      ra.student_id,
      ra.first_name,
      ra.last_name,
      ra.middle_name,
      ra.class_name,
      ra.parent_id,
      least(
        coalesce(min(ra.day_number) filter (where ra.status <> 'absent' or ra.is_excused) - 1, count(*)::integer),
        6
      )::integer as absence_streak,
      bool_or(
        ra.day_number = 1
        and ra.attendance_date = p_attendance_date
        and ra.status = 'absent'
        and not ra.is_excused
      ) as absent_on_requested_date
    from recent_attendance ra
    group by ra.student_id, ra.first_name, ra.last_name, ra.middle_name, ra.class_name, ra.parent_id
  )
  select
    st.student_id,
    concat_ws(' ', st.last_name, st.first_name, st.middle_name),
    st.class_name,
    st.parent_id,
    st.absence_streak
  from streaks st
  where st.absent_on_requested_date
    and st.absence_streak >= 3;
$$;

revoke all on function public.attendance_absence_alerts(bigint, date)
  from public, anon, authenticated;
grant execute on function public.attendance_absence_alerts(bigint, date)
  to service_role;

create or replace function public.claim_attendance_sms_delivery(
  p_student_id bigint,
  p_attendance_date date,
  p_streak_days smallint,
  p_recipient_profile_id uuid,
  p_recipient_phone text
)
returns uuid
language plpgsql
security definer
set search_path = ''
as $$
declare
  claimed_id uuid;
begin
  insert into public.attendance_sms_deliveries (
    student_id,
    attendance_date,
    streak_days,
    recipient_profile_id,
    recipient_phone,
    status,
    error_message
  )
  values (
    p_student_id,
    p_attendance_date,
    p_streak_days,
    p_recipient_profile_id,
    p_recipient_phone,
    'sending',
    null
  )
  on conflict (student_id, attendance_date, streak_days, recipient_profile_id)
  do update set
    recipient_phone = excluded.recipient_phone,
    status = 'sending',
    error_message = null,
    updated_at = now()
  where public.attendance_sms_deliveries.status = 'failed'
     or (
       public.attendance_sms_deliveries.status = 'sending'
       and public.attendance_sms_deliveries.updated_at < now() - interval '15 minutes'
     )
  returning id into claimed_id;

  return claimed_id;
end;
$$;

revoke all on function public.claim_attendance_sms_delivery(bigint, date, smallint, uuid, text)
  from public, anon, authenticated;
grant execute on function public.claim_attendance_sms_delivery(bigint, date, smallint, uuid, text)
  to service_role;
