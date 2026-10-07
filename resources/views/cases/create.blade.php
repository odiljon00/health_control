@extends('layouts.app')

@section('title', 'Yangi holat')
@section('page-title', 'Yangi xavf holati')

@section('content')

<div class="row justify-content-center">
    <div class="col-lg-10">
        {{-- Header card --}}
        <div class="card mb-3">
            <div class="card-body">
                <h4 class="mb-1 fw-bold">
                    <i class="bi bi-exclamation-triangle-fill text-warning"></i>
                    Yangi xavf holatini qayd etish
                </h4>
                <p class="text-muted small mb-0">
                    O'quvchida xavf belgisi aniqlansa, quyidagi shaklni to'ldiring.
                    Tizim avtomatik ravishda <strong>psixolog</strong>, <strong>sinf rahbari</strong> va
                    <strong>ota-onaga</strong> xabar yuboradi.
                </p>
            </div>
        </div>

        {{-- Form --}}
        <form method="POST" action="{{ route('cases.store') }}">
            @csrf

            <div class="card">
                <div class="card-body p-4">
                    <div class="row g-3">
                        {{-- O'quvchi --}}
                        <div class="col-md-6">
                            <label class="form-label">
                                O'quvchi <span class="text-danger">*</span>
                            </label>
                            <select name="student_id" required class="form-select @error('student_id') is-invalid @enderror">
                                <option value="">— Tanlang —</option>
                                @foreach($students as $s)
                                    <option value="{{ $s->id }}" {{ old('student_id')==$s->id?'selected':'' }}>
                                        {{ $s->full_name }} ({{ $s->class_name }})
                                    </option>
                                @endforeach
                            </select>
                            @error('student_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Xavf turi --}}
                        <div class="col-md-6">
                            <label class="form-label">
                                Xavf turi <span class="text-danger">*</span>
                            </label>
                            <select name="risk_type" required class="form-select @error('risk_type') is-invalid @enderror">
                                <option value="">— Tanlang —</option>
                                <option value="tobacco"     {{ old('risk_type')=='tobacco'?'selected':'' }}>🚬 Tamaki</option>
                                <option value="alcohol"     {{ old('risk_type')=='alcohol'?'selected':'' }}>🍷 Spirtli ichimlik</option>
                                <option value="drugs"       {{ old('risk_type')=='drugs'?'selected':'' }}>💊 Giyohvandlik</option>
                                <option value="crime"       {{ old('risk_type')=='crime'?'selected':'' }}>⚖️ Jinoyat</option>
                                <option value="aggression"  {{ old('risk_type')=='aggression'?'selected':'' }}>😠 Agressiya</option>
                                <option value="depression"  {{ old('risk_type')=='depression'?'selected':'' }}>😔 Depressiya</option>
                                <option value="absenteeism" {{ old('risk_type')=='absenteeism'?'selected':'' }}>🚪 Darsdan qochish</option>
                                <option value="other"       {{ old('risk_type')=='other'?'selected':'' }}>Boshqa</option>
                            </select>
                            @error('risk_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Tavsif --}}
                        <div class="col-12">
                            <label class="form-label">Tavsif (ixtiyoriy)</label>
                            <textarea name="description" rows="5"
                                      class="form-control @error('description') is-invalid @enderror"
                                      placeholder="Kuzatuv tafsilotlari: nima sodir bo'ldi, qachon, kimlar guvoh bo'ldi...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Xabarnomalar --}}
            <div class="card">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3">
                        <i class="bi bi-bell-fill text-warning"></i>
                        Xabarnomalar
                    </h6>

                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox"
                               name="notify_parent" value="1" id="notifyParent"
                               {{ old('notify_parent', true)?'checked':'' }}>
                        <label class="form-check-label" for="notifyParent">
                            <strong>Ota-onaga xabar yuborish</strong>
                            <div class="text-muted small">Holat haqida ota-onani xabardor qilish</div>
                        </label>
                    </div>

                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox"
                               name="notify_iio" value="1" id="notifyIio"
                               {{ old('notify_iio')?'checked':'' }}>
                        <label class="form-check-label" for="notifyIio">
                            <strong>IIO profilaktika xizmatiga xabar</strong>
                            <div class="text-muted small">Yuqori xavfli holatlarda ichki ishlar organlarini xabardor qilish</div>
                        </label>
                    </div>
                </div>
            </div>

            {{-- Tugmalar --}}
            <div class="d-flex gap-2 justify-content-end mb-4">
                <a href="{{ route('cases.index') }}" class="btn btn-light px-4">
                    <i class="bi bi-x-lg"></i> Bekor qilish
                </a>
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-save"></i> Holatni saqlash
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

    .form-check-input {
        width: 42px;
        height: 22px;
        margin-top: 2px;
        cursor: pointer;
    }

    .form-check-input:checked {
        background-color: var(--green);
        border-color: var(--green);
    }

    .form-check-input:focus {
        border-color: var(--green);
        box-shadow: 0 0 0 4px rgba(42,157,143,.15);
    }

    .form-check-label { cursor: pointer; padding-left: 4px; }

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