@extends('layouts.app')

@section('title', 'Sozlamalar')
@section('page-title', 'Sozlamalar')

@section('content')

<div class="row g-3">
    {{-- ==================== CHAP: MENYU ==================== --}}
    <div class="col-lg-3">
        <div class="card">
            <div class="card-body p-0">
                <div class="settings-nav">
                    <a href="#profile" class="settings-link active" data-tab="profile">
                        <i class="bi bi-person-circle"></i>
                        <span>Profil</span>
                    </a>
                    <a href="#security" class="settings-link" data-tab="security">
                        <i class="bi bi-shield-lock"></i>
                        <span>Xavfsizlik</span>
                    </a>
                    <a href="#notifications" class="settings-link" data-tab="notifications">
                        <i class="bi bi-bell"></i>
                        <span>Bildirishnomalar</span>
                    </a>
                    <a href="#system" class="settings-link" data-tab="system">
                        <i class="bi bi-gear"></i>
                        <span>Tizim</span>
                    </a>
                </div>
            </div>
        </div>

        {{-- Profil ko'rinishi --}}
        <div class="card">
            <div class="card-body text-center">
                @if($user->avatar)
                    <img src="{{ asset($user->avatar) }}" 
                         style="width:80px;height:80px;border-radius:50%;object-fit:cover;margin:0 auto 12px;display:block;border:3px solid var(--green);">
                @else
                    <div class="profile-avatar-mini">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                @endif
                <h6 class="fw-bold mb-1">{{ $user->name }}</h6>
                <p class="text-muted small mb-2">{{ $user->email }}</p>
                <span class="badge bg-green">{{ $user->role }}</span>
            </div>
        </div>
    </div>

    {{-- ==================== O'NG: KONTENT ==================== --}}
    <div class="col-lg-9">
        
        {{-- ========== PROFIL TAB ========== --}}
        <div class="settings-tab active" id="tab-profile">
            <form method="POST" action="{{ route('settings.profile') }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="card">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3">
                            <i class="bi bi-person-circle text-success"></i>
                            Profil ma'lumotlari
                        </h5>

                        @if($errors->any())
                            <div class="alert alert-danger">
                                <i class="bi bi-exclamation-circle"></i>
                                @foreach($errors->all() as $e) <div>{{ $e }}</div> @endforeach
                            </div>
                        @endif

                        <div class="row g-3">
                            {{-- Avatar --}}
                            <div class="col-12">
                                <label class="form-label">Profil rasmi</label>
                                <div class="d-flex align-items-center gap-3">
                                    @if($user->avatar)
                                        <img src="{{ asset($user->avatar) }}" 
                                             id="avatarPreview"
                                             style="width:72px;height:72px;border-radius:50%;object-fit:cover;border:3px solid var(--green);">
                                    @else
                                        <div class="profile-avatar-mini" id="avatarPreview">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <input type="file" name="avatar" id="avatarInput" 
                                               class="form-control" accept="image/*">
                                        <small class="text-muted">JPG, PNG. Maks: 2MB</small>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">To'liq ism <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" required
                                       value="{{ old('name', $user->name) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" required
                                       value="{{ old('email', $user->email) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Telefon raqami</label>
                                <input type="text" name="phone" class="form-control"
                                       placeholder="+998 90 123 45 67"
                                       value="{{ old('phone', $user->phone) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Lavozim</label>
                                <input type="text" name="position" class="form-control"
                                       placeholder="Masalan: Sinf rahbari"
                                       value="{{ old('position', $user->position) }}">
                            </div>
                        </div>
                    </div>

                    <div class="card-footer bg-white border-top d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Saqlash
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- ========== XAVFSIZLIK TAB ========== --}}
        <div class="settings-tab" id="tab-security">
            <form method="POST" action="{{ route('settings.password') }}">
                @csrf
                @method('PUT')

                <div class="card">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3">
                            <i class="bi bi-shield-lock text-success"></i>
                            Parolni o'zgartirish
                        </h5>

                        @if(session('success'))
                            <div class="alert alert-success">
                                <i class="bi bi-check-circle"></i> {{ session('success') }}
                            </div>
                        @endif

                        @if($errors->has('current_password'))
                            <div class="alert alert-danger">
                                <i class="bi bi-exclamation-circle"></i>
                                {{ $errors->first('current_password') }}
                            </div>
                        @endif

                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Hozirgi parol <span class="text-danger">*</span></label>
                                <input type="password" name="current_password" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Yangi parol <span class="text-danger">*</span></label>
                                <input type="password" name="password" class="form-control" required minlength="8">
                                <small class="text-muted">Kamida 8 belgi</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Yangi parolni tasdiqlash <span class="text-danger">*</span></label>
                                <input type="password" name="password_confirmation" class="form-control" required minlength="8">
                            </div>
                        </div>
                    </div>

                    <div class="card-footer bg-white border-top d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-key"></i> Parolni yangilash
                        </button>
                    </div>
                </div>
            </form>

            {{-- 2FA --}}
            <div class="card">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">
                        <i class="bi bi-phone text-success"></i>
                        Ikki bosqichli tekshiruv (2FA)
                    </h5>
                    <div class="d-flex justify-content-between align-items-center p-3 rounded" style="background:#f5f7fa;">
                        <div>
                            <strong>SMS orqali 2FA</strong>
                            <p class="text-muted small mb-0">Har safar kirganda telefon raqamga kod keladi</p>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" style="width:42px;height:22px;">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ========== BILDIRISHNOMALAR TAB ========== --}}
        <div class="settings-tab" id="tab-notifications">
            <form method="POST" action="{{ route('settings.notifications') }}">
                @csrf
                @method('PUT')

                <div class="card">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3">
                            <i class="bi bi-bell text-success"></i>
                            Bildirishnomalar
                        </h5>

                        <div class="notification-row">
                            <div>
                                <strong>📧 Email xabarnomalar</strong>
                                <p class="text-muted small mb-0">Muhim voqealar haqida email olish</p>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="notify_email" value="1"
                                       {{ $user->notify_email ? 'checked' : '' }} style="width:42px;height:22px;">
                            </div>
                        </div>

                        <div class="notification-row">
                            <div>
                                <strong>📱 SMS xabarnomalar</strong>
                                <p class="text-muted small mb-0">Telefon raqamga SMS olish</p>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="notify_sms" value="1"
                                       {{ $user->notify_sms ? 'checked' : '' }} style="width:42px;height:22px;">
                            </div>
                        </div>

                        <div class="notification-row">
                            <div>
                                <strong>⚠️ Xavf holatlari</strong>
                                <p class="text-muted small mb-0">Yangi xavf holati yaratilganda xabar berish</p>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="notify_risk_cases" value="1"
                                       {{ $user->notify_risk_cases ? 'checked' : '' }} style="width:42px;height:22px;">
                            </div>
                        </div>

                        <div class="notification-row">
                            <div>
                                <strong>📋 Davomat</strong>
                                <p class="text-muted small mb-0">3+ kun kelmagan o'quvchilar haqida xabar</p>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="notify_attendance" value="1"
                                       {{ $user->notify_attendance ? 'checked' : '' }} style="width:42px;height:22px;">
                            </div>
                        </div>
                    </div>

                    <div class="card-footer bg-white border-top d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Saqlash
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- ========== TIZIM TAB ========== --}}
        <div class="settings-tab" id="tab-system">
            <form method="POST" action="{{ route('settings.system') }}">
                @csrf
                @method('PUT')

                <div class="card">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3">
                            <i class="bi bi-gear text-success"></i>
                            Tizim sozlamalari
                        </h5>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Til</label>
                                <select name="language" class="form-select">
                                    <option value="uz" {{ $user->language == 'uz' ? 'selected' : '' }}>🇺🇿 O'zbekcha</option>
                                    <option value="ru" {{ $user->language == 'ru' ? 'selected' : '' }}>🇷🇺 Русский</option>
                                    <option value="en" {{ $user->language == 'en' ? 'selected' : '' }}>🇬🇧 English</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Vaqt zonasi</label>
                                <select name="timezone" class="form-select">
                                    <option value="Asia/Tashkent" {{ $user->timezone == 'Asia/Tashkent' ? 'selected' : '' }}>
                                        Asia/Tashkent (UTC+5)
                                    </option>
                                    <option value="Asia/Samarkand" {{ $user->timezone == 'Asia/Samarkand' ? 'selected' : '' }}>
                                        Asia/Samarkand (UTC+5)
                                    </option>
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Mavzu (theme)</label>
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <label class="theme-card {{ $user->theme == 'light' ? 'active' : '' }}">
                                            <input type="radio" name="theme" value="light" 
                                                   {{ $user->theme == 'light' ? 'checked' : '' }}>
                                            <div class="theme-preview" style="background:#fff;border:2px solid #e3e8ef;">
                                                <div style="height:12px;background:#0A2540;border-radius:4px 4px 0 0;"></div>
                                                <div style="padding:6px;">
                                                    <div style="height:6px;background:#e3f4f1;border-radius:3px;margin-bottom:3px;"></div>
                                                    <div style="height:6px;background:#e3f4f1;border-radius:3px;width:70%;"></div>
                                                </div>
                                            </div>
                                            <div class="theme-label">
                                                <i class="bi bi-sun"></i> Yorqin
                                            </div>
                                        </label>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="theme-card {{ $user->theme == 'dark' ? 'active' : '' }}">
                                            <input type="radio" name="theme" value="dark"
                                                   {{ $user->theme == 'dark' ? 'checked' : '' }}>
                                            <div class="theme-preview" style="background:#0A2540;border:2px solid #1a3a5c;">
                                                <div style="height:12px;background:#1a3a5c;border-radius:4px 4px 0 0;"></div>
                                                <div style="padding:6px;">
                                                    <div style="height:6px;background:#2A9D8F;border-radius:3px;margin-bottom:3px;"></div>
                                                    <div style="height:6px;background:#2A9D8F;border-radius:3px;width:70%;"></div>
                                                </div>
                                            </div>
                                            <div class="theme-label">
                                                <i class="bi bi-moon-stars"></i> Tungi
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer bg-white border-top d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Saqlash
                        </button>
                    </div>
                </div>
            </form>

            {{-- Xavfli hudud --}}
            <div class="card border-danger">
                <div class="card-body">
                    <h5 class="fw-bold mb-2 text-danger">
                        <i class="bi bi-exclamation-triangle"></i>
                        Xavfli hudud
                    </h5>
                    <p class="text-muted small mb-3">
                        Hisobdan chiqish barcha qurilmalarda sessiyani tugatadi.
                    </p>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-box-arrow-right"></i> Hisobdan chiqish
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection

@push('styles')
<style>
    /* Settings navigatsiya */
    .settings-nav {
        display: flex;
        flex-direction: column;
        padding: 8px;
    }
    .settings-link {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 16px;
        border-radius: 10px;
        color: #012970;
        text-decoration: none;
        font-weight: 500;
        font-size: 14px;
        transition: all .2s ease;
        cursor: pointer;
    }
    .settings-link i {
        font-size: 18px;
        color: #899bbd;
        transition: all .2s ease;
    }
    .settings-link:hover {
        background: #f5f7fa;
        color: var(--green);
    }
    .settings-link:hover i { color: var(--green); }
    .settings-link.active {
        background: linear-gradient(135deg, var(--green), var(--green-dark));
        color: #fff;
        box-shadow: 0 6px 16px rgba(42,157,143,.3);
    }
    .settings-link.active i { color: #fff; }

    /* Tab ko'rinishi */
    .settings-tab {
        display: none;
        animation: fadeIn .3s ease;
    }
    .settings-tab.active { display: block; }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(8px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    /* Profil avatar mini */
    .profile-avatar-mini {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--green), #4ecdc4);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 32px;
        margin: 0 auto;
        box-shadow: 0 6px 18px rgba(42,157,143,.3);
    }

    /* Bildirishnoma qatori */
    .notification-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px;
        border-bottom: 1px dashed #eef1f5;
    }
    .notification-row:last-child { border-bottom: none; }

    .form-check-input:checked {
        background-color: var(--green);
        border-color: var(--green);
    }
    .form-check-input:focus {
        border-color: var(--green);
        box-shadow: 0 0 0 4px rgba(42,157,143,.15);
    }

    /* Theme kartalar */
    .theme-card {
        display: block;
        cursor: pointer;
        border: 2px solid #e3e8ef;
        border-radius: 12px;
        padding: 12px;
        transition: all .2s ease;
        text-align: center;
    }
    .theme-card input { display: none; }
    .theme-card:hover {
        border-color: #b8c4d4;
        transform: translateY(-2px);
    }
    .theme-card.active {
        border-color: var(--green);
        background: #f5fbfa;
        box-shadow: 0 4px 12px rgba(42,157,143,.15);
    }
    .theme-preview {
        border-radius: 8px;
        overflow: hidden;
        margin-bottom: 10px;
        height: 70px;
    }
    .theme-label {
        font-size: 13px;
        font-weight: 700;
        color: var(--navy);
    }

    .form-label {
        font-weight: 600;
        font-size: 13px;
        color: #334;
    }
    .form-control, .form-select {
        padding: 10px 14px;
        border-radius: 9px;
        border: 1.5px solid #d8dee6;
        font-size: 14px;
    }
    .form-control:focus, .form-select:focus {
        border-color: var(--green);
        box-shadow: 0 0 0 4px rgba(42,157,143,.12);
    }
</style>
@endpush

@push('scripts')
<script>
    // Tab almashish
    document.querySelectorAll('.settings-link').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const tab = this.dataset.tab;

            // Aktiv klasslarni yangilash
            document.querySelectorAll('.settings-link').forEach(l => l.classList.remove('active'));
            document.querySelectorAll('.settings-tab').forEach(t => t.classList.remove('active'));

            this.classList.add('active');
            document.getElementById('tab-' + tab).classList.add('active');

            // URL hash'ni yangilash
            history.replaceState(null, '', '#' + tab);
        });
    });

    // URL hash bo'lsa — tabni ochish
    if (window.location.hash) {
        const hash = window.location.hash.substring(1);
        const link = document.querySelector(`.settings-link[data-tab="${hash}"]`);
        if (link) link.click();
    }

    // Avatar preview
    const avatarInput = document.getElementById('avatarInput');
    if (avatarInput) {
        avatarInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(ev) {
                    const preview = document.getElementById('avatarPreview');
                    if (preview.tagName === 'IMG') {
                        preview.src = ev.target.result;
                    } else {
                        const img = document.createElement('img');
                        img.id = 'avatarPreview';
                        img.src = ev.target.result;
                        img.style.cssText = 'width:72px;height:72px;border-radius:50%;object-fit:cover;border:3px solid var(--green);';
                        preview.parentNode.replaceChild(img, preview);
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // Theme tanlash
    document.querySelectorAll('.theme-card').forEach(card => {
        card.addEventListener('click', function() {
            document.querySelectorAll('.theme-card').forEach(c => c.classList.remove('active'));
            this.classList.add('active');
            this.querySelector('input[type="radio"]').checked = true;
        });
    });
</script>
@endpush