create policy "Public users submit contact messages"
on public.messages for insert to anon
with check (
  char_length(btrim(name)) between 2 and 100
  and char_length(email) between 5 and 254
  and email = btrim(email)
  and email ~ '^[^[:space:]@]+@[^[:space:]@]+[.][^[:space:]@]+$'
  and (phone is null or char_length(btrim(phone)) between 7 and 30)
  and (subject is null or char_length(btrim(subject)) <= 160)
  and char_length(btrim(message)) between 10 and 5000
);

grant insert (name, email, phone, subject, message)
on public.messages to anon;
