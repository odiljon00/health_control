@extends('layouts.auth')

@section('title', 'Ro\'yxatdan o\'tish')
@section('form-title', 'Ro\'yxatdan o\'tish')
@section('form-subtitle', 'Yangi hisob yarating')

@section('form')
<form method="POST" action="{{ route('register') }}">
    @csrf

    <div class="form-group">
        <label class="form-label">To'liq ism</label>
        <div class="form-input-wrapper">
            <input type="text" name="name" class="form-input @error('name') is-invalid @enderror"
                   value="{{ old('name') }}" placeholder="Ism Familiya" required autofocus>
            <i class="bi bi-person input-icon"></i>
        </div>
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="form-group">
        <label class="form-label">Email manzil</label>
        <div class="form-input-wrapper">
            <input type="email" name="email" class="form-input @error('email') is-invalid @enderror"
                   value="{{ old('email') }}" placeholder="email@example.com" required>
            <i class="bi bi-envelope input-icon"></i>
        </div>
        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="form-group">
        <label class="form-label">Parol</label>
        <div class="form-input-wrapper">
            <input type="password" name="password" class="form-input @error('password') is-invalid @enderror"
                   placeholder="Kamida 8 belgi" required>
            <i class="bi bi-lock input-icon"></i>
        </div>
        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="form-group">
        <label class="form-label">Parolni tasdiqlash</label>
        <div class="form-input-wrapper">
            <input type="password" name="password_confirmation" class="form-input"
                   placeholder="Parolni qayta kiriting" required>
            <i class="bi bi-lock-fill input-icon"></i>
        </div>
    </div>

    <button type="submit" class="btn-auth">
        <i class="bi bi-person-plus-fill"></i>
        Ro'yxatdan o'tish
    </button>

    <div class="auth-link">
        Hisobingiz bormi? <a href="{{ route('login') }}">Kirish</a>
    </div>
</form>
@endsection