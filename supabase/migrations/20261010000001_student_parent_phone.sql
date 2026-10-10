alter table public.students
  add column parent_phone text;

alter table public.attendance_sms_deliveries
  alter column recipient_profile_id drop not null;

alter table public.attendance_sms_deliveries
  add column recipient_key text generated always as (
    case
      when recipient_profile_id is not null then 'profile:' || recipient_profile_id::text
      else 'phone:' || recipient_phone
    end
  ) stored;

alter table public.attendance_sms_deliveries
  add constraint attendance_sms_deliveries_recipient_unique
  unique (student_id, attendance_date, streak_days, recipient_key);

drop function public.attendance_absence_alerts(bigint, date);

create function public.attendance_absence_alerts(
  p_school_id bigint,
  p_attendance_date date
)
returns table (
  student_id bigint,
  student_name text,
  class_name text,
  parent_id uuid,
  parent_phone text,
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
      s.parent_phone,
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
      ra.parent_phone,
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
    group by
      ra.student_id,
      ra.first_name,
      ra.last_name,
      ra.middle_name,
      ra.class_name,
      ra.parent_id,
      ra.parent_phone
  )
  select
    st.student_id,
    concat_ws(' ', st.last_name, st.first_name, st.middle_name),
    st.class_name,
    st.parent_id,
    st.parent_phone,
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
  on conflict (student_id, attendance_date, streak_days, recipient_key)
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
