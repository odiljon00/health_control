# Sog‘lom Avlod

A school health and attendance platform built with React, Vite, Tailwind CSS, and Supabase. Supabase provides email/password authentication, PostgreSQL, private photo storage, row-level security, and Realtime updates.

## Requirements

- Node.js 20 or newer
- A Supabase project

## Local development

1. Install packages with `npm install`.
2. Copy `.env.example` to `.env.local` and set:

   ```dotenv
   VITE_SUPABASE_URL=https://your-project.supabase.co
   VITE_SUPABASE_ANON_KEY=your-project-anon-key
   ```

   Use the project's publishable/anon key only. Never put a Supabase `service_role` key in a Vite environment variable or browser code.

3. Apply `supabase/migrations/20261007000000_initial_schema.sql` from the Supabase SQL Editor, or link the CLI to your project with `supabase link --project-ref your-project-ref` and run `supabase db push`.
4. Create an initial administrator account in Supabase Authentication, then promote its generated profile using the SQL Editor:

   ```sql
   update public.profiles
   set role = 'admin', school_id = null
   where email = 'admin@example.com';
   ```

5. Create at least one school in the SQL Editor:

   ```sql
   insert into public.schools (name, region, district)
   values ('1-son umumta’lim maktabi', 'Toshkent shahri', 'Chilonzor')
   returning id;
   ```

6. Provision each staff account in Supabase Authentication, then assign its profile to the appropriate school and role:

   ```sql
   update public.profiles
   set role = 'teacher', school_id = 1, position = 'Sinf rahbari'
   where email = 'teacher@example.com';
   ```

   Supported roles are `admin`, `teacher`, `psychologist`, `school_admin`, `parent`, and `iio`. New Auth accounts receive the least-privileged `parent` role with no school assignment; users cannot change their own role, school, or active status. Disable public sign-ups in Supabase Auth if accounts should only be provisioned by an administrator.

7. Run `npm run dev` and open the local URL printed by Vite.

The public welcome page is available at `/`, and the sign-in page is at `/login`. Visitors can send a contact message from the welcome page; administrators can review it under **Xabarlar** after signing in. Apply `supabase/migrations/20261009000001_public_contact_messages.sql` and `supabase/migrations/20261009000002_contact_message_rpc.sql` to allow public message submissions. The second migration adds the RPC used by the form and is required even if the first migration was already applied. Signing out returns to the welcome page. The app intentionally displays a setup screen when either Supabase environment variable is missing. It does not fall back to mock or local data.

### Attendance SMS alerts (Eskiz)

Apply `supabase/migrations/20261010000000_attendance_sms.sql` and `supabase/migrations/20261010000001_student_parent_phone.sql`, deploy the `send-attendance-sms` Edge Function, and set these secrets in the Supabase project (never in `.env`, Vite variables, or browser code):

```text
ESKIZ_EMAIL=your Eskiz account email
ESKIZ_PASSWORD=your Eskiz account password
ESKIZ_FROM=4546
```

`ESKIZ_FROM` must be a sender ID enabled for the Eskiz account; `4546` is the usual shared sender. Deploy with `supabase functions deploy send-attendance-sms` and set secrets with `supabase secrets set ESKIZ_EMAIL=... ESKIZ_PASSWORD=... ESKIZ_FROM=4546`. Keep the password out of chat and source control. Each time staff save a day's attendance, the app checks the latest recorded attendance days: an unexcused absence on day 3 sends an SMS to the parent phone entered on the student's record; days 4 and 5 send SMS to active, SMS-enabled IIO profiles assigned to that school. A present or excused record breaks the streak. New student forms require a valid Uzbekistan parent phone number; existing students need the number added by editing their record. IIO profiles must have a school and phone number. SMS sending needs an active Eskiz account with sufficient balance. Failed SMS attempts are recorded and can be retried by saving that attendance again; successful alerts are not sent twice for the same day and recipient.
An administrator adding students does not choose a school on the student form: the administrator's default school is used automatically. Set it under **Settings → Personal information → Main school for students**. Saving the setting also assigns that school to staff accounts that currently have no school. Apply the `supabase/migrations/20261009000000_default_school.sql` migration to enable the shared default-school fallback for later unassigned staff accounts.

Authentication/session and profile checks time out with a recoverable error instead of leaving the protected pages on an endless loading screen. If an account still cannot enter, confirm its profile exists, is active, and has been assigned the intended role and school.

## Features

- Supabase email/password sign-in and password recovery
- Role-aware dashboard and navigation
- Student records with private photo uploads
- Daily attendance marking and absence alerts
- Risk case tracking with status/action history
- Attendance, class, and risk-level analytics
- Admin-only contact message management
- Profile, notification, theme, and password settings
- Live refresh for students, attendance, risk cases, and messages

## Database and access control

The SQL migration creates `schools`, `profiles`, `students`, `attendances`, `risk_cases`, `case_actions`, and `messages`. It enables RLS on every application table, scopes school records to the authenticated user's active school, restricts message management and reading to active admins, and adds database guards against self-promoting roles or reassigning tenant-owned records. Public contact messages are submitted through a narrowly scoped security-definer RPC that validates inputs; anonymous users cannot read or modify submitted messages. Parents get only a safe view of their own child’s parent-visible cases; internal case notes and actions are not exposed. Student/attendance/risk-case school relationships are constrained in PostgreSQL.

The `profiles-avatars` and `student-photos` Storage buckets are private and restrict upload/read access using the authenticated user and school. The client only uses Supabase's public anon key; authorization is enforced by Postgres and Storage RLS, not by client-side role checks.

Realtime is enabled for student, attendance, risk-case, and message changes. Records received through Realtime remain subject to the same RLS policies.

## Deployment

Build the static site with `npm run build`; the generated site is in `dist/`.

### Vercel

Import the repository as a Vite project, configure `VITE_SUPABASE_URL` and `VITE_SUPABASE_ANON_KEY` in the project's environment settings, then deploy. `vercel.json` rewrites client-side routes to the SPA entry point.

### Netlify

Import the repository, use `npm run build` as the build command and `dist` as the publish directory, and set the same two environment variables. `public/_redirects` provides the SPA route fallback.

Apply the Supabase migration to the production project, disable public sign-ups, and configure its Auth redirect URLs to include the deployed site before inviting users.
