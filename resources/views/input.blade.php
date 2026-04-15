@extends('layouts.app')

@section('page_title', 'Input Produksi' . ($plant ? ' — Plant ' . $plant : ''))

@section('content')
    <style>
        .input-accent-wrap {
            position: relative;
            padding-left: 18px;
        }

        .input-accent-wrap::before {
            content: '';
            position: absolute;
            left: 0;
            top: 4px;
            bottom: 4px;
            width: 6px;
            border-radius: 999px;
            background: linear-gradient(180deg, #0d6efd 0%, #0dcaf0 33%, #ffc107 66%, #fd7e14 100%);
            opacity: 0.35;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.12);
        }

        .reset-btn:active {
            transform: scale(0.95);
            transition: transform 0.1s ease;
        }
        
        /* Class untuk sembunyikan input ritase di form */
        .ritase-form-group {
            display: none;
        }
        .ritase-form-group.show-ritase {
            display: block;
        }

        @php $hasDriver = false; @endphp

        /* Definitive Alignment Fix */
        .monitoring-table {
            table-layout: fixed;
            width: 100%;
        }
        .monitoring-table th, 
        .monitoring-table td {
            padding: 12px 16px !important;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
        }
        .monitoring-table .col-no { width: 50px; text-align: center; }
        .monitoring-table .col-nama { width: auto; text-align: left; }
        .monitoring-table .col-pekerjaan { width: 180px; text-align: left; }
        .monitoring-table .col-shift { width: 90px; text-align: center; }
        .monitoring-table .col-hasil { width: 120px; text-align: right; }
        .monitoring-table .col-aksi { width: 90px; text-align: center; }
        .monitoring-table .col-foto { width: 70px; text-align: center; }

        /* Job Dropdown Multiselect */
        .job-dropdown-wrapper {
            position: relative;
        }
        .job-dropdown-trigger {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #fff;
            border: 1.5px solid #dee2e6;
            border-radius: 0.5rem;
            padding: 0.5rem 0.75rem;
            cursor: pointer;
            min-height: 42px;
            transition: border-color 0.2s;
            user-select: none;
        }
        .job-dropdown-trigger:hover,
        .job-dropdown-trigger.open {
            border-color: #0d6efd;
        }
        .job-dropdown-trigger .placeholder { color: #adb5bd; font-size: 0.875rem; }
        .job-dropdown-trigger .selected-text { font-size: 0.8rem; color: #1e293b; font-weight: 500; }
        .job-dropdown-panel {
            display: none;
            position: absolute;
            top: calc(100% + 4px);
            left: 0; right: 0;
            background: #fff;
            border: 1.5px solid #0d6efd;
            border-radius: 0.5rem;
            box-shadow: 0 8px 24px rgba(0,0,0,0.12);
            z-index: 200;
            padding: 0.5rem 0.75rem;
            max-height: 220px;
            overflow-y: auto;
        }
        .job-dropdown-panel.open { display: block; }
        .job-check-label {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 5px 4px;
            cursor: pointer;
            border-radius: 6px;
            font-size: 0.875rem;
            transition: background 0.15s;
        }
        .job-check-label:hover { background: #f0f9ff; }
        .job-check-label:has(input:checked) { color: #0369a1; font-weight: 600; }
        .photo-thumb {
            width: 48px;
            height: 48px;
            object-fit: cover;
            border-radius: 8px;
            border: 2px solid #e2e8f0;
            cursor: zoom-in;
            transition: all 0.2s ease;
        }
        .photo-thumb:hover {
            border-color: #0ea5e9;
            transform: scale(1.1);
            box-shadow: 0 4px 15px rgba(14,165,233,0.35);
        }
        .photo-preview-input {
            width: 64px;
            height: 64px;
            object-fit: cover;
            border-radius: 10px;
            border: 2px solid #0d6efd;
        }

        /* Lightbox */
        #lightbox-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.92);
            z-index: 9999;
            align-items: center;
            justify-content: center;
        }
        #lightbox-overlay.active { display: flex; }
        #lightbox-img {
            max-width: 92vw;
            max-height: 88vh;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.5);
            object-fit: contain;
        }
        #lightbox-close {
            position: absolute;
            top: 1.25rem;
            right: 1.25rem;
            width: 40px; height: 40px;
            background: rgba(255,255,255,0.15);
            border: none;
            border-radius: 50%;
            color: white;
            font-size: 1.3rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s;
        }
        #lightbox-close:hover { background: rgba(255,255,255,0.3); }

        @media (max-width: 768px) {
            @if($hasDriver)
            .ritase-col {
                display: flex !important;
            }
            @endif

            .table-responsive thead {
                display: none; 
            }

            .table-responsive tbody tr {
                display: block;
                margin-bottom: 20px;
                border: 1px solid #e2e8f0 !important;
                border-radius: 15px;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
                background: #ffffff;
                overflow: hidden;
            }

            .table-responsive tbody td {
                display: flex;
                justify-content: space-between;
                align-items: center;
                text-align: right !important;
                padding: 12px 15px !important;
                border-bottom: 1px solid #f1f5f9 !important;
            }

            /* Label di sebelah kiri */
            .table-responsive tbody td::before {
                content: attr(data-label);
                font-weight: 800;
                text-transform: uppercase;
                font-size: 10px;
                color: #64748b;
                text-align: left;
                flex: 1;
            }

            /* Hilangkan border bawah di baris terakhir kartu */
            .table-responsive tbody td:last-child {
                border-bottom: none !important;
                background: #f8fafc;
                justify-content: center;
            }

            .ritase-form-group.show-ritase {
                display: block !important;
            }
        }
</style>

    <div class="input-accent-wrap">
        <div class="row animate__animated animate__fadeIn">
            {{-- FILTER PLANT & GRUP --}}
            <div class="col-12 mb-4">
                <div class="glass-card p-3 shadow-sm border-0">
                    <div class="row g-3 align-items-center">
                        {{-- PLANT --}}
                        <div class="col-md-6">
                            <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center gap-3">
                                <div class="d-flex align-items-center gap-2 flex-shrink-0">
                                    <div class="bg-primary text-white p-2 rounded-3">
                                        <i data-lucide="building-2" size="18"></i>
                                    </div>
                                    <h6 class="fw-bold mb-0 small text-uppercase">Plant</h6>
                                </div>
                                <div class="btn-group shadow-sm" role="group" style="min-width: max-content;">
                                    @foreach (['B', 'H', 'I', 'T'] as $p)
                                        <a href="{{ route('input.form', ['plant' => $p, 'group' => request('group')]) }}"
                                            class="btn btn-outline-primary px-3 py-2 fw-bold {{ $plant == $p ? 'active' : '' }}">
                                            {{ $p }}
                                        </a>
                                    @endforeach
                                    <a href="{{ route('input.form') }}"
                                        class="btn btn-outline-secondary px-3 py-2 fw-bold reset-btn {{ !$plant ? 'active' : '' }}">
                                        <i data-lucide="refresh-ccw" size="14"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        {{-- GRUP --}}
                        <div class="col-md-6">
                            <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center gap-3">
                                <div class="d-flex align-items-center gap-2 flex-shrink-0">
                                    <div class="bg-warning text-white p-2 rounded-3">
                                        <i data-lucide="filter" size="18"></i>
                                    </div>
                                    <h6 class="fw-bold mb-0 small text-uppercase">Grup</h6>
                                </div>
                                <div class="btn-group shadow-sm" role="group" style="min-width: max-content;">
                                    @foreach (['A', 'B', 'C', 'D'] as $g)
                                        <a href="{{ route('input.form', ['plant' => $plant, 'group' => $g]) }}"
                                            class="btn btn-outline-primary px-3 py-2 fw-bold {{ request('group') == $g ? 'active' : '' }}">
                                            {{ $g }}
                                        </a>
                                    @endforeach
                                    <a href="{{ route('input.form', ['plant' => $plant]) }}"
                                        class="btn btn-outline-secondary px-3 py-2 fw-bold reset-btn {{ !request('group') ? 'active' : '' }}">
                                        <i data-lucide="refresh-ccw" size="14"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-12">
                <div class="glass-card shadow-sm border-0 p-4">
                    <div class="mb-4 d-flex justify-content-between align-items-center border-bottom pb-3">
                        <div>
                            <h5 class="fw-bold text-dark mb-1">Entry Data</h5>
                            <p class="text-muted small mb-0">Plant: <strong>{{ $plant ?? 'Semua' }}</strong> | Filter Terpilih: <strong>{{ request('group') ?? 'Semua Grup' }}</strong></p>
                        </div>
                    </div>

                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('input.store', $plant ?? 'B') }}" method="POST" id="production-form" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="group" value="{{ request('group') }}">
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-bold text-uppercase text-muted">Nama Operator</label>
                                <input type="hidden" name="employee_id" id="employee-id-hidden" required>
                                <div class="position-relative" id="operator-search-wrapper">
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i data-lucide="search" size="18"></i></span>
                                        <input type="text" id="operator-search" class="form-control border-start-0 border-end-0 shadow-none"
                                               placeholder="Ketik nama operator..." autocomplete="off">
                                        <button type="button" class="btn btn-outline-secondary px-3" id="btn-clear-operator" title="Hapus" style="display:none;">
                                            <i data-lucide="x" size="18"></i>
                                        </button>
                                    </div>
                                    <div id="operator-dropdown" class="position-absolute w-100 bg-white border rounded-3 shadow mt-1 overflow-auto" 
                                         style="max-height: 220px; z-index: 100; display: none;">
                                        @foreach ($employees as $employee)
                                            @php $alreadyInputted = in_array($employee->employee_id, $inputtedIds); @endphp
                                            <div class="operator-option px-3 py-2{{ $alreadyInputted ? ' opacity-50' : '' }}" 
                                                 style="cursor: {{ $alreadyInputted ? 'not-allowed' : 'pointer' }};"
                                                 data-id="{{ $employee->employee_id }}" data-name="{{ $employee->name }}"
                                                 data-inputted="{{ $alreadyInputted ? 'true' : 'false' }}">
                                                <span class="fw-bold{{ $alreadyInputted ? ' text-decoration-line-through' : '' }}">{{ $employee->employee_id }}</span>
                                                <span class="text-muted mx-1">—</span>
                                                <span class="{{ $alreadyInputted ? 'text-decoration-line-through' : '' }}">{{ $employee->name }}</span>
                                                @if($alreadyInputted)
                                                    <span class="badge bg-success bg-opacity-10 text-success ms-2" style="font-size: 10px;">✓ Sudah Input</span>
                                                @endif
                                            </div>
                                        @endforeach
                                        <div id="no-result" class="px-3 py-2 text-muted small" style="display: none;">Tidak ditemukan</div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-bold text-uppercase text-muted">
                                    Pekerjaan Hari Ini <span class="text-danger">*</span>
                                </label>
                                <div class="job-dropdown-wrapper" id="job-dropdown-wrapper">
                                    <div class="job-dropdown-trigger" id="job-dropdown-trigger" onclick="toggleJobDropdown()">
                                        <span id="job-selected-text" class="placeholder">Pilih</span>
                                        <svg id="job-chevron" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="transition:transform 0.2s;flex-shrink:0;"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                    </div>
                                    <div class="job-dropdown-panel" id="job-dropdown-panel">
                                        @foreach(['Scan','Strapping','Tempel Stiker','Susun Tire','Pressing','Driver','Leader','Pasang Product Tage OE'] as $job)
                                        <label class="job-check-label">
                                            <input type="checkbox"
                                                   name="job_today[]"
                                                   value="{{ $job }}"
                                                   class="job-checkbox form-check-input mt-0"
                                                   onchange="updateJobText(); toggleRitase();">
                                            {{ $job }}
                                        </label>
                                        @endforeach
                                        <div id="job-validation-msg" class="text-danger small mt-1" style="display:none;">&#9888; Pilih minimal satu!</div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-sm-6 col-md-3">
                                <label class="form-label small fw-bold text-uppercase text-muted">Shift</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i data-lucide="clock" size="18"></i></span>
                                    <select name="shift" id="shift-select" class="form-select border-start-0 shadow-none" required onchange="onShiftChange()">
                                        <option value="">-- Pilih --</option>
                                        <option value="1">1 (Pagi)</option>
                                        <option value="2">2 (Sore)</option>
                                        <option value="3">3 (Malam)</option>
                                    </select>
                                </div>
                            </div>

                            {{-- Tanggal Input --}}
                            <div class="col-12 col-sm-6 col-md-3">
                                <label class="form-label small fw-bold text-uppercase text-muted">
                                    Tanggal
                                    <span class="text-muted fw-normal ms-1" id="date-hint" style="font-size:0.7rem;"></span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i data-lucide="calendar" size="18"></i></span>
                                    <input type="date" name="date" id="date-input"
                                           class="form-control border-start-0 shadow-none"
                                           value="{{ date('Y-m-d') }}"
                                           max="{{ date('Y-m-d') }}"
                                           required>
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-bold text-uppercase text-muted">Jumlah Produksi</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i data-lucide="layers" size="18"></i></span>
                                    <input type="number" name="production_count" id="production-count" class="form-control border-start-0 shadow-none" placeholder="Total unit">
                                </div>
                            </div>



                            <div class="col-12">
                                <label class="form-label small fw-bold text-uppercase text-muted">Catatan</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i data-lucide="message-square" size="18"></i></span>
                                    <textarea name="notes" rows="2" class="form-control border-start-0 shadow-none" placeholder="Kendala cuaca, mesin, dll..."></textarea>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label small fw-bold text-uppercase text-muted">Lampiran Foto (Opsional)</label>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i data-lucide="camera" size="18"></i></span>
                                        <input type="file" name="photo" id="photo-input" class="form-control border-start-0 shadow-none" accept="image/*">
                                    </div>
                                    <img id="photo-preview" src="" alt="Preview" class="photo-preview-input" style="display:none;">
                                </div>
                            </div>

                            <div class="col-12 mt-3">
                                <button type="submit" class="btn btn-primary w-100 py-3 fw-bold shadow-sm">
                                    <i data-lucide="send" class="me-2" size="20"></i> SIMPAN DATA
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-12 mt-5">
                <div class="glass-card shadow-sm border-0 p-4">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <div>
                            <h5 class="fw-bold text-dark mb-1">Monitoring Input Langsung</h5>
                            <p class="text-muted small mb-0">Daftar operator yang sudah diinput hari ini {{ $plant ? '(Plant ' . $plant . ')' : '(Semua Plant)' }}</p>
                        </div>
                        <div class="bg-success bg-opacity-10 text-success p-2 rounded-3">
                            <i data-lucide="activity" size="20"></i>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover monitoring-table">
                            <thead class="table-light">
                                <tr class="text-uppercase small fw-bold">
                                    <th class="col-no">No</th>
                                    <th class="col-nama">Nama</th>
                                    <th class="col-pekerjaan">Pekerjaan</th>
                                    <th class="col-shift">Shift</th>
                                    <th class="col-hasil">Hasil</th>
                                    <th class="col-foto">Foto</th>
                                    <th class="py-3 d-md-none text-center" style="width: 100px;">Catatan</th>
                                    <th class="col-aksi">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($liveData as $index => $data)
                                    <tr>
                                        <td data-label="No" class="col-no">{{ $index + 1 }}</td>
                                        <td data-label="Nama" class="col-nama">
                                            <div class="fw-bold text-dark">{{ $data->operator_name }}</div>
                                            <div class="d-flex flex-column">
                                                <small class="text-muted">ID: {{ $data->operator_id }}</small>
                                                <div class="d-none d-md-block mt-1">
                                                    @if($data->notes)
                                                    <span class="badge bg-secondary bg-opacity-10 text-secondary fw-normal py-1 px-2" style="font-size: 10px; border: 1px dashed rgba(100, 116, 139, 0.2);">
                                                        {{ $data->notes }}
                                                    </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td data-label="Pekerjaan" class="col-pekerjaan">
                                            <div class="d-flex flex-wrap gap-1">
                                                @foreach(array_filter(array_map('trim', explode(',', $data->job_today))) as $job)
                                                    <span class="badge bg-info bg-opacity-10 text-info px-2 py-1" style="font-size:0.68rem;">{{ $job }}</span>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td data-label="Shift" class="col-shift">Shift {{ $data->shift }}</td>
                                        <td data-label="Hasil" class="col-hasil">{{ number_format($data->production_count) }}</td>
                                        <td data-label="Foto" class="col-foto">
                                            @if($data->photo)
                                                <img src="{{ asset($data->photo) }}"
                                                     alt="Foto"
                                                     class="photo-thumb"
                                                     title="Klik untuk perbesar"
                                                     onclick="openLightbox('{{ asset($data->photo) }}')">
                                            @else
                                                <span class="text-muted small">-</span>
                                            @endif
                                        </td>
                                        <td data-label="Catatan" class="d-md-none text-center">
                                            <div class="small text-muted">{{ $data->notes ?? '-' }}</div>
                                        </td>
                                        <td data-label="Aksi" class="col-aksi">
                                            <div class="d-flex gap-1 justify-content-center">
                                                <a href="{{ route('input.edit', ['plant' => $plant ?? $data->emp_plant, 'id' => $data->id]) }}"
                                                    class="btn btn-sm btn-outline-warning rounded-pill px-2">
                                                    <i data-lucide="edit-3" size="12"></i>
                                                </a>
                                                <form action="{{ route('input.delete', ['plant' => $plant ?? $data->emp_plant, 'id' => $data->id]) }}"
                                                      method="POST" style="display:inline;"
                                                      onsubmit="return confirm('Hapus data {{ $data->operator_name }}?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2">
                                                        <i data-lucide="trash-2" size="12"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5 text-muted">Belum ada data.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
    </div>

    {{-- Lightbox --}}
    <div id="lightbox-overlay" onclick="closeLightbox()">
        <button id="lightbox-close" onclick="closeLightbox()">&#x2715;</button>
        <img id="lightbox-img" src="" alt="Foto Fullscreen">
    </div>

    <script>
        // === SEARCHABLE OPERATOR DROPDOWN ===
        (function() {
            const searchInput = document.getElementById('operator-search');
            const dropdown = document.getElementById('operator-dropdown');
            const hiddenInput = document.getElementById('employee-id-hidden');
            const options = document.querySelectorAll('.operator-option');
            const noResult = document.getElementById('no-result');
            const btnClear = document.getElementById('btn-clear-operator');

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

            // TOMBOL HAPUS
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
                    if (this.dataset.inputted === 'true') {
                        alert('Operator ini sudah menginput laporan hari ini!');
                        return;
                    }
                    searchInput.value = this.dataset.name;
                    hiddenInput.value = this.dataset.id;
                    dropdown.style.display = 'none';
                    searchInput.style.borderColor = '#198754';
                    btnClear.style.display = 'block';
                });

                opt.addEventListener('mouseenter', function() {
                    if (this.dataset.inputted !== 'true') {
                        this.style.background = '#f0f9ff';
                    }
                });
                opt.addEventListener('mouseleave', function() {
                    this.style.background = '';
                });
            });

            document.addEventListener('click', function(e) {
                if (!document.getElementById('operator-search-wrapper').contains(e.target)) {
                    dropdown.style.display = 'none';
                }
            });
        })();

        // === JOB DROPDOWN ===
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
            const checked  = document.querySelectorAll('.job-checkbox:checked');
            const textEl   = document.getElementById('job-selected-text');
            if (checked.length === 0) {
                textEl.textContent = 'Pilih';
                textEl.className   = 'placeholder';
            } else {
                textEl.textContent = Array.from(checked).map(c => c.value).join(', ');
                textEl.className   = 'selected-text';
            }
        }

        // Tutup dropdown klik di luar
        document.addEventListener('click', function(e) {
            const wrapper = document.getElementById('job-dropdown-wrapper');
            if (wrapper && !wrapper.contains(e.target)) {
                document.getElementById('job-dropdown-panel').classList.remove('open');
                document.getElementById('job-dropdown-trigger').classList.remove('open');
                document.getElementById('job-chevron').style.transform = '';
            }
        });

        // === SHIFT → AUTO DATE ===
        function onShiftChange() {
            const shift     = document.getElementById('shift-select').value;
            const dateInput = document.getElementById('date-input');
            const hint      = document.getElementById('date-hint');

            const today     = new Date();
            const yesterday = new Date(today);
            yesterday.setDate(today.getDate() - 1);

            const fmt = d => d.toISOString().split('T')[0];

            if (shift === '3') {
                dateInput.value = fmt(yesterday);
                hint.textContent = '(otomatis kemarin)';
                hint.style.color = '#f59e0b';
            } else {
                dateInput.value = fmt(today);
                hint.textContent = '';
            }
        }

        // === TOGGLE: visual feedback checkbox ===
        function toggleRitase() {
            document.querySelectorAll('.job-checkbox').forEach(cb => {
                const label = cb.closest('.job-check-label');
                if (label) {
                    label.style.background  = cb.checked ? '#e0f2fe' : '';
                    label.style.color       = cb.checked ? '#0369a1' : '';
                }
            });
        }

        // === FORM VALIDATION ===
        document.getElementById('production-form').onsubmit = function(e) {
            const employeeId      = document.getElementById('employee-id-hidden').value;
            const checkedJobs     = document.querySelectorAll('.job-checkbox:checked');
            const productionCount = document.getElementById('production-count').value;

            if (!employeeId) {
                alert('Harap pilih Nama Operator!');
                return false;
            }

            if (checkedJobs.length === 0) {
                document.getElementById('job-validation-msg').style.display = 'block';
                document.querySelector('.border.rounded-3').scrollIntoView({ behavior: 'smooth', block: 'center' });
                return false;
            } else {
                document.getElementById('job-validation-msg').style.display = 'none';
            }

            if (!productionCount || parseInt(productionCount) <= 0) {
                alert('Harap isi Jumlah Produksi dengan benar!');
                return false;
            }
        };

        document.addEventListener('DOMContentLoaded', function() {
            toggleRitase();
        });

        // === PHOTO PREVIEW ===
        document.getElementById('photo-input').addEventListener('change', function(e) {
            var preview = document.getElementById('photo-preview');
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

        // === LIGHTBOX ===
        function openLightbox(src) {
            document.getElementById('lightbox-img').src = src;
            document.getElementById('lightbox-overlay').classList.add('active');
        }
        function closeLightbox() {
            document.getElementById('lightbox-overlay').classList.remove('active');
            document.getElementById('lightbox-img').src = '';
        }
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeLightbox();
        });
    </script>
@endsection