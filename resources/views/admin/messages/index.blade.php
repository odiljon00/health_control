@extends('layouts.app')

@section('title', 'Xabarlar')
@section('page-title', 'Xabarlar')

@section('content')

{{-- ==================== STATISTIKA ==================== --}}
<div class="row g-3 mb-3">
    <div class="col-lg-2 col-md-4 col-6">
        <div class="info-card navy">
            <div class="info-content">
                <div class="info-label">Jami</div>
                <div class="info-value">{{ $stats['total'] }}</div>
            </div>
            <div class="info-icon"><i class="bi bi-envelope"></i></div>
        </div>
    </div>
    <div class="col-lg-2 col-md-4 col-6">
        <div class="info-card red">
            <div class="info-content">
                <div class="info-label">🆕 Yangi</div>
                <div class="info-value">{{ $stats['new'] }}</div>
            </div>
            <div class="info-icon"><i class="bi bi-envelope-exclamation"></i></div>
        </div>
    </div>
    <div class="col-lg-2 col-md-4 col-6">
        <div class="info-card orange">
            <div class="info-content">
                <div class="info-label">👁️ O'qilgan</div>
                <div class="info-value">{{ $stats['read'] }}</div>
            </div>
            <div class="info-icon"><i class="bi bi-envelope-open"></i></div>
        </div>
    </div>
    <div class="col-lg-2 col-md-4 col-6">
        <div class="info-card green">
            <div class="info-content">
                <div class="info-label">✅ Javob</div>
                <div class="info-value">{{ $stats['replied'] }}</div>
            </div>
            <div class="info-icon"><i class="bi bi-reply"></i></div>
        </div>
    </div>
    <div class="col-lg-2 col-md-4 col-6">
        <div class="info-card yellow">
            <div class="info-content">
                <div class="info-label">⭐ Belgilangan</div>
                <div class="info-value">{{ $stats['starred'] }}</div>
            </div>
            <div class="info-icon"><i class="bi bi-star-fill"></i></div>
        </div>
    </div>
    <div class="col-lg-2 col-md-4 col-6">
        <div class="info-card navy">
            <div class="info-content">
                <div class="info-label">📅 Bugun</div>
                <div class="info-value">{{ $stats['today'] }}</div>
            </div>
            <div class="info-icon"><i class="bi bi-calendar-day"></i></div>
        </div>
    </div>
</div>

{{-- ==================== FILTRLAR ==================== --}}
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label">🔍 Qidirish</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                       class="form-control" placeholder="Ism, email yoki matn...">
            </div>
            <div class="col-md-3">
                <label class="form-label">📋 Holat</label>
                <select name="status" class="form-select">
                    <option value="">— Barchasi —</option>
                    <option value="new"      {{ request('status') == 'new'      ? 'selected' : '' }}>🆕 Yangi</option>
                    <option value="read"     {{ request('status') == 'read'     ? 'selected' : '' }}>👁️ O'qilgan</option>
                    <option value="replied"  {{ request('status') == 'replied'  ? 'selected' : '' }}>✅ Javob berilgan</option>
                    <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>📦 Arxiv</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label d-block">&nbsp;</label>
                <div class="form-check">
                    <input type="checkbox" name="starred" value="1" class="form-check-input" 
                           id="starred" {{ request('starred') ? 'checked' : '' }}>
                    <label class="form-check-label" for="starred">⭐ Faqat belgilangan</label>
                </div>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-navy flex-fill">
                    <i class="bi bi-funnel"></i> Filtrlash
                </button>
                <a href="{{ route('admin.messages.index') }}" class="btn btn-light">
                    <i class="bi bi-x-lg"></i>
                </a>
            </div>
        </form>
    </div>
</div>

{{-- ==================== XABARLAR RO'YXATI ==================== --}}
<div class="card">
    <div class="card-body p-0">
        @if($messages->count())
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width:40px;"></th>
                            <th>Kimdan</th>
                            <th>Mavzu</th>
                            <th>Xabar</th>
                            <th style="width:120px;">Holat</th>
                            <th style="width:130px;">Sana</th>
                            <th style="width:110px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($messages as $m)
                        <tr style="{{ $m->status === 'new' ? 'background:#fef8f9;' : '' }}">
                            {{-- Yulduzcha --}}
                            <td>
                                <form method="POST" action="{{ route('admin.messages.star', $m) }}" style="display:inline;">
                                    @csrf @method('PATCH')
                                    <button type="submit" style="background:none;border:none;cursor:pointer;font-size:18px;">
                                        @if($m->is_starred)
                                            <i class="bi bi-star-fill" style="color:#F4A261;"></i>
                                        @else
                                            <i class="bi bi-star" style="color:#ccc;"></i>
                                        @endif
                                    </button>
                                </form>
                            </td>

                            {{-- Kimdan --}}
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar-circle">
                                        {{ strtoupper(substr($m->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <strong>{{ $m->name }}</strong>
                                        <div class="text-muted small">{{ $m->email }}</div>
                                    </div>
                                </div>
                            </td>

                            {{-- Mavzu --}}
                            <td>
                                <span class="badge bg-navy">{{ $m->subject ?? 'Mavzusiz' }}</span>
                            </td>

                            {{-- Xabar (qisqartirilgan) --}}
                            <td>
                                <div style="max-width:300px;">
                                    {{ Str::limit($m->message, 60) }}
                                </div>
                            </td>

                            {{-- Holat --}}
                            <td>
                                @php $badge = $m->status_badge; @endphp
                                <span class="badge bg-{{ $badge[2] }}">{{ $badge[0] }} {{ $badge[1] }}</span>
                            </td>

                            {{-- Sana --}}
                            <td class="text-muted small">
                                {{ $m->created_at->format('d.m.Y') }}<br>
                                {{ $m->created_at->format('H:i') }}
                            </td>

                            {{-- Amallar --}}
                            <td>
                                <a href="{{ route('admin.messages.show', $m) }}" class="btn btn-navy btn-sm">
                                    <i class="bi bi-eye"></i> Ko'rish
                                </a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            @if($messages->hasPages())
                <div class="card-body border-top">
                    {{ $messages->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-5 text-muted">
                <i class="bi bi-envelope-open" style="font-size:56px;opacity:.3;"></i>
                <p class="mt-3 mb-0">Hozircha xabarlar yo'q</p>
            </div>
        @endif
    </div>
</div>

@endsection

@push('styles')
<style>
    .avatar-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--green), #4ecdc4);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 15px;
        flex-shrink: 0;
    }
</style>
@endpush