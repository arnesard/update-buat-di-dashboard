@extends('layouts.app')

@section('title', 'Ajukan Lembur')

@push('styles')
    <style>
        :root {
            --sky-500: #0ea5e9;
            --sky-600: #0284c7;
            --slate-50: #f8fafc;
            --slate-200: #e2e8f0;
            --slate-900: #0f172a;
        }

        .max-w-3xl {
            max-width: 48rem;
        }

        .form-control-custom {
            background-color: var(--slate-50);
            border: 1px solid var(--slate-200);
            padding: 0.75rem 1rem;
            border-radius: 0.85rem;
        }
    </style>
@endpush

@section('content')
    <div class="p-3 p-md-4 max-w-3xl mx-auto">
        <div class="mb-4">
            <a href="{{ route('overtime.index') }}"
                class="btn btn-link text-decoration-none text-slate-500 p-0 mb-2 small d-inline-flex align-items-center gap-1">
                <i data-lucide="arrow-left" size="14"></i> Kembali
            </a>
            <h1 class="h3 fw-bold text-slate-900">Ajukan Lembur Baru</h1>
        </div>

        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4 p-md-5">
                <form action="{{ route('overtime.store') }}" method="POST">
                    @csrf
                    <div class="row g-4">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Nama Karyawan *</label>
                            <div class="position-relative" id="ot-operator-wrapper">
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                                    </span>
                                    <input type="text" id="ot-operator-search"
                                        class="form-control form-control-custom border-start-0 border-end-0 shadow-none"
                                        placeholder="Ketik nama karyawan..." autocomplete="off">
                                    <button type="button" class="btn btn-outline-secondary px-3" id="ot-btn-clear" title="Hapus" style="display:none; border-radius: 0 0.85rem 0.85rem 0;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                    </button>
                                </div>
                                {{-- Hidden input yang dikirim ke server --}}
                                <input type="hidden" name="employee_name" id="ot-employee-name-hidden">
                                <div id="ot-operator-dropdown"
                                    class="position-absolute w-100 bg-white border rounded-3 shadow mt-1 overflow-auto"
                                    style="max-height: 220px; z-index: 1000; display: none;">
                                    @foreach ($employees as $employee)
                                        <div class="ot-operator-option px-3 py-2"
                                            style="cursor: pointer;"
                                            data-name="{{ $employee->name }}"
                                            data-id="{{ $employee->employee_id }}"
                                            data-plant="{{ $employee->plant }}">
                                            <span class="fw-bold">{{ $employee->employee_id }}</span>
                                            <span class="text-muted mx-1">—</span>
                                            <span>{{ $employee->name }}</span>
                                            <span class="badge bg-primary bg-opacity-10 text-primary ms-2" style="font-size:10px;">Plant {{ $employee->plant }}</span>
                                        </div>
                                    @endforeach
                                    <div id="ot-no-result" class="px-3 py-2 text-muted small" style="display:none;">Tidak ditemukan</div>
                                </div>
                            </div>

                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold">Tanggal Lembur *</label>
                            <input type="date" name="overtime_date" class="form-control form-control-custom" required
                                value="{{ old('overtime_date', now()->format('Y-m-d')) }}"
                                max="{{ now()->format('Y-m-d') }}">
                        </div>

                        <div class="col-6 col-md-3">
                            <label class="form-label fw-semibold">Jam Mulai *</label>
                            <input type="time" name="start_time" id="start_time" class="form-control form-control-custom"
                                required value="{{ old('start_time') }}">
                        </div>

                        <div class="col-6 col-md-3">
                            <label class="form-label fw-semibold">Jam Selesai *</label>
                            <input type="time" name="end_time" id="end_time" class="form-control form-control-custom"
                                required value="{{ old('end_time') }}">
                        </div>

                        <div id="durationPreview" class="col-12 d-none">
                            <div class="alert alert-info border-0 rounded-3 py-2 px-3 mb-0 d-flex align-items-center justify-content-between"
                                style="background-color: #f0f9ff; color: #0284c7;">
                                <div class="d-flex align-items-center gap-2">
                                    <i data-lucide="info" size="14"></i>
                                    <span class="small fw-semibold">Total Lembur Dihitung: <span id="durationText">0</span>
                                        Jam</span>
                                </div>
                                <span id="capWarning" class="text-xs d-none" style="color: #0369a1;">(Dipotong istirahat /
                                    Max 7 jam)</span>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Alasan Lembur *</label>
                            <textarea name="reason" class="form-control form-control-custom" rows="4" required
                                placeholder="Jelaskan alasan pengajuan lembur...">{{ old('reason') }}</textarea>
                        </div>

                        <div class="col-12 pt-2">
                            <button type="submit" class="btn btn-primary w-100 rounded-pill py-3 fw-bold shadow-sm"
                                style="background-color: var(--sky-500); border: none;">
                                Ajukan Lembur
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function calculateDuration() {
            const start = document.getElementById('start_time').value;
            const end = document.getElementById('end_time').value;
            const preview = document.getElementById('durationPreview');
            const text = document.getElementById('durationText');
            const warning = document.getElementById('capWarning');

            if (start && end) {
                const startDate = new Date(`2000-01-01T${start}`);
                const endDate = new Date(`2000-01-01T${end}`);

                if (endDate > startDate) {
                    let diff = (endDate - startDate) / (1000 * 60 * 60);
                    let capped = Math.min(7, diff);

                    text.textContent = capped;
                    preview.classList.remove('d-none');

                    if (diff > 7) {
                        warning.classList.remove('d-none');
                    } else {
                        warning.classList.add('d-none');
                    }
                } else if (start && end) {
                    // Overnight logic: end time is technically next day
                    let diff = ((endDate - startDate) / (1000 * 60 * 60)) + 24;
                    let capped = Math.min(7, diff);

                    text.textContent = capped;
                    preview.classList.remove('d-none');
                    warning.classList.add('d-none');
                    if (diff > 7) warning.classList.remove('d-none');
                } else {
                    preview.classList.add('d-none');
                }
            } else {
                preview.classList.add('d-none');
            }
        }

        document.getElementById('start_time').addEventListener('change', calculateDuration);
        document.getElementById('end_time').addEventListener('change', calculateDuration);

        document.addEventListener('DOMContentLoaded', function() {
            if (window.lucide) {
                lucide.createIcons();
            }

            // === CUSTOM SEARCHABLE DROPDOWN KARYAWAN LEMBUR ===
            var searchInput = document.getElementById('ot-operator-search');
            var dropdown    = document.getElementById('ot-operator-dropdown');
            var hiddenInput = document.getElementById('ot-employee-name-hidden');
            var options     = document.querySelectorAll('.ot-operator-option');
            var noResult    = document.getElementById('ot-no-result');
            var btnClear    = document.getElementById('ot-btn-clear');

            function filterOptions() {
                var query = searchInput.value.toLowerCase();
                var found = 0;
                options.forEach(function(opt) {
                    var name = opt.dataset.name.toLowerCase();
                    var id   = opt.dataset.id.toLowerCase();
                    if (name.includes(query) || id.includes(query)) {
                        opt.style.display = 'block';
                        found++;
                    } else {
                        opt.style.display = 'none';
                    }
                });
                noResult.style.display = found === 0 ? 'block' : 'none';
            }

            searchInput.addEventListener('focus', function() {
                dropdown.style.display = 'block';
                filterOptions();
            });

            searchInput.addEventListener('input', function() {
                filterOptions();
                btnClear.style.display = searchInput.value ? 'block' : 'none';
                // Reset hidden value jika user hapus manual
                if (!searchInput.value) hiddenInput.value = '';
            });

            btnClear.addEventListener('mousedown', function(e) {
                e.preventDefault();
                searchInput.value  = '';
                hiddenInput.value  = '';
                btnClear.style.display = 'none';
                searchInput.style.borderColor = '';
                dropdown.style.display = 'block';
                filterOptions();
                searchInput.focus();
            });

            options.forEach(function(opt) {
                opt.addEventListener('mousedown', function(e) {
                    e.preventDefault();
                    searchInput.value = this.dataset.name;
                    hiddenInput.value = this.dataset.name;
                    dropdown.style.display = 'none';
                    searchInput.style.borderColor = '#198754';
                    btnClear.style.display = 'block';
                });

                opt.addEventListener('mouseenter', function() {
                    this.style.background = '#f0f9ff';
                });
                opt.addEventListener('mouseleave', function() {
                    this.style.background = '';
                });

                // Touch support untuk mobile
                opt.addEventListener('touchstart', function(e) {
                    searchInput.value = this.dataset.name;
                    hiddenInput.value = this.dataset.name;
                    dropdown.style.display = 'none';
                    searchInput.style.borderColor = '#198754';
                    btnClear.style.display = 'block';
                });
            });

            document.addEventListener('click', function(e) {
                if (!document.getElementById('ot-operator-wrapper').contains(e.target)) {
                    dropdown.style.display = 'none';
                }
            });

            // Validasi sebelum submit: pastikan nama sudah dipilih dari dropdown
            document.querySelector('form').addEventListener('submit', function(e) {
                if (!hiddenInput.value) {
                    e.preventDefault();
                    searchInput.style.borderColor = '#dc3545';
                    searchInput.focus();
                    alert('Pilih nama karyawan dari daftar terlebih dahulu!');
                }
            });
        });

    </script>
@endpush