<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Kirish') — SOG'LOM AVLOD</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --green: #2A9D8F;
            --green-dark: #21867a;
            --green-light: #e3f4f1;
            --navy: #0A2540;
            --navy-light: #1a3a5c;
            --orange: #F4A261;
            --red: #E63946;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Nunito', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #f6f9ff 0%, #e8f4f1 100%);
            padding: 20px;
            position: relative;
            overflow: hidden;
            color: var(--navy);
        }

        /* Fon animatsiyalari */
        body::before {
            content: '';
            position: absolute;
            top: -50%; right: -20%;
            width: 700px; height: 700px;
            background: radial-gradient(circle, rgba(42,157,143,.15), transparent 70%);
            border-radius: 50%;
            animation: float 8s ease-in-out infinite;
            z-index: 0;
        }
        body::after {
            content: '';
            position: absolute;
            bottom: -30%; left: -10%;
            width: 500px; height: 500px;
            background: radial-gradient(circle, rgba(10,37,64,.08), transparent 70%);
            border-radius: 50%;
            animation: float 10s ease-in-out infinite reverse;
            z-index: 0;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50%      { transform: translateY(-20px); }
        }
        @keyframes slideUp {
            from { transform: translateY(30px); opacity: 0; }
            to   { transform: translateY(0);    opacity: 1; }
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 1; }
            50%      { transform: scale(1.08); opacity: .8; }
        }

        .auth-wrapper {
            width: 100%;
            max-width: 960px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            background: #fff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 30px 80px rgba(10,37,64,.15);
            position: relative;
            z-index: 2;
            animation: slideUp .6s cubic-bezier(0.16,1,0.3,1) both;
        }

        /* Chap panel — branding */
        .auth-brand-panel {
            background: linear-gradient(135deg, var(--navy) 0%, var(--navy-light) 100%);
            color: #fff;
            padding: 50px 40px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }

        .auth-brand-panel::before {
            content: '';
            position: absolute;
            top: -30%; right: -30%;
            width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(42,157,143,.25), transparent 70%);
            border-radius: 50%;
        }

        .auth-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 22px;
            font-weight: 800;
            position: relative;
            z-index: 2;
        }

        .auth-brand-icon {
            width: 46px;
            height: 46px;
            background: linear-gradient(135deg, var(--green), #4ecdc4);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            box-shadow: 0 8px 20px rgba(42,157,143,.4);
        }

        .auth-brand span { color: #4ecdc4; }

        .auth-brand-content {
            position: relative;
            z-index: 2;
            margin: 40px 0;
        }

        .auth-brand-content h2 {
            font-family: 'Poppins', sans-serif;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 14px;
            line-height: 1.3;
            color: #fff;
        }

        .auth-brand-content h2 span { color: #4ecdc4; }

        .auth-brand-content p {
            font-size: 15px;
            color: rgba(255,255,255,.75);
            line-height: 1.7;
            margin-bottom: 24px;
        }

        .auth-features {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .auth-features li {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 7px 0;
            font-size: 14px;
            color: rgba(255,255,255,.9);
        }

        .auth-features li i {
            color: #4ecdc4;
            font-size: 18px;
            flex-shrink: 0;
            margin-top: 2px;
        }

        .auth-brand-footer {
            position: relative;
            z-index: 2;
            font-size: 12px;
            color: rgba(255,255,255,.5);
            padding-top: 24px;
            border-top: 1px solid rgba(255,255,255,.1);
        }

        /* O'ng panel — forma */
        .auth-form-panel {
            padding: 50px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: #fff;
        }

        .auth-form-header {
            margin-bottom: 28px;
        }

        .auth-form-header h1 {
            font-family: 'Poppins', sans-serif;
            font-size: 26px;
            font-weight: 700;
            color: var(--navy);
            margin-bottom: 8px;
        }

        .auth-form-header p {
            font-size: 14px;
            color: #6c757d;
        }

        /* Form fields */
        .form-group {
            margin-bottom: 18px;
        }

        .form-label {
            display: block;
            font-weight: 700;
            font-size: 13px;
            margin-bottom: 8px;
            color: var(--navy);
        }

        .form-input-wrapper {
            position: relative;
        }

        .form-input-wrapper i.input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #899bbd;
            font-size: 18px;
            transition: color .2s ease;
        }

        .form-input {
            width: 100%;
            padding: 13px 14px 13px 44px;
            border: 1.5px solid #d8dee6;
            border-radius: 10px;
            font-family: inherit;
            font-size: 14px;
            color: var(--navy);
            transition: all .2s ease;
            background: #fff;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--green);
            box-shadow: 0 0 0 4px rgba(42,157,143,.12);
        }

        .form-input:focus + i.input-icon,
        .form-input-wrapper:focus-within i.input-icon {
            color: var(--green);
        }

        .form-input.is-invalid {
            border-color: var(--red);
        }

        .invalid-feedback {
            display: block;
            color: var(--red);
            font-size: 12px;
            margin-top: 6px;
            font-weight: 600;
        }

        /* Checkbox */
        .form-check {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
        }

        .form-check input[type="checkbox"] {
            width: 18px;
            height: 18px;
            margin: 0;
            cursor: pointer;
            accent-color: var(--green);
        }

        .form-check label {
            font-size: 14px;
            color: #495057;
            cursor: pointer;
            margin: 0;
            font-weight: 500;
        }

        /* Submit button */
        .btn-auth {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, var(--green), var(--green-dark));
            color: #fff;
            border: none;
            border-radius: 10px;
            font-family: inherit;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: all .25s cubic-bezier(0.16,1,0.3,1);
            box-shadow: 0 4px 14px rgba(42,157,143,.3);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-auth:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(42,157,143,.45);
        }

        .btn-auth:active { transform: translateY(0); }

        /* Link */
        .auth-link {
            display: block;
            text-align: center;
            margin-top: 18px;
            font-size: 14px;
            color: #6c757d;
        }

        .auth-link a {
            color: var(--green);
            font-weight: 700;
            text-decoration: none;
            transition: color .2s ease;
        }

        .auth-link a:hover { color: var(--green-dark); }

        /* Alert */
        .alert {
            padding: 12px 16px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 14px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .alert-danger {
            background: #fde8ea;
            color: #842029;
            border-left: 4px solid var(--red);
        }

        /* Mobil */
        @media (max-width: 900px) {
            .auth-wrapper { grid-template-columns: 1fr; max-width: 480px; }
            .auth-brand-panel { padding: 32px 28px; }
            .auth-brand-content { margin: 24px 0; }
            .auth-brand-content h2 { font-size: 22px; }
            .auth-form-panel { padding: 32px 28px; }
        }

        @media (max-width: 500px) {
            body { padding: 10px; }
            .auth-wrapper { border-radius: 16px; }
            .auth-form-header h1 { font-size: 22px; }
        }
    </style>
</head>
<body>

<div class="auth-wrapper">
    {{-- CHAP: Brending --}}
    <div class="auth-brand-panel">
        <div>
            <div class="auth-brand">
                <div class="auth-brand-icon">🛡️</div>
                <div>SOG'LOM <span>AVLOD</span></div>
            </div>

            <div class="auth-brand-content">
                <h2>Yoshlarni <span>himoya qilish</span> platformasi</h2>
                <p>
                    Maktab, kollej va litseylarda o'quvchilarni giyohvandlik, tamaki va
                    jinoyatchilikdan erta aniqlash va o'z vaqtida yordam berish tizimi.
                </p>

                <ul class="auth-features">
                    <li>
                        <i class="bi bi-check-circle-fill"></i>
                        <span>Erta aniqlash va profilaktika</span>
                    </li>
                    <li>
                        <i class="bi bi-check-circle-fill"></i>
                        <span>Ustoz, psixolog va ota-ona aloqasi</span>
                    </li>
                    <li>
                        <i class="bi bi-check-circle-fill"></i>
                        <span>Avtomatik xabarnoma tizimi</span>
                    </li>
                    <li>
                        <i class="bi bi-check-circle-fill"></i>
                        <span>IIO bilan integratsiya</span>
                    </li>
                </ul>
            </div>
        </div>

        <div class="auth-brand-footer">
            © {{ date('Y') }} SOG'LOM AVLOD • Bugun e'tibor — ertaga sog'lom avlod
        </div>
    </div>

    {{-- O'NG: Forma --}}
    <div class="auth-form-panel">
        <div class="auth-form-header">
            <h1>@yield('form-title', 'Tizimga kirish')</h1>
            <p>@yield('form-subtitle', 'Hisobingizga kiring va ishlashni davom ettiring')</p>
        </div>

        @if(session('status'))
            <div class="alert alert-danger">
                <i class="bi bi-info-circle-fill"></i>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        @yield('form')
    </div>
</div>

@stack('scripts')
</body>
</html>