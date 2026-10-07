@extends('layouts.app')

@section('title', 'Profilni tahrirlash')
@section('page-title', 'Profilni tahrirlash')

@section('content')

<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="fw-bold mb-1">
                    <i class="bi bi-pencil-square text-success"></i> Profilni tahrirlash
                </h5>
                <p class="text-muted small mb-0">
                    Ma'lumotlaringizni yangilang va saqlang
                </p>
            </div>
        </div>

        <form method="POST" action="{{ route('profile.update') }}">
            @csrf
            @method('PUT')

            <div class="card">
                <div class="card-body p-4">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-circle"></i>
                            @foreach($errors->all() as $e)
                                <div>{{ $e }}</div>
                            @endforeach
                        </div>
                    @endif

                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">
                                To'liq ism <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $user->name) }}" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label">
                                Email <span class="text-danger">*</span>
                            </label>
                            <input type="email" name="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email', $user->email) }}" required>
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Telefon</label>
                            <input type="text" name="phone" class="form-control"
                                   placeholder="+998 90 123 45 67"
                                   value="{{ old('phone', $user->phone) }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Telegram ID</label>
                            <input type="text" name="telegram_id" class="form-control"
                                   placeholder="@username"
                                   value="{{ old('telegram_id', $user->telegram_id) }}">
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2 justify-content-end">
                <a href="{{ route('profile') }}" class="btn btn-light px-4">
                    <i class="bi bi-x-lg"></i> Bekor qilish
                </a>
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-save"></i> Saqlash
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('styles')
<style>
    .form-label {
        font-weight: 600;
        font-size: 13px;
        color: #334;
    }

    .form-control {
        padding: 10px 14px;
        border-radius: 9px;
        border: 1.5px solid #d8dee6;
        font-size: 14px;
    }

    .form-control:focus {
        border-color: var(--green);
        box-shadow: 0 0 0 4px rgba(42,157,143,.12);
    }
</style>
@endpush