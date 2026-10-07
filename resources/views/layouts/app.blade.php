<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', "SOG'LOM AVLOD") — Admin</title>

    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    {{-- Bootstrap 5 CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Google Font --}}
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
            --sidebar-width: 260px;
            --sidebar-collapsed: 70px;
            --topbar-height: 60px;
            --bg: #f6f9ff;
            --card-shadow: 0 0 20px rgba(10,37,64,.06);
            --transition: all .3s cubic-bezier(0.16,1,0.3,1);
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Nunito', sans-serif;
            background: var(--bg);
            color: #012970;
            font-size: 14px;
            margin: 0;
            transition: var(--transition);
        }

        /* ==================== HEADER (TOPBAR) ==================== */
        .header {
            position: fixed;
            top: 0; left: 0; right: 0;
            height: var(--topbar-height);
            background: #fff;
            box-shadow: 0 2px 20px rgba(1,41,112,.08);
            z-index: 997;
            display: flex;
            align-items: center;
            padding: 0 20px;
            transition: var(--transition);
        }

        .header .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            width: var(--sidebar-width);
            font-size: 20px;
            font-weight: 700;
            color: var(--navy);
            text-decoration: none;
            transition: var(--transition);
        }

        .header .logo-icon {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, var(--green), #4ecdc4);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 20px;
            box-shadow: 0 4px 12px rgba(42,157,143,.4);
        }

        .header .logo span { color: var(--green); }

        .header-toggle {
            background: none;
            border: none;
            font-size: 22px;
            color: var(--navy);
            cursor: pointer;
            padding: 6px 10px;
            border-radius: 8px;
            transition: var(--transition);
        }

        .header-toggle:hover {
            background: var(--green-light);
            color: var(--green);
        }

        .header-nav {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-left: auto;
        }

        .header-nav-item {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 10px;
            color: #012970;
            text-decoration: none;
            font-size: 20px;
            transition: var(--transition);
            position: relative;
        }

        .header-nav-item:hover {
            background: var(--green-light);
            color: var(--green);
        }

        .header-nav-item .badge-dot {
            position: absolute;
            top: 6px; right: 6px;
            width: 8px; height: 8px;
            background: var(--red);
            border-radius: 50%;
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.3); opacity: .7; }
        }

        /* User dropdown */
        .user-dropdown {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 6px 12px;
            border-radius: 30px;
            cursor: pointer;
            transition: var(--transition);
            border: none;
            background: none;
        }

        .user-dropdown:hover { background: var(--green-light); }

        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--navy), var(--green));
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 14px;
        }

        .user-name {
            font-size: 14px;
            font-weight: 600;
            color: var(--navy);
            line-height: 1.2;
        }

        .user-role {
            font-size: 11px;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        /* ==================== SIDEBAR ==================== */
        .sidebar {
            position: fixed;
            top: var(--topbar-height);
            left: 0;
            bottom: 0;
            width: var(--sidebar-width);
            background: #fff;
            padding: 20px 0;
            box-shadow: 0 0 20px rgba(1,41,112,.06);
            overflow-y: auto;
            z-index: 996;
            transition: var(--transition);
        }

        .sidebar::-webkit-scrollbar { width: 6px; }
        .sidebar::-webkit-scrollbar-thumb {
            background: #d8dee6;
            border-radius: 3px;
        }

        .sidebar-section {
            padding: 20px 20px 8px;
            font-size: 11px;
            font-weight: 700;
            color: #899bbd;
            text-transform: uppercase;
            letter-spacing: .8px;
        }

        .sidebar-nav {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sidebar-nav li { margin: 2px 12px; }

        .sidebar-nav a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 16px;
            border-radius: 10px;
            color: #012970;
            text-decoration: none;
            font-weight: 500;
            font-size: 14px;
            transition: var(--transition);
            position: relative;
        }

        .sidebar-nav a i {
            font-size: 18px;
            color: #899bbd;
            transition: var(--transition);
            min-width: 20px;
        }

        .sidebar-nav a:hover {
            background: var(--green-light);
            color: var(--green);
            transform: translateX(4px);
        }

        .sidebar-nav a:hover i { color: var(--green); }

        .sidebar-nav a.active {
            background: linear-gradient(135deg, var(--green), var(--green-dark));
            color: #fff;
            box-shadow: 0 6px 16px rgba(42,157,143,.35);
        }

        .sidebar-nav a.active i { color: #fff; }

        .sidebar-nav a.active::before {
            content: '';
            position: absolute;
            left: -12px;
            top: 50%;
            transform: translateY(-50%);
            width: 4px;
            height: 24px;
            background: var(--green);
            border-radius: 0 4px 4px 0;
        }

        .sidebar-nav .badge {
            margin-left: auto;
            font-size: 11px;
            padding: 3px 8px;
            border-radius: 20px;
            background: var(--red);
            color: #fff;
        }

        /* ==================== MAIN ==================== */
        .main {
            margin-left: var(--sidebar-width);
            padding: calc(var(--topbar-height) + 24px) 24px 40px;
            transition: var(--transition);
            min-height: 100vh;
        }

        /* Breadcrumb */
        .page-header {
            margin-bottom: 20px;
        }

        .page-title {
            font-size: 24px;
            font-weight: 700;
            color: var(--navy);
            margin: 0 0 8px;
        }

        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: #6c757d;
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .breadcrumb a {
            color: var(--green);
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
        }

        .breadcrumb a:hover { color: var(--green-dark); }
        .breadcrumb li::after { content: '›'; color: #adb5bd; margin-left: 8px; }
        .breadcrumb li:last-child::after { content: ''; }
        .breadcrumb li:last-child { color: #6c757d; }

        /* Cards */
        .card {
            background: #fff;
            border-radius: 12px;
            border: none;
            box-shadow: var(--card-shadow);
            margin-bottom: 24px;
            transition: var(--transition);
            overflow: hidden;
        }

        .card:hover { box-shadow: 0 4px 30px rgba(1,41,112,.12); }

        .card-body { padding: 20px 24px; }

        .card-title {
            font-size: 18px;
            font-weight: 700;
            color: var(--navy);
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Info stat cards */
        .info-card {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px 24px;
            background: #fff;
            border-radius: 12px;
            box-shadow: var(--card-shadow);
            transition: var(--transition);
            position: relative;
            overflow: hidden;
            height: 100%;
        }

        .info-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 30px rgba(1,41,112,.15);
        }

        .info-card .info-content { flex: 1; }
        .info-card .info-label {
            font-size: 13px;
            color: #899bbd;
            margin-bottom: 4px;
            font-weight: 500;
        }
        .info-card .info-value {
            font-size: 28px;
            font-weight: 800;
            color: var(--navy);
            line-height: 1;
        }
        .info-card .info-icon {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            color: #fff;
            flex-shrink: 0;
        }

        .info-card.green .info-icon  { background: linear-gradient(135deg, #2A9D8F, #4ecdc4); }
        .info-card.red .info-icon    { background: linear-gradient(135deg, #E63946, #ff6b7a); }
        .info-card.orange .info-icon { background: linear-gradient(135deg, #F4A261, #ffbe80); }
        .info-card.navy .info-icon   { background: linear-gradient(135deg, #0A2540, #1a3a5c); }

        /* Tables */
        .table {
            width: 100%;
            margin: 0;
            border-collapse: collapse;
        }

        .table th {
            background: #f6f9ff;
            color: #012970;
            font-weight: 700;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .5px;
            padding: 14px 16px;
            text-align: left;
            border-bottom: 1px solid #eef1f5;
        }

        .table td {
            padding: 14px 16px;
            border-bottom: 1px solid #eef1f5;
            font-size: 14px;
            color: #495057;
            vertical-align: middle;
        }

        .table tbody tr { transition: var(--transition); }
        .table tbody tr:hover { background: #f9fbff; }
        .table tbody tr:last-child td { border-bottom: none; }

        /* Badges */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 5px 12px;
            border-radius: 30px;
            font-size: 12px;
            font-weight: 700;
        }

        .badge.bg-green  { background: #e3f4f1; color: var(--green); }
        .badge.bg-red    { background: #fde8ea; color: var(--red); }
        .badge.bg-yellow { background: #fef3e7; color: #c47a2e; }
        .badge.bg-navy   { background: #e6ecf3; color: var(--navy); }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 9px 18px;
            border-radius: 9px;
            font-weight: 600;
            font-size: 14px;
            text-decoration: none;
            border: none;
            cursor: pointer;
            font-family: inherit;
            transition: var(--transition);
            line-height: 1;
        }

        .btn:hover { transform: translateY(-2px); }

        .btn-primary {
            background: linear-gradient(135deg, var(--green), var(--green-dark));
            color: #fff;
            box-shadow: 0 4px 12px rgba(42,157,143,.28);
        }
        .btn-primary:hover { box-shadow: 0 8px 20px rgba(42,157,143,.4); color: #fff; }

        .btn-navy {
            background: linear-gradient(135deg, var(--navy), var(--navy-light));
            color: #fff;
        }
        .btn-navy:hover { color: #fff; box-shadow: 0 8px 20px rgba(10,37,64,.3); }

        .btn-danger {
            background: linear-gradient(135deg, var(--red), #b62d38);
            color: #fff;
        }
        .btn-danger:hover { color: #fff; }

        .btn-warn {
            background: linear-gradient(135deg, var(--orange), #e08a45);
            color: #fff;
        }
        .btn-warn:hover { color: #fff; }

        .btn-sm { padding: 6px 12px; font-size: 13px; }

        /* Alerts */
        .alert {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 14px 18px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-weight: 500;
            border-left: 4px solid;
            animation: slideDown .4s ease both;
        }

        .alert-success {
            background: #d1f4ea;
            color: #0f5132;
            border-left-color: var(--green);
        }

        .alert-danger {
            background: #fde8ea;
            color: #842029;
            border-left-color: var(--red);
        }

        @keyframes slideDown {
            from { transform: translateY(-10px); opacity: 0; }
            to   { transform: translateY(0); opacity: 1; }
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .main > * { animation: fadeIn .4s ease both; }

        /* Collapsed state */
        body.sidebar-collapsed .sidebar { width: var(--sidebar-collapsed); }
        body.sidebar-collapsed .sidebar-section,
        body.sidebar-collapsed .sidebar-nav a span,
        body.sidebar-collapsed .sidebar-nav a .badge { display: none; }
        body.sidebar-collapsed .sidebar-nav a { justify-content: center; padding: 11px; }
        body.sidebar-collapsed .main { margin-left: var(--sidebar-collapsed); }
        body.sidebar-collapsed .header .logo { width: var(--sidebar-collapsed); }

        /* Mobile */
        @media (max-width: 900px) {
            .sidebar { transform: translateX(-100%); }
            body.sidebar-open .sidebar { transform: translateX(0); }
            .main { margin-left: 0; padding: calc(var(--topbar-height) + 16px) 16px 30px; }
            .header .logo { width: auto; }
            .user-info-block { display: none; }
        }
        body.theme-dark {
        background: #0d1b2a;
        color: #e0e6ed;
    }

    body.theme-dark .header {
        background: #1a3a5c;
        box-shadow: 0 2px 20px rgba(0,0,0,.4);
    }

    body.theme-dark .header .logo {
        color: #e0e6ed;
    }

    body.theme-dark .header-toggle {
        color: #e0e6ed;
    }

    body.theme-dark .sidebar {
        background: #152c44;
        box-shadow: 0 0 20px rgba(0,0,0,.4);
    }

    body.theme-dark .sidebar-section {
        color: #6c7a8c;
    }

    body.theme-dark .sidebar-nav a {
        color: #b8c4d4;
    }

    body.theme-dark .sidebar-nav a:hover {
        background: rgba(42,157,143,.15);
        color: #4ecdc4;
    }

    body.theme-dark .sidebar-nav a.active {
        background: linear-gradient(135deg, #2A9D8F, #21867a);
        color: #fff;
    }

    body.theme-dark .main {
        background: #0d1b2a;
    }

    body.theme-dark .page-title,
    body.theme-dark .card-title,
    body.theme-dark .card h2,
    body.theme-dark .card h3,
    body.theme-dark .card h4,
    body.theme-dark .card h5,
    body.theme-dark .card h6 {
        color: #e0e6ed;
    }

    body.theme-dark .card {
        background: #152c44;
        box-shadow: 0 2px 20px rgba(0,0,0,.3);
    }

    body.theme-dark .info-card {
        background: #152c44;
    }

    body.theme-dark .info-label {
        color: #6c7a8c;
    }

    body.theme-dark .info-value {
        color: #e0e6ed;
    }

    body.theme-dark table th {
        background: #0f2237;
        color: #b8c4d4;
    }

    body.theme-dark table td {
        border-bottom-color: #1a3a5c;
        color: #b8c4d4;
    }

    body.theme-dark table tbody tr:hover {
        background: #1a3a5c;
    }

    body.theme-dark input,
    body.theme-dark select,
    body.theme-dark textarea,
    body.theme-dark .form-control,
    body.theme-dark .form-select {
        background: #0f2237;
        border-color: #1a3a5c;
        color: #e0e6ed;
    }

    body.theme-dark input::placeholder,
    body.theme-dark textarea::placeholder {
        color: #6c7a8c;
    }

    body.theme-dark .form-label {
        color: #b8c4d4;
    }

    body.theme-dark .dropdown-menu {
        background: #152c44;
        border: 1px solid #1a3a5c;
    }

    body.theme-dark .dropdown-item {
        color: #b8c4d4;
    }

    body.theme-dark .dropdown-item:hover {
        background: #1a3a5c;
        color: #4ecdc4;
    }

    body.theme-dark .breadcrumb,
    body.theme-dark .breadcrumb a {
        color: #6c7a8c;
    }

    body.theme-dark .breadcrumb a:hover {
        color: #4ecdc4;
    }

    body.theme-dark .user-name {
        color: #e0e6ed;
    }

    body.theme-dark .badge.bg-navy {
        background: #1a3a5c;
        color: #4ecdc4;
    }

    body.theme-dark .text-muted {
        color: #6c7a8c !important;
    }

    body.theme-dark .border-top,
    body.theme-dark .card-footer {
        border-color: #1a3a5c !important;
        background: #152c44 !important;
    }

    body.theme-dark .modal-content {
        background: #152c44;
        color: #e0e6ed;
    }

    body.theme-dark .modal-header {
        border-bottom-color: #1a3a5c;
    }

    body.theme-dark .modal-footer {
        border-top-color: #1a3a5c;
    }

    body.theme-dark .settings-nav {
        background: transparent;
    }

    body.theme-dark .settings-link {
        color: #b8c4d4;
    }

    body.theme-dark .settings-link:hover {
        background: #1a3a5c;
    }

    body.theme-dark .settings-link.active {
        background: linear-gradient(135deg, #2A9D8F, #21867a);
        color: #fff;
    }

    body.theme-dark .notification-row {
        border-bottom-color: #1a3a5c;
    }

    body.theme-dark .theme-preview {
        filter: brightness(.9);
    }

    body.theme-dark .table {
        color: #b8c4d4;
    }

    body.theme-dark ::-webkit-scrollbar-track {
        background: #0f2237;
    }

    body.theme-dark ::-webkit-scrollbar-thumb {
        background: #1a3a5c;
    }
   /* ==================== TIL TANLASH ==================== */
.lang-dropdown {
    position: relative;
}

.lang-btn {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 8px 14px;
    background: rgba(255,255,255,.1);
    border: 1px solid rgba(255,255,255,.2);
    color: #fff;
    border-radius: 10px;
    cursor: pointer;
    font-family: inherit;
    font-size: 14px;
    font-weight: 600;
    transition: all .2s ease;
    height: 40px;
}

.lang-btn:hover {
    background: rgba(255,255,255,.2);
    transform: translateY(-1px);
}

.lang-btn .flag { font-size: 18px; line-height: 1; }
.lang-btn .short { font-size: 13px; letter-spacing: .5px; }
.lang-btn .chevron { font-size: 12px; opacity: .7; transition: transform .2s ease; }

.lang-dropdown.open .lang-btn .chevron { transform: rotate(180deg); }

.lang-menu {
    position: absolute;
    top: calc(100% + 8px);
    right: 0;
    min-width: 200px;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 12px 32px rgba(10,37,64,.15);
    padding: 6px;
    opacity: 0;
    visibility: hidden;
    transform: translateY(-8px);
    transition: all .2s cubic-bezier(0.16,1,0.3,1);
    z-index: 9999;
}

.lang-dropdown.open .lang-menu {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

.lang-menu-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 14px;
    border-radius: 8px;
    text-decoration: none;
    color: #012970;
    font-size: 14px;
    font-weight: 600;
    transition: all .15s ease;
}

.lang-menu-item:hover {
    background: #e3f4f1;
    color: #2A9D8F;
}

.lang-menu-item.active {
    background: linear-gradient(135deg, #2A9D8F, #21867a);
    color: #fff;
}

.lang-menu-item .flag { font-size: 20px; line-height: 1; }
.lang-menu-item .name { flex: 1; }
.lang-menu-item .check { font-size: 14px; color: #2A9D8F; }
.lang-menu-item.active .check { color: #fff; }

/* Dark theme */
body.theme-dark .lang-menu {
    background: #152c44;
    box-shadow: 0 12px 32px rgba(0,0,0,.4);
}
body.theme-dark .lang-menu-item { color: #b8c4d4; }
body.theme-dark .lang-menu-item:hover { background: #1a3a5c; color: #4ecdc4; }

/* ==================== FOOTER (YANGI) ==================== */
.site-footer {
    margin-left: 0;
    margin-top: 40px;
    background: linear-gradient(135deg, #0A2540 0%, #1a3a5c 100%);
    color: rgba(255,255,255,.85);
    position: relative;
    transition: var(--transition);
    width: 100%;
    box-sizing: border-box;
}

body.sidebar-collapsed .site-footer {
    margin-left: 0;
    width: 100%;
}

@media (max-width: 900px) {
    .site-footer {
        margin-left: 0;
        width: 100%;
        margin-top: 40px;
    }
}

/* To'lqinli ajratgich */
.footer-wave {
    position: absolute;
    top: -59px;
    left: 0;
    width: 100%;
    height: 60px;
    overflow: hidden;
    line-height: 0;
}

.footer-wave svg {
    width: 100%;
    height: 100%;
    display: block;
}

/* Asosiy konteyner */
.footer-inner {
    max-width: 1400px;
    margin: 0 auto;
    padding: 40px 40px 24px;
}

/* ============ ASOSIY QATOR ============ */
.footer-main {
    display: grid;
    grid-template-columns: 1.3fr 1fr 1.2fr;
    gap: 48px;
    padding-bottom: 36px;
    border-bottom: 1px solid rgba(255,255,255,.1);
}

.footer-col {
    min-width: 0;
}

/* ===== USTUN 1: Brand ===== */
.footer-col-brand .footer-brand {
    display: flex;
    align-items: center;
    gap: 14px;
    margin-bottom: 16px;
}

.footer-brand-icon {
    width: 52px;
    height: 52px;
    background: linear-gradient(135deg, #2A9D8F, #4ecdc4);
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    box-shadow: 0 8px 20px rgba(42,157,143,.4);
    flex-shrink: 0;
}

.footer-brand-name {
    font-size: 22px;
    font-weight: 800;
    color: #fff;
    letter-spacing: .5px;
    line-height: 1.2;
}

.footer-brand-name span {
    color: #4ecdc4;
}

.footer-brand-tag {
    font-size: 11px;
    color: rgba(255,255,255,.5);
    text-transform: uppercase;
    letter-spacing: .8px;
    font-weight: 600;
    margin-top: 2px;
}

.footer-description {
    font-size: 13px;
    color: rgba(255,255,255,.65);
    line-height: 1.7;
    margin: 0 0 16px;
}

.footer-tagline {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 16px;
    background: rgba(42,157,143,.15);
    border: 1px solid rgba(42,157,143,.3);
    border-radius: 30px;
    font-size: 13px;
    font-weight: 600;
    color: #4ecdc4;
}

.footer-tagline i {
    font-size: 16px;
    animation: pulse 2s ease-in-out infinite;
}

/* ===== USTUN 2: Havolalar ===== */
.footer-title {
    font-size: 13px;
    font-weight: 800;
    color: #fff;
    text-transform: uppercase;
    letter-spacing: 1.2px;
    margin-bottom: 18px;
    position: relative;
    padding-bottom: 10px;
}

.footer-title::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 32px;
    height: 3px;
    background: linear-gradient(90deg, #2A9D8F, #4ecdc4);
    border-radius: 2px;
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
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: rgba(255,255,255,.7);
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    transition: all .25s ease;
    padding: 4px 0;
}

.footer-links a i {
    font-size: 12px;
    color: #4ecdc4;
    transition: transform .25s ease;
}

.footer-links a:hover {
    color: #4ecdc4;
    padding-left: 6px;
}

.footer-links a:hover i {
    transform: translateX(4px);
}

/* ===== USTUN 3: Yaratuvchi ===== */
.dev-card {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 16px;
    background: rgba(255,255,255,.05);
    border: 1px solid rgba(255,255,255,.1);
    border-radius: 14px;
    margin-bottom: 16px;
    transition: all .25s ease;
}

.dev-card:hover {
    background: rgba(255,255,255,.08);
    border-color: rgba(42,157,143,.4);
    transform: translateY(-2px);
}

.dev-avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: linear-gradient(135deg, #2A9D8F, #4ecdc4);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    flex-shrink: 0;
    box-shadow: 0 6px 16px rgba(42,157,143,.4);
}

.dev-name {
    font-size: 15px;
    font-weight: 800;
    color: #fff;
    line-height: 1.2;
}

.dev-role {
    font-size: 12px;
    color: #4ecdc4;
    font-weight: 600;
    margin-top: 2px;
}

.dev-contacts {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.dev-contact {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 14px;
    background: rgba(255,255,255,.04);
    border: 1px solid rgba(255,255,255,.1);
    border-radius: 12px;
    text-decoration: none;
    transition: all .25s cubic-bezier(0.16,1,0.3,1);
    color: inherit;
    overflow: hidden;
}

.dev-contact:hover {
    transform: translateX(4px);
    border-color: rgba(42,157,143,.5);
    background: rgba(42,157,143,.1);
}

.dev-contact-icon {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    color: #fff;
    flex-shrink: 0;
}

.dev-contact.telegram .dev-contact-icon {
    background: linear-gradient(135deg, #0088cc, #00aced);
    box-shadow: 0 4px 12px rgba(0,136,204,.35);
}

.dev-contact.email .dev-contact-icon {
    background: linear-gradient(135deg, #E63946, #ff6b7a);
    box-shadow: 0 4px 12px rgba(230,57,70,.35);
}

.dev-contact-text {
    flex: 1;
    min-width: 0;
    text-align: left;
}

.dev-contact-label {
    font-size: 10px;
    color: rgba(255,255,255,.5);
    text-transform: uppercase;
    letter-spacing: .8px;
    font-weight: 700;
    line-height: 1;
    margin-bottom: 2px;
}

.dev-contact-value {
    font-size: 13px;
    font-weight: 700;
    color: #fff;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* ============ PASTKI QATOR ============ */
.footer-bottom {
    padding-top: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 12px;
    font-size: 12px;
    color: rgba(255,255,255,.5);
}

.footer-bottom-left {
    display: flex;
    align-items: center;
    gap: 8px;
}

.footer-bottom-icon {
    font-size: 16px;
    color: #4ecdc4;
    font-weight: 800;
}

.footer-bottom-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 14px;
    background: rgba(42,157,143,.12);
    border: 1px solid rgba(42,157,143,.25);
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    color: #4ecdc4;
}

.footer-bottom-badge i {
    font-size: 14px;
}

/* ============ DARK THEME ============ */
body.theme-dark .site-footer {
    background: linear-gradient(135deg, #0a1929 0%, #0d2137 100%);
}

body.theme-dark .footer-wave svg path {
    fill: #0a1929;
}

/* ============ RESPONSIVE ============ */
@media (max-width: 1024px) {
    .footer-main {
        grid-template-columns: 1fr 1fr;
        gap: 32px;
    }
    .footer-col-brand {
        grid-column: span 2;
    }
}

@media (max-width: 900px) {
    .site-footer {
        margin-left: 0;
        width: 100%;
        margin-top: 40px;
    }
    body.sidebar-collapsed .site-footer {
        margin-left: 0;
        width: 100%;
    }
    .footer-inner {
        padding: 32px 20px 20px;
    }
    .footer-wave {
        display: none;
    }
}

@media (max-width: 640px) {
    .footer-main {
        grid-template-columns: 1fr;
        gap: 28px;
        padding-bottom: 24px;
    }
    .footer-col-brand {
        grid-column: span 1;
    }
    .footer-brand-icon {
        width: 44px;
        height: 44px;
        font-size: 24px;
    }
    .footer-brand-name {
        font-size: 18px;
    }
    .footer-bottom {
        flex-direction: column;
        text-align: center;
        gap: 10px;
    }
    .dev-contact-value {
        font-size: 12px;
    }
}

    </style>
</head>
@php
    $theme = session('theme') ?? (Auth::check() ? Auth::user()->theme : 'light');
@endphp
<body class="theme-{{ $theme ?? 'light' }}">

{{-- ==================== HEADER ==================== --}}
<header class="header">
    <button class="header-toggle" onclick="toggleSidebar()">
        <i class="bi bi-list"></i>
    </button>

    <a href="{{ route('dashboard') }}" class="logo">
        <div class="logo-icon">🛡️</div>
        <div>SOG'LOM <span>AVLOD</span></div>
    </a>
     <button type="button" class="header-nav-item" onclick="toggleTheme()" title="Mavzuni o'zgartirish">
    <i class="bi bi-moon-stars"></i>
</button>
@php
    $currentLocale = app()->getLocale();
    $languages = config('languages.available', [
        'uz' => ['name' => "O'zbekcha", 'flag' => '🇺🇿', 'short' => 'UZ'],
        'ru' => ['name' => 'Русский',   'flag' => '🇷🇺', 'short' => 'RU'],
        'en' => ['name' => 'English',   'flag' => '🇬🇧', 'short' => 'EN'],
    ]);
    $currentLang = $languages[$currentLocale] ?? $languages['uz'];
@endphp

<div class="lang-dropdown" id="langDropdown">
    <button type="button" class="lang-btn" onclick="toggleLangMenu(event)">
        <span class="flag">{{ $currentLang['flag'] }}</span>
        <span class="short">{{ $currentLang['short'] }}</span>
        <i class="bi bi-chevron-down chevron"></i>
    </button>

    <div class="lang-menu">
        @foreach($languages as $code => $lang)
            <a href="{{ route('language.switch', $code) }}"
               class="lang-menu-item {{ $currentLocale == $code ? 'active' : '' }}">
                <span class="flag">{{ $lang['flag'] }}</span>
                <span class="name">{{ $lang['name'] }}</span>
                @if($currentLocale == $code)
                    <i class="bi bi-check-lg check"></i>
                @endif
            </a>
        @endforeach
    </div>
</div>


    <nav class="header-nav">
        <a href="{{ route('attendance.alerts') ?? '#' }}" class="header-nav-item" title="Ogohlantirishlar">
            <i class="bi bi-bell"></i>
            @if(isset($alertsCount) && $alertsCount > 0)
                <span class="badge-dot"></span>
            @endif
        </a>
        <a href="{{ route('cases.index') ?? '#' }}" class="header-nav-item" title="Holatlar">
            <i class="bi bi-exclamation-triangle"></i>
        </a>

       <div class="dropdown">
    <button class="user-dropdown" data-bs-toggle="dropdown">
        <div class="user-avatar">
            {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
        </div>
        <div class="user-info-block" style="text-align:left;">
            <div class="user-name">{{ Auth::user()->name ?? 'Mehmon' }}</div>
            <div class="user-role">{{ Auth::user()->role ?? '' }}</div>
        </div>
        <i class="bi bi-chevron-down ms-1"></i>
    </button>
    
    <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="border-radius:10px;">
        <li>
            <a class="dropdown-item" href="{{ route('profile') }}">
                <i class="bi bi-person me-2"></i> Profil
            </a>
        </li>
        <li>
            <a class="dropdown-item" href="{{ route('profile.edit') }}">
                <i class="bi bi-gear me-2"></i> Sozlamalar
            </a>
        </li>
        <li><hr class="dropdown-divider"></li>
        <li>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="dropdown-item text-danger">
                    <i class="bi bi-box-arrow-right me-2"></i> Chiqish
                </button>
            </form>
        </li>
    </ul>
</div>
    </nav>
</header>

{{-- ==================== SIDEBAR ==================== --}}
<aside class="sidebar">
    {{-- ASOSIY --}}
    <div class="sidebar-section">{{ __('general.main') }}</div>
    <ul class="sidebar-nav">
        <li>
            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i>
                <span>{{ __('general.dashboard') }}</span>
            </a>
        </li>
        <li>
            <a href="{{ route('attendance.today') }}" class="{{ request()->routeIs('attendance.today') ? 'active' : '' }}">
                <i class="bi bi-clipboard-check"></i>
                <span>{{ __('general.attendance') }}</span>
            </a>
        </li>
        <li>
            <a href="{{ route('attendance.alerts') }}" class="{{ request()->routeIs('attendance.alerts') ? 'active' : '' }}">
                <i class="bi bi-exclamation-octagon"></i>
                <span>{{ __('general.alerts') }}</span>
            </a>
        </li>
    </ul>

    {{-- PROFILAKTIKA --}}
    <div class="sidebar-section">{{ __('general.prevention') }}</div>
    <ul class="sidebar-nav">
        <li>
            <a href="{{ route('cases.index') }}" class="{{ request()->routeIs('cases.*') ? 'active' : '' }}">
                <i class="bi bi-shield-exclamation"></i>
                <span>{{ __('general.risk_cases') }}</span>
            </a>
        </li>
        <li>
            <a href="{{ route('cases.create') }}" class="{{ request()->routeIs('cases.create') ? 'active' : '' }}">
                <i class="bi bi-plus-circle"></i>
                <span>{{ __('general.new_case') }}</span>
            </a>
        </li>
    </ul>

    {{-- MA'LUMOTLAR --}}
    <div class="sidebar-section">{{ __('general.data') }}</div>
    <ul class="sidebar-nav">
        <li>
            <a href="{{ route('students.index') }}" class="{{ request()->routeIs('students.*') ? 'active' : '' }}">
                <i class="bi bi-people"></i>
                <span>{{ __('general.students') }}</span>
            </a>
        </li>
        <li>
            <a href="{{ Route::has('schools.index') ? route('schools.index') : '#' }}">
                <i class="bi bi-building"></i>
                <span>{{ __('general.schools') }}</span>
            </a>
        </li>
    </ul>

    {{-- ALOQA --}}
    <div class="sidebar-section">{{ __('general.contact') }}</div>
    <ul class="sidebar-nav">
        <li>
            <a href="{{ route('admin.messages.index') }}" class="{{ request()->routeIs('admin.messages.*') ? 'active' : '' }}">
                <i class="bi bi-envelope"></i>
                <span>{{ __('general.messages') }}</span>
                @php $newCount = \App\Message::where('status', 'new')->count(); @endphp
                @if($newCount > 0)
                    <span class="badge bg-red">{{ $newCount }}</span>
                @endif
            </a>
        </li>
    </ul>

    {{-- HISOBOT --}}
    <div class="sidebar-section">{{ __('general.report') }}</div>
    <ul class="sidebar-nav">
        <li>
            <a href="{{ route('statistics') }}">
                <i class="bi bi-graph-up-arrow"></i>
                <span>{{ __('general.statistics') }}</span>
            </a>
        </li>
        <li>
            <a href="{{ route('settings.index') }}" class="{{ request()->routeIs('settings.*') ? 'active' : '' }}">
                <i class="bi bi-gear"></i>
                <span>{{ __('general.settings') }}</span>
            </a>
        </li>
    </ul>
</aside>
<aside class="sidebar">
    @if(Auth::user()->isParent())
        {{-- ==================== OTA-ONA UCHUN ==================== --}}
        <div class="sidebar-section">Mening</div>
        <ul class="sidebar-nav">
            <li>
                <a href="{{ route('parent.dashboard') }}" class="{{ request()->routeIs('parent.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-house-heart"></i>
                    <span>Farzandlarim</span>
                </a>
            </li>
            <li>
                <a href="{{ route('parent.notifications') }}" class="{{ request()->routeIs('parent.notifications') ? 'active' : '' }}">
                    <i class="bi bi-bell"></i>
                    <span>Xabarnomalar</span>
                </a>
            </li>
        </ul>

        <div class="sidebar-section">Profil</div>
        <ul class="sidebar-nav">
            <li>
                <a href="{{ route('parent.profile') }}" class="{{ request()->routeIs('parent.profile') ? 'active' : '' }}">
                    <i class="bi bi-person"></i>
                    <span>Profilim</span>
                </a>
            </li>
        </ul>
    @else
        {{-- ==================== USTOZ/PSIXOLOG/ADMIN UCHUN (eski sidebar) ==================== --}}
        <div class="sidebar-section">Asosiy</div>
        <ul class="sidebar-nav">
            <li>
                <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i>
                    <span>Boshqaruv paneli</span>
                </a>
            </li>
            <li>
                <a href="{{ route('attendance.today') }}" class="{{ request()->routeIs('attendance.today') ? 'active' : '' }}">
                    <i class="bi bi-clipboard-check"></i>
                    <span>Davomat</span>
                </a>
            </li>
            <li>
                <a href="{{ route('attendance.alerts') }}" class="{{ request()->routeIs('attendance.alerts') ? 'active' : '' }}">
                    <i class="bi bi-exclamation-octagon"></i>
                    <span>Ogohlantirishlar</span>
                </a>
            </li>
        </ul>

        <div class="sidebar-section">Profilaktika</div>
        <ul class="sidebar-nav">
            <li>
                <a href="{{ route('cases.index') }}" class="{{ request()->routeIs('cases.*') ? 'active' : '' }}">
                    <i class="bi bi-shield-exclamation"></i>
                    <span>Xavf holatlari</span>
                </a>
            </li>
            <li>
                <a href="{{ route('cases.create') }}" class="{{ request()->routeIs('cases.create') ? 'active' : '' }}">
                    <i class="bi bi-plus-circle"></i>
                    <span>Yangi holat</span>
                </a>
            </li>
        </ul>

        <div class="sidebar-section">Ma'lumotlar</div>
        <ul class="sidebar-nav">
            <li>
                <a href="{{ route('students.index') }}" class="{{ request()->routeIs('students.*') ? 'active' : '' }}">
                    <i class="bi bi-people"></i>
                    <span>O'quvchilar</span>
                </a>
            </li>
            <li>
                <a href="{{ Route::has('schools.index') ? route('schools.index') : '#' }}">
                    <i class="bi bi-building"></i>
                    <span>Maktablar</span>
                </a>
            </li>
        </ul>
        <div class="sidebar-section">Aloqa</div>
<ul class="sidebar-nav">
    <li>
        <a href="{{ route('admin.messages.index') }}" class="{{ request()->routeIs('admin.messages.*') ? 'active' : '' }}">
            <i class="bi bi-envelope"></i>
            <span>Xabarlar</span>
            @php
                $newMessagesCount = \App\Message::where('status', 'new')->count();
            @endphp
            @if($newMessagesCount > 0)
                <span class="badge bg-red">{{ $newMessagesCount }}</span>
            @endif
        </a>
    </li>
</ul>

        <div class="sidebar-section">Hisobot</div>
        <ul class="sidebar-nav">
            <li>
                <a href="{{ route('statistics') }}">
                    <i class="bi bi-graph-up-arrow"></i>
                    <span>Statistika</span>
                </a>
            </li>
            <li>
                <a href="{{ route('settings.index') }}" class="{{ request()->routeIs('settings.*') ? 'active' : '' }}">
                    <i class="bi bi-gear"></i>
                    <span>Sozlamalar</span>
                </a>
            </li>
        </ul>
    @endif
</aside>

{{-- ==================== MAIN ==================== --}}
<main class="main">
    {{-- Breadcrumb va sahifa sarlavhasi --}}
    <div class="page-header">
        <h1 class="page-title">@yield('page-title', 'Boshqaruv paneli')</h1>
        <ul class="breadcrumb">
            <li><a href="{{ route('dashboard') }}"><i class="bi bi-house-door"></i> Asosiy</a></li>
            <li>@yield('page-title', 'Panel')</li>
        </ul>
    </div>

    {{-- Alert xabarlari --}}
    @if(session('success'))
        <div class="alert alert-success">
            <i class="bi bi-check-circle-fill"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            <i class="bi bi-x-circle-fill"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    
    @yield('content')
{{-- ==================== FOOTER ==================== --}}
<footer class="site-footer">
    {{-- To'lqinli ajratgich --}}
    <div class="footer-wave">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 60" preserveAspectRatio="none">
            <path d="M0,30 C240,60 480,0 720,30 C960,60 1200,0 1440,30 L1440,60 L0,60 Z" 
                  fill="#0A2540"/>
        </svg>
    </div>

    <div class="footer-inner">
        {{-- ============ ASOSIY QATOR ============ --}}
        <div class="footer-main">
            {{-- ===== USTUN 1: Brand ===== --}}
            <div class="footer-col footer-col-brand">
                <div class="footer-brand">
                    <div class="footer-brand-icon">🛡️</div>
                    <div>
                        <div class="footer-brand-name">SOG'LOM <span>AVLOD</span></div>
                        <div class="footer-brand-tag">Yoshlarni himoya qilish platformasi</div>
                    </div>
                </div>
                <p class="footer-description">
                    Maktab, kollej va litseylarda o'quvchilarni giyohvandlik, tamaki va jinoyatchilikdan 
                    erta aniqlash va o'z vaqtida yordam berish uchun raqamli tizim.
                </p>
                <div class="footer-tagline">
                    <i class="bi bi-lightbulb-fill"></i>
                    <span>Bugun e'tibor — ertaga sog'lom avlod</span>
                </div>
            </div>

            {{-- ===== USTUN 2: Tezkor havolalar ===== --}}
            <div class="footer-col footer-col-links">
                <h6 class="footer-title">Tezkor havolalar</h6>
                <ul class="footer-links">
                    <li>
                        <a href="{{ route('dashboard') }}">
                            <i class="bi bi-chevron-right"></i>
                            <span>Boshqaruv paneli</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('students.index') }}">
                            <i class="bi bi-chevron-right"></i>
                            <span>O'quvchilar</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('attendance.today') }}">
                            <i class="bi bi-chevron-right"></i>
                            <span>Davomat</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('cases.index') }}">
                            <i class="bi bi-chevron-right"></i>
                            <span>Xavf holatlari</span>
                        </a>
                    </li>
                </ul>
            </div>

            {{-- ===== USTUN 3: Yaratuvchi ===== --}}
            <div class="footer-col footer-col-dev">
                <h6 class="footer-title">Sayt yaratuvchisi</h6>
                <div class="dev-card">
                    <div class="dev-avatar">
                        <i class="bi bi-person-fill"></i>
                    </div>
                    <div class="dev-info">
                        <div class="dev-name">Zoirov Odiljon</div>
                        <div class="dev-role">Full-Stack Developer</div>
                    </div>
                </div>

                <div class="dev-contacts">
                    <a href="https://t.me/CodeU10" target="_blank" class="dev-contact telegram">
                        <div class="dev-contact-icon">
                            <i class="bi bi-telegram"></i>
                        </div>
                        <div class="dev-contact-text">
                            <div class="dev-contact-label">Telegram</div>
                            <div class="dev-contact-value">@CodeU10</div>
                        </div>
                    </a>

                    <a href="mailto:dasturchiodil01@gmail.com" class="dev-contact email">
                        <div class="dev-contact-icon">
                            <i class="bi bi-envelope-fill"></i>
                        </div>
                        <div class="dev-contact-text">
                            <div class="dev-contact-label">Email</div>
                            <div class="dev-contact-value">dasturchiodil01@gmail.com</div>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        {{-- ============ PASTKI QATOR ============ --}}
        <div class="footer-bottom">
            <div class="footer-bottom-left">
                <span class="footer-bottom-icon">©</span>
                <span>{{ date('Y') }} SOG'LOM AVLOD. Barcha huquqlar himoyalangan.</span>
            </div>

            <div class="footer-bottom-right">
                <span class="footer-bottom-badge">
                    <i class="bi bi-shield-check"></i>
                    IIO Ixtisoslashtirilgan litseyi loyihasi
                </span>
            </div>
        </div>
    </div>
</footer>
</main>




{{-- Bootstrap JS --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Sidebar toggle
    function toggleSidebar() {
        if (window.innerWidth <= 900) {
            document.body.classList.toggle('sidebar-open');
        } else {
            document.body.classList.toggle('sidebar-collapsed');
        }
    }

    // Alertlarni 4 sekunddan keyin avtomatik yopish
    document.querySelectorAll('.alert').forEach(function(alert) {
        setTimeout(() => {
            alert.style.transition = 'all .4s ease';
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-10px)';
            setTimeout(() => alert.remove(), 400);
        }, 4000);
    });
   
    function toggleTheme() {
        const body = document.body;
        const isDark = body.classList.contains('theme-dark');
        const newTheme = isDark ? 'light' : 'dark';
        
        body.classList.remove('theme-light', 'theme-dark');
        body.classList.add('theme-' + newTheme);
        
        // Sessiyaga yozish
        fetch('{{ route("settings.system") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ _method: 'PUT', theme: newTheme, language: 'uz', timezone: 'Asia/Tashkent' })
        });
    }


    // Til menyusini ochish/yopish
    function toggleLangMenu(event) {
        event.stopPropagation();
        const dropdown = document.getElementById('langDropdown');
        if (dropdown) dropdown.classList.toggle('open');
    }

    document.addEventListener('click', function(e) {
        const dropdown = document.getElementById('langDropdown');
        if (dropdown && !dropdown.contains(e.target)) {
            dropdown.classList.remove('open');
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const dropdown = document.getElementById('langDropdown');
            if (dropdown) dropdown.classList.remove('open');
        }
    });
</script>

@stack('scripts')
</body>
</html>