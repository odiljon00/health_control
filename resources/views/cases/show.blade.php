@extends('layouts.app')

@section('title', 'Holat #'.$case->id)
@section('page-title', 'Xavf holati tafsiloti')

@section('content')

<div class="row justify-content-center">
    <div class="col-lg-10">

        {{-- ============ ASOSIY KARTA ============ --}}
        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar-lg">
                            {{ strtoupper(substr($case->student->first_name ?? '?', 0, 1)) }}
                        </div>
                        <div>
                            <h4 class="mb-1 fw-bold">{{ $case->student->full_name ?? '—' }}</h4>
                            <p class="text-muted mb-0 small">
                                <i class="bi bi-mortarboard"></i>
                                {{ $case->student->class_name ?? '—' }} •
                                <i class="bi bi-building"></i>
                                {{ $case->school->name ?? '—' }}
                            </p>
                        </div>
                    </div>

                    <div class="text-end">
                        <div class="risk-score" style="color:
                            @if($case->risk_level=='red') var(--red)
                            @elseif($case->risk_level=='yellow') var(--orange)
                            @else var(--green) @endif;">
                            {{ $case->risk_score }}<span class="fs-6 text-muted">/100</span>
                        </div>
                        <div class="mt-1">
                            @if($case->risk_level == 'red')
                                <span class="badge bg-red">🔴 Yuqori xavf</span>
                            @elseif($case->risk_level == 'yellow')
                                <span class="badge bg-yellow">🟡 O'rta xavf</span>
                            @else
                                <span class="badge bg-green">🟢 Past xavf</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============ TAFSILOTLAR ============ --}}
        <div class="card mb-3">
            <div class="card-body">
                <h6 class="fw-bold mb-3">
                    <i class="bi bi-info-circle text-success"></i> Holat tafsilotlari
                </h6>

                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="detail-item">
                            <div class="detail-label">Xavf turi</div>
                            <div class="detail-value">
                                @php
                                    $typeLabels = [
                                        'tobacco' => '🚬 Tamaki',
                                        'alcohol' => '🍷 Spirtli',
                                        'drugs' => '💊 Giyohvandlik',
                                        'crime' => '⚖️ Jinoyat',
                                        'aggression' => '😠 Agressiya',
                                        'depression' => '😔 Depressiya',
                                        'absenteeism' => '🚪 Darsdan qochish',
                                        'other' => 'Boshqa',
                                    ];
                                @endphp
                                {{ $typeLabels[$case->risk_type] ?? $case->risk_type }}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="detail-item">
                            <div class="detail-label">Holat</div>
                            <div class="detail-value">
                                <span class="badge bg-navy">{{ $case->status }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="detail-item">
                            <div class="detail-label">Kim qayd etdi</div>
                            <div class="detail-value">
                                {{ $case->reporter->name ?? '—' }}
                                <span class="text-muted small">({{ $case->reporter->role ?? '—' }})</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="detail-item">
                            <div class="detail-label">Psixolog</div>
                            <div class="detail-value">
                                @if($case->psychologist)
                                    {{ $case->psychologist->name }}
                                @else
                                    <span class="text-muted">— tayinlanmagan —</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="detail-item">
                            <div class="detail-label">Yaratilgan</div>
                            <div class="detail-value">
                                {{ $case->created_at->format('d.m.Y H:i') }}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="detail-item">
                            <div class="detail-label">Xabar yuborilgan</div>
                            <div class="detail-value">
                                @if($case->notified_at)
                                    ✅ {{ $case->notified_at->format('d.m.Y H:i') }}
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                @if($case->description)
                    <div class="mt-3 p-3 rounded" style="background:#f5f7fa;border-left:4px solid var(--green);">
                        <strong>Tavsif:</strong><br>
                        <span class="text-muted">{{ $case->description }}</span>
                    </div>
                @endif
            </div>
        </div>

        {{-- ============ PSIXOLOG XULOSASI ============ --}}
        @if(Auth::user()->isPsychologist() || Auth::user()->isAdmin())
            <div class="card mb-3">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">
                        <i class="bi bi-clipboard-pulse text-success"></i> Psixolog xulosasi
                    </h6>

                    <form method="POST" action="{{ route('cases.status', $case) }}">
                        @csrf
                        @method('PATCH')

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Holatni yangilash</label>
                                <select name="status" class="form-select">
                                    <option value="in_progress" {{ $case->status=='in_progress'?'selected':'' }}>🔄 Ko'rib chiqilmoqda</option>
                                    <option value="improved"    {{ $case->status=='improved'?'selected':'' }}>✅ Yaxshilandi</option>
                                    <option value="ongoing"     {{ $case->status=='ongoing'?'selected':'' }}>⚠️ Davom etmoqda</option>
                                    <option value="severe"      {{ $case->status=='severe'?'selected':'' }}>🚨 Og'ir holat</option>
                                    <option value="closed"      {{ $case->status=='closed'?'selected':'' }}>🔒 Yopildi</option>
                                </select>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label">Izoh</label>
                                <textarea name="psychologist_note" rows="3" class="form-control"
                                          placeholder="O'quvchi bilan suhbat natijasi...">{{ $case->psychologist_note }}</textarea>
                            </div>
                            <div class="col-12 text-end">
                                <button class="btn btn-primary">
                                    <i class="bi bi-save"></i> Yangilash
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        {{-- ============ CHORALAR ============ --}}
        @if($case->actions && $case->actions->count())
            <div class="card mb-3">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">
                        <i class="bi bi-list-check text-success"></i> Amalga oshirilgan choralar
                    </h6>
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Sana</th>
                                    <th>Xodim</th>
                                    <th>Chora</th>
                                    <th>Izoh</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($case->actions as $a)
                                <tr>
                                    <td class="text-muted small">{{ $a->created_at->format('d.m.Y') }}</td>
                                    <td>{{ $a->user->name ?? '—' }}</td>
                                    <td><span class="badge bg-navy">{{ $a->action_type }}</span></td>
                                    <td class="text-muted">{{ $a->note }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        {{-- ============ ORQAGA ============ --}}
        <div class="mb-4">
            <a href="{{ route('cases.index') }}" class="btn btn-navy">
                <i class="bi bi-arrow-left"></i> Orqaga
            </a>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .avatar-lg {
        width: 64px;
        height: 64px;
        border-radius: 16px;
        background: linear-gradient(135deg, var(--green), #4ecdc4);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 26px;
        flex-shrink: 0;
        box-shadow: 0 6px 18px rgba(42,157,143,.3);
    }

    .risk-score {
        font-size: 44px;
        font-weight: 800;
        line-height: 1;
    }

    .detail-item {
        padding: 10px 0;
        border-bottom: 1px solid #eef1f5;
    }

    .detail-item:last-child { border-bottom: none; }

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

    .table td { vertical-align: middle; }
</style>
@endpush