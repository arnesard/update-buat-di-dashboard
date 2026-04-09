@extends('layouts.app')

@section('title', 'Manajemen Karyawan')

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
        --warning-gradient: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        --info-gradient: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
    }

    body {
        background-color: #f8fafc !important;
    }

    .text-slate-500 { color: var(--slate-500) !important; }
    .text-slate-600 { color: var(--slate-600) !important; }
    .text-slate-900 { color: var(--slate-900) !important; }
    .bg-slate-50 { background-color: var(--slate-50) !important; }
    .bg-sky-light { background-color: var(--sky-light) !important; }
    .text-sky { color: var(--sky-600) !important; }
    
    .btn-sky {
        background-color: var(--sky-500);
        background-image: var(--primary-gradient);
        color: white;
        border: none;
        transition: all 0.3s ease;
    }
    .btn-sky:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(14, 165, 233, 0.3);
        color: white;
    }
    
    .max-w-7xl { max-width: 80rem; }
    .tracking-tight { letter-spacing: -0.025em; }
    .rounded-4 { border-radius: 1.25rem !important; }
    .rounded-3 { border-radius: 0.85rem !important; }
    
    .badge-sky-light {
        background-color: rgba(14, 165, 233, 0.1);
        color: var(--sky-600);
    }
    
    /* Plant Colors */
    .badge-plant-B { background-color: #0d6efd !important; color: white !important; }
    .badge-plant-H { background-color: #0dcaf0 !important; color: white !important; }
    .badge-plant-I { background-color: #ffc107 !important; color: white !important; }
    .badge-plant-T { background-color: #fd7e14 !important; color: white !important; }
    
    .border-bottom-slate-100 {
        border-bottom: 1px solid var(--slate-100);
    }
    
    /* Stats Cards */
    .card-vibrant {
        border: none !important;
        color: white !important;
        transition: transform 0.3s ease, box-shadow 0.3s ease !important;
    }
    .card-vibrant .text-muted, .card-vibrant .text-slate-900 {
        color: white !important;
        opacity: 0.9;
    }
    .card-vibrant .h2 {
        color: white !important;
        opacity: 1;
        text-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .card-vibrant:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px -5px rgba(0,0,0,0.15) !important;
    }
    .bg-gradient-sky { background: var(--primary-gradient); }
    .bg-gradient-success { background: var(--success-gradient); }
    .bg-gradient-info { background: var(--info-gradient); }
    
    .icon-box-white {
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(4px);
        color: white;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(14, 165, 233, 0.02) !important;
    }
    
    .avatar-vibrant {
        background: var(--primary-gradient);
        color: white;
        box-shadow: 0 2px 5px rgba(14, 165, 233, 0.2);
    }
</style>
@endpush

@section('content')
<div class="p-3 p-md-4 max-w-7xl mx-auto">
    {{-- Header --}}
    <div class="mb-4 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
        <div>
            <h1 class="h2 fw-bold tracking-tight text-slate-900 mb-1">Manajemen Karyawan</h1>
            <p class="text-muted mb-0">Kelola data karyawan perusahaan secara efisien</p>
        </div>
        <button type="button" class="btn btn-sky rounded-3 px-4 py-2 d-inline-flex align-items-center gap-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#employeeModal" onclick="openCreateEmployee()">
            <i data-lucide="plus" size="18"></i>
            <span class="fw-semibold">Tambah Karyawan</span>
        </button>
    </div>

    {{-- Statistics Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6">
            <div class="card h-100 card-vibrant bg-gradient-sky rounded-4 shadow">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-xs font-bold text-uppercase tracking-wider mb-1">Total Karyawan</p>
                            <p class="h2 fw-bold mb-0">{{ $employees->count() }}</p>
                        </div>
                        <div class="p-3 icon-box-white rounded-circle">
                            <i data-lucide="users" size="28"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-12 col-sm-6">
            <div class="card h-100 card-vibrant bg-gradient-success rounded-4 shadow">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-xs font-bold text-uppercase tracking-wider mb-1">Plant Terdaftar</p>
                            <p class="h2 fw-bold mb-0">{{ $employees->pluck('plant')->unique()->count() }}</p>
                        </div>
                        <div class="p-3 icon-box-white rounded-circle">
                            <i data-lucide="building-2" size="28"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Employee Table Card --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-white border-bottom p-4">
            <div class="row g-3 align-items-center">
                <div class="col-md-6">
                    <h5 class="fw-bold text-slate-900 mb-1">Daftar Karyawan</h5>
                    <p class="text-xs text-muted mb-0">Semua karyawan yang terdaftar dalam sistem</p>
                </div>
                <div class="col-md-6">
                    <div class="d-flex flex-column flex-md-row gap-2">
                        <div class="input-group flex-grow-1">
                            <span class="input-group-text bg-slate-50 border-end-0">
                                <i data-lucide="search" size="16" class="text-muted"></i>
                            </span>
                            <input type="text" class="form-control bg-slate-50 border-start-0 ps-0 shadow-none" id="searchInput" placeholder="Cari karyawan..." onkeyup="filterTable()">
                        </div>
                        <select class="form-select bg-slate-50 shadow-none border" id="plantFilter" onchange="filterTable()" style="max-width: 130px;">
                            <option value="">Plant</option>
                            <option value="B">Plant B</option>
                            <option value="H">Plant H</option>
                            <option value="I">Plant I</option>
                            <option value="T">Plant T</option>
                        </select>
                        <select class="form-select bg-slate-50 shadow-none border" id="groupFilter" onchange="filterTable()" style="max-width: 130px;">
                            <option value="">Grup</option>
                            <option value="A">Grup A</option>
                            <option value="B">Grup B</option>
                            <option value="C">Grup C</option>
                            <option value="D">Grup D</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="px-4 pt-3">
                <div class="alert alert-success border-0 rounded-3 shadow-sm alert-dismissible fade show" role="alert">
                    <i data-lucide="check-circle" class="me-2" size="18"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        @endif
        
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="employeeTable">
                    <thead class="bg-slate-50 text-slate-500 font-bold text-uppercase text-xs tracking-wider border-bottom text-muted">
                        <tr>
                            <th class="px-4 py-3 border-0">ID Karyawan</th>
                            <th class="px-4 py-3 border-0">Nama</th>
                            <th class="px-4 py-3 border-0">Plant</th>
                            <th class="px-4 py-3 border-0">Group</th>
                            <th class="px-4 py-3 border-0">Status</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        @forelse($employees as $employee)
                        <tr class="border-bottom-slate-100 employee-row" 
                            data-plant="{{ $employee->plant }}" 
                            data-group="{{ $employee->group }}">
                            <td class="px-4 py-3 font-monospace text-xs fw-medium text-slate-600">
                                {{ $employee->employee_id }}
                            </td>
                            <td class="px-4 py-3 fw-semibold text-slate-900">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm avatar-vibrant rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 32px; height: 32px; font-size: 11px;">
                                        {{ substr($employee->name, 0, 2) }}
                                    </div>
                                    {{ $employee->name }}
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="badge badge-plant-{{ $employee->plant }} rounded-pill fw-bold text-xs px-3 py-1">
                                    Plant {{ $employee->plant }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-slate-600 fw-medium">{{ $employee->group }}</td>
                            <td class="px-4 py-3">
                                <span class="badge bg-{{ $employee->default_status == 'Team Leader' ? 'success' : ($employee->default_status == 'Driver Forklift' ? 'warning' : 'info') }} rounded-pill px-3 py-1 fw-bold text-xs border-0 shadow-sm" style="background-color: {{ $employee->default_status == 'Team Leader' ? '#10b981' : ($employee->default_status == 'Driver Forklift' ? '#f59e0b' : '#6366f1') }} !important; color: white !important;">
                                    {{ $employee->default_status }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i data-lucide="users" class="mb-3 opacity-25" size="48"></i>
                                <p class="mb-0">Belum ada karyawan terdaftar</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="employeeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header">
                <div>
                    <h5 id="employeeModalTitle" class="modal-title fw-bold mb-0">Tambah Karyawan</h5>
                    <small id="employeeModalDesc" class="text-muted">Lengkapi data karyawan</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="employeeForm" method="POST">
                @csrf
                <div id="employeeFormMethod"></div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nama</label>
                            <input type="text" class="form-control form-control-custom" name="name" id="emp_name" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">ID Karyawan</label>
                            <input type="text" class="form-control form-control-custom" name="employee_id" id="emp_employee_id" required>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Plant</label>
                            <select class="form-control form-control-custom" name="plant" id="emp_plant" required>
                                <option value="">Pilih</option>
                                <option value="B">Plant B</option>
                                <option value="H">Plant H</option>
                                <option value="I">Plant I</option>
                                <option value="T">Plant T</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Grup</label>
                            <select class="form-control form-control-custom" name="group" id="emp_group" required>
                                <option value="">Pilih</option>
                                <option value="A">Grup A</option>
                                <option value="B">Grup B</option>
                                <option value="C">Grup C</option>
                                <option value="D">Grup D</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Department</label>
                            <input type="text" class="form-control form-control-custom" name="department" id="emp_department" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Position</label>
                            <input type="text" class="form-control form-control-custom" name="position" id="emp_position" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Status Default</label>
                            <select class="form-control form-control-custom" name="default_status" id="emp_default_status" required>
                                <option value="">Pilih</option>
                                <option value="Team Leader">Team Leader</option>
                                <option value="Operator">Operator</option>
                                <option value="Driver Forklift">Driver Forklift</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Jenis Pekerjaan Utama</label>
                            <select class="form-control form-control-custom" name="primary_job_type" id="emp_primary_job_type" required>
                                <option value="">Pilih</option>
                                <option value="Scan">Scan</option>
                                <option value="Strapping">Strapping</option>
                                <option value="Tempel Stiker">Tempel Stiker</option>
                                <option value="Susun Tire">Susun Tire</option>
                                <option value="Pressing">Pressing</option>
                                <option value="Pasang Product Tage OE">Pasang Product Tage OE</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tanggal Masuk</label>
                            <input type="date" class="form-control form-control-custom" name="hire_date" id="emp_hire_date" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">No. Telepon</label>
                            <input type="tel" class="form-control form-control-custom" name="phone" id="emp_phone">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Alamat</label>
                            <textarea class="form-control form-control-custom" name="address" id="emp_address" rows="2"></textarea>
                        </div>


                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light border rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteEmployeeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header">
                <h5 class="modal-title fw-bold mb-0">Hapus Karyawan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">Apakah Anda yakin ingin menghapus karyawan <strong id="deleteEmployeeName"></strong>?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light border rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                <form id="deleteEmployeeForm" method="POST" class="m-0">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger rounded-pill px-4 fw-semibold">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openCreateEmployee() {
    document.getElementById('employeeModalTitle').textContent = 'Tambah Karyawan';
    document.getElementById('employeeModalDesc').textContent = 'Lengkapi data karyawan';
    document.getElementById('employeeForm').action = "{{ route('employees.store') }}";
    document.getElementById('employeeFormMethod').innerHTML = '';
    document.getElementById('employeeForm').reset();

    const hireDate = document.getElementById('emp_hire_date');
    if (hireDate) {
        hireDate.value = new Date().toISOString().split('T')[0];
    }
    if (window.lucide) {
        lucide.createIcons();
    }
}

function filterTable() {
    const searchText = document.getElementById('searchInput').value.toLowerCase();
    const plantFilter = document.getElementById('plantFilter').value;
    const groupFilter = document.getElementById('groupFilter').value;
    const rows = document.querySelectorAll('.employee-row');
    
    rows.forEach(row => {
        const plant = row.getAttribute('data-plant');
        const group = row.getAttribute('data-group');
        const text = row.textContent.toLowerCase();
        
        const matchesSearch = text.includes(searchText);
        const matchesPlant = !plantFilter || plant === plantFilter;
        const matchesGroup = !groupFilter || group === groupFilter;
        
        if (matchesSearch && matchesPlant && matchesGroup) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    if (window.lucide) {
        lucide.createIcons();
    }
});
</script>
@endpush
