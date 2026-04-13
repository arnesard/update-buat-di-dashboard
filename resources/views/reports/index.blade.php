@extends('layouts.app')

@section('title', 'Rekap Laporan')

@push('styles')
<style>
    :root {
        --sky-50: #f0f9ff;
        --sky-100: #e0f2fe;
        --sky-500: #0ea5e9;
        --sky-600: #0284c7;
        --slate-50: #f8fafc;
        --slate-100: #f1f5f9;
        --slate-200: #e2e8f0;
        --slate-500: #64748b;
        --slate-600: #475569;
        --slate-900: #0f172a;
    }

    .glass-card {
        background: white;
        border: 1px solid var(--slate-200);
        border-radius: 1.25rem;
        padding: 1.5rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }

    .bento-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1.25rem;
    }

    @media (max-width: 1200px) {
        .bento-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 768px) {
        .bento-grid {
            grid-template-columns: 1fr;
        }
    }

    .bento-card {
        background: white;
        border: 1px solid var(--slate-100);
        border-radius: 1rem;
        padding: 1.25rem;
        transition: all 0.2s;
    }
    .bento-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 20px -8px rgba(0, 0, 0, 0.1);
    }

    .bento-card.success { border-left: 4px solid #10b981; }
    .bento-card.warning { border-left: 4px solid #f59e0b; }
    .bento-card.info { border-left: 4px solid #3b82f6; }
    .bento-card.danger { border-left: 4px solid #ef4444; }

    .stat-icon {
        width: 40px; height: 40px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        margin-bottom: 0.75rem;
    }
    .stat-icon.primary { background: var(--sky-50); color: var(--sky-600); }
    .stat-icon.success { background: #ecfdf5; color: #10b981; }
    .stat-icon.warning { background: #fffbeb; color: #f59e0b; }
    .stat-icon.info { background: #f0f9ff; color: #3b82f6; }
    .stat-icon.danger { background: #fef2f2; color: #ef4444; }

    .stat-value { font-size: 1.5rem; font-weight: 700; color: var(--slate-900); }
    .stat-label { font-size: 0.875rem; color: var(--slate-500); font-weight: 500; }

    .table-modern {
        background: white;
        border: 1px solid var(--slate-200);
        border-radius: 1rem;
        overflow: hidden;
    }
    .table-modern table th {
        background: var(--slate-50);
        color: var(--slate-600);
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        padding: 0.75rem 1rem;
    }
    .table-modern table td { padding: 0.75rem 1rem; vertical-align: middle; }

    .form-control-custom {
        background-color: #ffffff;
        border: 1.5px solid var(--slate-200);
        padding: 0.75rem 1rem;
        border-radius: 0.75rem;
        font-size: 0.875rem;
        color: var(--slate-900);
        font-weight: 500;
        transition: all 0.2s;
    }
    .form-control-custom:focus {
        border-color: var(--sky-500);
        box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.1);
        background-color: #fff;
    }

    .filter-card {
        background: #ffffff;
        border: 1px solid var(--slate-200);
        border-radius: 1.25rem;
        padding: 2rem;
        box-shadow: 0 10px 30px -12px rgba(0, 0, 0, 0.05);
    }

    .filter-label {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--slate-500);
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .filter-label i { color: var(--sky-500); }

    /* Compact Stats */
    .bento-card {
        padding: 1rem !important;
        display: flex !important;
        align-items: center !important;
        gap: 0.75rem !important;
    }
    .stat-icon {
        width: 32px !important;
        height: 32px !important;
        min-width: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        margin-bottom: 0 !important;
    }
    .stat-value {
        font-size: 1.1rem !important;
        font-weight: 700;
        line-height: 1;
        margin-bottom: 2px;
    }
    .stat-label {
        font-size: 0.7rem !important;
        color: #64748b;
        font-weight: 500;
        margin-top: 0 !important;
    }

    
    .hover-lift:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(14, 165, 233, 0.3) !important;
    }

    /* Photo thumbnail styles */
    .report-photo-thumb {
        width: 40px;
        height: 40px;
        object-fit: cover;
        border-radius: 8px;
        border: 2px solid #e0f2fe;
        cursor: pointer;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .report-photo-thumb:hover {
        transform: scale(1.15);
        box-shadow: 0 4px 14px rgba(14, 165, 233, 0.4);
        border-color: #0ea5e9;
    }
    .badge-foto {
        font-size: 0.6rem;
        background: #ecfdf5;
        color: #047857;
        border: 1px solid #a7f3d0;
        border-radius: 6px;
        padding: 2px 6px;
        cursor: pointer;
    }
    .badge-no-foto {
        font-size: 0.6rem;
        background: #f1f5f9;
        color: #94a3b8;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        padding: 2px 6px;
    }
    /* Detail Modal */
    #detailModal .modal-content {
        border: none;
        border-radius: 1.25rem;
        box-shadow: 0 25px 60px rgba(0,0,0,0.18);
        overflow: hidden;
    }
    #detailModal .modal-header {
        background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
        border: none;
        padding: 1.25rem 1.5rem;
    }
    #detailModal .modal-body {
        padding: 1.5rem;
    }
    .detail-photo-full {
        width: 100%;
        max-height: 380px;
        object-fit: contain;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        cursor: zoom-in;
        transition: transform 0.3s ease;
    }
    .detail-photo-full.zoomed {
        transform: scale(1.5);
        cursor: zoom-out;
        z-index: 10;
        position: relative;
    }
    .detail-info-row {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        padding: 0.6rem 0;
        border-bottom: 1px solid #f1f5f9;
    }
    .detail-info-row:last-child { border-bottom: none; }
    .detail-info-label {
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #94a3b8;
        min-width: 90px;
    }
    .detail-info-value {
        font-size: 0.85rem;
        font-weight: 600;
        color: #1e293b;
        flex: 1;
    }
    /* Lightbox overlay */
    #photoLightbox {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.88);
        z-index: 9999;
        align-items: center;
        justify-content: center;
        flex-direction: column;
    }
    #photoLightbox.active { display: flex; }
    #photoLightbox img {
        max-width: 92vw;
        max-height: 88vh;
        border-radius: 12px;
        box-shadow: 0 0 60px rgba(0,0,0,0.6);
        object-fit: contain;
    }
    #photoLightbox .lb-close {
        position: absolute;
        top: 1.25rem;
        right: 1.5rem;
        color: white;
        font-size: 2rem;
        cursor: pointer;
        line-height: 1;
        opacity: 0.85;
        transition: opacity 0.2s;
    }
    #photoLightbox .lb-close:hover { opacity: 1; }
    #photoLightbox .lb-caption {
        margin-top: 1rem;
        color: rgba(255,255,255,0.75);
        font-size: 0.85rem;
        text-align: center;
    }

    @media print {
        .no-print { display: none !important; }
        .print-full-width { width: 100% !important; flex: 0 0 100% !important; max-width: 100% !important; }
        .print-no-border { border: none !important; box-shadow: none !important; }
        .table-modern { border: none !important; break-inside: avoid; }
        .table-modern table th { background: #f8fafc !important; color: black !important; border-bottom: 2px solid #e2e8f0 !important; }
        .table-modern table td { border-bottom: 1px solid #f1f5f9 !important; }
        body { background: white !important; }
        #main-content { padding: 0 !important; }
        .glass-card { border: none !important; box-shadow: none !important; padding: 0 !important; margin-bottom: 2rem !important; }
    }


</style>
@endpush

@section('content')
<div class="glass-card shadow-sm border-0 mb-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h4 class="mb-1 fw-bold">Laporan</h4>
            <p class="text-muted mb-0 no-print">Analisis data produksi dan lembur</p>
            <div class="d-none d-print-block mt-2">
                <p class="text-dark fw-bold mb-0">LAPORAN RECEIVING PRODUKSI & LEMBUR</p>
                <p class="text-muted small mb-0">Periode: {{ $filterType == 'daily' ? $start_date . ' s/d ' . $end_date : ($filterType == 'monthly' ? $start_month . ' s/d ' . $end_month : $year) }}</p>
            </div>
        </div>
        <div class="d-flex gap-2 no-print">
            <button class="btn btn-outline-primary rounded-pill px-4 fw-bold transition-all hover-lift" onclick="window.print()" type="button">
                <i data-lucide="printer" class="me-2" size="18"></i> Cetak Laporan
            </button>
        </div>
    </div>
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ $errors->first() }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif



<!-- Filter Section -->
<div class="filter-card mb-4 no-print">
    {{-- Row 1: Common Filters --}}
    <div class="row g-3">
        <div class="col-md-3">
            <label class="filter-label">
                <i data-lucide="layers-3" size="14"></i> Tipe Filter
            </label>
            <select name="filter_type" id="filterType" class="form-control form-control-custom" onchange="updateFilters(); applyFilters();">
                <option value="daily" {{ $filterType == 'daily' ? 'selected' : '' }}>Harian</option>
                <option value="monthly" {{ $filterType == 'monthly' ? 'selected' : '' }}>Bulanan</option>
                <option value="yearly" {{ $filterType == 'yearly' ? 'selected' : '' }}>Tahunan</option>
                <option value="all" {{ $filterType == 'all' ? 'selected' : '' }}>Semua Waktu (All Time) - Rekomendasi Cari Nama</option>
            </select>
        </div>
        <div class="col-md-3">
            <label class="filter-label">
                <i data-lucide="clock-4" size="14"></i> Shift
            </label>
            <select name="shift" id="shiftFilter" class="form-control form-control-custom" onchange="applyFilters()">
                <option value="">Semua Shift</option>
                <option value="1" {{ $shift == '1' ? 'selected' : '' }}>Shift 1</option>
                <option value="2" {{ $shift == '2' ? 'selected' : '' }}>Shift 2</option>
                <option value="3" {{ $shift == '3' ? 'selected' : '' }}>Shift 3</option>
            </select>
        </div>
        <div class="col-md-3">
            <label class="filter-label">
                <i data-lucide="factory" size="14"></i> Plant
            </label>
            <select name="plant" id="plantFilter" class="form-control form-control-custom" onchange="applyFilters()">
                <option value="">Semua Plant</option>
                @foreach(['B', 'H', 'I', 'T'] as $p)
                    <option value="{{ $p }}" {{ $plant_filter == $p ? 'selected' : '' }}>Plant {{ $p }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="filter-label">
                <i data-lucide="users-2" size="14"></i> Grup
            </label>
            <select name="group" id="groupFilter" class="form-control form-control-custom" onchange="applyFilters()">
                <option value="">Semua Grup</option>
                @foreach(['A', 'B', 'C', 'D'] as $g)
                    <option value="{{ $g }}" {{ $group_filter == $g ? 'selected' : '' }}>Grup {{ $g }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="row g-3 mt-2">
        <div class="col-md-6">
            <div id="dateFilterRange_Start" class="mb-3">
                <label class="filter-label">
                    <i data-lucide="calendar" size="14"></i> <span id="startLabel">Tanggal Awal</span>
                </label>
                <div id="dailyStart">
                    <input type="date" id="startDateInput" class="form-control form-control-custom" value="{{ $start_date }}" onchange="applyFilters()">
                </div>
                <div id="monthlyStart" style="display: none;">
                    <input type="month" id="startMonthInput" class="form-control form-control-custom" value="{{ $start_month }}" onchange="applyFilters()">
                </div>
                <div id="yearlyStart" style="display: none;">
                    <input type="number" id="yearInput" class="form-control form-control-custom" value="{{ $year }}" min="2020" max="{{ now()->year }}" onchange="applyFilters()">
                </div>
            </div>

            <div id="dateFilterRange_End">
                <label class="filter-label">
                    <i data-lucide="calendar-range" size="14"></i> <span id="endLabel">Tanggal Akhir</span>
                </label>
                <div id="dailyEnd">
                    <input type="date" id="endDateInput" class="form-control form-control-custom" value="{{ $end_date }}" onchange="applyFilters()">
                </div>
                <div id="monthlyEnd" style="display: none;">
                    <input type="month" id="endMonthInput" class="form-control form-control-custom" value="{{ $end_month }}" onchange="applyFilters()">
                </div>
                <div id="yearlyEnd" style="display: none;">
                    <input type="text" class="form-control form-control-custom bg-light" value="Full Year" disabled>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <label class="filter-label">
                <i data-lucide="user-search" size="14"></i> Nama Operator
            </label>
            <div class="input-group">
                <input type="text" id="operatorNameInput" list="operatorNames" 
                       class="form-control form-control-custom" 
                       placeholder="Cari nama operator..." 
                       value="{{ $operator_name }}"
                       onkeypress="if(event.key === 'Enter') applyFilters()">
                <button class="btn btn-primary px-3" type="button" onclick="applyFilters()">
                    <i data-lucide="search" size="16"></i>
                </button>
            </div>
            <datalist id="operatorNames">
                @foreach($all_employee_names as $name)
                    <option value="{{ $name }}">
                @endforeach
            </datalist>
            <p class="small text-muted mt-2 mb-0">
                <i data-lucide="info" size="12"></i> <strong>Tips:</strong> Ketik nama dan tekan <strong>Enter</strong>. Sistem akan mencari secara luas.
            </p>
        </div>
    </div>

    <div class="mt-4 pt-3 border-top d-flex justify-content-end gap-2">
        <button type="button" onclick="resetFilters()" class="btn btn-outline-secondary rounded-pill px-4 py-2 d-inline-flex align-items-center gap-2 transition-all">
            <i data-lucide="refresh-cw" size="16"></i>
            <span class="fw-bold">Reset</span>
        </button>
        <button type="button" onclick="applyFilters()" class="btn btn-primary rounded-pill px-4 py-2 d-inline-flex align-items-center gap-2 shadow-sm transition-all hover-lift">
            <i data-lucide="search" size="16"></i>
            <span class="fw-bold">Cari Data</span>
        </button>
    </div>
</div>
<!-- Results Layout: Full Width Stacked -->
<div class="row g-4 mb-4">
    {{-- Monitoring Data (Hasil Filter) --}}
    <div class="col-12">
        <div class="table-modern shadow-sm border-0">
            <div class="p-3 border-bottom d-flex justify-content-between align-items-center bg-white rounded-top">
                <div class="d-flex align-items-center gap-2">
                    <div class="stat-icon primary mb-0" style="width: 28px; height: 28px;">
                        <i data-lucide="monitor" style="width: 14px; height: 14px;"></i>
                    </div>
                    <h6 class="mb-0 fw-bold" style="color: #1e293b;">Monitoring Hasil</h6>
                </div>
                <div class="badge border rounded-pill px-3" style="background: #f1f5f9; color: #334155;">{{ $receptions->count() }} Item</div>
            </div>

            <div class="p-0 table-responsive" style="max-height: 600px; overflow-y: auto;">
                <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                    <thead style="background: #f8fafc;" class="sticky-top">
                        <tr class="border-bottom" style="color: #475569;">
                            <th class="ps-3 py-2 border-0">Detail Produksi</th>
                            <th class="text-center py-2 border-0" style="width:70px;">Foto</th>
                            <th class="text-end pe-3 py-2 border-0">Hasil</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse($receptions as $reception)
                        @php
                            $hasPhoto = !empty($reception->photo);
                            $photoUrl = $hasPhoto ? asset($reception->photo) : null;
                            $empName  = $reception->emp_name ?? 'Unknown';
                        @endphp
                        <tr style="cursor:pointer;" onclick="openDetailModal(
                            '{{ $empName }}',
                            '{{ $reception->date->format('d/m/Y') }}',
                            '{{ $reception->emp_plant ?? '-' }}',
                            '{{ $reception->emp_group ?? '-' }}',
                            '{{ $reception->shift }}',
                            '{{ $reception->job_today ?? '-' }}',
                            '{{ number_format($reception->production_count) }}',
                            '{{ $reception->ritase_result > 0 ? $reception->ritase_result . ' Rit' : '-' }}',
                            '{{ addslashes(Str::limit($reception->notes ?? '', 200)) }}',
                            '{{ $photoUrl }}'
                        )">
                            <td class="ps-3 py-3">
                                <div class="d-flex flex-column gap-1">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge" style="font-size: 0.65rem; background: #e0e7ff; color: #4338ca;">
                                            {{ $reception->date->format('d/m/Y') }}
                                        </span>
                                        <span class="fw-bold" style="color: #1e293b;">{{ $empName }}</span>
                                    </div>
                                    <div class="d-flex align-items-center gap-1 fw-medium" style="font-size: 0.7rem;">
                                        <span class="text-uppercase" style="color: #64748b;">PLANT {{ $reception->emp_plant }}</span>
                                        <span class="mx-1 opacity-50">•</span>
                                        <span style="color: #64748b;">GRUP {{ $reception->emp_group }}</span>
                                        <span class="mx-1 opacity-50">•</span>
                                        <span class="fw-bold" style="color: #0284c7;">SHIFT {{ $reception->shift }}</span>
                                        @if($reception->job_today)
                                            <span class="mx-1 opacity-50">•</span>
                                            <span style="color: #7c3aed;">{{ $reception->job_today }}</span>
                                        @endif
                                    </div>
                                    @if($reception->notes)
                                        <div class="text-muted mt-1 fst-italic" style="font-size: 0.7rem;">
                                            <i data-lucide="info" size="10"></i> {{ Str::limit($reception->notes, 30) }}
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="text-center py-3" onclick="event.stopPropagation();">
                                @if($hasPhoto)
                                    <img src="{{ $photoUrl }}" alt="Foto"
                                         class="report-photo-thumb"
                                         onclick="openLightbox('{{ $photoUrl }}', '{{ $empName }} – {{ $reception->date->format('d/m/Y') }}')"
                                         title="Klik untuk perbesar">
                                @else
                                    <span class="badge-no-foto">—</span>
                                @endif
                            </td>
                            <td class="text-end pe-3 py-3">
                                <div class="d-flex flex-column align-items-end gap-1">
                                    <span class="badge fs-6 fw-bold" style="background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0;">
                                        {{ number_format($reception->production_count) }}
                                    </span>
                                    @if($reception->ritase_result > 0)
                                        <span class="badge" style="font-size: 0.7rem; background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd;">
                                            {{ $reception->ritase_result }} Rit
                                        </span>
                                    @endif
                                    @if($hasPhoto)
                                        <span class="badge-foto">📷 Foto</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="text-center py-5 text-muted">
                                <div class="d-flex flex-column align-items-center opacity-50">
                                    <i data-lucide="inbox" class="mb-2" size="48"></i>
                                    <div class="small fw-bold">Data Tidak Ditemukan</div>
                                    <div class="smallest">Coba sesuaikan filter Anda</div>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($receptions->count() > 0)
                <div class="p-2 bg-light border-top text-center smallest text-muted">
                    Menampilkan {{ $receptions->count() }} baris data terakhir sesuai perintah filter
                </div>
            @endif
        </div>
    </div>

    {{-- Peringkat Grup Per Plant --}}
    <div class="col-12">
        <div class="table-modern">
            <div class="p-3 border-bottom d-flex align-items-center gap-2">
                <div class="stat-icon warning mb-0" style="width: 28px; height: 28px;">
                    <i data-lucide="users-2" style="width: 14px; height: 14px;"></i>
                </div>
                <h6 class="mb-0 fw-bold">Peringkat Grup Per Plant</h6>
            </div>
            <div class="p-3">
                <div class="row g-3">
                    @forelse($groupRanking as $plantName => $groups)
                    <div class="col-md-3">
                        <div class="border rounded-3 p-2 bg-light">
                            <div class="fw-bold small text-uppercase text-muted border-bottom mb-2 pb-1">Plant {{ $plantName }}</div>
                            <table class="table table-sm table-borderless mb-0">
                                <thead>
                                    <tr class="text-muted" style="font-size: 0.65rem;">
                                        <th>GRUP</th>
                                        <th class="text-end">PROD / RIT</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($groups as $group)
                                    <tr>
                                        <td class="fw-semibold small">Grup {{ $group['name'] }}</td>
                                        <td class="text-end">
                                            <span class="badge bg-success fw-bold" style="font-size: 0.75rem;" title="Produksi">
                                                {{ number_format($group['production']) }}
                                            </span>
                                            <span class="badge bg-info text-dark fw-bold" style="font-size: 0.75rem;" title="Ritase">
                                                {{ number_format($group['ritase']) }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @empty
                    <div class="col-12 text-center py-4 text-muted">No data</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Peringkat Operator Per Plant --}}
    <div class="col-12">
        <div class="table-modern">
            <div class="p-3 border-bottom d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <div class="stat-icon primary mb-0" style="width: 28px; height: 28px;">
                        <i data-lucide="award" style="width: 14px; height: 14px;"></i>
                    </div>
                    <h6 class="mb-0 fw-bold">Peringkat Operator Per Plant</h6>
                </div>
                <div class="badge bg-sky-50 text-sky-600 fw-medium">Produksi (Hijau) & Ritase (Biru)</div>
            </div>
            
            <div class="p-3">
                <div class="row g-4">
                    @forelse($operatorRanking as $plantName => $operators)
                    <div class="col-md-6 col-xl-3">
                        <div class="p-3 rounded-lg border bg-light shadow-sm h-100">
                            <h6 class="fw-bold mb-3 d-flex justify-content-between">
                                <span>Plant {{ $plantName }}</span>
                                <span class="badge bg-white text-dark border">{{ $operators->count() }} Orang</span>
                            </h6>
                            <div class="list-group list-group-flush rounded shadow-sm overflow-hidden border">
                                {{-- Header row --}}
                                <div class="list-group-item bg-light py-1 px-3 d-flex justify-content-between align-items-center">
                                    <span class="text-muted fw-bold" style="font-size: 0.6rem; text-transform: uppercase; letter-spacing: 0.05em;">Operator</span>
                                    <span class="text-muted fw-bold" style="font-size: 0.6rem; text-transform: uppercase; letter-spacing: 0.05em;">Prod / Rit</span>
                                </div>
                                @forelse($operators as $op)
                                <div class="list-group-item d-flex justify-content-between align-items-center py-2 px-3">
                                    <div class="d-flex align-items-center gap-1" style="max-width: 60%;">
                                        <div class="text-muted fw-bold small" style="width: 15px;">{{ $loop->iteration }}.</div>
                                        <div class="small fw-semibold text-truncate">{{ $op['name'] }}</div>
                                    </div>
                                    <div class="d-flex gap-1">
                                        <span class="badge bg-success small" style="font-size: 0.65rem;" title="Produksi">{{ number_format($op['production']) }}</span>
                                        <span class="badge bg-info text-dark small" style="font-size: 0.65rem;" title="Ritase">{{ number_format($op['ritase']) }}</span>
                                    </div>
                                </div>
                                @empty
                                <div class="list-group-item text-center py-3 text-muted">No data</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-12 text-center py-5">
                        <i data-lucide="inbox" class="text-muted mb-2" size="48"></i>
                        <p class="text-muted">Tidak ada data operator untuk periode ini</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ====== DETAIL MODAL ====== --}}
<div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex align-items-center gap-2">
                    <i data-lucide="clipboard-list" style="color:white; width:20px; height:20px;"></i>
                    <h5 class="modal-title fw-bold text-white mb-0" id="detailModalTitle">Detail Produksi</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-4">
                    {{-- Kolom kiri: Info --}}
                    <div class="col-md-6">
                        <div class="detail-info-row">
                            <span class="detail-info-label"><i data-lucide="user" size="12"></i> Operator</span>
                            <span class="detail-info-value" id="d-name">-</span>
                        </div>
                        <div class="detail-info-row">
                            <span class="detail-info-label"><i data-lucide="calendar" size="12"></i> Tanggal</span>
                            <span class="detail-info-value" id="d-date">-</span>
                        </div>
                        <div class="detail-info-row">
                            <span class="detail-info-label"><i data-lucide="factory" size="12"></i> Plant</span>
                            <span class="detail-info-value" id="d-plant">-</span>
                        </div>
                        <div class="detail-info-row">
                            <span class="detail-info-label"><i data-lucide="users-2" size="12"></i> Grup</span>
                            <span class="detail-info-value" id="d-group">-</span>
                        </div>
                        <div class="detail-info-row">
                            <span class="detail-info-label"><i data-lucide="clock" size="12"></i> Shift</span>
                            <span class="detail-info-value" id="d-shift">-</span>
                        </div>
                        <div class="detail-info-row">
                            <span class="detail-info-label"><i data-lucide="briefcase" size="12"></i> Pekerjaan</span>
                            <span class="detail-info-value" id="d-job">-</span>
                        </div>
                        <div class="detail-info-row">
                            <span class="detail-info-label"><i data-lucide="layers" size="12"></i> Produksi</span>
                            <span class="detail-info-value" id="d-prod">-</span>
                        </div>
                        <div class="detail-info-row">
                            <span class="detail-info-label"><i data-lucide="navigation" size="12"></i> Ritase</span>
                            <span class="detail-info-value" id="d-rit">-</span>
                        </div>
                        <div class="detail-info-row">
                            <span class="detail-info-label"><i data-lucide="message-square" size="12"></i> Catatan</span>
                            <span class="detail-info-value" id="d-notes" style="white-space:pre-line;">-</span>
                        </div>
                    </div>
                    {{-- Kolom kanan: Foto --}}
                    <div class="col-md-6 d-flex flex-column align-items-center justify-content-start" id="d-photo-col">
                        <div class="w-100 text-center" id="d-photo-wrap">
                            <img id="d-photo" src="" alt="Foto Hasil Kerja" class="detail-photo-full mb-2"
                                 onclick="this.classList.toggle('zoomed')"
                                 title="Klik untuk zoom | Klik lagi untuk kecil">
                            <div class="text-muted small mt-1">Klik foto untuk zoom • Klik lagi untuk normal</div>
                            <button class="btn btn-sm btn-outline-primary mt-2 rounded-pill"
                                    onclick="openLightbox(document.getElementById('d-photo').src, document.getElementById('d-name').textContent)">
                                <i data-lucide="maximize-2" size="14" class="me-1"></i> Buka Fullscreen
                            </button>
                        </div>
                        <div id="d-no-photo" class="text-center py-5" style="display:none;">
                            <div style="font-size:3rem;">📷</div>
                            <div class="text-muted small mt-2">Tidak ada foto terlampir</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ====== PHOTO LIGHTBOX ====== --}}
<div id="photoLightbox" onclick="closeLightbox()">
    <span class="lb-close" onclick="closeLightbox()">&times;</span>
    <img id="lbImg" src="" alt="">
    <div class="lb-caption" id="lbCaption"></div>
</div>

@endsection

@push('scripts')
<script>
function updateFilters() {
    const type = document.getElementById('filterType').value;
    const startLabel = document.getElementById('startLabel');
    const endLabel = document.getElementById('endLabel');
    
    // Hide all input containers
    ['dailyStart', 'monthlyStart', 'yearlyStart', 'dailyEnd', 'monthlyEnd', 'yearlyEnd'].forEach(id => {
        document.getElementById(id).style.display = 'none';
    });
    
    if (type === 'daily') {
        startLabel.innerText = "Tanggal Awal";
        endLabel.innerText = "Tanggal Akhir";
        document.getElementById('dailyStart').style.display = 'block';
        document.getElementById('dailyEnd').style.display = 'block';
    } else if (type === 'monthly') {
        startLabel.innerText = "Bulan Awal";
        endLabel.innerText = "Bulan Akhir";
        document.getElementById('monthlyStart').style.display = 'block';
        document.getElementById('monthlyEnd').style.display = 'block';
    } else if (type === 'yearly') {
        document.getElementById('yearlyStart').style.display = 'block';
        document.getElementById('yearlyEnd').style.display = 'block';
    } else if (type === 'all') {
        startLabel.innerText = "-";
        endLabel.innerText = "Seluruh Sejarah Data";
        // Hide date inputs via container
        document.getElementById('dateFilterRange_Start').style.display = 'none';
        document.getElementById('dateFilterRange_End').style.display = 'none';
        return; // Early return to avoid showing containers
    }
    
    // Ensure containers are visible if not 'all'
    document.getElementById('dateFilterRange_Start').style.display = 'block';
    document.getElementById('dateFilterRange_End').style.display = 'block';
}

function applyFilters() {
    // COMPLIANCE: IDs match EXACTLY as per instructions (filterType, operatorNameInput, etc.)
    let filterType = document.getElementById('filterType').value;
    const shift = document.getElementById('shiftFilter').value;
    const plant = document.getElementById('plantFilter').value;
    const group = document.getElementById('groupFilter').value;
    const operatorName = document.getElementById('operatorNameInput').value;

    // Build absolute URL for consistency
    const baseUrl = window.location.origin + window.location.pathname;
    let url = new URL(baseUrl);
    
    // Set parameters explicitly
    url.searchParams.set('filter_type', filterType);
    if (shift) url.searchParams.set('shift', shift);
    if (plant) url.searchParams.set('plant', plant);
    if (group) url.searchParams.set('group', group);
    if (operatorName.trim()) url.searchParams.set('operator_name', operatorName.trim());
    
    // Handle Date/Month/Year logic based on type
    if (filterType === 'daily') {
        url.searchParams.set('start_date', document.getElementById('startDateInput').value);
        url.searchParams.set('end_date', document.getElementById('endDateInput').value);
    } else if (filterType === 'monthly') {
        url.searchParams.set('start_month', document.getElementById('startMonthInput').value);
        url.searchParams.set('end_month', document.getElementById('endMonthInput').value);
    } else if (filterType === 'yearly') {
        url.searchParams.set('year', document.getElementById('yearInput').value);
    }
    
    // Force browser redirection
    window.location.href = url.toString();
}

function resetFilters() {
    window.location.href = window.location.pathname;
}


// ECharts Trend
document.addEventListener('DOMContentLoaded', function() {
    updateFilters();
});

// ====== DETAIL MODAL ======
function openDetailModal(name, date, plant, group, shift, job, prod, rit, notes, photoUrl) {
    document.getElementById('d-name').textContent  = name  || '-';
    document.getElementById('d-date').textContent  = date  || '-';
    document.getElementById('d-plant').textContent = plant !== '-' ? 'Plant ' + plant : '-';
    document.getElementById('d-group').textContent = group !== '-' ? 'Grup ' + group  : '-';
    document.getElementById('d-shift').textContent = shift ? 'Shift ' + shift : '-';
    document.getElementById('d-job').textContent   = job   || '-';
    document.getElementById('d-prod').textContent  = prod  || '-';
    document.getElementById('d-rit').textContent   = rit   || '-';
    document.getElementById('d-notes').textContent = notes || '(Tidak ada catatan)';

    const photoEl   = document.getElementById('d-photo');
    const photoWrap = document.getElementById('d-photo-wrap');
    const noPhoto   = document.getElementById('d-no-photo');

    // Reset zoom
    photoEl.classList.remove('zoomed');

    if (photoUrl && photoUrl !== 'null' && photoUrl !== '') {
        photoEl.src = photoUrl;
        photoWrap.style.display = 'block';
        noPhoto.style.display   = 'none';
    } else {
        photoEl.src = '';
        photoWrap.style.display = 'none';
        noPhoto.style.display   = 'block';
    }

    document.getElementById('detailModalTitle').textContent = name || 'Detail Produksi';

    var modal = new bootstrap.Modal(document.getElementById('detailModal'));
    modal.show();

    // Re-init lucide icons inside modal
    if (window.lucide) lucide.createIcons();
}

// ====== LIGHTBOX ======
function openLightbox(src, caption) {
    if (!src || src === 'null' || src === '') return;
    document.getElementById('lbImg').src      = src;
    document.getElementById('lbCaption').textContent = caption || '';
    document.getElementById('photoLightbox').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeLightbox() {
    document.getElementById('photoLightbox').classList.remove('active');
    document.body.style.overflow = '';
}

// Close lightbox with ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeLightbox();
});
</script>
@endpush
