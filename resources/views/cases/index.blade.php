@extends('layouts.app')

@section('title', 'Xavf holatlari')
@section('page-title', 'Xavf holatlari')

@section('content')

{{-- ============ HEADER ============ --}}
<div class="card mb-3">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h4 class="mb-1 fw-bold">
                    <i class="bi bi-shield-exclamation text-warning"></i>
                    Xavf holatlari
                </h4>
                <p class="text-muted mb-0 small">
                    Barcha qayd etilgan holatlar — xavf darajasi bo'yicha
                </p>
            </div>
            <a href="{{ route('cases.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> Yangi holat
            </a>
        </div>

        {{-- FILTRLAR --}}
        <form method="GET" class="row g-2 align-items-end mt-3">
            <div class="col-md-3 col-sm-6">
                <label class="form-label">Daraja</label>
                <select name="level" class="form-select">
                    <option value="">— Barchasi —</option>
                    <option value="red"    {{ request('level')=='red'?'selected':'' }}>🔴 Yuqori</option>
                    <option value="yellow" {{ request('level')=='yellow'?'selected':'' }}>🟡 O'rta</option>
                    <option value="green"  {{ request('level')=='green'?'selected':'' }}>🟢 Past</option>
                </select>
            </div>
            <div class="col-md-3 col-sm-6">
                <label class="form-label">Holat</label>
                <select name="status" class="form-select">
                    <option value="">— Barchasi —</option>
                    <option value="new"         {{ request('status')=='new'?'selected':'' }}>🆕 Yangi</option>
                    <option value="in_progress" {{ request('status')=='in_progress'?'selected':'' }}>🔄 Ko'rilmoqda</option>
                    <option value="improved"    {{ request('status')=='improved'?'selected':'' }}>✅ Yaxshilandi</option>
                    <option value="ongoing"     {{ request('status')=='ongoing'?'selected':'' }}>⚠️ Davom etmoqda</option>
                    <option value="severe"      {{ request('status')=='severe'?'selected':'' }}>🚨 Og'ir</option>
                    <option value="closed"      {{ request('status')=='closed'?'selected':'' }}>🔒 Yopildi</option>
                </select>
            </div>
            <div class="col-md-3 col-sm-6">
                <label class="form-label">Tur</label>
                <select name="type" class="form-select">
                    <option value="">— Barchasi —</option>
                    <option value="tobacco"     {{ request('type')=='tobacco'?'selected':'' }}>🚬 Tamaki</option>
                    <option value="alcohol"     {{ request('type')=='alcohol'?'selected':'' }}>🍷 Spirtli</option>
                    <option value="drugs"       {{ request('type')=='drugs'?'selected':'' }}>💊 Giyohvandlik</option>
                    <option value="crime"       {{ request('type')=='crime'?'selected':'' }}>⚖️ Jinoyat</option>
                    <option value="aggression"  {{ request('type')=='aggression'?'selected':'' }}>😠 Agressiya</option>
                    <option value="depression"  {{ request('type')=='depression'?'selected':'' }}>😔 Depressiya</option>
                    <option value="absenteeism" {{ request('type')=='absenteeism'?'selected':'' }}>🚪 Darsdan qochish</option>
                    <option value="other"       {{ request('type')=='other'?'selected':'' }}>Boshqa</option>
                </select>
            </div>
            <div class="col-md-3 col-sm-6 d-flex gap-2">
                <button class="btn btn-navy flex-fill">
                    <i class="bi bi-funnel"></i> Filtrlash
                </button>
                <a href="{{ route('cases.index') }}" class="btn btn-light">
                    <i class="bi bi-x-lg"></i>
                </a>
            </div>
        </form>
    </div>
</div>

{{-- ============ JADVAL ============ --}}
<div class="card">
    <div class="card-body p-0">
        @if($cases->count())
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width:50px;">#</th>
                            <th>O'quvchi</th>
                            <th style="width:90px;">Sinf</th>
                            <th>Tur</th>
                            <th style="width:110px;">Ball</th>
                            <th style="width:120px;">Daraja</th>
                            <th style="width:130px;">Holat</th>
                            <th style="width:110px;">Sana</th>
                            <th style="width:110px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($cases as $i => $c)
                        <tr>
                            <td class="text-muted">{{ $cases->firstItem() + $i }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar-circle">
                                        {{ strtoupper(substr($c->student->first_name ?? '?', 0, 1)) }}
                                    </div>
                                    <strong>{{ $c->student->full_name ?? '—' }}</strong>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-navy">{{ $c->student->class_name ?? '—' }}</span>
                            </td>
                            <td>
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
                                {{ $typeLabels[$c->risk_type] ?? $c->risk_type }}
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <strong>{{ $c->risk_score }}</strong>
                                    <span class="text-muted small">/100</span>
                                </div>
                            </td>
                            <td>
                                @if($c->risk_level == 'red')
                                    <span class="badge bg-red">🔴 Yuqori</span>
                                @elseif($c->risk_level == 'yellow')
                                    <span class="badge bg-yellow">🟡 O'rta</span>
                                @else
                                    <span class="badge bg-green">🟢 Past</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $statusLabels = [
                                        'new' => ['🆕', 'Yangi', 'navy'],
                                        'in_progress' => ['🔄', 'Ko\'rilmoqda', 'yellow'],
                                        'improved' => ['✅', 'Yaxshilandi', 'green'],
                                        'ongoing' => ['⚠️', 'Davom etmoqda', 'yellow'],
                                        'severe' => ['🚨', 'Og\'ir', 'red'],
                                        'closed' => ['🔒', 'Yopildi', 'navy'],
                                    ];
                                    $st = $statusLabels[$c->status] ?? ['', $c->status, 'navy'];
                                @endphp
                                <span class="badge bg-{{ $st[2] }}">{{ $st[0] }} {{ $st[1] }}</span>
                            </td>
                            <td class="text-muted small">
                                {{ $c->created_at->format('d.m.Y') }}
                            </td>
                            <td>
                                <a href="{{ route('cases.show', $c) }}" class="btn btn-navy btn-sm">
                                    <i class="bi bi-eye"></i> Ko'rish
                                </a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            {{-- PAGINATION --}}
            @if($cases->hasPages())
                <div class="card-body border-top">
                    {{ $cases->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-5 text-muted">
                <i class="bi bi-inbox" style="font-size:56px;opacity:.3;"></i>
                <p class="mt-3 mb-0">Hozircha hech qanday xavf holati qayd etilmagan.</p>
                <a href="{{ route('cases.create') }}" class="btn btn-primary mt-3">
                    <i class="bi bi-plus-lg"></i> Birinchi holatni qo'shish
                </a>
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

    .table td { vertical-align: middle; }
</style>
@endpush