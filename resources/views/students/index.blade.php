@extends('layouts.app')

@section('title', "O'quvchilar")
@section('page-title', "O'quvchilar")

@section('content')

{{-- ==================== HEADER ==================== --}}
<div class="card mb-3">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h4 class="mb-1 fw-bold">
                    <i class="bi bi-people text-success"></i>
                    O'quvchilar ro'yxati
                </h4>
                <p class="text-muted mb-0 small">
                    Jami: <strong>{{ $students->total() }}</strong> ta o'quvchi
                </p>
            </div>
            <button type="button" class="btn btn-primary btn-lg" onclick="openAddStudentModal()">
                <i class="bi bi-plus-lg"></i> O'quvchi qo'shish
            </button>
        </div>

        {{-- FILTR --}}
        <form method="GET" action="{{ route('students.index') }}" class="row g-2 align-items-end mt-3">
            <div class="col-md-4">
                <label class="form-label">🏫 Sinf</label>
                <select name="class" class="form-select">
                    <option value="">— Barcha sinflar —</option>
                    @php
                        $classes = \App\Student::distinct()->orderBy('class_name')->pluck('class_name');
                    @endphp
                    @foreach($classes as $c)
                        <option value="{{ $c }}" {{ request('class') == $c ? 'selected' : '' }}>{{ $c }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">🔍 Qidirish</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                       class="form-control" placeholder="Ism, familiya...">
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-navy flex-fill">
                    <i class="bi bi-funnel"></i> Filtrlash
                </button>
                <a href="{{ route('students.index') }}" class="btn btn-light">
                    <i class="bi bi-x-lg"></i>
                </a>
            </div>
        </form>
    </div>
</div>

{{-- ==================== JADVAL ==================== --}}
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
                            <th>Ota-ona</th>
                            <th style="width:180px;">Telefon</th>
                            <th style="width:100px;">Holat</th>
                            <th style="width:120px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($students as $i => $s)
                        <tr>
                            <td class="text-muted">{{ $students->firstItem() + $i }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar-circle">
                                        {{ strtoupper(substr($s->first_name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <strong>{{ $s->full_name }}</strong>
                                        @if($s->birth_date)
                                            <div class="text-muted small">
                                                🎂 {{ $s->birth_date->format('d.m.Y') }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-navy">{{ $s->class_name }}</span>
                            </td>
                            <td>
                                @if($s->parent)
                                    <strong>{{ $s->parent->name }}</strong>
                                    <div class="text-muted small">{{ $s->parent->email }}</div>
                                @else
                                    <span class="text-muted">— Yo'q —</span>
                                @endif
                            </td>
                            <td>
                                @if($s->parent && $s->parent->phone)
                                    <span class="badge bg-green">
                                        <i class="bi bi-telephone"></i> {{ $s->parent->phone }}
                                    </span>
                                @else
                                    <span class="badge bg-red">
                                        <i class="bi bi-telephone-x"></i> Yo'q
                                    </span>
                                @endif
                            </td>
                            <td>
                                @php $cons = $s->consecutiveAbsences(); @endphp
                                @if($cons >= 5)
                                    <span class="badge bg-red">🚨 {{ $cons }} kun</span>
                                @elseif($cons >= 3)
                                    <span class="badge bg-yellow">⚠️ {{ $cons }} kun</span>
                                @elseif($cons > 0)
                                    <span class="badge bg-navy">{{ $cons }} kun</span>
                                @else
                                    <span class="badge bg-green">✅ Yaxshi</span>
                                @endif
                            </td>
                           <td>
    <div class="d-flex gap-1">
        <a href="{{ route('students.show', $s) }}" 
           class="btn btn-navy btn-sm" 
           title="Ko'rish">
            <i class="bi bi-eye"></i>
        </a>
        <button type="button" 
                class="btn btn-warn btn-sm" 
                title="Tahrirlash"
                onclick="openEditModal({{ $s->id }})">
            <i class="bi bi-pencil"></i>
        </button>
        <button type="button" 
                class="btn btn-danger btn-sm" 
                title="O'chirish"
                onclick="confirmDelete({{ $s->id }}, '{{ addslashes($s->full_name) }}')">
            <i class="bi bi-trash"></i>
        </button>
    </div>
</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            @if($students->hasPages())
                <div class="card-body border-top">
                    {{ $students->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-5 text-muted">
                <i class="bi bi-people" style="font-size:64px;opacity:.3;"></i>
                <h5 class="mt-3 mb-2">O'quvchilar topilmadi</h5>
                <p class="text-muted mb-3">Birinchi o'quvchini qo'shing</p>
                <button type="button" class="btn btn-primary" onclick="openAddStudentModal()">
                    <i class="bi bi-plus-lg"></i> O'quvchi qo'shish
                </button>
            </div>
        @endif
    </div>
</div>
{{-- ==================== MODAL: TAHRIRLASH ==================== --}}
<div class="modal fade" id="editStudentModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius:14px;border:none;overflow:hidden;">
            <div class="modal-header" style="background:linear-gradient(135deg,var(--green),var(--green-dark));color:#fff;border:none;">
                <h5 class="modal-title">
                    <i class="bi bi-pencil-square me-2"></i>
                    O'quvchini tahrirlash
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form method="POST" id="editStudentForm">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="row g-3">
                        {{-- O'QUVCHI --}}
                        <div class="col-12">
                            <div class="divider-title">
                                <i class="bi bi-person-badge"></i> O'quvchi ma'lumotlari
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Familiya <span class="text-danger">*</span></label>
                            <input type="text" name="last_name" id="edit_last_name" required class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Ism <span class="text-danger">*</span></label>
                            <input type="text" name="first_name" id="edit_first_name" required class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Otasining ismi</label>
                            <input type="text" name="middle_name" id="edit_middle_name" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Sinf <span class="text-danger">*</span></label>
                            <input type="text" name="class_name" id="edit_class_name" required class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tug'ilgan sana</label>
                            <input type="date" name="birth_date" id="edit_birth_date" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Jinsi</label>
                            <select name="gender" id="edit_gender" class="form-select">
                                <option value="">— Tanlash —</option>
                                <option value="male">👦 O'g'il</option>
                                <option value="female">👧 Qiz</option>
                            </select>
                        </div>

                        {{-- OTA-ONA --}}
                        <div class="col-12 mt-3">
                            <div class="divider-title">
                                <i class="bi bi-people-fill"></i> Ota-ona ma'lumotlari
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Ota-ona F.I.Sh.</label>
                            <input type="text" name="parent_name" id="edit_parent_name" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="parent_email" id="edit_parent_email" required class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">
                                <i class="bi bi-telephone-fill text-success"></i>
                                Telefon <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="parent_phone" id="edit_parent_phone" required class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0 px-4 pb-4">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Bekor qilish</button>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-save"></i> Saqlash
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ==================== O'CHIRISH FORMASI (yashirin) ==================== --}}
<form method="POST" id="deleteStudentForm" style="display:none;">
    @csrf
    @method('DELETE')
</form>

{{-- ==================== MODAL: O'QUVCHI QO'SHISH ==================== --}}
<div class="modal fade" id="addStudentModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius:14px;border:none;overflow:hidden;">
            <div class="modal-header" style="background:linear-gradient(135deg,var(--navy),var(--navy-light));color:#fff;border:none;">
                <h5 class="modal-title">
                    <i class="bi bi-person-plus-fill me-2"></i>
                    Yangi o'quvchi qo'shish
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form method="POST" action="{{ route('students.store') }}" id="addStudentForm">
                @csrf
                <div class="modal-body p-4">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-circle"></i>
                            @foreach($errors->all() as $e)
                                <div>{{ $e }}</div>
                            @endforeach
                        </div>
                    @endif

                    <div class="row g-3">
                        {{-- O'QUVCHI --}}
                        <div class="col-12">
                            <div class="divider-title">
                                <i class="bi bi-person-badge"></i> O'quvchi ma'lumotlari
                            </div>
                        </div>

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

                        {{-- OTA-ONA --}}
                        <div class="col-12 mt-3">
                            <div class="divider-title">
                                <i class="bi bi-people-fill"></i> Ota-ona ma'lumotlari
                                <span class="text-danger small ms-2">(SMS yuborish uchun majburiy)</span>
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
                                <i class="bi bi-telephone-fill text-success"></i>
                                Telefon raqami <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="parent_phone" required
                                   class="form-control @error('parent_phone') is-invalid @enderror"
                                   placeholder="+998 90 123 45 67"
                                   value="{{ old('parent_phone') }}"
                                   id="parentPhone">
                            @error('parent_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">
                                <i class="bi bi-info-circle"></i>
                                Bu raqamga 3+ kun kelmasa SMS yuboriladi
                            </small>
                        </div>

                        <div class="col-12">
                            <div class="alert" style="background:#e3f4f1;border-left:4px solid #2A9D8F;color:#21867a;padding:12px;border-radius:8px;font-size:13px;">
                                <strong>ℹ️ Ma'lumot:</strong> O'quvchi 3 kun ketma-ket darsga kelmasa,
                                ota-onaga avtomatik SMS yuboriladi. 4-5 kunda esa IIO profilaktika xizmatiga
                                ham xabar yuboriladi.
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-top-0 pt-0 px-4 pb-4">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Bekor qilish</button>
                    <button type="submit" class="btn btn-primary px-4">
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

    .divider-title {
        font-size: 13px;
        font-weight: 700;
        color: var(--navy);
        padding-bottom: 8px;
        border-bottom: 1px dashed #d8dee6;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .form-label {
        font-weight: 600;
        font-size: 13px;
        color: #334;
    }

    .form-control, .form-select {
        padding: 10px 14px;
        border-radius: 9px;
        border: 1.5px solid #d8dee6;
        font-size: 14px;
    }

    .form-control:focus, .form-select:focus {
        border-color: var(--green);
        box-shadow: 0 0 0 4px rgba(42,157,143,.12);
    }
</style>
@endpush

@push('scripts')
<script>
    function openAddStudentModal() {
        const modalEl = document.getElementById('addStudentModal');
        if (modalEl && typeof bootstrap !== 'undefined') {
            const modal = new bootstrap.Modal(modalEl);
            modal.show();
        } else {
            console.error('Modal yoki Bootstrap topilmadi!');
        }
    }

    // Xatolik bo'lsa modalni qayta ochish
    @if($errors->any())
        document.addEventListener('DOMContentLoaded', function() {
            openAddStudentModal();
        });
    @endif

    // Telefon raqam formati
    const phoneInput = document.getElementById('parentPhone');
    if (phoneInput) {
        phoneInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.startsWith('998')) {
                value = '+' + value;
            } else if (value.length === 9) {
                value = '+998' + value;
            }
            e.target.value = value;
        });
    }
    // ==================== TAHRIRLASH ====================
function openEditModal(studentId) {
    fetch(`/students/${studentId}/edit`, {
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        }
    })
    .then(res => res.json())
    .then(data => {
        const s = data.student;
        
        // Formani to'ldirish
        document.getElementById('edit_last_name').value  = s.last_name || '';
        document.getElementById('edit_first_name').value = s.first_name || '';
        document.getElementById('edit_middle_name').value= s.middle_name || '';
        document.getElementById('edit_class_name').value = s.class_name || '';
        document.getElementById('edit_birth_date').value = s.birth_date || '';
        document.getElementById('edit_gender').value     = s.gender || '';
        
        if (s.parent) {
            document.getElementById('edit_parent_name').value  = s.parent.name || '';
            document.getElementById('edit_parent_email').value = s.parent.email || '';
            document.getElementById('edit_parent_phone').value = s.parent.phone || '';
        }
        
        // Form action
        document.getElementById('editStudentForm').action = `/students/${studentId}`;
        
        // Modalni ochish
        const modalEl = document.getElementById('editStudentModal');
        const modal = new bootstrap.Modal(modalEl);
        modal.show();
    })
    .catch(err => {
        console.error(err);
        alert('Xato: ma\'lumotlarni yuklab bo\'lmadi');
    });
}

// ==================== O'CHIRISH ====================
function confirmDelete(studentId, studentName) {
    if (!confirm(`"${studentName}" ni o'chirishni tasdiqlaysizmi?\n\nBu amalni qaytarib bo'lmaydi!`)) {
        return;
    }
    
    const form = document.getElementById('deleteStudentForm');
    form.action = `/students/${studentId}`;
    form.submit();
}
</script>
@endpush