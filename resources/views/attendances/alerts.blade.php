@extends('layouts.app')

@section('title', 'Ogohlantirishlar')
@section('page-title', 'Ogohlantirishlar')

@section('content')

<div class="card mb-3">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h4 class="mb-1 fw-bold">
                    <i class="bi bi-exclamation-octagon-fill text-danger"></i>
                    Ogohlantirishlar
                </h4>
                <p class="text-muted mb-0 small">
                    3 va undan ko'p kun darsga kelmagan o'quvchilar
                </p>
            </div>
            <a href="{{ route('attendance.today') }}" class="btn btn-navy">
                <i class="bi bi-clipboard-check"></i> Davomatga qaytish
            </a>
        </div>
    </div>
</div>

{{-- STATISTIKA --}}
<div class="row g-3 mb-3">
    <div class="col-md-4">
        <div class="info-card red">
            <div class="info-content">
                <div class="info-label">🚨 5+ kun kelmagan</div>
                <div class="info-value">{{ $students->where('consecutive_absences', '>=', 5)->count() }}</div>
            </div>
            <div class="info-icon">
                <i class="bi bi-exclamation-octagon"></i>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="info-card orange">
            <div class="info-content">
                <div class="info-label">⚠️ 3-4 kun kelmagan</div>
                <div class="info-value">{{ $students->whereBetween('consecutive_absences', [3, 4])->count() }}</div>
            </div>
            <div class="info-icon">
                <i class="bi bi-exclamation-triangle"></i>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="info-card navy">
            <div class="info-content">
                <div class="info-label">📊 Jami ogohlantirish</div>
                <div class="info-value">{{ $students->count() }}</div>
            </div>
            <div class="info-icon">
                <i class="bi bi-list-check"></i>
            </div>
        </div>
    </div>
</div>

{{-- RO'YXAT --}}
<div class="card">
    <div class="card-body p-0">
        @if($students->count())
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width:50px;">#</th>
                            <th>O'quvchi</th>
                            <th style="width:90px;">Sinf</th>
                            <th>Maktab</th>
                            <th style="width:150px;">Ketma-ket</th>
                            <th style="width:150px;">Oxirgi 7 kun</th>
                            <th style="width:200px;">Daraja</th>
                            <th style="width:120px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($students as $i => $s)
                        <tr>
                            <td class="text-muted">{{ $i + 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar-circle">
                                        {{ strtoupper(substr($s->first_name, 0, 1)) }}
                                    </div>
                                    <strong>{{ $s->full_name }}</strong>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-navy">{{ $s->class_name }}</span>
                            </td>
                            <td class="text-muted small">{{ $s->school->name ?? '—' }}</td>
                            <td>
                                @if($s->consecutive_absences >= 5)
                                    <span class="badge bg-red">
                                        🚨 {{ $s->consecutive_absences }} kun
                                    </span>
                                @elseif($s->consecutive_absences >= 3)
                                    <span class="badge bg-yellow">
                                        ⚠️ {{ $s->consecutive_absences }} kun
                                    </span>
                                @else
                                    <span class="badge bg-navy">{{ $s->consecutive_absences }} kun</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $s->absences_last_7 >= 3 ? 'red' : 'navy' }}">
                                    {{ $s->absences_last_7 }} kun
                                </span>
                            </td>
                            <td>
                                <div class="progress" style="height:8px;border-radius:6px;">
                                    <div class="progress-bar"
                                         style="width:{{ min(100, $s->consecutive_absences * 20) }}%;
                                                background:{{ $s->consecutive_absences >= 5 ? '#E63946' : ($s->consecutive_absences >= 3 ? '#F4A261' : '#0A2540') }};">
                                    </div>
                                </div>
                            </td>
                            <td>
                                <a href="{{ route('attendance.student', $s) }}" class="btn btn-navy btn-sm">
                                    <i class="bi bi-eye"></i> Tarix
                                </a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5 text-muted">
                <i class="bi bi-shield-check" style="font-size:56px;color:var(--green);opacity:.5;"></i>
                <p class="mt-3 mb-0 fw-bold" style="color:var(--green);">
                    Ayni damda 3+ kun kelmagan o'quvchi yo'q
                </p>
                <p class="text-muted small mt-1">Barcha o'quvchilar davomatda.</p>
            </div>
        @endif
    </div>
</div>

@endsection

@push('styles')
<style>
    .avatar-circle {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--green), #4ecdc4);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 14px;
        flex-shrink: 0;
    }
</style>
@endpush