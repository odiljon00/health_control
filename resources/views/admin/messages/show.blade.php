@extends('layouts.app')

@section('title', 'Xabar: ' . $message->subject)
@section('page-title', 'Xabar tafsiloti')

@section('content')

<div class="row justify-content-center">
    <div class="col-lg-9">
        
        {{-- Header --}}
        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar-circle-lg">
                            {{ strtoupper(substr($message->name, 0, 1)) }}
                        </div>
                        <div>
                            <h4 class="mb-1 fw-bold">{{ $message->name }}</h4>
                            <p class="text-muted mb-0 small">
                                <i class="bi bi-envelope"></i> {{ $message->email }}
                                @if($message->phone)
                                    &nbsp;•&nbsp; <i class="bi bi-telephone"></i> {{ $message->phone }}
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="text-end">
                        @php $badge = $message->status_badge; @endphp
                        <span class="badge bg-{{ $badge[2] }}" style="font-size:13px;padding:6px 14px;">
                            {{ $badge[0] }} {{ $badge[1] }}
                        </span>
                        @if($message->is_starred)
                            <div class="mt-1"><i class="bi bi-star-fill" style="color:#F4A261;"></i> Belgilangan</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Xabar matni --}}
        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between mb-3">
                    <h5 class="fw-bold mb-0">
                        <i class="bi bi-chat-left-text text-success"></i>
                        {{ $message->subject ?? 'Mavzusiz' }}
                    </h5>
                    <small class="text-muted">{{ $message->created_at->format('d.m.Y H:i') }}</small>
                </div>
                <div style="background:#f5f7fa;padding:20px;border-radius:12px;border-left:4px solid var(--green);">
                    {!! nl2br(e($message->message)) !!}
                </div>
            </div>
        </div>

        {{-- Amallar --}}
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="fw-bold mb-3">
                    <i class="bi bi-gear text-success"></i>
                    Amallar
                </h5>

                <div class="row g-3">
                    {{-- Holatni yangilash --}}
                    <div class="col-md-6">
                        <form method="POST" action="{{ route('admin.messages.status', $message) }}">
                            @csrf @method('PATCH')
                            <label class="form-label">Holatni o'zgartirish</label>
                            <div class="d-flex gap-2">
                                <select name="status" class="form-select">
                                    <option value="new"      {{ $message->status == 'new'      ? 'selected' : '' }}>🆕 Yangi</option>
                                    <option value="read"     {{ $message->status == 'read'     ? 'selected' : '' }}>👁️ O'qilgan</option>
                                    <option value="replied"  {{ $message->status == 'replied'  ? 'selected' : '' }}>✅ Javob berilgan</option>
                                    <option value="archived" {{ $message->status == 'archived' ? 'selected' : '' }}>📦 Arxivlangan</option>
                                </select>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check"></i>
                                </button>
                            </div>
                        </form>
                    </div>

                    {{-- Yulduzcha --}}
                    <div class="col-md-3">
                        <form method="POST" action="{{ route('admin.messages.star', $message) }}">
                            @csrf @method('PATCH')
                            <label class="form-label d-block">&nbsp;</label>
                            <button type="submit" class="btn {{ $message->is_starred ? 'btn-warn' : 'btn-light' }} w-100">
                                <i class="bi bi-star{{ $message->is_starred ? '-fill' : '' }}"></i>
                                {{ $message->is_starred ? 'Belgidan olish' : 'Belgilash' }}
                            </button>
                        </form>
                    </div>

                    {{-- O'chirish --}}
                    <div class="col-md-3">
                        <form method="POST" action="{{ route('admin.messages.destroy', $message) }}"
                              onsubmit="return confirm('Rostdan ham o\'chirmoqchimisiz?');">
                            @csrf @method('DELETE')
                            <label class="form-label d-block">&nbsp;</label>
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="bi bi-trash"></i> O'chirish
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Admin izohi --}}
                <form method="POST" action="{{ route('admin.messages.note', $message) }}" class="mt-3">
                    @csrf @method('PATCH')
                    <label class="form-label">📝 Admin izohi</label>
                    <textarea name="admin_note" class="form-control" rows="3"
                              placeholder="Ichki izoh yoki eslatma...">{{ $message->admin_note }}</textarea>
                    <div class="mt-2 text-end">
                        <button type="submit" class="btn btn-navy btn-sm">
                            <i class="bi bi-save"></i> Izohni saqlash
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Javob yozish --}}
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="fw-bold mb-3">
                    <i class="bi bi-reply-fill text-success"></i>
                    Javob yozish
                </h5>
                <a href="mailto:{{ $message->email }}?subject=Re: {{ urlencode($message->subject ?? 'Xabar') }}"
                   class="btn btn-primary">
                    <i class="bi bi-envelope-fill"></i> {{ $message->email }} ga email yozish
                </a>
            </div>
        </div>

        {{-- Metadata --}}
        <div class="card mb-3">
            <div class="card-body">
                <h6 class="fw-bold mb-3">ℹ️ Qo'shimcha ma'lumot</h6>
                <table class="table table-sm table-borderless">
                    <tr><th style="width:200px;">IP manzil</th><td>{{ $message->ip_address ?? '—' }}</td></tr>
                    <tr><th>Brauzer</th><td class="small">{{ Str::limit($message->user_agent, 100) ?? '—' }}</td></tr>
                    <tr><th>Yuborilgan</th><td>{{ $message->created_at->format('d.m.Y H:i:s') }}</td></tr>
                    @if($message->read_at)
                        <tr><th>O'qilgan</th><td>{{ $message->read_at->format('d.m.Y H:i') }}</td></tr>
                    @endif
                    @if($message->replied_at)
                        <tr><th>Javob berilgan</th><td>{{ $message->replied_at->format('d.m.Y H:i') }}</td></tr>
                    @endif
                </table>
            </div>
        </div>

        <div class="mb-4">
            <a href="{{ route('admin.messages.index') }}" class="btn btn-navy">
                <i class="bi bi-arrow-left"></i> Orqaga
            </a>
        </div>

    </div>
</div>

@endsection

@push('styles')
<style>
    .avatar-circle-lg {
        width: 64px;
        height: 64px;
        border-radius: 50%;
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
</style>
@endpush