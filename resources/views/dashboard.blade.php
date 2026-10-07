@extends('layouts.app')

@section('title', __('general.dashboard'))
@section('page-title', __('general.dashboard'))

@section('content')

{{-- ============ STAT CARDS ============ --}}
<div class="row g-3 mb-3">
    <div class="col-lg-3 col-md-6">
        <div class="info-card navy">
            <div class="info-content">
                <div class="info-label">{{ __('general.total_cases') }}</div>
                <div class="info-value">{{ $stats['total'] }}</div>
            </div>
            <div class="info-icon">
                <i class="bi bi-shield-exclamation"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="info-card red">
            <div class="info-content">
                <div class="info-label">🔴 {{ __('general.high_risk') }}</div>
                <div class="info-value">{{ $stats['red'] }}</div>
            </div>
            <div class="info-icon">
                <i class="bi bi-exclamation-octagon"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="info-card orange">
            <div class="info-content">
                <div class="info-label">🟡 {{ __('general.medium_risk') }}</div>
                <div class="info-value">{{ $stats['yellow'] }}</div>
            </div>
            <div class="info-icon">
                <i class="bi bi-exclamation-triangle"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="info-card green">
            <div class="info-content">
                <div class="info-label">🟢 {{ __('general.low_risk') }}</div>
                <div class="info-value">{{ $stats['green'] }}</div>
            </div>
            <div class="info-icon">
                <i class="bi bi-shield-check"></i>
            </div>
        </div>
    </div>
</div>

{{-- ============ DAVOMAT STATISTIKASI ============ --}}
<div class="row g-3 mb-3">
    <div class="col-lg-3 col-md-6">
        <div class="info-card green">
            <div class="info-content">
                <div class="info-label">✅ {{ __('general.present_today') }}</div>
                <div class="info-value">{{ $attStats['present'] ?? 0 }}</div>
            </div>
            <div class="info-icon">
                <i class="bi bi-person-check"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="info-card red">
            <div class="info-content">
                <div class="info-label">❌ {{ __('general.absent_today') }}</div>
                <div class="info-value">{{ $attStats['absent'] ?? 0 }}</div>
            </div>
            <div class="info-icon">
                <i class="bi bi-person-x"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="info-card orange">
            <div class="info-content">
                <div class="info-label">⏰ {{ __('general.late_today') }}</div>
                <div class="info-value">{{ $attStats['late'] ?? 0 }}</div>
            </div>
            <div class="info-icon">
                <i class="bi bi-clock-history"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="info-card red">
            <div class="info-content">
                <div class="info-label">🚨 {{ __('general.absent_3_days') }}</div>
                <div class="info-value">{{ $attStats['alerts'] ?? 0 }}</div>
            </div>
            <div class="info-icon">
                <i class="bi bi-bell-fill"></i>
            </div>
        </div>
    </div>
</div>

{{-- ============ ASOSIY JADVAL ============ --}}
<div class="row g-3">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-list-check text-success"></i>
                        {{ __('general.latest_cases') }}
                    </h5>
                    <a href="{{ route('cases.create') }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus-lg"></i> {{ __('general.new_case') }}
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{ __('general.student') }}</th>
                                <th>{{ __('general.class') }}</th>
                                <th>{{ __('general.type') }}</th>
                                <th>{{ __('general.score') }}</th>
                                <th>{{ __('general.level') }}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($latest as $c)
                            <tr>
                                <td><strong>{{ $c->student->full_name ?? '—' }}</strong></td>
                                <td>{{ $c->student->class_name ?? '—' }}</td>
                                <td>{{ $c->risk_type }}</td>
                                <td><strong>{{ $c->risk_score }}</strong>/100</td>
                                <td>
                                    <span class="badge bg-{{ $c->risk_level == 'red' ? 'red' : ($c->risk_level == 'yellow' ? 'yellow' : 'green') }}">
                                        @if($c->risk_level == 'red') 🔴 {{ __('general.high') }}
                                        @elseif($c->risk_level == 'yellow') 🟡 {{ __('general.medium') }}
                                        @else 🟢 {{ __('general.low') }} @endif
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('cases.show', $c) }}" class="btn btn-navy btn-sm">
                                        {{ __('general.view') }}
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    <i class="bi bi-inbox" style="font-size:32px;opacity:.4;"></i>
                                    <p class="mb-0 mt-2">{{ __('general.no_data') }}</p>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">
                    <i class="bi bi-bar-chart text-success"></i>
                    {{ __('general.by_class') }}
                </h5>
                @forelse($byClass as $row)
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="fw-bold">{{ $row->class_name }}</span>
                            <span class="text-muted">{{ $row->cnt }} {{ __('general.items') }}</span>
                        </div>
                        <div style="background:#eef1f5;border-radius:6px;height:8px;overflow:hidden;">
                            <div style="background:linear-gradient(90deg,var(--green),var(--green-dark));
                                        height:100%;width:{{ min(100, $row->cnt * 15) }}%;
                                        border-radius:6px;transition:width .6s ease;"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-muted text-center py-3">{{ __('general.no_data') }}</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

@endsection