@extends('layouts.app')
@section('title', 'Kelmaganlar — ' . $date->format('d.m.Y'))
@section('page-title', 'Kelmagan o\'quvchilar')

@section('content')

<div class="card mb-3">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h4 class="mb-1 fw-bold">
                    <i class="bi bi-person-x-fill text-danger"></i>
                    {{ $date->translatedFormat('d-F, Y') }} — kelmagan o'quvchilar
                </h4>
                <p class="text-muted mb-0 small">
                    Jami: <strong>{{ $students->count() }}</strong> ta
                </p>
            </div>
            <a href="{{ route('attendance.today') }}" class="btn btn-navy">
                <i class="bi bi-arrow-left"></i> Davomatga qaytish
            </a>
        </div>

        <form method="GET" class="row g-2 align-items-end mt-3">
            <div class="col-md-6">
                <label class="form-label">📅 Sana</label>
                <input type="date" name="date" value="{{ $date->format('Y-m-d') }}" class="form-control">
            </div>
            <div class="col-md-6">
                <button class="btn btn-navy w-100">
                    <i class="bi bi-search"></i> Ko'rsatish
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        @if($students->count())
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width:50px;">#</th>
                            <th>F.I.Sh.</th>
                            <th style="width:90px;">Sinf</th>
                            <th>Holat</th>
                            <th>Sabab</th>
                            <th style="width:130px;">Ketma-ket</th>
                            <th style="width:120px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($students as $i => $s)
                        @php $att = $s->attendances->first(); @endphp
                        <tr>
                            <td class="text-muted">{{ $i + 1 }}</td>
                            <td><strong>{{ $s->full_name }}</strong></td>
                            <td><span class="badge bg-navy">{{ $s->class_name }}</span></td>
                            <td>
                                @if($att && $att->status === 'absent')
                                    <span class="badge bg-red">❌ Kelmadi</span>
                                @elseif($att && $att->status === 'excused')
                                    <span class="badge bg-navy">📝 Sababli</span>
                                @endif
                            </td>
                            <td>{{ $att->reason ?? '—' }}</td>
                            <td>
                                @php $cons = $s->consecutiveAbsences(); @endphp
                                @if($cons >= 5)
                                    <span class="badge bg-red">🚨 {{ $cons }} kun</span>
                                @elseif($cons >= 3)
                                    <span class="badge bg-yellow">⚠️ {{ $cons }} kun</span>
                                @elseif($cons > 0)
                                    <span class="badge bg-navy">{{ $cons }} kun</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
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
                <i class="bi bi-emoji-smile" style="font-size:56px;color:var(--green);opacity:.5;"></i>
                <p class="mt-3 mb-0 fw-bold" style="color:var(--green);">
                    Bu kunda hamma o'quvchilar darsda
                </p>
            </div>
        @endif
    </div>
</div>

@endsection