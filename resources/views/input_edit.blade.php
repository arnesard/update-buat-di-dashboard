@extends('layouts.app')

@section('title', 'Edit Data Produksi')

@section('content')
<div class="container-fluid py-3">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="glass-card shadow-sm border-0 p-4">
                <div class="d-flex align-items-center mb-4">
                    <a href="{{ route('input.form', $plant) }}"
                        class="btn btn-light border rounded-pill px-3 py-2 me-3 d-inline-flex align-items-center gap-2 shadow-sm">
                        <i data-lucide="arrow-left" size="18"></i>
                        <span class="fw-semibold">Kembali</span>
                    </a>
                    <div>
                        <h4 class="mb-1 fw-bold">Edit Data Produksi</h4>
                        <p class="text-muted mb-0">Perbarui data produksi yang sudah diinput</p>
                    </div>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i data-lucide="alert-triangle" class="me-2" size="20"></i>
                        {{ $errors->first() }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form action="{{ route('input.update', ['plant' => $plant, 'id' => $data->id]) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row g-4">

                        {{-- nama operator --}}
                        <div class="col-12">
                            <label class="form-label small fw-bold text-uppercase text-muted">Nama Operator</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 d-flex align-items-center justify-content-center" style="width: 46px;">
                                    <i data-lucide="user-check" size="18"></i>
                                </span>
                                <select name="employee_id"
                                    class="form-select form-control-custom border-start-0 shadow-none" required>
                                    <option value="">-- Pilih Operator --</option>
                                    @foreach ($employees as $employee)
                                        <option value="{{ $employee->employee_id }}" 
                                            {{ $data->employee_id == $employee->employee_id ? 'selected' : '' }}>
                                            {{ $employee->name }} ({{ $employee->employee_id }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @if ($employees->isEmpty())
                                <small class="text-danger mt-2 d-block">* Tidak ada operator di Plant ini</small>
                            @endif
                        </div>

                        {{-- job hari ini --}}
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-bold text-uppercase text-muted">
                                Pekerjaan Hari Ini <span class="text-danger">*</span>
                            </label>
                            @php
                                $selectedJobs = array_filter(array_map('trim', explode(',', $data->job_today ?? '')));
                                $selectedText = count($selectedJobs) ? implode(', ', $selectedJobs) : '';
                            @endphp
                            <div class="job-dropdown-wrapper" id="job-dropdown-wrapper">
                                <div class="job-dropdown-trigger" id="job-dropdown-trigger" onclick="toggleJobDropdown()">
                                    <span id="job-selected-text" class="{{ $selectedText ? 'selected-text' : 'placeholder' }}">{{ $selectedText ?: 'Pilih' }}</span>
                                    <i data-lucide="chevron-down" size="16" id="job-chevron"></i>
                                </div>
                                <div class="job-dropdown-panel" id="job-dropdown-panel">
                                    @foreach(['Scan','Strapping','Tempel Stiker','Susun Tire','Pressing','Driver','Leader','Pasang Product Tage OE'] as $job)
                                    <label class="job-check-label">
                                        <input type="checkbox"
                                               name="job_today[]"
                                               value="{{ $job }}"
                                               class="job-checkbox form-check-input mt-0"
                                               {{ in_array($job, $selectedJobs) ? 'checked' : '' }}
                                               onchange="updateJobText(); toggleRitase();">
                                        {{ $job }}
                                    </label>
                                    @endforeach
                                    <div id="job-validation-msg" class="text-danger small mt-1" style="display:none;">&#9888; Pilih minimal satu!</div>
                                </div>
                            </div>
                        </div>

                        {{-- Shift Kerja --}}
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-uppercase text-muted">Shift Kerja</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 d-flex align-items-center justify-content-center" style="width: 46px;">
                                    <i data-lucide="clock" size="18"></i>
                                </span>
                                <select name="shift" class="form-select form-control-custom border-start-0 shadow-none"
                                    required>
                                    <option value="">-- Pilih Shift --</option>
                                    <option value="1" {{ $data->shift == '1' ? 'selected' : '' }}>Shift 1 (Pagi)</option>
                                    <option value="2" {{ $data->shift == '2' ? 'selected' : '' }}>Shift 2 (Sore)</option>
                                    <option value="3" {{ $data->shift == '3' ? 'selected' : '' }}>Shift 3 (Malam)</option>
                                </select>
                            </div>
                        </div>

                        {{-- jumalah produksi --}}
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-uppercase text-muted">Jumlah Produksi</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 d-flex align-items-center justify-content-center" style="width: 46px;">
                                    <i data-lucide="layers" size="18"></i>
                                </span>
                                <input type="number" name="production_count"
                                    class="form-control form-control-custom border-start-0 shadow-none"
                                    value="{{ $data->production_count }}"
                                    placeholder="Masukkan total unit" required>
                            </div>
                        </div>



                        {{-- Catatan --}}
                        <div class="col-12">
                            <label class="form-label small fw-bold text-uppercase text-muted">Catatan (Opsional)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 d-flex align-items-center justify-content-center" style="width: 46px;">
                                    <i data-lucide="message-square" size="18"></i>
                                </span>
                                <textarea name="notes" rows="2" class="form-control form-control-custom border-start-0 shadow-none"
                                    placeholder="Contoh: Kendala cuaca atau mesin trouble...">{{ $data->notes ?? '' }}</textarea>
                            </div>
                        </div>

                        {{-- Lampiran Foto --}}
                        <div class="col-12">
                            <label class="form-label small fw-bold text-uppercase text-muted">Lampiran Foto (Opsional)</label>
                            <div class="d-flex align-items-center gap-3">
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 d-flex align-items-center justify-content-center" style="width: 46px;">
                                        <i data-lucide="camera" size="18"></i>
                                    </span>
                                    <input type="file" name="photo" id="photo-input" class="form-control form-control-custom border-start-0 shadow-none" accept="image/*">
                                </div>
                                @if($data->photo)
                                    <img src="{{ asset($data->photo) }}" alt="Foto saat ini" style="width: 64px; height: 64px; object-fit: cover; border-radius: 10px; border: 2px solid #198754;">
                                @endif
                                <img id="photo-preview-edit" src="" alt="Preview" style="width: 64px; height: 64px; object-fit: cover; border-radius: 10px; border: 2px solid #0d6efd; display: none;">
                            </div>
                            @if($data->photo)
                                <small class="text-muted mt-1 d-block">Foto saat ini sudah ada. Upload baru untuk mengganti.</small>
                            @endif
                        </div>

                        <div class="col-12 mt-4">
                            <button type="submit" class="btn btn-primary btn-submit w-100 py-3 text-white shadow">
                                <i data-lucide="save" class="me-2" size="20"></i> UPDATE DATA PRODUKSI
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Job Dropdown Multiselect */
    .job-dropdown-wrapper { position: relative; }
    .job-dropdown-trigger {
        display: flex; align-items: center; justify-content: space-between;
        background: #fff; border: 1.5px solid #dee2e6; border-radius: 0.5rem;
        padding: 0.5rem 0.75rem; cursor: pointer; min-height: 42px;
        transition: border-color 0.2s; user-select: none;
    }
    .job-dropdown-trigger:hover, .job-dropdown-trigger.open { border-color: #0d6efd; }
    .job-dropdown-trigger .placeholder { color: #adb5bd; font-size: 0.875rem; }
    .job-dropdown-trigger .selected-text { font-size: 0.8rem; color: #1e293b; font-weight: 500; }
    .job-dropdown-panel {
        display: none; position: absolute; top: calc(100% + 4px); left: 0; right: 0;
        background: #fff; border: 1.5px solid #0d6efd; border-radius: 0.5rem;
        box-shadow: 0 8px 24px rgba(0,0,0,0.12); z-index: 200;
        padding: 0.5rem 0.75rem; max-height: 220px; overflow-y: auto;
    }
    .job-dropdown-panel.open { display: block; }
    .job-check-label {
        display: flex; align-items: center; gap: 8px;
        padding: 5px 4px; cursor: pointer; border-radius: 6px;
        font-size: 0.875rem; transition: background 0.15s;
    }
    .job-check-label:hover { background: #f0f9ff; }
    .job-check-label:has(input:checked) { color: #0369a1; font-weight: 600; }
</style>
@endpush

@push('scripts')
<script>
    function toggleJobDropdown() {
        const panel   = document.getElementById('job-dropdown-panel');
        const trigger = document.getElementById('job-dropdown-trigger');
        const chevron = document.getElementById('job-chevron');
        const isOpen  = panel.classList.contains('open');
        panel.classList.toggle('open');
        trigger.classList.toggle('open');
        chevron.style.transform = isOpen ? '' : 'rotate(180deg)';
    }
    function updateJobText() {
        const checked = document.querySelectorAll('.job-checkbox:checked');
        const textEl  = document.getElementById('job-selected-text');
        if (checked.length === 0) {
            textEl.textContent = 'Pilih';
            textEl.className   = 'placeholder';
        } else {
            textEl.textContent = Array.from(checked).map(c => c.value).join(', ');
            textEl.className   = 'selected-text';
        }
    }
    document.addEventListener('click', function(e) {
        const wrapper = document.getElementById('job-dropdown-wrapper');
        if (wrapper && !wrapper.contains(e.target)) {
            document.getElementById('job-dropdown-panel').classList.remove('open');
            document.getElementById('job-dropdown-trigger').classList.remove('open');
            document.getElementById('job-chevron').style.transform = '';
        }
    });
    function toggleRitase() {
        document.querySelectorAll('.job-checkbox').forEach(cb => {
            const label = cb.closest('.job-check-label');
            if (label) {
                label.style.background = cb.checked ? '#e0f2fe' : '';
                label.style.color      = cb.checked ? '#0369a1' : '';
            }
        });
    }
    document.addEventListener('DOMContentLoaded', toggleRitase);

    // Photo preview
    var photoInput = document.getElementById('photo-input');
    if (photoInput) {
        photoInput.addEventListener('change', function() {
            var preview = document.getElementById('photo-preview-edit');
            if (this.files && this.files[0]) {
                var reader = new FileReader();
                reader.onload = function(ev) {
                    preview.src = ev.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(this.files[0]);
            } else {
                preview.style.display = 'none';
            }
        });
    }
</script>
@endpush
