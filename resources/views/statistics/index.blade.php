@extends('layouts.app')

@section('title', 'Statistika')
@section('page-title', 'Statistika')

@section('content')

{{-- ==================== FILTRLAR ==================== --}}
<div class="card mb-3">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h4 class="mb-1 fw-bold">
                    <i class="bi bi-graph-up-arrow text-success"></i>
                    Umumiy statistika
                </h4>
                <p class="text-muted mb-0 small">
                    Tizimning to'liq ko'rsatkichlari va dinamikasi
                </p>
            </div>

            <form method="GET" class="d-flex gap-2 flex-wrap">
                @if($schools->count())
                    <select name="school_id" class="form-select" style="width:auto;" onchange="this.form.submit()">
                        <option value="">— Barcha maktablar —</option>
                        @foreach($schools as $sch)
                            <option value="{{ $sch->id }}" {{ $schoolId == $sch->id ? 'selected' : '' }}>
                                {{ $sch->name }}
                            </option>
                        @endforeach
                    </select>
                @endif

                <select name="period" class="form-select" style="width:auto;" onchange="this.form.submit()">
                    <option value="7"  {{ $period == 7  ? 'selected' : '' }}>Oxirgi 7 kun</option>
                    <option value="30" {{ $period == 30 ? 'selected' : '' }}>Oxirgi 30 kun</option>
                    <option value="90" {{ $period == 90 ? 'selected' : '' }}>Oxirgi 90 kun</option>
                </select>
            </form>
        </div>
    </div>
</div>

{{-- ==================== ASOSIY STATISTIKA ==================== --}}
<div class="row g-3 mb-3">
    <div class="col-lg-3 col-md-6">
        <div class="info-card navy">
            <div class="info-content">
                <div class="info-label">Jami holatlar</div>
                <div class="info-value">{{ $totalCases }}</div>
            </div>
            <div class="info-icon"><i class="bi bi-shield-exclamation"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="info-card red">
            <div class="info-content">
                <div class="info-label">🔴 Yuqori xavf</div>
                <div class="info-value">{{ $redCases }}</div>
            </div>
            <div class="info-icon"><i class="bi bi-exclamation-octagon"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="info-card orange">
            <div class="info-content">
                <div class="info-label">🟡 O'rta xavf</div>
                <div class="info-value">{{ $yellowCases }}</div>
            </div>
            <div class="info-icon"><i class="bi bi-exclamation-triangle"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="info-card green">
            <div class="info-content">
                <div class="info-label">🟢 Past xavf</div>
                <div class="info-value">{{ $greenCases }}</div>
            </div>
            <div class="info-icon"><i class="bi bi-shield-check"></i></div>
        </div>
    </div>
</div>

{{-- ==================== DAVOMAT ==================== --}}
<div class="row g-3 mb-3">
    <div class="col-lg-3 col-md-6">
        <div class="info-card green">
            <div class="info-content">
                <div class="info-label">✅ Bugun keldi</div>
                <div class="info-value">{{ $todayStats['present'] }}</div>
            </div>
            <div class="info-icon"><i class="bi bi-person-check"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="info-card red">
            <div class="info-content">
                <div class="info-label">❌ Bugun kelmadi</div>
                <div class="info-value">{{ $todayStats['absent'] }}</div>
            </div>
            <div class="info-icon"><i class="bi bi-person-x"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="info-card orange">
            <div class="info-content">
                <div class="info-label">⏰ Kechikdi</div>
                <div class="info-value">{{ $todayStats['late'] }}</div>
            </div>
            <div class="info-icon"><i class="bi bi-clock-history"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="info-card navy">
            <div class="info-content">
                <div class="info-label">👥 Jami o'quvchilar</div>
                <div class="info-value">{{ $totalStudents }}</div>
            </div>
            <div class="info-icon"><i class="bi bi-people"></i></div>
        </div>
    </div>
</div>

{{-- ==================== GRAFIK ==================== --}}
<div class="card mb-3">
    <div class="card-body">
        <h5 class="card-title mb-3">
            <i class="bi bi-graph-up text-success"></i>
            Davomat dinamikasi (oxirgi {{ $period }} kun)
        </h5>
        <div style="position:relative;height:340px;">
            <canvas id="dailyChart"></canvas>
        </div>
    </div>
</div>

{{-- ==================== 2 USTUN ==================== --}}
<div class="row g-3 mb-3">
    {{-- Sinf bo'yicha --}}
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title mb-3">
                    <i class="bi bi-bar-chart text-success"></i>
                    Sinf bo'yicha (Top 10)
                </h5>
                @if($byClass->count())
                    @foreach($byClass as $row)
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="fw-bold">{{ $row->class_name }}</span>
                                <span class="text-muted">{{ $row->cnt }} ta</span>
                            </div>
                            <div style="background:#eef1f5;border-radius:6px;height:8px;overflow:hidden;">
                                <div style="background:linear-gradient(90deg,var(--green),var(--green-dark));
                                            height:100%;width:{{ min(100, $row->cnt * 10) }}%;
                                            border-radius:6px;transition:width .6s ease;"></div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <p class="text-muted text-center py-4">Ma'lumot yo'q</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Xavf turlari --}}
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title mb-3">
                    <i class="bi bi-pie-chart text-success"></i>
                    Xavf turlari
                </h5>
                @if($byType->count())
                    <div style="position:relative;height:280px;">
                        <canvas id="typeChart"></canvas>
                    </div>
                @else
                    <p class="text-muted text-center py-4">Ma'lumot yo'q</p>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ==================== 2 USTUN ==================== --}}
<div class="row g-3">
    {{-- Eng ko'p kelmagan --}}
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title mb-3">
                    <i class="bi bi-person-x text-danger"></i>
                    Eng ko'p kelmagan o'quvchilar
                </h5>
                @if($topAbsent->count())
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr><th>#</th><th>F.I.Sh.</th><th>Sinf</th><th>Kelmagan</th></tr>
                        </thead>
                        <tbody>
                        @foreach($topAbsent as $i => $s)
                            <tr>
                                <td class="text-muted">{{ $i + 1 }}</td>
                                <td><strong>{{ $s->full_name }}</strong></td>
                                <td><span class="badge bg-navy">{{ $s->class_name }}</span></td>
                                <td>
                                    @if($s->absences_count >= 5)
                                        <span class="badge bg-red">🚨 {{ $s->absences_count }} kun</span>
                                    @elseif($s->absences_count >= 3)
                                        <span class="badge bg-yellow">⚠️ {{ $s->absences_count }} kun</span>
                                    @else
                                        <span class="badge bg-navy">{{ $s->absences_count }} kun</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-muted text-center py-4">Ma'lumot yo'q</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Oylik statistika --}}
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title mb-3">
                    <i class="bi bi-calendar-month text-success"></i>
                    Oylik dinamika
                </h5>
                @if($byMonth->count())
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr><th>Oy</th><th>Holatlar</th><th>Dinamika</th></tr>
                        </thead>
                        <tbody>
                        @foreach($byMonth as $row)
                            <tr>
                                <td><strong>{{ $row->month }}</strong></td>
                                <td>{{ $row->cnt }} ta</td>
                                <td>
                                    <div style="background:#eef1f5;border-radius:6px;height:8px;overflow:hidden;min-width:80px;">
                                        <div style="background:linear-gradient(90deg,var(--navy),var(--navy-light));
                                                    height:100%;width:{{ min(100, $row->cnt * 20) }}%;"></div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-muted text-center py-4">Ma'lumot yo'q</p>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    // ==================== KUNLIK DINAMIKA ====================
    const dailyCtx = document.getElementById('dailyChart');
    if (dailyCtx) {
        new Chart(dailyCtx, {
            type: 'line',
            data: {
                labels: @json($chartData['labels']),
                datasets: [
                    {
                        label: '✅ Keldi',
                        data: @json($chartData['present']),
                        borderColor: '#2A9D8F',
                        backgroundColor: 'rgba(42,157,143,.1)',
                        borderWidth: 3,
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#2A9D8F',
                        pointRadius: 4,
                        pointHoverRadius: 6,
                    },
                    {
                        label: '❌ Kelmadi',
                        data: @json($chartData['absent']),
                        borderColor: '#E63946',
                        backgroundColor: 'rgba(230,57,70,.1)',
                        borderWidth: 3,
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#E63946',
                        pointRadius: 4,
                        pointHoverRadius: 6,
                    },
                    {
                        label: '⚠️ Holatlar',
                        data: @json($chartData['cases']),
                        borderColor: '#F4A261',
                        backgroundColor: 'rgba(244,162,97,.1)',
                        borderWidth: 2,
                        borderDash: [5, 5],
                        tension: 0.4,
                        fill: false,
                        pointBackgroundColor: '#F4A261',
                        pointRadius: 3,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { intersect: false, mode: 'index' },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: { usePointStyle: true, padding: 16, font: { size: 13, weight: '600' } }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(10,37,64,.95)',
                        padding: 12,
                        titleFont: { size: 14, weight: '700' },
                        bodyFont: { size: 13 },
                        cornerRadius: 8,
                        displayColors: true,
                    },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(10,37,64,.05)' },
                        ticks: { font: { size: 12 }, color: '#899bbd' },
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 11 }, color: '#899bbd', maxRotation: 0 },
                    },
                },
            },
        });
    }

    // ==================== XAVF TURLARI (DONUT) ====================
    const typeCtx = document.getElementById('typeChart');
    if (typeCtx) {
        const typeLabels = {
            'tobacco': '🚬 Tamaki',
            'alcohol': '🍷 Spirtli',
            'drugs': '💊 Giyohvandlik',
            'crime': '⚖️ Jinoyat',
            'aggression': '😠 Agressiya',
            'depression': '😔 Depressiya',
            'absenteeism': '🚪 Darsdan qochish',
            'other': 'Boshqa',
        };

        const labels = @json($byType->pluck('risk_type')->map(fn($t) => $typeLabels[$t] ?? $t));
        const data = @json($byType->pluck('cnt'));

        new Chart(typeCtx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: [
                        '#2A9D8F', '#E63946', '#F4A261', '#0A2540',
                        '#7c3aed', '#4ecdc4', '#ffbe80', '#a855f7',
                    ],
                    borderWidth: 0,
                    hoverOffset: 8,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: {
                        position: 'right',
                        labels: { usePointStyle: true, padding: 10, font: { size: 12, weight: '600' } }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(10,37,64,.95)',
                        padding: 12,
                        cornerRadius: 8,
                    },
                },
            },
        });
    }
</script>
@endpush