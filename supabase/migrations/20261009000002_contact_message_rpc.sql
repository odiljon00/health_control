create or replace function public.submit_contact_message(
  p_name text,
  p_email text,
  p_phone text,
  p_subject text,
  p_message text
)
returns void
language plpgsql
security definer
set search_path = ''
as $$
begin
  if p_name is null or char_length(btrim(p_name)) not between 2 and 100 then
    raise exception 'Ism 2 dan 100 tagacha belgidan iborat bo‘lishi kerak.'
      using errcode = '22023';
  end if;

  if p_email is null
    or char_length(p_email) not between 5 and 254
    or p_email <> btrim(p_email)
    or p_email !~ '^[^[:space:]@]+@[^[:space:]@]+[.][^[:space:]@]+$' then
    raise exception 'Elektron pochta manzili noto‘g‘ri.'
      using errcode = '22023';
  end if;

  if p_phone is not null and btrim(p_phone) <> ''
    and char_length(btrim(p_phone)) not between 7 and 30 then
    raise exception 'Telefon raqami 7 dan 30 tagacha belgidan iborat bo‘lishi kerak.'
      using errcode = '22023';
  end if;

  if p_subject is not null and char_length(btrim(p_subject)) > 160 then
    raise exception 'Mavzu 160 ta belgidan oshmasligi kerak.'
      using errcode = '22023';
  end if;

  if p_message is null or char_length(btrim(p_message)) not between 10 and 5000 then
    raise exception 'Xabar 10 dan 5000 tagacha belgidan iborat bo‘lishi kerak.'
      using errcode = '22023';
  end if;

  insert into public.messages (name, email, phone, subject, message)
  values (
    btrim(p_name),
    btrim(p_email),
    nullif(btrim(coalesce(p_phone, '')), ''),
    nullif(btrim(coalesce(p_subject, '')), ''),
    btrim(p_message)
  );
end;
$$;

revoke all on function public.submit_contact_message(text, text, text, text, text)
from public, anon, authenticated;
grant execute on function public.submit_contact_message(text, text, text, text, text)
to anon;
