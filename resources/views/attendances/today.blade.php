@extends('layouts.app')

@section('title', 'Davomat kiritish')
@section('page-title', 'Davomat kiritish')

@section('content')

{{-- ============ HEADER CARD ============ --}}
<div class="card mb-3 anim-card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h4 class="mb-1 fw-bold">
                    <i class="bi bi-clipboard-check text-success title-icon"></i>
                    Davomat kiritisddddh
                </h4>
                <p class="text-muted mb-0 small">
                    {{ $date->translatedFormat('d- F, Y') }} —
                    <strong>{{ Auth::user()->school->name ?? 'Maktab' }}</strong>
                </p>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-primary btn-anim" onclick="openAddStudentModal()">
                    <i class="bi bi-plus-lg"></i> Yangi o'quvchi
                </button>
                <a href="{{ route('attendance.alerts') }}" class="btn btn-danger btn-anim btn-pulse">
                    <i class="bi bi-bell-fill"></i> 3+ kun kelmaganlar
                </a>
            </div>
        </div>

        {{-- FILTRLAR --}}
        <form method="GET" class="row g-2 align-items-end mt-3">
            <div class="col-md-4">
                <label class="form-label">📅 Sana</label>
                <input type="date" name="date" value="{{ $date->format('Y-m-d') }}" class="form-control">
            </div>
            <div class="col-md-4">
                <label class="form-label">🏫 Sinf</label>
                <select name="class" class="form-select">
                    <option value="">— Barcha sinflar —</option>
                    @foreach($classes as $c)
                        <option value="{{ $c }}" {{ $class == $c ? 'selected' : '' }}>{{ $c }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
    <label class="form-label d-block">&nbsp;</label>
    <div class="form-check">
        <input class="form-check-input" type="checkbox" name="only_absent" value="1" 
               id="onlyAbsent" {{ $showOnlyAbsent ? 'checked' : '' }}>
        <label class="form-check-label" for="onlyAbsent">
            Faqat kelmaganlar
        </label>
    </div>
</div>
            <div class="col-md-4">
                <button class="btn btn-navy w-100 btn-anim">
                    <i class="bi bi-search"></i> Ko'rsatish
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ============ STAT CARDS ============ --}}
<div class="row g-3 mb-3">
    <div class="col-lg-3 col-md-6 anim-card" style="--d:.08s">
        <div class="info-card navy">
            <div class="info-content">
                <div class="info-label">Jami o'quvchilar</div>
                <div class="info-value" data-count>{{ $stats['total'] }}</div>
            </div>
            <div class="info-icon">
                <i class="bi bi-people-fill"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 anim-card" style="--d:.16s">
        <div class="info-card green">
            <div class="info-content">
                <div class="info-label">✅ Keldi</div>
                <div class="info-value" data-count>{{ $stats['present'] }}</div>
            </div>
            <div class="info-icon">
                <i class="bi bi-person-check-fill"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 anim-card" style="--d:.24s">
        <div class="info-card red">
            <div class="info-content">
                <div class="info-label">❌ Kelmadi</div>
                <div class="info-value" data-count>{{ $stats['absent'] }}</div>
            </div>
            <div class="info-icon">
                <i class="bi bi-person-x-fill"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 anim-card" style="--d:.32s">
        <div class="info-card orange">
            <div class="info-content">
                <div class="info-label">⏰ Kechikdi / 📝 Sababli</div>
                <div class="info-value" data-count>{{ $stats['late'] + $stats['excused'] }}</div>
            </div>
            <div class="info-icon">
                <i class="bi bi-clock-history"></i>
            </div>
        </div>
    </div>
</div>

{{-- ============ ASOSIY JADVAL ============ --}}
<div class="card anim-card" style="--d:.4s">
    <div class="card-body p-0">
        @if($students->count())
        <form method="POST" action="{{ route('attendance.store') }}" id="attendanceForm">
            @csrf
            <input type="hidden" name="attendance_date" value="{{ $date->format('Y-m-d') }}">

            <div class="table-responsive">
                <table class="table align-middle mb-0 attendance-table">
                    <thead>
                        <tr>
                            <th style="width:50px;">#</th>
                            <th>F.I.Sh.</th>
                            <th style="width:80px;">Sinf</th>
                            <th style="width:360px;">Holat</th>
                            <th style="width:220px;">Sabab</th>
                            <th style="width:130px;">Ogohlantirish</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($students as $i => $s)
                        @php
                            $att = $s->attendances->first();
                            $currentStatus = optional($att)->status ?? 'present';
                            $currentExcused = optional($att)->is_excused ?? false;
                        @endphp
                        <tr data-student="{{ $s->id }}" data-status="{{ $currentStatus }}" class="row-anim" style="--i:{{ $i }}">
                            <td class="text-muted">
                                <span class="row-number">{{ $i + 1 }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar-circle">
                                        {{ strtoupper(substr($s->first_name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <strong>{{ $s->full_name }}</strong>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-navy class-badge">
                                    <i class="bi bi-mortarboard-fill me-1"></i>{{ $s->class_name }}
                                </span>
                            </td>
                            <td>
                                <div class="status-grid">
                                    <label class="status-card status-present {{ $currentStatus=='present'?'active':'' }}">
                                        <input type="radio" name="statuses[{{ $s->id }}]"
                                               value="present"
                                               {{ $currentStatus=='present'?'checked':'' }}
                                               onchange="updateRow({{ $s->id }})">
                                        <div class="status-icon">
                                            <i class="bi bi-check-lg"></i>
                                        </div>
                                        <div class="status-text">Keldi</div>
                                    </label>

                                    <label class="status-card status-absent {{ $currentStatus=='absent'?'active':'' }}">
                                        <input type="radio" name="statuses[{{ $s->id }}]"
                                               value="absent"
                                               {{ $currentStatus=='absent'?'checked':'' }}
                                               onchange="updateRow({{ $s->id }})">
                                        <div class="status-icon">
                                            <i class="bi bi-x-lg"></i>
                                        </div>
                                        <div class="status-text">Kelmadi</div>
                                    </label>

                                    <label class="status-card status-late {{ $currentStatus=='late'?'active':'' }}">
                                        <input type="radio" name="statuses[{{ $s->id }}]"
                                               value="late"
                                               {{ $currentStatus=='late'?'checked':'' }}
                                               onchange="updateRow({{ $s->id }})">
                                        <div class="status-icon">
                                            <i class="bi bi-clock"></i>
                                        </div>
                                        <div class="status-text">Kechikdi</div>
                                    </label>

                                    <label class="status-card status-excused {{ $currentStatus=='excused'?'active':'' }}">
                                        <input type="radio" name="statuses[{{ $s->id }}]"
                                               value="excused"
                                               {{ $currentStatus=='excused'?'checked':'' }}
                                               onchange="updateRow({{ $s->id }})">
                                        <div class="status-icon">
                                            <i class="bi bi-file-text"></i>
                                        </div>
                                        <div class="status-text">Sababli</div>
                                    </label>
                                </div>

                                {{-- Sababli/Sababsiz tanlash --}}
                                <div class="excused-toggle mt-2" id="excused-{{ $s->id }}"
                                     style="{{ in_array($currentStatus,['absent','excused'])?'':'display:none;' }}">
                                    <div class="excused-pills">
                                        <label class="excused-pill {{ !$currentExcused?'active':'' }}">
                                            <input type="radio" name="is_excused[{{ $s->id }}]" value="0"
                                                   {{ !$currentExcused?'checked':'' }}>
                                            <i class="bi bi-x-circle"></i>
                                            <span>Sababsiz</span>
                                        </label>
                                        <label class="excused-pill {{ $currentExcused?'active':'' }}">
                                            <input type="radio" name="is_excused[{{ $s->id }}]" value="1"
                                                   {{ $currentExcused?'checked':'' }}>
                                            <i class="bi bi-check-circle"></i>
                                            <span>Sababli (uzrli)</span>
                                        </label>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="reason-wrapper">
                                    <i class="bi bi-pencil-square reason-icon"></i>
                                    <input type="text"
                                           name="reasons[{{ $s->id }}]"
                                           value="{{ optional($att)->reason }}"
                                           placeholder="Kasallik, oilaviy..."
                                           class="form-control form-control-sm"
                                           id="reason-{{ $s->id }}"
                                           {{ in_array($currentStatus,['absent','excused'])?'':'disabled' }}>
                                </div>
                            </td>
                            <td>
                                @php $cons = $s->consecutiveAbsences(); @endphp
                                @if($cons >= 5)
                                    <span class="badge bg-red badge-alert">
                                        <i class="bi bi-exclamation-triangle-fill me-1"></i>{{ $cons }} kun
                                    </span>
                                @elseif($cons >= 3)
                                    <span class="badge bg-yellow badge-alert">
                                        <i class="bi bi-exclamation-circle-fill me-1"></i>{{ $cons }} kun
                                    </span>
                                @elseif($cons > 0)
                                    <span class="badge bg-navy">
                                        <i class="bi bi-info-circle-fill me-1"></i>{{ $cons }} kun
                                    </span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            {{-- PASTKI TUGMALAR --}}
            <div class="card-body d-flex gap-2 flex-wrap align-items-center border-top">
                <button type="button" class="btn btn-navy btn-anim" onclick="markAll('present')">
                    <i class="bi bi-check-all"></i> Hammasi keldi
                </button>
                <button type="button" class="btn btn-warn btn-anim" onclick="markAll('late')">
                    <i class="bi bi-clock"></i> Hammasi kechikdi
                </button>
                <button type="submit" class="btn btn-primary btn-anim btn-save ms-auto px-4">
                    <i class="bi bi-save"></i> <span class="save-label">Saqlash</span>
                </button>
            </div>
        </form>
        @else
            <div class="text-center py-5 text-muted">
                <i class="bi bi-inbox empty-icon" style="font-size:48px;opacity:.3;"></i>
                <p class="mt-2 mb-0">Bu filtr bo'yicha o'quvchi topilmadi.</p>
            </div>
        @endif
    </div>
</div>

{{-- ==================== MODAL: YANGI O'QUVCHI ==================== --}}
<div class="modal fade" id="addStudentModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius:14px;border:none;overflow:hidden;">

            {{-- HEADER --}}
            <div class="modal-header" style="background:linear-gradient(135deg,var(--navy),var(--navy-light));color:#fff;border:none;">
                <h5 class="modal-title">
                    <i class="bi bi-person-plus-fill me-2"></i>
                    Yangi o'quvchi qo'shish
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            {{-- FORM --}}
            <form method="POST" action="{{ route('students.store') }}">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3 modal-stagger">

                        {{-- O'QUVCHI MA'LUMOTLARI --}}
                        <div class="col-md-6">
                            <label class="form-label">Familiya <span class="text-danger">*</span></label>
                            <input type="text" name="last_name" required class="form-control"
                                   placeholder="Tursunov" value="{{ old('last_name') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Ism <span class="text-danger">*</span></label>
                            <input type="text" name="first_name" required class="form-control"
                                   placeholder="Aziz" value="{{ old('first_name') }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Otasining ismi</label>
                            <input type="text" name="middle_name" class="form-control"
                                   placeholder="Baxtiyorovich" value="{{ old('middle_name') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Sinf <span class="text-danger">*</span></label>
                            <input type="text" name="class_name" required class="form-control"
                                   list="classList" placeholder="9-A" value="{{ old('class_name') }}">
                            <datalist id="classList">
                                @foreach($classes as $c)
                                    <option value="{{ $c }}">
                                @endforeach
                            </datalist>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Tug'ilgan sana</label>
                            <input type="date" name="birth_date" class="form-control" value="{{ old('birth_date') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Jinsi</label>
                            <select name="gender" class="form-select">
                                <option value="">— Tanlash —</option>
                                <option value="male"   {{ old('gender')=='male'?'selected':'' }}>👦 O'g'il</option>
                                <option value="female" {{ old('gender')=='female'?'selected':'' }}>👧 Qiz</option>
                            </select>
                        </div>

                        {{-- OTA-ONA MA'LUMOTLARI --}}
                        <div class="col-12 mt-3">
                            <div class="divider-title">
                                <i class="bi bi-people-fill"></i>
                                Ota-ona ma'lumotlari
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Ota-ona F.I.Sh.</label>
                            <input type="text" name="parent_name" class="form-control"
                                   placeholder="Tursunova Zilola" value="{{ old('parent_name') }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">
                                Ota-ona email <span class="text-danger">*</span>
                            </label>
                            <input type="email" name="parent_email" required
                                   class="form-control @error('parent_email') is-invalid @enderror"
                                   placeholder="ota.ona@gmail.com"
                                   value="{{ old('parent_email') }}">
                            @error('parent_email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">
                                Telefon raqami <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="parent_phone" required
                                   class="form-control @error('parent_phone') is-invalid @enderror"
                                   placeholder="+998 90 123 45 67"
                                   value="{{ old('parent_phone') }}">
                            @error('parent_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- FOOTER --}}
                <div class="modal-footer border-top-0 pt-0 px-4 pb-4">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Bekor qilish</button>
                    <button type="submit" class="btn btn-primary btn-anim px-4">
                        <i class="bi bi-save"></i> Saqlash
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* ==================== ANIMATSIYA KEYFRAMES ==================== */
    @keyframes riseIn {
        from { opacity: 0; transform: translateY(18px) scale(.985); }
        to   { opacity: 1; transform: none; }
    }
    @keyframes rowIn {
        from { opacity: 0; transform: translateX(-14px); }
        to   { opacity: 1; transform: none; }
    }
    @keyframes popIcon {
        0%   { transform: scale(.6) rotate(-12deg); }
        55%  { transform: scale(1.28) rotate(6deg); }
        100% { transform: scale(1.08) rotate(0); }
    }
    @keyframes ripple {
        from { transform: scale(0); opacity: .45; }
        to   { transform: scale(3.2); opacity: 0; }
    }
    @keyframes slideOpen {
        from { opacity: 0; transform: translateY(-8px) scaleY(.85); }
        to   { opacity: 1; transform: none; }
    }
    @keyframes rowFlash {
        0%   { background-color: rgba(42,157,143,.16); }
        100% { background-color: transparent; }
    }
    @keyframes softPulse {
        0%, 100% { box-shadow: 0 0 0 0 rgba(230,57,70,.45); }
        50%      { box-shadow: 0 0 0 9px rgba(230,57,70,0); }
    }
    @keyframes alertWiggle {
        0%, 88%, 100% { transform: rotate(0); }
        91% { transform: rotate(-7deg); }
        94% { transform: rotate(7deg); }
        97% { transform: rotate(-4deg); }
    }
    @keyframes spin { to { transform: rotate(360deg); } }
    @keyframes floaty {
        0%, 100% { transform: translateY(0); }
        50%      { transform: translateY(-7px); }
    }
    @keyframes fieldIn {
        from { opacity: 0; transform: translateY(12px); }
        to   { opacity: 1; transform: none; }
    }

    /* ==================== SAHIFA KIRISH ==================== */
    .anim-card {
        opacity: 0;
        animation: riseIn .6s cubic-bezier(.16,1,.3,1) forwards;
        animation-delay: var(--d, 0s);
    }
    .row-anim {
        opacity: 0;
        animation: rowIn .5s cubic-bezier(.16,1,.3,1) forwards;
        animation-delay: calc(.45s + var(--i, 0) * 35ms);
    }
    .title-icon { display: inline-block; animation: floaty 3s ease-in-out infinite; }

    /* Stat kartalar */
    .info-card {
        transition: transform .3s cubic-bezier(.16,1,.3,1), box-shadow .3s ease;
        position: relative;
        overflow: hidden;
    }
    .info-card::after {
        content: '';
        position: absolute;
        top: 0; left: -120%;
        width: 60%; height: 100%;
        background: linear-gradient(105deg, transparent, rgba(255,255,255,.28), transparent);
        transform: skewX(-20deg);
        transition: left .7s ease;
        pointer-events: none;
    }
    .info-card:hover { transform: translateY(-5px); box-shadow: 0 14px 30px rgba(10,37,64,.16); }
    .info-card:hover::after { left: 140%; }
    .info-icon { transition: transform .4s cubic-bezier(.34,1.56,.64,1); }
    .info-card:hover .info-icon { transform: scale(1.2) rotate(-8deg); }

    /* Tugmalar */
    .btn-anim {
        position: relative;
        overflow: hidden;
        transition: transform .2s cubic-bezier(.16,1,.3,1), box-shadow .25s ease, filter .2s ease;
    }
    .btn-anim:hover  { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(10,37,64,.2); filter: brightness(1.05); }
    .btn-anim:active { transform: translateY(0) scale(.96); }
    .btn-anim i { display: inline-block; transition: transform .3s cubic-bezier(.34,1.56,.64,1); }
    .btn-anim:hover i { transform: scale(1.2) rotate(-6deg); }
    .btn-pulse { animation: softPulse 2.4s ease-out infinite; }
    .btn-anim .ripple-dot {
        position: absolute;
        width: 40px; height: 40px;
        margin: -20px 0 0 -20px;
        border-radius: 50%;
        background: rgba(255,255,255,.7);
        pointer-events: none;
        animation: ripple .6s ease-out forwards;
    }
    .btn-save.loading { pointer-events: none; opacity: .85; }
    .btn-save.loading i { animation: spin .8s linear infinite; }

    /* ==================== AVATAR ==================== */
    .avatar-circle {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--green), #4ecdc4);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 14px;
        flex-shrink: 0;
        box-shadow: 0 3px 10px rgba(42,157,143,.25);
        transition: transform .35s cubic-bezier(.34,1.56,.64,1), box-shadow .3s ease;
    }
    tr:hover .avatar-circle { transform: scale(1.12) rotate(-6deg); box-shadow: 0 6px 16px rgba(42,157,143,.4); }

    /* ==================== QATOR HOLATI ==================== */
    .table tbody tr { transition: background-color .35s ease, box-shadow .3s ease; position: relative; }
    .table tbody tr:hover { background-color: rgba(10,37,64,.025); }
    .table tbody tr::before {
        content: '';
        position: absolute;
        left: 0; top: 10%;
        width: 4px; height: 80%;
        border-radius: 0 4px 4px 0;
        background: transparent;
        transform: scaleY(.3);
        transition: background .3s ease, transform .35s cubic-bezier(.34,1.56,.64,1);
    }
    .table tbody tr[data-status="absent"]::before  { background: var(--red);    transform: scaleY(1); }
    .table tbody tr[data-status="late"]::before    { background: var(--orange); transform: scaleY(1); }
    .table tbody tr[data-status="excused"]::before { background: var(--navy);   transform: scaleY(1); }
    .table tbody tr[data-status="present"]::before { background: var(--green);  transform: scaleY(1); }
    .table tbody tr.flash { animation: rowFlash .9s ease-out; }

    /* ==================== STATUS KARTALAR ==================== */
    .status-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 8px;
    }

    .status-card {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 4px;
        padding: 10px 6px;
        border: 2px solid #e3e8ef;
        border-radius: 12px;
        cursor: pointer;
        transition: all .25s cubic-bezier(0.16, 1, 0.3, 1);
        background: #fff;
        user-select: none;
        margin: 0;
        position: relative;
        overflow: hidden;
        min-height: 64px;
    }

    .status-card input[type="radio"] {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    .status-card .ripple-dot {
        position: absolute;
        width: 36px; height: 36px;
        margin: -18px 0 0 -18px;
        border-radius: 50%;
        background: currentColor;
        opacity: .3;
        pointer-events: none;
        animation: ripple .55s ease-out forwards;
    }
    .status-card:active { transform: scale(.94); }

    .status-icon {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        transition: all .25s cubic-bezier(0.16, 1, 0.3, 1);
        background: #f1f4f8;
        color: #899bbd;
        font-weight: 700;
    }

    .status-text {
        font-size: 11px;
        font-weight: 700;
        color: #6c757d;
        letter-spacing: .2px;
        transition: color .2s ease, transform .25s ease;
    }

    .status-card:hover:not(.active) {
        border-color: #b8c4d4;
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(10,37,64,.08);
    }
    .status-card:hover:not(.active) .status-icon {
        background: #e3e8ef;
        color: #495057;
        transform: scale(1.1);
    }

    /* ==================== AKTIV HOLATLAR ==================== */
    .status-card.active .status-icon { animation: popIcon .5s cubic-bezier(.34,1.56,.64,1) forwards; }
    .status-card.active .status-text { transform: translateY(1px); }

    .status-present { color: var(--green); }
    .status-absent  { color: var(--red); }
    .status-late    { color: var(--orange); }
    .status-excused { color: var(--navy); }

    .status-present.active {
        border-color: var(--green);
        background: linear-gradient(135deg, #e3f4f1, #fff);
        box-shadow: 0 6px 18px rgba(42,157,143,.25);
    }
    .status-present.active .status-icon {
        background: linear-gradient(135deg, var(--green), var(--green-dark));
        color: #fff;
        box-shadow: 0 4px 12px rgba(42,157,143,.4);
    }
    .status-present.active .status-text { color: var(--green); }

    .status-absent.active {
        border-color: var(--red);
        background: linear-gradient(135deg, #fde8ea, #fff);
        box-shadow: 0 6px 18px rgba(230,57,70,.25);
    }
    .status-absent.active .status-icon {
        background: linear-gradient(135deg, var(--red), #b62d38);
        color: #fff;
        box-shadow: 0 4px 12px rgba(230,57,70,.4);
    }
    .status-absent.active .status-text { color: var(--red); }

    .status-late.active {
        border-color: var(--orange);
        background: linear-gradient(135deg, #fef3e7, #fff);
        box-shadow: 0 6px 18px rgba(244,162,97,.25);
    }
    .status-late.active .status-icon {
        background: linear-gradient(135deg, var(--orange), #e08a45);
        color: #fff;
        box-shadow: 0 4px 12px rgba(244,162,97,.4);
    }
    .status-late.active .status-text { color: #c47a2e; }

    .status-excused.active {
        border-color: var(--navy);
        background: linear-gradient(135deg, #e6ecf3, #fff);
        box-shadow: 0 6px 18px rgba(10,37,64,.25);
    }
    .status-excused.active .status-icon {
        background: linear-gradient(135deg, var(--navy), var(--navy-light));
        color: #fff;
        box-shadow: 0 4px 12px rgba(10,37,64,.4);
    }
    .status-excused.active .status-text { color: var(--navy); }

    /* ==================== SABABLI / SABABSIZ PILLS ==================== */
    .excused-toggle.show-anim { animation: slideOpen .35s cubic-bezier(.16,1,.3,1); transform-origin: top; }

    .excused-pills {
        display: flex;
        gap: 6px;
        padding: 4px;
        background: #f5f7fa;
        border-radius: 10px;
    }

    .excused-pill {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        padding: 7px 10px;
        border-radius: 7px;
        font-size: 12px;
        font-weight: 700;
        color: #6c757d;
        cursor: pointer;
        transition: all .25s cubic-bezier(.16,1,.3,1);
        user-select: none;
        margin: 0;
        background: transparent;
        border: none;
        position: relative;
    }

    .excused-pill input[type="radio"] {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    .excused-pill:hover { background: rgba(255,255,255,.8); color: #495057; }
    .excused-pill:active { transform: scale(.95); }

    .excused-pill.active {
        background: #fff;
        color: var(--navy);
        box-shadow: 0 2px 8px rgba(10,37,64,.1);
        transform: translateY(-1px);
    }
    .excused-pill.active i { animation: popIcon .4s cubic-bezier(.34,1.56,.64,1); display: inline-block; }
    .excused-pill.active:first-child { color: var(--red); }
    .excused-pill.active:last-child  { color: var(--green); }

    /* ==================== SABAB INPUT ==================== */
    .form-control-sm {
        font-size: 13px;
        padding: 8px 12px;
        border-radius: 8px;
        border: 1.5px solid #d8dee6;
        transition: all .25s ease;
    }
    .form-control-sm:focus {
        border-color: var(--green);
        box-shadow: 0 0 0 3px rgba(42,157,143,.12);
        outline: none;
        transform: translateY(-1px);
    }
    .form-control-sm:disabled { background: #f5f7fa; cursor: not-allowed; opacity: .6; }

    /* ==================== OGOHLANTIRISH ==================== */
    .badge-alert { display: inline-block; animation: alertWiggle 3.5s ease-in-out infinite; }

    /* ==================== JADVAL ==================== */
    .table td { vertical-align: middle; padding: 16px 12px; }
    .empty-icon { display: inline-block; animation: floaty 3s ease-in-out infinite; }

    /* ==================== MODAL ==================== */
    #addStudentModal.fade .modal-dialog {
        transform: translateY(30px) scale(.92);
        transition: transform .45s cubic-bezier(.34,1.56,.64,1), opacity .3s ease;
    }
    #addStudentModal.show .modal-dialog { transform: none; }
    #addStudentModal.show .modal-stagger > div {
        opacity: 0;
        animation: fieldIn .45s cubic-bezier(.16,1,.3,1) forwards;
    }
    #addStudentModal.show .modal-stagger > div:nth-child(1) { animation-delay: .10s; }
    #addStudentModal.show .modal-stagger > div:nth-child(2) { animation-delay: .15s; }
    #addStudentModal.show .modal-stagger > div:nth-child(3) { animation-delay: .20s; }
    #addStudentModal.show .modal-stagger > div:nth-child(4) { animation-delay: .25s; }
    #addStudentModal.show .modal-stagger > div:nth-child(5) { animation-delay: .30s; }
    #addStudentModal.show .modal-stagger > div:nth-child(6) { animation-delay: .35s; }
    #addStudentModal.show .modal-stagger > div:nth-child(n+7) { animation-delay: .42s; }
    #addStudentModal .form-control,
    #addStudentModal .form-select { transition: border-color .25s ease, box-shadow .25s ease, transform .25s ease; }
    #addStudentModal .form-control:focus,
    #addStudentModal .form-select:focus { transform: translateY(-1px); }

    /* ==================== MOBIL ==================== */
    @media (max-width: 900px) {
        .status-grid { grid-template-columns: repeat(2, 1fr); }
        .status-card { min-height: 56px; padding: 8px 4px; }
        .status-icon { width: 24px; height: 24px; font-size: 12px; }
        .status-text { font-size: 10px; }
    }

    /* Harakatni kamaytirishni xohlaganlar uchun */
    @media (prefers-reduced-motion: reduce) {
        *, *::before, *::after {
            animation-duration: .01ms !important;
            animation-iteration-count: 1 !important;
            transition-duration: .01ms !important;
        }
        .anim-card, .row-anim { opacity: 1; }
    }
    /* ==================== JADVAL QO'SHIMCHA CHIROYLI ==================== */
.attendance-table thead th {
    background: linear-gradient(135deg, var(--navy) 0%, var(--navy-light) 100%);
    color: #fff;
    font-weight: 600;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: .5px;
    padding: 14px 12px;
    border: none;
    position: relative;
}
.attendance-table thead th:first-child { border-radius: 10px 0 0 0; }
.attendance-table thead th:last-child  { border-radius: 0 10px 0 0; }
.attendance-table thead th::after {
    content: '';
    position: absolute;
    bottom: 0; left: 12px; right: 12px;
    height: 2px;
    background: rgba(255,255,255,.15);
    border-radius: 2px;
}

.row-number {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 26px;
    height: 26px;
    border-radius: 8px;
    background: #f0f3f8;
    color: #6c757d;
    font-size: 12px;
    font-weight: 700;
    transition: all .3s ease;
}
tr:hover .row-number {
    background: var(--navy);
    color: #fff;
    transform: scale(1.1);
}

.class-badge {
    display: inline-flex;
    align-items: center;
    padding: 5px 10px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 600;
    letter-spacing: .2px;
    background: linear-gradient(135deg, var(--navy), var(--navy-light));
    box-shadow: 0 2px 8px rgba(10,37,64,.2);
    transition: transform .3s cubic-bezier(.34,1.56,.64,1), box-shadow .3s ease;
}
tr:hover .class-badge {
    transform: translateY(-2px) scale(1.05);
    box-shadow: 0 6px 14px rgba(10,37,64,.3);
}
.class-badge i { font-size: 11px; }

/* Sabab input wrapper */
.reason-wrapper {
    position: relative;
    display: flex;
    align-items: center;
}
.reason-wrapper .reason-icon {
    position: absolute;
    left: 10px;
    color: #adb5bd;
    font-size: 13px;
    pointer-events: none;
    transition: color .25s ease, transform .25s ease;
    z-index: 2;
}
.reason-wrapper .form-control-sm {
    padding-left: 32px;
}
.reason-wrapper .form-control-sm:focus ~ .reason-icon,
.reason-wrapper:focus-within .reason-icon {
    color: var(--green);
    transform: scale(1.15);
}
.reason-wrapper .form-control-sm:disabled ~ .reason-icon {
    opacity: .4;
}

/* Badge lar chiroyli */
.badge-alert i { font-size: 11px; }
.badge.bg-red, .badge.bg-yellow, .badge.bg-navy {
    padding: 6px 10px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    transition: transform .3s cubic-bezier(.34,1.56,.64,1), box-shadow .3s ease;
}
tr:hover .badge.bg-red,
tr:hover .badge.bg-yellow,
tr:hover .badge.bg-navy {
    transform: translateY(-2px) scale(1.05);
    box-shadow: 0 5px 12px rgba(0,0,0,.15);
}
.badge.bg-red    { background: linear-gradient(135deg, #e63946, #b62d38); box-shadow: 0 3px 10px rgba(230,57,70,.3); }
.badge.bg-yellow { background: linear-gradient(135deg, #f4a261, #e08a45); box-shadow: 0 3px 10px rgba(244,162,97,.3); }
.badge.bg-navy   { background: linear-gradient(135deg, var(--navy), var(--navy-light)); box-shadow: 0 3px 10px rgba(10,37,64,.25); }

/* Jadval qatorlari */
.attendance-table tbody tr {
    border-bottom: 1px solid #f0f3f8;
}
.attendance-table tbody tr:last-child { border-bottom: none; }
.attendance-table tbody tr:hover {
    background: linear-gradient(90deg, rgba(42,157,143,.04), rgba(10,37,64,.02));
}
</style>
@endpush

@push('scripts')
<script>
    function updateRow(studentId) {
        const radios = document.querySelectorAll(`input[name="statuses[${studentId}]"]`);
        let value = null;
        radios.forEach(r => { if (r.checked) value = r.value; });

        const excusedDiv = document.getElementById(`excused-${studentId}`);
        const reasonInput = document.getElementById(`reason-${studentId}`);
        const row = document.querySelector(`tr[data-student="${studentId}"]`);

        // Aktiv klasslarni yangilash
        row.querySelectorAll('.status-card').forEach(btn => btn.classList.remove('active'));
        const activeInput = row.querySelector(`.status-card input[value="${value}"]`);
        if (activeInput) {
            const card = activeInput.closest('.status-card');
            card.classList.add('active');
            addRipple(card);
        }

        // Qator rangi va yonib o'chish effekti
        row.dataset.status = value;
        row.classList.remove('flash');
        void row.offsetWidth;
        row.classList.add('flash');

        // Sabab maydoni faollashtirish
        if (value === 'absent' || value === 'excused') {
            const wasHidden = excusedDiv.style.display === 'none';
            excusedDiv.style.display = 'block';
            reasonInput.disabled = false;
            if (wasHidden) {
                excusedDiv.classList.remove('show-anim');
                void excusedDiv.offsetWidth;
                excusedDiv.classList.add('show-anim');
                reasonInput.focus({ preventScroll: true });
            }

            // Sababsiz/Sababli pillarni yangilash
            excusedDiv.querySelectorAll('.excused-pill').forEach(pill => pill.classList.remove('active'));
            const checkedPill = excusedDiv.querySelector('input[type="radio"]:checked');
            if (checkedPill) checkedPill.closest('.excused-pill').classList.add('active');
        } else {
            excusedDiv.style.display = 'none';
            reasonInput.disabled = true;
            reasonInput.value = '';
        }
    }

    // Ripple (to'lqin) effekti
    function addRipple(el, e) {
        const rect = el.getBoundingClientRect();
        const dot = document.createElement('span');
        dot.className = 'ripple-dot';
        dot.style.left = (e ? e.clientX - rect.left : rect.width / 2) + 'px';
        dot.style.top  = (e ? e.clientY - rect.top  : rect.height / 2) + 'px';
        el.appendChild(dot);
        setTimeout(() => dot.remove(), 650);
    }

    document.querySelectorAll('.btn-anim').forEach(btn => {
        btn.addEventListener('click', e => addRipple(btn, e));
    });

    // Sababli/Sababsiz pillarni bosganda yangilash
    document.querySelectorAll('.excused-pill input[type="radio"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const parent = this.closest('.excused-pills');
            parent.querySelectorAll('.excused-pill').forEach(p => p.classList.remove('active'));
            this.closest('.excused-pill').classList.add('active');
        });
    });

    function markAll(status) {
        let n = 0;
        document.querySelectorAll('input[type="radio"][name^="statuses"]').forEach(r => {
            if (r.value === status) {
                r.checked = true;
                const studentId = r.name.match(/\[(\d+)\]/)[1];
                // Qatorlar birin-ketin "to'lqin" bo'lib o'zgaradi
                setTimeout(() => updateRow(studentId), Math.min(n * 25, 700));
                n++;
            }
        });
    }

    // Statistik raqamlar 0 dan sanab chiqadi
    document.querySelectorAll('[data-count]').forEach(el => {
        const target = parseInt(el.textContent, 10);
        if (isNaN(target) || target === 0) return;
        const duration = 900;
        const start = performance.now();
        el.textContent = '0';
        (function tick(now) {
            const p = Math.min((now - start) / duration, 1);
            el.textContent = Math.round(target * (1 - Math.pow(1 - p, 3)));
            if (p < 1) requestAnimationFrame(tick);
        })(start);
    });

    // Saqlash tugmasi: yuklanish holati
    const attForm = document.getElementById('attendanceForm');
    if (attForm) {
        attForm.addEventListener('submit', function () {
            const btn = attForm.querySelector('.btn-save');
            btn.classList.add('loading');
            btn.querySelector('i').className = 'bi bi-arrow-repeat';
            btn.querySelector('.save-label').textContent = 'Saqlanmoqda...';
        });
    }

    function openAddStudentModal() {
        const modalEl = document.getElementById('addStudentModal');
        if (modalEl && typeof bootstrap !== 'undefined') {
            const modal = new bootstrap.Modal(modalEl);
            modal.show();
        } else {
            console.error('Modal yoki Bootstrap topilmadi!');
        }
    }

    @if($errors->any())
        document.addEventListener('DOMContentLoaded', function() {
            openAddStudentModal();
        });
    @endif
</script>
@endpush