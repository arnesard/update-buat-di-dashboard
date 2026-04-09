@extends('layouts.app')

@section('title', 'Lembur')

@push('styles')
<style>
    :root {
        --sky-500: #0ea5e9;
        --sky-600: #0284c7;
        --sky-light: #f0f9ff;
        --slate-50: #f8fafc;
        --slate-100: #f1f5f9;
        --slate-200: #e2e8f0;
        --slate-500: #64748b;
        --slate-600: #475569;
        --slate-900: #0f172a;
        --primary-gradient: linear-gradient(135deg, #0ea5e9 0%, #2563eb 100%);
        --success-gradient: linear-gradient(135deg, #10b981 0%, #059669 100%);
        --info-gradient: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
    }

    .max-w-7xl { max-width: 80rem; }
    .card-vibrant {
        border: none !important;
        color: white !important;
        transition: transform 0.3s ease, box-shadow 0.3s ease !important;
    }
    .card-vibrant .h2 { color: white !important; }
    .card-vibrant .text-xs { color: rgba(255,255,255,0.8) !important; }
    
    .bg-gradient-sky { background: var(--primary-gradient); }
    .bg-gradient-success { background: var(--success-gradient); }
    .bg-gradient-info { background: var(--info-gradient); }
    
    .icon-box-white {
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(4px);
        color: white;
    }

    .form-control-custom {
        background-color: var(--slate-50);
        border: 1px solid var(--slate-200);
        padding: 0.6rem 1rem;
        border-radius: 0.75rem;
        transition: all 0.2s;
    }
    .form-control-custom:focus {
        background-color: #fff;
        border-color: var(--sky-500);
        box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.1);
    }

    .overtime-record {
        border: 1px solid var(--slate-100);
        border-radius: 1rem;
        transition: all 0.2s;
    }
    .overtime-record:hover {
        background-color: var(--slate-50);
        border-color: var(--slate-200);
    }

    .badge-status-pending { background-color: #fef3c7; color: #d97706; }
    .badge-status-approved { background-color: #dcfce7; color: #16a34a; }
    .badge-status-rejected { background-color: #fee2e2; color: #dc2626; }

    @media print {
        .no-print { display: none !important; }
        .print-full-width { width: 100% !important; flex: 0 0 100% !important; max-width: 100% !important; }
        .print-no-border { border: none !important; box-shadow: none !important; }
        .print-no-margin { margin: 0 !important; }
        .print-border-bottom { border-bottom: 1px solid #e2e8f0 !important; border-radius: 0 !important; }
        .overtime-list { max-height: none !important; overflow: visible !important; }
        .overtime-record { break-inside: avoid; }
        body { padding: 0 !important; }
        .p-3, .p-md-4 { padding: 0 !important; }
    }
</style>
@endpush

@section('content')
<div class="p-3 p-md-4 max-w-7xl mx-auto">
    {{-- Header --}}
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 fw-bold text-slate-900 mb-1">Pengajuan Lembur</h1>
            <p class="text-slate-600 no-print">Catat dan monitor jam lembur karyawan</p>
        </div>
        <div class="no-print">
            <button onclick="window.print()" class="btn btn-outline-primary rounded-pill px-4 fw-bold">
                <i data-lucide="printer" class="me-2" size="18"></i> Cetak Laporan
            </button>
        </div>
    </div>

    {{-- Print Only Header --}}
    <div class="d-none d-print-block mb-5 text-center">
        <h2 class="fw-bold mb-1">LAPORAN PENGAJUAN LEMBUR</h2>
        <p class="text-muted">Periode: {{ now()->translatedFormat('F Y') }}</p>
        <hr>
    </div>

    {{-- Stats Cards --}}
    <div class="row g-3 mb-4 no-print">
        <div class="col-12 col-md-4">
            <div class="card h-100 card-vibrant bg-gradient-sky rounded-4 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-xs font-bold text-uppercase tracking-wider mb-1">Total Pengajuan</p>
                            <p class="h2 fw-bold mb-0">{{ $overtimes->count() }}</p>
                        </div>
                        <div class="p-3 icon-box-white rounded-circle">
                            <i data-lucide="calendar" size="24"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card h-100 card-vibrant bg-gradient-success rounded-4 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-xs font-bold text-uppercase tracking-wider mb-1">Total Jam</p>
                            @php
                                $totalHours = $overtimes->filter(fn($ot) => $ot->status == 'approved')->sum(function($ot) {
                                    $start = \Carbon\Carbon::parse($ot->start_time);
                                    $end = \Carbon\Carbon::parse($ot->end_time);
                                    if ($end->lt($start)) $end->addDay();
                                    $gross = $start->diffInHours($end);
                                    return min(7, $gross);
                                });
                            @endphp
                            <p class="h2 fw-bold mb-0">{{ number_format($totalHours, 1) }}</p>
                        </div>
                        <div class="p-3 icon-box-white rounded-circle">
                            <i data-lucide="clock" size="24"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card h-100 card-vibrant bg-gradient-info rounded-4 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-xs font-bold text-uppercase tracking-wider mb-1">Bulan Ini</p>
                            <p class="h2 fw-bold mb-0 text-capitalize">{{ now()->translatedFormat('F') }}</p>
                        </div>
                        <div class="p-3 icon-box-white rounded-circle">
                            <i data-lucide="calendar" size="24"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Form Input --}}
        <div class="col-12 col-lg-5 no-print">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold text-slate-900 mb-3">Form Pengajuan Lembur</h5>
                    
                    @if (session('success'))
                        <div class="alert alert-success border-0 rounded-3 shadow-sm mb-4 small">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger border-0 rounded-3 shadow-sm mb-4 small">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <form action="{{ route('overtime.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Karyawan *</label>
                            <input type="hidden" name="employee_name" id="ot-employee-name-hidden" required>
                            <div class="position-relative" id="ot-operator-search-wrapper">
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i data-lucide="search" size="18"></i></span>
                                    <input type="text" id="ot-operator-search" class="form-control form-control-custom border-start-0 border-end-0 shadow-none"
                                           placeholder="Ketik nama atau ID karyawan..." autocomplete="off">
                                    <button type="button" class="btn btn-outline-secondary px-3" id="ot-btn-clear" title="Hapus" style="display:none;">
                                        <i data-lucide="x" size="18"></i>
                                    </button>
                                </div>
                                <div id="ot-operator-dropdown" class="position-absolute w-100 bg-white border rounded-3 shadow mt-1 overflow-auto"
                                     style="max-height: 220px; z-index: 100; display: none;">
                                    @foreach ($employees as $employee)
                                        <div class="operator-option px-3 py-2"
                                             style="cursor: pointer;"
                                             data-id="{{ $employee->employee_id }}"
                                             data-name="{{ $employee->name }}">
                                            <span class="fw-bold">{{ $employee->employee_id }}</span>
                                            <span class="text-muted mx-1">—</span>
                                            <span>{{ $employee->name }}</span>
                                        </div>
                                    @endforeach
                                    <div id="ot-no-result" class="px-3 py-2 text-muted small" style="display: none;">Tidak ditemukan</div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Tanggal *</label>
                            <input type="date" name="overtime_date" class="form-control form-control-custom" value="{{ now()->format('Y-m-d') }}" required>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <label class="form-label fw-semibold small">Jam Mulai *</label>
                                <input type="time" name="start_time" id="start_time" class="form-control form-control-custom" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold small">Jam Selesai *</label>
                                <input type="time" name="end_time" id="end_time" class="form-control form-control-custom" required>
                            </div>
                        </div>

                        <div id="durationPreview" class="alert alert-info border-0 rounded-3 py-2 px-3 mb-3 d-none" style="background-color: #f0f9ff; color: #0284c7;">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-2">
                                    <i data-lucide="info" size="14"></i>
                                    <span class="small fw-semibold">Estimasi: <span id="durationText">0</span> Jam</span>
                                </div>
                                <span id="capWarning" class="text-xs d-none" style="color: #0369a1;">(Maksimal 7 jam)</span>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold small">Alasan *</label>
                            <textarea name="reason" class="form-control form-control-custom" rows="4" placeholder="Contoh: Target produksi belum tercapai..." required></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 rounded-pill py-2 fw-bold shadow-sm" style="background-color: var(--sky-500); border: none;">
                            Ajukan Lembur
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Overtime List --}}
        <div class="col-12 col-lg-7 print-full-width">
            <div class="card border-0 shadow-sm rounded-4 print-no-border">
                <div class="card-header bg-white border-bottom-0 p-4 pb-0 no-print">
                    <h5 class="fw-bold text-slate-900 mb-1">Daftar Lembur Bulan Ini</h5>
                    <p class="text-xs text-muted mb-0">Pengajuan lembur yang tercatat</p>
                </div>
                <div class="card-body p-4">
                    @php
                        // Normalize names for better matching
                        $employeeMap = \App\Models\Employee::all()->mapWithKeys(function ($emp) {
                            return [trim(strtoupper($emp->name)) => $emp->employee_id];
                        });
                    @endphp
                    <div class="overtime-list overflow-visible">
                        @forelse($overtimes as $ot)
                        @php
                            $normalizedName = trim(strtoupper($ot->employee_name));
                            $displayId = $employeeMap[$normalizedName] ?? null;
                            $isApproved = $ot->status == 'approved';
                        @endphp
                        <div class="overtime-record p-4 mb-3 print-no-margin print-border-bottom">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        <p class="fw-bold text-slate-900 mb-0 {{ !$isApproved ? 'd-print-none' : '' }}">{{ $ot->employee_name }}</p>
                                        @if(!$isApproved)
                                            <p class="fw-bold text-muted mb-0 d-none d-print-block"><em>[Nama Tersembunyi - Belum Disetujui]</em></p>
                                        @endif
                                        @if($displayId)
                                        <span class="badge bg-primary bg-opacity-10 text-primary border-0 rounded-pill text-xs fw-bold px-2 py-1 {{ !$isApproved ? 'd-print-none' : '' }}" style="font-size: 0.65rem;">
                                            {{ $displayId }}
                                        </span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-slate-500 mb-0">
                                        {{ $ot->overtime_date->translatedFormat('l, d F Y') }}
                                    </p>
                                </div>
                                <div class="text-end">
                                    <div class="small fw-bold text-slate-900">
                                        @php
                                            $start = \Carbon\Carbon::parse($ot->start_time);
                                            $end = \Carbon\Carbon::parse($ot->end_time);
                                            if ($end->lt($start)) $end->addDay();
                                            $gross = $start->diffInHours($end);
                                            echo min(7, $gross);
                                        @endphp Jam
                                    </div>
                                    <div class="text-xs text-muted">{{ \Carbon\Carbon::parse($ot->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($ot->end_time)->format('H:i') }}</div>
                                    <span class="badge badge-status-{{ $ot->status }} rounded-pill px-2 py-1 text-xs fw-bold mt-1">
                                        {{ ucfirst($ot->status == 'pending' ? 'Menunggu' : ($ot->status == 'approved' ? 'Disetujui' : 'Ditolak')) }}
                                    </span>
                                </div>
                            </div>
                            <p class="text-sm text-slate-600 mb-3">{{ $ot->reason }}</p>
                            
                            <div class="d-flex justify-content-between align-items-center">
                                <p class="text-xs text-slate-400 mb-0">Diajukan: {{ $ot->created_at->format('H:i') }}</p>
                                <div class="d-flex gap-2 no-print">
                                    @if($ot->status == 'pending')
                                    <form action="{{ route('overtime.approve', $ot) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-outline-success rounded-pill px-2 py-0" title="Setujui">
                                            <i data-lucide="check" size="14"></i>
                                        </button>
                                    </form>
                                    <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-2 py-0" data-bs-toggle="modal" data-bs-target="#rejectModal" onclick="openRejectModal({{ $ot->id }}, '{{ $ot->employee_name }}')">
                                        <i data-lucide="x" size="14"></i>
                                    </button>
                                    @endif
                                    <form action="{{ route('overtime.destroy', $ot) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-secondary rounded-pill px-2 py-0" onclick="return confirm('Hapus data?')">
                                            <i data-lucide="trash-2" size="14"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-5 text-slate-400">
                            <i data-lucide="clock" class="opacity-25 mb-3" size="48"></i>
                            <p>Belum ada pengajuan lembur bulan ini</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Reject Modal --}}
<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4">
            <div class="modal-header border-bottom-0 p-4 pb-0">
                <h5 class="fw-bold">Tolak Lembur</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="rejectForm" method="POST">
                @csrf @method('PATCH')
                <div class="modal-body p-4">
                    <p class="text-sm text-slate-600 mb-4">Alasan penolakan untuk <strong id="rejectEmployeeName"></strong>:</p>
                    <textarea name="notes" class="form-control form-control-custom" rows="3" placeholder="Masukkan alasan penolakan..." required></textarea>
                </div>
                <div class="modal-footer border-top-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold">Tolak</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openRejectModal(id, name) {
        document.getElementById('rejectEmployeeName').textContent = name;
        document.getElementById('rejectForm').action = `/overtime/${id}/reject`;
    }

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
                warning.classList.add('d-none'); // Usually capped at 7 anyway
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

    // === SEARCHABLE DROPDOWN KARYAWAN ===
    (function() {
        const searchInput = document.getElementById('ot-operator-search');
        const dropdown = document.getElementById('ot-operator-dropdown');
        const hiddenInput = document.getElementById('ot-employee-name-hidden');
        const options = document.querySelectorAll('#ot-operator-dropdown .operator-option');
        const noResult = document.getElementById('ot-no-result');
        const btnClear = document.getElementById('ot-btn-clear');

        if (!searchInput) return;

        searchInput.addEventListener('focus', function() {
            dropdown.style.display = 'block';
            filterOptions();
        });

        searchInput.addEventListener('input', function() {
            filterOptions();
            btnClear.style.display = searchInput.value ? 'block' : 'none';
        });

        function filterOptions() {
            const query = searchInput.value.toLowerCase();
            let found = 0;
            options.forEach(opt => {
                const name = opt.dataset.name.toLowerCase();
                const id = opt.dataset.id.toLowerCase();
                if (name.includes(query) || id.includes(query)) {
                    opt.style.display = 'block';
                    found++;
                } else {
                    opt.style.display = 'none';
                }
            });
            noResult.style.display = found === 0 ? 'block' : 'none';
        }

        btnClear.addEventListener('mousedown', function(e) {
            e.preventDefault();
            searchInput.value = '';
            hiddenInput.value = '';
            searchInput.style.borderColor = '';
            btnClear.style.display = 'none';
            dropdown.style.display = 'block';
            filterOptions();
            searchInput.focus();
        });

        options.forEach(opt => {
            opt.addEventListener('mousedown', function(e) {
                e.preventDefault();
                searchInput.value = this.dataset.name;
                hiddenInput.value = this.dataset.name;
                dropdown.style.display = 'none';
                searchInput.style.borderColor = '#198754';
                btnClear.style.display = 'block';
            });
            opt.addEventListener('mouseenter', function() { this.style.background = '#f0f9ff'; });
            opt.addEventListener('mouseleave', function() { this.style.background = ''; });
        });

        document.addEventListener('click', function(e) {
            if (!document.getElementById('ot-operator-search-wrapper').contains(e.target)) {
                dropdown.style.display = 'none';
            }
        });
    })();

    document.addEventListener('DOMContentLoaded', function() {
        if (window.lucide) {
            lucide.createIcons();
        }
    });
</script>
@endpush
