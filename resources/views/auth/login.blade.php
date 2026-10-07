@extends('layouts.auth')

@section('title', 'Kirish')
@section('form-title', 'Tizimga kirish')
@section('form-subtitle', 'Hisobingizga kiring va ishlashni davom ettiring')

@section('form')
<form method="POST" action="{{ route('login') }}">
    @csrf

    @if($errors->any())
        <div class="alert alert-danger">
            <i class="bi bi-exclamation-circle-fill"></i>
            <span>{{ $errors->first() }}</span>
        </div>
    @endif

    <div class="form-group">
        <label class="form-label">Email manzil</label>
        <div class="form-input-wrapper">
            <input type="email"
                   name="email"
                   class="form-input @error('email') is-invalid @enderror"
                   value="{{ old('email') }}"
                   placeholder="email@soglomavlod.uz"
                   required autofocus>
            <i class="bi bi-envelope input-icon"></i>
        </div>
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label class="form-label">Parol</label>
        <div class="form-input-wrapper">
            <input type="password"
                   name="password"
                   class="form-input @error('password') is-invalid @enderror"
                   placeholder="••••••••"
                   required>
            <i class="bi bi-lock input-icon"></i>
        </div>
        @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-check">
        <input type="checkbox" name="remember" id="remember" {{ old('remember')?'checked':'' }}>
        <label for="remember">Meni eslab qol</label>
    </div>

    <button type="submit" class="btn-auth">
        <i class="bi bi-box-arrow-in-right"></i>
        Kirish
    </button>

    @if(Route::has('password.request'))
        <div class="auth-link">
            <a href="{{ route('password.request') }}">Parolni tiklash</a>
        </div>
    @endif
</form>
@endsection