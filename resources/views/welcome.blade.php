<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SOG'LOM AVLOD — Yoshlarni himoya qilish platformasi</title>
    <meta name="description" content="Maktab, kollej va litseylarda o'quvchilarni giyohvandlik, tamaki va jinoyatchilikdan erta aniqlash va oldini olish platformasi.">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --green: #2A9D8F;
            --green-dark: #21867a;
            --green-light: #e3f4f1;
            --navy: #0A2540;
            --navy-light: #1a3a5c;
            --orange: #F4A261;
            --red: #E63946;
            --gray-50: #f6f9ff;
            --gray-100: #eef1f5;
            --gray-500: #6c757d;
            --shadow-sm: 0 2px 10px rgba(10,37,64,.06);
            --shadow-md: 0 8px 28px rgba(10,37,64,.10);
            --shadow-lg: 0 20px 60px rgba(10,37,64,.18);
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'Nunito', sans-serif;
            color: var(--navy);
            background: #fff;
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
        }

        h1, h2, h3, h4 {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            line-height: 1.25;
            color: var(--navy);
        }

        a { text-decoration: none; color: inherit; }

        /* ==================== ANIMATSIYALAR ==================== */
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes slideUp {
            from { transform: translateY(30px); opacity: 0; }
            to   { transform: translateY(0);    opacity: 1; }
        }
        @keyframes slideDown {
            from { transform: translateY(-20px); opacity: 0; }
            to   { transform: translateY(0);     opacity: 1; }
        }
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50%      { transform: translateY(-12px); }
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 1; }
            50%      { transform: scale(1.08); opacity: .8; }
        }
        @keyframes glow {
            0%, 100% { box-shadow: 0 0 0 0 rgba(42,157,143,.5); }
            50%      { box-shadow: 0 0 0 20px rgba(42,157,143,0); }
        }
        @keyframes gradientShift {
            0%   { background-position: 0% 50%; }
            50%  { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        @keyframes bounceIn {
            0%   { transform: scale(0.85); opacity: 0; }
            60%  { transform: scale(1.03); opacity: 1; }
            100% { transform: scale(1); }
        }

        .animate-up { animation: slideUp .7s cubic-bezier(0.16,1,0.3,1) both; }
        .animate-down { animation: slideDown .6s ease both; }

        /* ==================== NAVBAR ==================== */
        .navbar {
            position: fixed;
            top: 0; left: 0; right: 0;
            background: rgba(255,255,255,.95);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            z-index: 1000;
            padding: 14px 0;
            box-shadow: 0 2px 20px rgba(10,37,64,.06);
            animation: slideDown .5s ease both;
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .nav-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 20px;
            font-weight: 800;
            color: var(--navy);
        }

        .nav-brand-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--green), #4ecdc4);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            color: #fff;
            box-shadow: 0 6px 16px rgba(42,157,143,.35);
        }

        .nav-brand span { color: var(--green); }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 32px;
        }

        .nav-link {
            font-weight: 600;
            font-size: 15px;
            color: var(--navy);
            transition: color .2s ease;
            position: relative;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -4px; left: 0;
            width: 0; height: 2px;
            background: var(--green);
            transition: width .3s ease;
        }

        .nav-link:hover { color: var(--green); }
        .nav-link:hover::after { width: 100%; }

        .nav-cta {
            display: flex;
            gap: 10px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 11px 22px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 14px;
            cursor: pointer;
            border: none;
            font-family: inherit;
            transition: all .25s cubic-bezier(0.16,1,0.3,1);
            text-decoration: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--green), var(--green-dark));
            color: #fff;
            box-shadow: 0 4px 14px rgba(42,157,143,.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(42,157,143,.45);
            color: #fff;
        }

        .btn-outline {
            background: transparent;
            color: var(--navy);
            border: 2px solid var(--navy);
        }

        .btn-outline:hover {
            background: var(--navy);
            color: #fff;
            transform: translateY(-2px);
        }

        .btn-white {
            background: #fff;
            color: var(--navy);
        }

        .btn-white:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(255,255,255,.3);
            color: var(--navy);
        }

        .btn-lg { padding: 15px 32px; font-size: 16px; }

        /* ==================== HERO ==================== */
        .hero {
            min-height: 100vh;
            padding: 140px 24px 80px;
            display: flex;
            align-items: center;
            background: linear-gradient(135deg, #f6f9ff 0%, #e8f4f1 100%);
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: -50%; right: -20%;
            width: 700px; height: 700px;
            background: radial-gradient(circle, rgba(42,157,143,.15), transparent 70%);
            border-radius: 50%;
            animation: float 8s ease-in-out infinite;
        }

        .hero::after {
            content: '';
            position: absolute;
            bottom: -30%; left: -10%;
            width: 500px; height: 500px;
            background: radial-gradient(circle, rgba(10,37,64,.08), transparent 70%);
            border-radius: 50%;
            animation: float 10s ease-in-out infinite reverse;
        }

        .hero-container {
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
            position: relative;
            z-index: 2;
            display: grid;
            grid-template-columns: 1.1fr 1fr;
            gap: 60px;
            align-items: center;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            background: rgba(42,157,143,.12);
            color: var(--green);
            border-radius: 30px;
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 20px;
            animation: slideUp .6s ease both;
        }

        .hero-badge i { font-size: 16px; animation: pulse 2s ease-in-out infinite; }

        .hero-title {
            font-size: clamp(36px, 5vw, 58px);
            font-weight: 800;
            margin-bottom: 20px;
            line-height: 1.15;
            color: var(--navy);
            animation: slideUp .7s ease both .1s;
        }

        .hero-title span {
            background: linear-gradient(135deg, var(--green), #4ecdc4);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-subtitle {
            font-size: 18px;
            color: var(--gray-500);
            margin-bottom: 30px;
            max-width: 540px;
            line-height: 1.7;
            animation: slideUp .7s ease both .2s;
        }

        .hero-tagline {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 12px 20px;
            background: #fff;
            border-radius: 12px;
            box-shadow: var(--shadow-sm);
            font-weight: 700;
            color: var(--navy);
            margin-bottom: 30px;
            font-size: 14px;
            animation: slideUp .7s ease both .3s;
        }

        .hero-tagline i { color: var(--green); font-size: 20px; }

        .hero-buttons {
            display: flex;
            gap: 14px;
            flex-wrap: wrap;
            animation: slideUp .7s ease both .4s;
        }

        /* Hero visual */
        .hero-visual {
            position: relative;
            animation: slideUp .8s ease both .3s;
        }

        .hero-card {
            background: #fff;
            border-radius: 20px;
            padding: 24px;
            box-shadow: var(--shadow-lg);
            animation: float 6s ease-in-out infinite;
        }

        .hero-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 18px;
            padding-bottom: 18px;
            border-bottom: 1px solid var(--gray-100);
        }

        .hero-card-title {
            font-weight: 700;
            font-size: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .hero-card-title i {
            color: var(--green);
            font-size: 18px;
        }

        .hero-stat-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px dashed var(--gray-100);
        }

        .hero-stat-row:last-child { border-bottom: none; }

        .hero-stat-left {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            font-weight: 600;
        }

        .hero-stat-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 16px;
        }

        .hero-stat-icon.green { background: linear-gradient(135deg, var(--green), #4ecdc4); }
        .hero-stat-icon.red { background: linear-gradient(135deg, var(--red), #ff6b7a); }
        .hero-stat-icon.orange { background: linear-gradient(135deg, var(--orange), #ffbe80); }
        .hero-stat-icon.navy { background: linear-gradient(135deg, var(--navy), var(--navy-light)); }

        .hero-stat-value {
            font-weight: 800;
            font-size: 18px;
        }

        .hero-card-footer {
            margin-top: 18px;
            padding-top: 18px;
            border-top: 1px solid var(--gray-100);
            display: flex;
            gap: 8px;
        }

        .hero-mini-btn {
            flex: 1;
            text-align: center;
            padding: 10px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 700;
            background: var(--green-light);
            color: var(--green);
        }

        .hero-mini-btn.active {
            background: linear-gradient(135deg, var(--green), var(--green-dark));
            color: #fff;
        }

        /* Floating notification */
        .floating-notif {
            position: absolute;
            top: 20%;
            right: -30px;
            background: #fff;
            padding: 12px 16px;
            border-radius: 14px;
            box-shadow: var(--shadow-md);
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13px;
            font-weight: 700;
            animation: float 5s ease-in-out infinite 0.5s;
        }

        .floating-notif i {
            width: 30px; height: 30px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--red), #ff6b7a);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            animation: pulse 2s ease-in-out infinite;
        }

        .floating-success {
            position: absolute;
            bottom: 10%;
            left: -20px;
            background: #fff;
            padding: 12px 16px;
            border-radius: 14px;
            box-shadow: var(--shadow-md);
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13px;
            font-weight: 700;
            animation: float 5.5s ease-in-out infinite 1s;
        }

        .floating-success i {
            width: 30px; height: 30px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--green), #4ecdc4);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
        }

        /* ==================== SECTIONS ==================== */
        section {
            padding: 90px 24px;
        }

        .section-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .section-header {
            text-align: center;
            max-width: 700px;
            margin: 0 auto 60px;
        }

        .section-label {
            display: inline-block;
            padding: 6px 16px;
            background: var(--green-light);
            color: var(--green);
            border-radius: 30px;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 16px;
        }

        .section-title {
            font-size: clamp(28px, 4vw, 42px);
            margin-bottom: 16px;
            color: var(--navy);
        }

        .section-subtitle {
            font-size: 17px;
            color: var(--gray-500);
            line-height: 1.7;
        }

        /* ==================== MUAMMO ==================== */
        .problem-section {
            background: #fff;
        }

        .problem-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 20px;
        }

        .problem-card {
            padding: 28px;
            background: linear-gradient(135deg, #fff 0%, #fef8f9 100%);
            border: 1.5px solid #fde8ea;
            border-radius: 16px;
            transition: all .3s cubic-bezier(0.16,1,0.3,1);
            position: relative;
            overflow: hidden;
        }

        .problem-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0;
            width: 4px; height: 100%;
            background: var(--red);
        }

        .problem-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 16px 40px rgba(230,57,70,.15);
        }

        .problem-icon {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            background: linear-gradient(135deg, #fde8ea, #fff);
            color: var(--red);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 16px;
            border: 1.5px solid #fde8ea;
        }

        .problem-title {
            font-size: 17px;
            font-weight: 700;
            margin-bottom: 8px;
            color: var(--navy);
        }

        .problem-text {
            font-size: 14px;
            color: var(--gray-500);
            line-height: 1.6;
        }

        .problem-conclusion {
            margin-top: 40px;
            padding: 24px 32px;
            background: linear-gradient(135deg, var(--navy), var(--navy-light));
            border-radius: 16px;
            text-align: center;
            color: #fff;
            font-size: 20px;
            font-weight: 700;
            position: relative;
            overflow: hidden;
        }

        .problem-conclusion::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,.1), transparent);
            animation: gradientShift 4s ease infinite;
            background-size: 200% 100%;
        }

        .problem-conclusion span {
            position: relative;
            z-index: 1;
        }

        .problem-conclusion strong { color: #4ecdc4; }

        /* ==================== YECHIM ==================== */
        .solution-section {
            background: linear-gradient(180deg, #f6f9ff 0%, #fff 100%);
        }

        .solution-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 22px;
        }

        .solution-card {
            padding: 30px 26px;
            background: #fff;
            border-radius: 16px;
            box-shadow: var(--shadow-sm);
            transition: all .3s cubic-bezier(0.16,1,0.3,1);
            border-top: 4px solid var(--green);
            position: relative;
        }

        .solution-card:hover {
            transform: translateY(-6px);
            box-shadow: var(--shadow-md);
        }

        .solution-icon {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            background: linear-gradient(135deg, var(--green-light), #fff);
            color: var(--green);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            margin-bottom: 18px;
            box-shadow: 0 4px 12px rgba(42,157,143,.15);
        }

        .solution-title {
            font-size: 17px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .solution-text {
            font-size: 14px;
            color: var(--gray-500);
            line-height: 1.65;
        }

        /* ==================== QANDAY ISHLAYDI ==================== */
        .how-section {
            background: #fff;
        }

        .steps-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 24px;
            position: relative;
        }

        .step-card {
            text-align: center;
            padding: 30px 20px;
            position: relative;
        }

        .step-num {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--green), var(--green-dark));
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            font-weight: 800;
            margin: 0 auto 20px;
            box-shadow: 0 8px 20px rgba(42,157,143,.35);
            position: relative;
            z-index: 2;
            transition: all .3s ease;
        }

        .step-card:hover .step-num {
            transform: scale(1.1) rotate(-5deg);
            animation: glow 1.5s ease infinite;
        }

        .step-title {
            font-size: 17px;
            font-weight: 700;
            margin-bottom: 8px;
            color: var(--navy);
        }

        .step-text {
            font-size: 14px;
            color: var(--gray-500);
            line-height: 1.6;
        }

        /* ==================== FOYDALAR (3 rol) ==================== */
        .roles-section {
            background: linear-gradient(180deg, #fff 0%, #f6f9ff 100%);
        }

        .roles-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 24px;
        }

        .role-card {
            background: #fff;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            transition: all .3s cubic-bezier(0.16,1,0.3,1);
        }

        .role-card:hover {
            transform: translateY(-6px);
            box-shadow: var(--shadow-md);
        }

        .role-header {
            padding: 24px;
            color: #fff;
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .role-header.teacher  { background: linear-gradient(135deg, var(--green), var(--green-dark)); }
        .role-header.psych    { background: linear-gradient(135deg, #7c3aed, #a855f7); }
        .role-header.admin    { background: linear-gradient(135deg, var(--navy), var(--navy-light)); }

        .role-emoji {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            background: rgba(255,255,255,.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            backdrop-filter: blur(10px);
        }

        .role-name {
            font-size: 18px;
            font-weight: 700;
        }

        .role-desc {
            font-size: 13px;
            opacity: .9;
        }

        .role-body {
            padding: 22px 24px;
        }

        .role-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .role-list li {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 9px 0;
            font-size: 14px;
            color: #495057;
            line-height: 1.5;
        }

        .role-list li i {
            color: var(--green);
            font-size: 16px;
            margin-top: 3px;
            flex-shrink: 0;
        }

        /* ==================== FOYDALAR (foyda) ==================== */
        .benefits-section {
            background: #fff;
        }

        .benefits-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 18px;
        }

        .benefit-card {
            padding: 24px;
            border-radius: 14px;
            background: linear-gradient(135deg, #fff, #f6f9ff);
            border: 1.5px solid var(--gray-100);
            transition: all .3s ease;
            text-align: center;
        }

        .benefit-card:hover {
            transform: translateY(-4px);
            border-color: var(--green);
            background: linear-gradient(135deg, #fff, var(--green-light));
        }

        .benefit-icon {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: var(--green-light);
            color: var(--green);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin: 0 auto 14px;
            transition: all .3s ease;
        }

        .benefit-card:hover .benefit-icon {
            background: linear-gradient(135deg, var(--green), var(--green-dark));
            color: #fff;
            transform: scale(1.1);
        }

        .benefit-title {
            font-size: 15px;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .benefit-text {
            font-size: 13px;
            color: var(--gray-500);
            line-height: 1.55;
        }

        /* ==================== MAXFIYLIK ==================== */
        .privacy-section {
            background: linear-gradient(135deg, var(--navy), var(--navy-light));
            color: #fff;
            position: relative;
            overflow: hidden;
        }

        .privacy-section::before {
            content: '';
            position: absolute;
            top: -50%; right: -20%;
            width: 600px; height: 600px;
            background: radial-gradient(circle, rgba(42,157,143,.15), transparent 70%);
            border-radius: 50%;
        }

        .privacy-grid {
            display: grid;
            grid-template-columns: 1.2fr 1fr;
            gap: 60px;
            align-items: center;
            position: relative;
            z-index: 2;
        }

        .privacy-section .section-title { color: #fff; }
        .privacy-section .section-label { background: rgba(255,255,255,.15); color: #4ecdc4; }
        .privacy-section .section-subtitle { color: rgba(255,255,255,.8); }

        .privacy-features {
            list-style: none;
            padding: 0;
            margin: 24px 0 0;
        }

        .privacy-features li {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 10px 0;
            font-size: 15px;
            color: rgba(255,255,255,.9);
        }

        .privacy-features li i {
            color: #4ecdc4;
            font-size: 20px;
            flex-shrink: 0;
        }

        .risk-visual {
            background: rgba(255,255,255,.08);
            border: 1.5px solid rgba(255,255,255,.15);
            border-radius: 20px;
            padding: 28px;
            backdrop-filter: blur(10px);
        }

        .risk-visual-title {
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: rgba(255,255,255,.7);
            margin-bottom: 18px;
            font-weight: 700;
        }

        .risk-bar {
            margin-bottom: 18px;
        }

        .risk-bar-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 6px;
            font-size: 14px;
            font-weight: 600;
        }

        .risk-bar-track {
            height: 10px;
            background: rgba(255,255,255,.1);
            border-radius: 6px;
            overflow: hidden;
        }

        .risk-bar-fill {
            height: 100%;
            border-radius: 6px;
            transition: width 1s ease;
        }

        .risk-bar-fill.red { background: linear-gradient(90deg, #E63946, #ff6b7a); }
        .risk-bar-fill.yellow { background: linear-gradient(90deg, #F4A261, #ffbe80); }
        .risk-bar-fill.green { background: linear-gradient(90deg, #2A9D8F, #4ecdc4); }

        /* ==================== TEXNOLOGIYA ==================== */
        .tech-section {
            background: #fff;
        }

        .tech-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 16px;
        }

        .tech-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 16px 20px;
            background: #fff;
            border: 1.5px solid var(--gray-100);
            border-radius: 12px;
            transition: all .25s ease;
        }

        .tech-item:hover {
            border-color: var(--green);
            transform: translateY(-3px);
            box-shadow: var(--shadow-sm);
        }

        .tech-item-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: var(--green-light);
            color: var(--green);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            flex-shrink: 0;
        }

        .tech-name {
            font-weight: 700;
            font-size: 14px;
        }

        .tech-role {
            font-size: 12px;
            color: var(--gray-500);
        }

        /* ==================== ROADMAP ==================== */
        .roadmap-section {
            background: linear-gradient(180deg, #f6f9ff, #fff);
        }

        .roadmap-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
        }

        .roadmap-card {
            padding: 26px;
            background: #fff;
            border-radius: 16px;
            box-shadow: var(--shadow-sm);
            position: relative;
            transition: all .3s ease;
            border-left: 4px solid var(--green);
        }

        .roadmap-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-md);
        }

        .roadmap-stage {
            font-size: 12px;
            font-weight: 800;
            color: var(--green);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
        }

        .roadmap-title {
            font-size: 17px;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .roadmap-text {
            font-size: 14px;
            color: var(--gray-500);
        }

        /* ==================== CTA ==================== */
        .cta-section {
            background: linear-gradient(135deg, var(--green), var(--green-dark));
            color: #fff;
            padding: 90px 24px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .cta-section::before {
            content: '';
            position: absolute;
            top: -50%; left: -20%;
            width: 600px; height: 600px;
            background: radial-gradient(circle, rgba(255,255,255,.15), transparent 70%);
            border-radius: 50%;
            animation: float 8s ease-in-out infinite;
        }

        .cta-section::after {
            content: '';
            position: absolute;
            bottom: -50%; right: -20%;
            width: 500px; height: 500px;
            background: radial-gradient(circle, rgba(255,255,255,.1), transparent 70%);
            border-radius: 50%;
            animation: float 10s ease-in-out infinite reverse;
        }

        .cta-content {
            position: relative;
            z-index: 2;
            max-width: 800px;
            margin: 0 auto;
        }

        .cta-title {
            font-size: clamp(28px, 4vw, 44px);
            color: #fff;
            margin-bottom: 16px;
        }

        .cta-subtitle {
            font-size: 18px;
            opacity: .95;
            margin-bottom: 32px;
            line-height: 1.6;
        }

        .cta-buttons {
            display: flex;
            gap: 14px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .cta-info {
            margin-top: 40px;
            display: flex;
            justify-content: center;
            gap: 40px;
            flex-wrap: wrap;
            font-size: 15px;
        }

        .cta-info-item {
            display: flex;
            align-items: center;
            gap: 8px;
            opacity: .95;
        }

        .cta-info-item i {
            font-size: 18px;
        }

        /* ==================== FOOTER ==================== */
        footer {
            background: var(--navy);
            color: rgba(255,255,255,.8);
            padding: 50px 24px 30px;
        }

        .footer-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .footer-top {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 40px;
            padding-bottom: 30px;
            border-bottom: 1px solid rgba(255,255,255,.1);
        }

        .footer-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 12px;
            font-weight: 800;
            font-size: 18px;
            color: #fff;
        }

        .footer-brand-icon {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, var(--green), #4ecdc4);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .footer-brand span { color: #4ecdc4; }

        .footer-text {
            font-size: 14px;
            line-height: 1.6;
            color: rgba(255,255,255,.65);
        }

        .footer-title {
            font-size: 13px;
            font-weight: 800;
            color: #fff;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 16px;
        }

        .footer-links {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .footer-links li {
            margin-bottom: 10px;
        }

        .footer-links a {
            font-size: 14px;
            color: rgba(255,255,255,.7);
            transition: color .2s ease;
        }

        .footer-links a:hover { color: #4ecdc4; }

        .footer-bottom {
            padding-top: 24px;
            text-align: center;
            font-size: 13px;
            color: rgba(255,255,255,.5);
        }

        /* ==================== RESPONSIVE ==================== */
        @media (max-width: 900px) {
            .nav-links { display: none; }
            .hero-container { grid-template-columns: 1fr; gap: 40px; }
            .hero-visual { display: none; }
            .privacy-grid { grid-template-columns: 1fr; gap: 40px; }
            .footer-top { grid-template-columns: 1fr; gap: 30px; }
            .nav-cta .btn-outline { display: none; }
        }

        @media (max-width: 600px) {
            section { padding: 60px 16px; }
            .hero { padding: 120px 16px 60px; }
            .btn-lg { padding: 13px 24px; font-size: 15px; }
            .cta-info { gap: 20px; }
        }
    </style>
</head>
<body>

{{-- ==================== NAVBAR ==================== --}}
<nav class="navbar">
    <div class="nav-container">
        <a href="{{ url('/') }}" class="nav-brand">
            <div class="nav-brand-icon">🛡️</div>
            <div>SOG'LOM <span>AVLOD</span></div>
        </a>

        <div class="nav-links">
            <a href="#problem" class="nav-link">Muammo</a>
            <a href="#solution" class="nav-link">Yechim</a>
            <a href="#how" class="nav-link">Qanday ishlaydi</a>
            <a href="#benefits" class="nav-link">Foydalar</a>
            <a href="#contact" class="nav-link">Aloqa</a>
        </div>

        <div class="nav-cta">
            @auth
                <a href="{{ url('/') }}" class="btn btn-primary">
                    <i class="bi bi-speedometer2"></i> Panelga
                </a>
            @else
                <a href="{{ route('login') }}" class="btn btn-outline">
                    <i class="bi bi-box-arrow-in-right"></i> Kirish
                </a>
                <a href="#contact" class="btn btn-primary">
                    <i class="bi bi-telegram"></i> Bog'lanish
                </a>
            @endauth
        </div>
    </div>
</nav>

{{-- ==================== HERO ==================== --}}
<section class="hero">
    <div class="hero-container">
        <div class="hero-content">
            <div class="hero-badge">
                <i class="bi bi-shield-check"></i>
                <span>Ta'lim uchun xavfsiz platforma</span>
            </div>

            <h1 class="hero-title">
                Yoshlarni <span>yomon illatlardan</span> himoya qilish platformasi
            </h1>

            <p class="hero-subtitle">
                Maktab, kollej va litseylarda o'quvchilarni giyohvandlik, tamaki va jinoyatchilikdan
                <strong>erta aniqlash</strong> va <strong>o'z vaqtida yordam berish</strong> uchun
                ustoz, psixolog va ma'muriyatni bog'lovchi raqamli tizim.
            </p>

            <div class="hero-tagline">
                <i class="bi bi-lightbulb-fill"></i>
                <span>Bugun e'tibor — ertaga sog'lom avlod</span>
            </div>

            <div class="hero-buttons">
                <a href="{{ route('login') }}" class="btn btn-primary btn-lg">
                    <i class="bi bi-box-arrow-in-right"></i> Tizimga kirish
                </a>
                <a href="#solution" class="btn btn-outline btn-lg">
                    <i class="bi bi-play-circle"></i> Batafsil ma'lumot
                </a>
            </div>
        </div>

        <div class="hero-visual">
            <div class="hero-card">
                <div class="hero-card-header">
                    <div class="hero-card-title">
                        <i class="bi bi-clipboard-check"></i>
                        Bugungi holat
                    </div>
                    <span class="badge" style="background:#e3f4f1;color:#2A9D8F;padding:4px 12px;border-radius:20px;font-size:12px;font-weight:700;">
                        Jonli
                    </span>
                </div>

                <div class="hero-stat-row">
                    <div class="hero-stat-left">
                        <div class="hero-stat-icon green"><i class="bi bi-person-check-fill"></i></div>
                        <span>Keldi</span>
                    </div>
                    <div class="hero-stat-value" style="color:#2A9D8F;">142</div>
                </div>

                <div class="hero-stat-row">
                    <div class="hero-stat-left">
                        <div class="hero-stat-icon red"><i class="bi bi-person-x-fill"></i></div>
                        <span>Kelmadi</span>
                    </div>
                    <div class="hero-stat-value" style="color:#E63946;">8</div>
                </div>

                <div class="hero-stat-row">
                    <div class="hero-stat-left">
                        <div class="hero-stat-icon orange"><i class="bi bi-exclamation-triangle-fill"></i></div>
                        <span>Xavf holati</span>
                    </div>
                    <div class="hero-stat-value" style="color:#F4A261;">3</div>
                </div>

                <div class="hero-stat-row">
                    <div class="hero-stat-left">
                        <div class="hero-stat-icon navy"><i class="bi bi-shield-check"></i></div>
                        <span>Hal qilindi</span>
                    </div>
                    <div class="hero-stat-value" style="color:#0A2540;">27</div>
                </div>

                <div class="hero-card-footer">
                    <div class="hero-mini-btn active">📋 Davomat</div>
                    <div class="hero-mini-btn">⚠️ Holatlar</div>
                </div>
            </div>

            <div class="floating-notif">
                <i class="bi bi-bell-fill"></i>
                <div>
                    <div style="font-size:11px;color:#899bbd;">Yangi xabar</div>
                    <div>3 kun kelmagan o'quvchi</div>
                </div>
            </div>

            <div class="floating-success">
                <i class="bi bi-check-lg"></i>
                <div>
                    <div style="font-size:11px;color:#899bbd;">Muvaffaqiyat</div>
                    <div>Ota-onaga xabar yuborildi</div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ==================== MUAMMO ==================== --}}
<section class="problem-section" id="problem">
    <div class="section-container">
        <div class="section-header">
            <span class="section-label">Muammo</span>
            <h2 class="section-title">Bugungi muammo</h2>
            <p class="section-subtitle">
                Yoshlar orasida tamaki, spirtli ichimlik va giyohvandlik tobora erta yoshda boshlanmoqda.
                Muammo ko'pincha kech aniqlanadi.
            </p>
        </div>

        <div class="problem-grid">
            <div class="problem-card">
                <div class="problem-icon"><i class="bi bi-person-dash"></i></div>
                <div class="problem-title">Erta boshlanish</div>
                <div class="problem-text">
                    Yoshlar maktabni bitirmasdan tamaki va narkotiklarga o'tib qolmoqda.
                </div>
            </div>

            <div class="problem-card">
                <div class="problem-icon"><i class="bi bi-eye-slash"></i></div>
                <div class="problem-title">Kech aniqlanadi</div>
                <div class="problem-text">
                    Muammo faqat oqibati og'irlashganda ko'rinadi — profilaktika uchun vaqt yo'qoladi.
                </div>
            </div>

            <div class="problem-card">
                <div class="problem-icon"><i class="bi bi-people"></i></div>
                <div class="problem-title">Aloqa yo'q</div>
                <div class="problem-text">
                    Ustoz, psixolog va ma'muriyat o'rtasida tizimli aloqa va umumiy baza mavjud emas.
                </div>
            </div>

            <div class="problem-card">
                <div class="problem-icon"><i class="bi bi-files"></i></div>
                <div class="problem-title">Tarqoq ma'lumot</div>
                <div class="problem-text">
                    Ma'lumotlar qog'oz, og'zaki yoki turli fayllarda — tizimlashtirilmagan.
                </div>
            </div>

            <div class="problem-card">
                <div class="problem-icon"><i class="bi bi-house-x"></i></div>
                <div class="problem-title">Ota-onalar bilmaydi</div>
                <div class="problem-text">
                    Farzandidagi o'zgarishlardan ota-onalar ko'pincha kech xabardor bo'ladi.
                </div>
            </div>
        </div>

        <div class="problem-conclusion">
            <span>
                <strong>Erta aniqlash — najot.</strong> &nbsp; Kech aniqlash — falokat.
            </span>
        </div>
    </div>
</section>

{{-- ==================== YECHIM ==================== --}}
<section class="solution-section" id="solution">
    <div class="section-container">
        <div class="section-header">
            <span class="section-label">Yechim</span>
            <h2 class="section-title">SOG'LOM AVLOD nima qiladi?</h2>
            <p class="section-subtitle">
                Ustoz, psixolog va ma'muriyatni bir platformada bog'laydigan raqamli tizim.
                Xavf belgilarini erta aniqlash va o'z vaqtida chora ko'rish.
            </p>
        </div>

        <div class="solution-grid">
            <div class="solution-card">
                <div class="solution-icon"><i class="bi bi-laptop"></i></div>
                <div class="solution-title">Ustozlar uchun veb va mobil</div>
                <div class="solution-text">
                    Har bir ustoz o'z sinfidagi o'quvchilarni kuzatadi va xavf belgisini darhol qayd etadi.
                </div>
            </div>

            <div class="solution-card">
                <div class="solution-icon"><i class="bi bi-brain"></i></div>
                <div class="solution-title">Psixologlar uchun bo'lim</div>
                <div class="solution-text">
                    Psixolog tizimga tushgan har bir holatni ko'radi va individual ish olib boradi.
                </div>
            </div>

            <div class="solution-card">
                <div class="solution-icon"><i class="bi bi-bell-fill"></i></div>
                <div class="solution-title">Avtomatik xabarnoma</div>
                <div class="solution-text">
                    Xavf aniqlansa, tizim bir zumda psixolog, sinf rahbari va ota-onaga xabar yuboradi.
                </div>
            </div>

            <div class="solution-card">
                <div class="solution-icon"><i class="bi bi-graph-up"></i></div>
                <div class="solution-title">Profilaktika rejasi</div>
                <div class="solution-text">
                    Har bir holat uchun individual chora-tadbirlar rejasi tuziladi va kuzatiladi.
                </div>
            </div>

            <div class="solution-card">
                <div class="solution-icon"><i class="bi bi-shield-check"></i></div>
                <div class="solution-title">IIO bilan integratsiya</div>
                <div class="solution-text">
                    Yuqori xavfli holatlarda ichki ishlar organlari profilaktika xizmati bilan hamkorlik.
                </div>
            </div>

            <div class="solution-card">
                <div class="solution-icon"><i class="bi bi-bar-chart"></i></div>
                <div class="solution-title">Statistika va hisobot</div>
                <div class="solution-text">
                    Har bir maktab, sinf va o'quvchi bo'yicha to'liq statistik ma'lumotlar.
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ==================== QANDAY ISHLAYDI ==================== --}}
<section class="how-section" id="how">
    <div class="section-container">
        <div class="section-header">
            <span class="section-label">Jarayon</span>
            <h2 class="section-title">Qanday ishlaydi?</h2>
            <p class="section-subtitle">
                4 oddiy qadamda xavfni aniqlash va o'z vaqtida yordam berish
            </p>
        </div>

        <div class="steps-grid">
            <div class="step-card">
                <div class="step-num">1</div>
                <div class="step-title">ANIQLASH</div>
                <div class="step-text">
                    Ustoz yoki psixolog o'quvchida xavf belgisini ko'radi
                </div>
            </div>

            <div class="step-card">
                <div class="step-num">2</div>
                <div class="step-title">KIRITISH</div>
                <div class="step-text">
                    Tizimga ism, sinf va holat turi kiritiladi
                </div>
            </div>

            <div class="step-card">
                <div class="step-num">3</div>
                <div class="step-title">XABAR</div>
                <div class="step-text">
                    Psixolog, sinf rahbari va ota-ona avtomatik xabardor qilinadi
                </div>
            </div>

            <div class="step-card">
                <div class="step-num">4</div>
                <div class="step-title">CHORA</div>
                <div class="step-text">
                    Individual suhbat, profilaktika, kerak bo'lsa — IIO aralashuvi
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ==================== 3 ROL ==================== --}}
<section class="roles-section">
    <div class="section-container">
        <div class="section-header">
            <span class="section-label">Kim ishlatadi</span>
            <h2 class="section-title">Uch xil rol uchun uch xil imkoniyat</h2>
            <p class="section-subtitle">
                Har bir foydalanuvchi o'z roli uchun mo'ljallangan panelda ishlaydi
            </p>
        </div>

        <div class="roles-grid">
            <div class="role-card">
                <div class="role-header teacher">
                    <div class="role-emoji">👨‍🏫</div>
                    <div>
                        <div class="role-name">Ustozlar uchun</div>
                        <div class="role-desc">Sinf rahbari va fan o'qituvchilari</div>
                    </div>
                </div>
                <div class="role-body">
                    <ul class="role-list">
                        <li><i class="bi bi-check-circle-fill"></i> O'quvchida xavf belgisini qayd etish</li>
                        <li><i class="bi bi-check-circle-fill"></i> Davomat kiritish — kelmaganlarni belgilash</li>
                        <li><i class="bi bi-check-circle-fill"></i> Sinf rahbari va psixologga avtomatik xabar</li>
                        <li><i class="bi bi-check-circle-fill"></i> Holatni kuzatib borish</li>
                    </ul>
                </div>
            </div>

            <div class="role-card">
                <div class="role-header psych">
                    <div class="role-emoji">🧠</div>
                    <div>
                        <div class="role-name">Psixologlar uchun</div>
                        <div class="role-desc">Maktab psixologi</div>
                    </div>
                </div>
                <div class="role-body">
                    <ul class="role-list">
                        <li><i class="bi bi-check-circle-fill"></i> Tizimga tushgan har bir holatni ko'rish</li>
                        <li><i class="bi bi-check-circle-fill"></i> O'quvchi bilan individual suhbat</li>
                        <li><i class="bi bi-check-circle-fill"></i> Natijani tizimga yozish</li>
                        <li><i class="bi bi-check-circle-fill"></i> Ota-ona va IIO ga yo'naltirish</li>
                    </ul>
                </div>
            </div>

            <div class="role-card">
                <div class="role-header admin">
                    <div class="role-emoji">🏛</div>
                    <div>
                        <div class="role-name">Ma'muriyat uchun</div>
                        <div class="role-desc">Maktab direktori va o'rinbosarlari</div>
                    </div>
                </div>
                <div class="role-body">
                    <ul class="role-list">
                        <li><i class="bi bi-check-circle-fill"></i> Umumiy statistika va tahlil</li>
                        <li><i class="bi bi-check-circle-fill"></i> Profilaktika tadbirlarini rejalashtirish</li>
                        <li><i class="bi bi-check-circle-fill"></i> IIO bilan hamkorlik hisoboti</li>
                        <li><i class="bi bi-check-circle-fill"></i> Sinf va o'quvchi kesimida tahlil</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ==================== FOYDALAR ==================== --}}
<section class="benefits-section" id="benefits">
    <div class="section-container">
        <div class="section-header">
            <span class="section-label">Foydalar</span>
            <h2 class="section-title">Kim uchun qanday foyda?</h2>
            <p class="section-subtitle">
                Platforma barcha ishtirokchilarga aniq va o'lchanadigan foyda keltiradi
            </p>
        </div>

        <div class="benefits-grid">
            <div class="benefit-card">
                <div class="benefit-icon"><i class="bi bi-person-workspace"></i></div>
                <div class="benefit-title">Ustozlar</div>
                <div class="benefit-text">
                    Muammoni erta aniqlash, hisobot avtomatik — qog'oz ishi yo'q
                </div>
            </div>

            <div class="benefit-card">
                <div class="benefit-icon"><i class="bi bi-heart-pulse"></i></div>
                <div class="benefit-title">Psixologlar</div>
                <div class="benefit-text">
                    Barcha holatlar bir joyda, tarixni kuzatish oson
                </div>
            </div>

            <div class="benefit-card">
                <div class="benefit-icon"><i class="bi bi-building-check"></i></div>
                <div class="benefit-title">Maktab</div>
                <div class="benefit-text">
                    Sog'lom muhit, obro' ortadi, statistika rahbariyatga tayyor
                </div>
            </div>

            <div class="benefit-card">
                <div class="benefit-icon"><i class="bi bi-people-fill"></i></div>
                <div class="benefit-title">Ota-onalar</div>
                <div class="benefit-text">
                    Farzandi holatidan xabardor, yordam qayerdan olishni biladi
                </div>
            </div>

            <div class="benefit-card">
                <div class="benefit-icon"><i class="bi bi-flag-fill"></i></div>
                <div class="benefit-title">Davlat</div>
                <div class="benefit-text">
                    Sog'lom avlod — kelajak, jinoyat va giyohvandlik oldini olinadi
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ==================== MAXFIYLIK ==================== --}}
<section class="privacy-section">
    <div class="section-container">
        <div class="privacy-grid">
            <div>
                <span class="section-label">Xavfsizlik</span>
                <h2 class="section-title">Erta ogohlantirish va maxfiylik</h2>
                <p class="section-subtitle">
                    Tizim AI yordamida xavfni bashorat qiladi, lekin barcha ma'lumotlar qat'iy maxfiy saqlanadi.
                </p>

                <ul class="privacy-features">
                    <li>
                        <i class="bi bi-check-circle-fill"></i>
                        <div>
                            <strong>AI xavf balli</strong> — har bir o'quvchi uchun 0 dan 100 gacha
                        </div>
                    </li>
                    <li>
                        <i class="bi bi-check-circle-fill"></i>
                        <div>
                            <strong>Rangli darajalar</strong> — 🔴 qizil (yuqori) • 🟡 sariq (o'rta) • 🟢 yashil (past)
                        </div>
                    </li>
                    <li>
                        <i class="bi bi-check-circle-fill"></i>
                        <div>
                            <strong>Bashorat</strong> — "Falon sinfda keyingi oyda xavf oshishi mumkin"
                        </div>
                    </li>
                    <li>
                        <i class="bi bi-check-circle-fill"></i>
                        <div>
                            <strong>Shifrlangan ma'lumotlar</strong> — faqat vakolatli shaxslar kiradi
                        </div>
                    </li>
                    <li>
                        <i class="bi bi-check-circle-fill"></i>
                        <div>
                            <strong>"Jazolash" emas — "yordam berish" tamoyili</strong>
                        </div>
                    </li>
                </ul>
            </div>

            <div class="risk-visual">
                <div class="risk-visual-title">Xavf darajalari</div>

                <div class="risk-bar">
                    <div class="risk-bar-header">
                        <span>🔴 Yuqori xavf</span>
                        <span style="color:#ff6b7a;">3 ta</span>
                    </div>
                    <div class="risk-bar-track">
                        <div class="risk-bar-fill red" style="width: 85%;"></div>
                    </div>
                </div>

                <div class="risk-bar">
                    <div class="risk-bar-header">
                        <span>🟡 O'rta xavf</span>
                        <span style="color:#ffbe80;">7 ta</span>
                    </div>
                    <div class="risk-bar-track">
                        <div class="risk-bar-fill yellow" style="width: 55%;"></div>
                    </div>
                </div>

                <div class="risk-bar">
                    <div class="risk-bar-header">
                        <span>🟢 Past xavf</span>
                        <span style="color:#4ecdc4;">142 ta</span>
                    </div>
                    <div class="risk-bar-track">
                        <div class="risk-bar-fill green" style="width: 95%;"></div>
                    </div>
                </div>

                <div style="margin-top:20px;padding-top:20px;border-top:1px solid rgba(255,255,255,.15);">
                    <div style="font-size:12px;color:rgba(255,255,255,.6);margin-bottom:6px;">
                        KEYINGI OY BASHORATI
                    </div>
                    <div style="font-size:14px;font-weight:600;">
                        <i class="bi bi-graph-up-arrow" style="color:#F4A261;"></i>
                        9-A sinfda xavf 12% oshishi mumkin
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ==================== TEXNOLOGIYA ==================== --}}
<section class="tech-section">
    <div class="section-container">
        <div class="section-header">
            <span class="section-label">Texnologiya</span>
            <h2 class="section-title">Zamonaviy va ishonchli texnologiyalar</h2>
            <p class="section-subtitle">
                Tizim eng so'nggi va ishonchli texnologiyalar asosida quriladi
            </p>
        </div>

        <div class="tech-grid">
            <div class="tech-item">
                <div class="tech-item-icon"><i class="bi bi-server"></i></div>
                <div>
                    <div class="tech-name">NestJS / Django</div>
                    <div class="tech-role">Backend</div>
                </div>
            </div>

            <div class="tech-item">
                <div class="tech-item-icon"><i class="bi bi-window"></i></div>
                <div>
                    <div class="tech-name">React</div>
                    <div class="tech-role">Frontend</div>
                </div>
            </div>

            <div class="tech-item">
                <div class="tech-item-icon"><i class="bi bi-phone"></i></div>
                <div>
                    <div class="tech-name">Flutter</div>
                    <div class="tech-role">Mobil ilova</div>
                </div>
            </div>

            <div class="tech-item">
                <div class="tech-item-icon"><i class="bi bi-cpu"></i></div>
                <div>
                    <div class="tech-name">Python</div>
                    <div class="tech-role">AI tahlil</div>
                </div>
            </div>

            <div class="tech-item">
                <div class="tech-item-icon"><i class="bi bi-database"></i></div>
                <div>
                    <div class="tech-name">PostgreSQL</div>
                    <div class="tech-role">Shifrlangan DB</div>
                </div>
            </div>

            <div class="tech-item">
                <div class="tech-item-icon"><i class="bi bi-shield-lock"></i></div>
                <div>
                    <div class="tech-name">2FA + RBAC</div>
                    <div class="tech-role">Xavfsizlik</div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ==================== ROADMAP ==================== --}}
<section class="roadmap-section">
    <div class="section-container">
        <div class="section-header">
            <span class="section-label">Reja</span>
            <h2 class="section-title">Bosqichlar va ustunliklarimiz</h2>
            <p class="section-subtitle">
                Kichik pilotdan respublika miqyosiga — aniq va o'lchanadigan reja
            </p>
        </div>

        <div class="roadmap-grid">
            <div class="roadmap-card">
                <div class="roadmap-stage">1-bosqich</div>
                <div class="roadmap-title">MVP — Sinov</div>
                <div class="roadmap-text">
                    2-3 oy ichida bitta maktabda to'liq ishlaydigan versiya
                </div>
            </div>

            <div class="roadmap-card">
                <div class="roadmap-stage">2-bosqich</div>
                <div class="roadmap-title">Jizzax</div>
                <div class="roadmap-text">
                    4-6 oy ichida Jizzaxdagi 10 ta maktabda joriy etish
                </div>
            </div>

            <div class="roadmap-card">
                <div class="roadmap-stage">3-bosqich</div>
                <div class="roadmap-title">Viloyat</div>
                <div class="roadmap-text">
                    1 yil ichida butun viloyatlar bo'ylab kengaytirish
                </div>
            </div>

            <div class="roadmap-card">
                <div class="roadmap-stage">4-bosqich</div>
                <div class="roadmap-title">Kollej va Litseylar</div>
                <div class="roadmap-text">
                    Kollej, litseylar va IIO bilan to'liq integratsiya
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ==================== CTA + ALOQA FORMASI ==================== --}}
<section class="cta-section" id="contact">
    <div class="cta-content">
        <h2 class="cta-title">Sog'lom avlod — kelajagimiz</h2>
        <p class="cta-subtitle">
            Savollaringiz bormi? Biz bilan bog'laning yoki hamkorlikni boshlaymiz.
        </p>

        <div class="cta-buttons">
            <a href="mailto:info@soglomavlod.uz" class="btn btn-white btn-lg">
                <i class="bi bi-envelope-fill"></i> info@soglomavlod.uz
            </a>
            <a href="tel:+998901234567" class="btn btn-outline btn-lg" style="border-color:#fff;color:#fff;">
                <i class="bi bi-telephone-fill"></i> +998 90 123 45 67
            </a>
        </div>
    </div>
</section>

{{-- ==================== ALOQA FORMASI ==================== --}}
<section style="padding: 80px 24px; background: linear-gradient(180deg, #fff 0%, #f6f9ff 100%);">
    <div class="section-container" style="max-width: 900px;">
        <div class="section-header">
            <span class="section-label">Aloqa</span>
            <h2 class="section-title">Bizga xabar yuboring</h2>
            <p class="section-subtitle">
                Savolingiz, taklifingiz yoki hamkorlik bo'yicha murojaat qiling
            </p>
        </div>

        @if(session('contact_success'))
            <div style="background: #e3f4f1; color: #21867a; padding: 16px 24px; border-radius: 12px; margin-bottom: 24px; border-left: 4px solid #2A9D8F; font-weight: 600; display: flex; align-items: center; gap: 10px; animation: slideUp .4s ease;">
                <i class="bi bi-check-circle-fill" style="font-size: 20px;"></i>
                <span>{{ session('contact_success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div style="background: #fde8ea; color: #842029; padding: 16px 24px; border-radius: 12px; margin-bottom: 24px; border-left: 4px solid #E63946; font-weight: 600;">
                <i class="bi bi-exclamation-triangle-fill"></i>
                @foreach($errors->all() as $e)
                    <div>{{ $e }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('contact.send') }}" 
              style="background: #fff; padding: 40px; border-radius: 20px; box-shadow: 0 8px 28px rgba(10,37,64,.10);">
            @csrf

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; font-weight: 700; font-size: 13px; margin-bottom: 8px; color: #334;">
                        Ismingiz <span style="color: #E63946;">*</span>
                    </label>
                    <input type="text" name="name" required
                           value="{{ old('name') }}"
                           placeholder="Aziz Tursunov"
                           style="width: 100%; padding: 12px 16px; border: 1.5px solid #d8dee6; border-radius: 10px; font-family: inherit; font-size: 14px; transition: all .2s;">
                </div>
                <div>
                    <label style="display: block; font-weight: 700; font-size: 13px; margin-bottom: 8px; color: #334;">
                        Email <span style="color: #E63946;">*</span>
                    </label>
                    <input type="email" name="email" required
                           value="{{ old('email') }}"
                           placeholder="sizning@email.com"
                           style="width: 100%; padding: 12px 16px; border: 1.5px solid #d8dee6; border-radius: 10px; font-family: inherit; font-size: 14px;">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; font-weight: 700; font-size: 13px; margin-bottom: 8px; color: #334;">
                        Telefon raqami
                    </label>
                    <input type="text" name="phone"
                           value="{{ old('phone') }}"
                           placeholder="+998 90 123 45 67"
                           style="width: 100%; padding: 12px 16px; border: 1.5px solid #d8dee6; border-radius: 10px; font-family: inherit; font-size: 14px;">
                </div>
                <div>
                    <label style="display: block; font-weight: 700; font-size: 13px; margin-bottom: 8px; color: #334;">
                        Mavzu
                    </label>
                    <select name="subject" style="width: 100%; padding: 12px 16px; border: 1.5px solid #d8dee6; border-radius: 10px; font-family: inherit; font-size: 14px;">
                        <option value="">— Mavzuni tanlang —</option>
                        <option value="Umumiy savol" {{ old('subject') == 'Umumiy savol' ? 'selected' : '' }}>Umumiy savol</option>
                        <option value="Hamkorlik" {{ old('subject') == 'Hamkorlik' ? 'selected' : '' }}>Hamkorlik</option>
                        <option value="Texnik yordam" {{ old('subject') == 'Texnik yordam' ? 'selected' : '' }}>Texnik yordam</option>
                        <option value="Shikoyat" {{ old('subject') == 'Shikoyat' ? 'selected' : '' }}>Shikoyat</option>
                        <option value="Taklif" {{ old('subject') == 'Taklif' ? 'selected' : '' }}>Taklif</option>
                    </select>
                </div>
            </div>

            <div style="margin-bottom: 24px;">
                <label style="display: block; font-weight: 700; font-size: 13px; margin-bottom: 8px; color: #334;">
                    Xabar matni <span style="color: #E63946;">*</span>
                </label>
                <textarea name="message" required rows="6"
                          placeholder="Xabaringizni batafsil yozing..."
                          style="width: 100%; padding: 12px 16px; border: 1.5px solid #d8dee6; border-radius: 10px; font-family: inherit; font-size: 14px; resize: vertical; min-height: 120px;">{{ old('message') }}</textarea>
            </div>

            <div style="text-align: center;">
                <button type="submit" 
                        class="btn btn-primary btn-lg"
                        style="padding: 14px 40px; font-size: 15px;">
                    <i class="bi bi-send-fill"></i>
                    Xabarni yuborish
                </button>
            </div>

            <p style="text-align: center; color: #6c757d; font-size: 12px; margin-top: 16px; margin-bottom: 0;">
                <i class="bi bi-shield-check"></i>
                Ma'lumotlaringiz maxfiy saqlanadi
            </p>
        </form>
    </div>
</section>


{{-- ==================== FOOTER ==================== --}}
<footer>
    <div class="footer-container">
        <div class="footer-top">
            <div>
                <div class="footer-brand">
                    <div class="footer-brand-icon">🛡️</div>
                    <div>SOG'LOM <span>AVLOD</span></div>
                </div>
                <p class="footer-text">
                    Yoshlarni yomon illatlardan himoya qilish uchun raqamli profilaktika platformasi.
                    Bugun e'tibor — ertaga sog'lom avlod.
                </p>
            </div>

            <div>
                <div class="footer-title">Platforma</div>
                <ul class="footer-links">
                    <li><a href="#problem">Muammo</a></li>
                    <li><a href="#solution">Yechim</a></li>
                    <li><a href="#how">Qanday ishlaydi</a></li>
                    <li><a href="#benefits">Foydalar</a></li>
                </ul>
            </div>

            <div>
                <div class="footer-title">Resurslar</div>
                <ul class="footer-links">
                    <li><a href="#">Hujjatlar</a></li>
                    <li><a href="#">Qo'llanma</a></li>
                    <li><a href="#">Maxfiylik siyosati</a></li>
                    <li><a href="#">Foydalanish shartlari</a></li>
                </ul>
            </div>

            <div>
                <div class="footer-title">Aloqa</div>
                <ul class="footer-links">
                    <li><a href="mailto:info@soglomavlod.uz">info@soglomavlod.uz</a></li>
                    <li><a href="tel:+998901234567">+998 90 123 45 67</a></li>
                    <li><a href="#">Toshkent, O'zbekiston</a></li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            © {{ date('Y') }} SOG'LOM AVLOD. Barcha huquqlar himoyalangan.
            &nbsp;•&nbsp; IIO Ixtisoslashtirilgan litseyi loyihasi
        </div>
    </div>
</footer>

</body>
</html>