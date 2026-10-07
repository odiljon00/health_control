@extends('layouts.app')

@section('title', $student->full_name)
@section('page-title', $student->full_name)

@section('content')

{{-- ==================== ASOSIY MA'LUMOTLAR ==================== --}}
<div class="card mb-3">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="avatar-large">
                    {{ strtoupper(substr($student->first_name, 0, 1)) }}
                </div>
                <div>
                    <h4 class="mb-1 fw-bold">{{ $student->full_name }}</h4>
                    <p class="text-muted mb-0 small">
                        <span class="badge bg-navy">{{ $student->class_name }}</span>
                        {{ $student->school->name ?? '—' }}
                    </p>
                </div>
            </div>
            <a href="{{ route('students.index') }}" class="btn btn-navy">
                <i class="bi bi-arrow-left"></i> Orqaga
            </a>
        </div>
    </div>
</div>

{{-- ==================== STATISTIKA ==================== --}}
<div class="row g-3 mb-3">
    <div class="col-md-3">
        <div class="info-card {{ $consecutiveAbsences >= 5 ? 'red' : ($consecutiveAbsences >= 3 ? 'orange' : 'green') }}">
            <div class="info-content">
                <div class="info-label">Ketma-ket kelmagan</div>
                <div class="info-value">{{ $consecutiveAbsences }} kun</div>
            </div>
            <div class="info-icon">
                <i class="bi bi-person-x-fill"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="info-card navy">
            <div class="info-content">
                <div class="info-label">Oxirgi 7 kun</div>
                <div class="info-value">{{ $absencesLast7 }} kun</div>
            </div>
            <div class="info-icon">
                <i class="bi bi-calendar-week"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="info-card orange">
            <div class="info-content">
                <div class="info-label">Jami holatlar</div>
                <div class="info-value">{{ $student->cases->count() }}</div>
            </div>
            <div class="info-icon">
                <i class="bi bi-exclamation-triangle"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="info-card green">
            <div class="info-content">
                <div class="info-label">Ota-ona</div>
                <div class="info-value" style="font-size:14px;">
                    {{ $student->parent->name ?? '—' }}
                </div>
            </div>
            <div class="info-icon">
                <i class="bi bi-people-fill"></i>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    {{-- ==================== SHAXSIY MA'LUMOTLAR ==================== --}}
    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="fw-bold mb-3">
                    <i class="bi bi-person-badge text-success"></i>
                    Shaxsiy ma'lumotlar
                </h5>
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <th style="width:120px;">F.I.Sh.</th>
                        <td>{{ $student->full_name }}</td>
                    </tr>
                    <tr>
                        <th>Sinf</th>
                        <td><span class="badge bg-navy">{{ $student->class_name }}</span></td>
                    </tr>
                    <tr>
                        <th>Tug'ilgan</th>
                        <td>
                            @if($student->birth_date)
                                {{ \Carbon\Carbon::parse($student->birth_date)->format('d.m.Y') }}
                            @else
                                —
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Jinsi</th>
                        <td>
                            @if($student->gender == 'male') 👦 O'g'il
                            @elseif($student->gender == 'female') 👧 Qiz
                            @else —
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Maktab</th>
                        <td>{{ $student->school->name ?? '—' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- Ota-ona --}}
        @if($student->parent)
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="fw-bold mb-3">
                    <i class="bi bi-people-fill text-success"></i>
                    Ota-ona
                </h5>
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <th style="width:120px;">F.I.Sh.</th>
                        <td><strong>{{ $student->parent->name }}</strong></td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td><a href="mailto:{{ $student->parent->email }}">{{ $student->parent->email }}</a></td>
                    </tr>
                    <tr>
                        <th>Telefon</th>
                        <td>
                            @if($student->parent->phone)
                                <span class="badge bg-green">
                                    <i class="bi bi-telephone"></i>
                                    {{ $student->parent->phone }}
                                </span>
                            @else
                                <span class="badge bg-red">Yo'q</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        @endif
    </div>

    {{-- ==================== O'NG: DAVOMAT VA HOLATLAR ==================== --}}
    <div class="col-lg-8">
        {{-- Oylik statistika --}}
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="fw-bold mb-3">
                    <i class="bi bi-calendar-month text-success"></i>
                    Oylik statistika
                </h5>
                @if($monthlyStats->count())
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Oy</th>
                                <th>✅ Keldi</th>
                                <th>❌ Kelmadi</th>
                                <th>⏰ Kechikdi</th>
                                <th>📝 Sababli</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($monthlyStats as $row)
                            <tr>
                                <td><strong>{{ $row->month }}</strong></td>
                                <td><span class="badge bg-green">{{ $row->present }}</span></td>
                                <td><span class="badge bg-red">{{ $row->absent }}</span></td>
                                <td><span class="badge bg-yellow">{{ $row->late }}</span></td>
                                <td><span class="badge bg-navy">{{ $row->excused }}</span></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-muted text-center py-3">Ma'lumot yo'q</p>
                @endif
            </div>
        </div>

        {{-- Xavf holatlari --}}
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="fw-bold mb-3">
                    <i class="bi bi-shield-exclamation text-success"></i>
                    Xavf holatlari
                </h5>
                @if($student->cases->count())
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Tur</th>
                                <th>Ball</th>
                                <th>Daraja</th>
                                <th>Holat</th>
                                <th>Sana</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($student->cases as $case)
                            <tr>
                                <td>{{ $case->risk_type }}</td>
                                <td><strong>{{ $case->risk_score }}</strong>/100</td>
                                <td>
                                    @if($case->risk_level == 'red')
                                        <span class="badge bg-red">🔴 Yuqori</span>
                                    @elseif($case->risk_level == 'yellow')
                                        <span class="badge bg-yellow">🟡 O'rta</span>
                                    @else
                                        <span class="badge bg-green">🟢 Past</span>
                                    @endif
                                </td>
                                <td><span class="badge bg-navy">{{ $case->status }}</span></td>
                                <td class="text-muted small">{{ $case->created_at->format('d.m.Y') }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-muted text-center py-3">Xavf holatlari yo'q ✅</p>
                @endif
            </div>
        </div>

        {{-- So'nggi davomat --}}
        <div class="card">
            <div class="card-body">
                <h5 class="fw-bold mb-3">
                    <i class="bi bi-list-check text-success"></i>
                    So'nggi davomat
                </h5>
                @if($attendances->count())
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Sana</th>
                                <th>Holat</th>
                                <th>Sabab</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($attendances as $a)
                            <tr>
                                <td>{{ $a->attendance_date->format('d.m.Y') }}</td>
                                <td>
                                    @if($a->status == 'present')
                                        <span class="badge bg-green">✅ Keldi</span>
                                    @elseif($a->status == 'absent')
                                        <span class="badge bg-red">❌ Kelmadi</span>
                                    @elseif($a->status == 'late')
                                        <span class="badge bg-yellow">⏰ Kechikdi</span>
                                    @else
                                        <span class="badge bg-navy">📝 Sababli</span>
                                    @endif
                                </td>
                                <td>{{ $a->reason ?? '—' }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <div class="mt-2">{{ $attendances->links() }}</div>
                @else
                    <p class="text-muted text-center py-3">Davomat yozuvlari yo'q</p>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .avatar-large {
        width: 72px;
        height: 72px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--green), #4ecdc4);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 28px;
        flex-shrink: 0;
        box-shadow: 0 6px 18px rgba(42,157,143,.3);
    }
</style>
@endpush