@extends('layouts.app')

@section('title', 'Profil')
@section('page-title', 'Mening profilim')

@section('content')

<div class="row g-3">
    {{-- ============ CHAP TOMON: PROFIL KARTASI ============ --}}
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body text-center">
                <div class="profile-avatar mx-auto mb-3">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>

                <h4 class="mb-1 fw-bold">{{ $user->name }}</h4>
                <p class="text-muted mb-2 small">{{ $user->email }}</p>

                <div class="mb-3">
                    @php
                        $roleLabels = [
                            'admin' => ['⚙️', 'Administrator'],
                            'teacher' => ['👨‍🏫', 'Ustoz'],
                            'psychologist' => ['🧠', 'Psixolog'],
                            'school_admin' => ['🏛', 'Maktab ma\'muriyati'],
                            'parent' => ['👨‍👩‍👧', 'Ota-ona'],
                            'iio' => ['🛡️', 'IIO xodimi'],
                        ];
                        $role = $roleLabels[$user->role] ?? ['👤', $user->role];
                    @endphp
                    <span class="badge bg-green">
                        {{ $role[0] }} {{ $role[1] }}
                    </span>
                </div>

                @if($user->school)
                    <div class="p-3 rounded" style="background:#f5f7fa;">
                        <div class="small text-muted mb-1">Maktab</div>
                        <div class="fw-bold">{{ $user->school->name }}</div>
                    </div>
                @endif

                <div class="d-grid gap-2 mt-3">
                    <a href="{{ route('profile.edit') }}" class="btn btn-primary">
                        <i class="bi bi-pencil"></i> Profilni tahrirlash
                    </a>
                </div>
            </div>
        </div>

        {{-- Statistika --}}
        <div class="card">
            <div class="card-body">
                <h6 class="fw-bold mb-3">
                    <i class="bi bi-bar-chart text-success"></i> Faoliyat
                </h6>

                <div class="profile-stat-row">
                    <span class="text-muted">Qayd etgan holatlar</span>
                    <strong>{{ $stats['cases_reported'] }}</strong>
                </div>
                <div class="profile-stat-row">
                    <span class="text-muted">Farzandlar (ota-ona)</span>
                    <strong>{{ $stats['children'] }}</strong>
                </div>
                <div class="profile-stat-row">
                    <span class="text-muted">Ro'yxatdan o'tgan</span>
                    <strong>{{ $user->created_at->format('d.m.Y') }}</strong>
                </div>
            </div>
        </div>
    </div>

    {{-- ============ O'NG TOMON: MA'LUMOTLAR ============ --}}
    <div class="col-lg-8">
        {{-- Shaxsiy ma'lumotlar --}}
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="fw-bold mb-3">
                    <i class="bi bi-person-badge text-success"></i> Shaxsiy ma'lumotlar
                </h5>

                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="detail-item">
                            <div class="detail-label">To'liq ism</div>
                            <div class="detail-value">{{ $user->name }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-item">
                            <div class="detail-label">Email</div>
                            <div class="detail-value">{{ $user->email }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-item">
                            <div class="detail-label">Telefon</div>
                            <div class="detail-value">{{ $user->phone ?? '—' }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-item">
                            <div class="detail-label">Telegram ID</div>
                            <div class="detail-value">{{ $user->telegram_id ?? '—' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Parolni o'zgartirish --}}
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="fw-bold mb-3">
                    <i class="bi bi-shield-lock text-success"></i> Parolni o'zgartirish
                </h5>

                @if($errors->any())
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-circle"></i>
                        @foreach($errors->all() as $e)
                            <div>{{ $e }}</div>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('profile.password') }}">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label">Hozirgi parol</label>
                            <input type="password" name="current_password" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Yangi parol</label>
                            <input type="password" name="password" class="form-control" required minlength="6">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Parolni tasdiqlash</label>
                            <input type="password" name="password_confirmation" class="form-control" required minlength="6">
                        </div>
                    </div>

                    <div class="mt-3 text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-key"></i> Parolni yangilash
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Xavfli hudud --}}
        <div class="card border-danger">
            <div class="card-body">
                <h5 class="fw-bold mb-2 text-danger">
                    <i class="bi bi-exclamation-triangle"></i> Xavfli hudud
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

@endsection

@push('styles')
<style>
    .profile-avatar {
        width: 96px;
        height: 96px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--green), #4ecdc4);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 38px;
        box-shadow: 0 8px 24px rgba(42,157,143,.35);
    }

    .profile-stat-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
        border-bottom: 1px dashed #eef1f5;
        font-size: 14px;
    }

    .profile-stat-row:last-child { border-bottom: none; }

    .detail-item {
        padding: 8px 0;
    }

    .detail-label {
        font-size: 12px;
        color: #899bbd;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .5px;
        margin-bottom: 4px;
    }

    .detail-value {
        font-size: 15px;
        font-weight: 600;
        color: var(--navy);
    }
</style>
@endpush