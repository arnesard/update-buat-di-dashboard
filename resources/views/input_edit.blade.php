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
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-uppercase text-muted">Pekerjaan hari ini</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 d-flex align-items-center justify-content-center" style="width: 46px;">
                                    <i data-lucide="hammer" size="18"></i>
                                </span>
                                <select name="job_today" id="job-select" class="form-select form-control-custom border-start-0 shadow-none"
                                    required onchange="toggleRitase()">
                                    <option value="">-- Pilih pekerjaan --</option>
                                    <option value="Scan" {{ $data->job_today == 'Scan' ? 'selected' : '' }}>Scan</option>
                                    <option value="Strapping" {{ $data->job_today == 'Strapping' ? 'selected' : '' }}>Strapping</option>
                                    <option value="Tempel Stiker" {{ $data->job_today == 'Tempel Stiker' ? 'selected' : '' }}>Tempel Stiker</option>
                                    <option value="Susun Tire" {{ $data->job_today == 'Susun Tire' ? 'selected' : '' }}>Susun Tire</option>
                                    <option value="Pressing" {{ $data->job_today == 'Pressing' ? 'selected' : '' }}>Pressing</option>
                                    <option value="Driver" {{ $data->job_today == 'Driver' ? 'selected' : '' }}>Driver</option>
                                    <option value="Leader" {{ $data->job_today == 'Leader' ? 'selected' : '' }}>Leader</option>
                                    <option value="Pasang Product Tage OE" {{ $data->job_today == 'Pasang Product Tage OE' ? 'selected' : '' }}>Pasang Product Tage OE</option>
                                </select>
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

                        {{-- ritase --}}
                        <div class="col-md-6 ritase-col">
                            <label class="form-label small fw-bold text-uppercase text-muted">Ritase / Trip</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 d-flex align-items-center justify-content-center" style="width: 46px;">
                                    <i data-lucide="navigation" size="18"></i>
                                </span>
                                <input type="number" name="ritase_result"
                                    class="form-control form-control-custom border-start-0 shadow-none"
                                    value="{{ $data->ritase_result ?? 0 }}"
                                    placeholder="Masukkan total ritase">
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

@push('scripts')
<script>
    function toggleRitase() {
        const jobSelect = document.getElementById('job-select');
        const ritaseCols = document.querySelectorAll('.ritase-col');
        const isDriver = jobSelect.value === 'Driver';

        ritaseCols.forEach(col => {
            if (isDriver) {
                if (window.innerWidth > 768) {
                    col.style.display = 'block';
                } else {
                    col.style.display = 'flex';
                }
            } else {
                col.style.display = 'none';
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
