@extends('layouts.app')

@section('title', 'Edit Karyawan')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h2 class="mb-1">Edit Data Karyawan</h2>
            <p class="text-muted mb-0">Ubah data karyawan: {{ $employee->name }}</p>
        </div>
        <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary btn-custom">
            <i data-lucide="arrow-left" class="me-2" size="18"></i>Kembali
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="bento-card">
            <h5 class="mb-4">Form Data Karyawan</h5>
            
            <form action="{{ route('employees.update', $employee) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control form-control-custom" required value="{{ old('name', $employee->name) }}" placeholder="Masukkan nama lengkap">
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">ID Karyawan <span class="text-danger">*</span></label>
                        <input type="text" name="employee_id" class="form-control form-control-custom" required value="{{ old('employee_id', $employee->employee_id) }}" placeholder="Contoh: EMP001">
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">Plant <span class="text-danger">*</span></label>
                        <select name="plant" class="form-control form-control-custom" required>
                            <option value="">Pilih Plant</option>
                            <option value="B" {{ old('plant', $employee->plant) == 'B' ? 'selected' : '' }}>Plant B</option>
                            <option value="H" {{ old('plant', $employee->plant) == 'H' ? 'selected' : '' }}>Plant H</option>
                            <option value="I" {{ old('plant', $employee->plant) == 'I' ? 'selected' : '' }}>Plant I</option>
                            <option value="T" {{ old('plant', $employee->plant) == 'T' ? 'selected' : '' }}>Plant T</option>
                        </select>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">Group <span class="text-danger">*</span></label>
                        <select name="group" class="form-control form-control-custom" required>
                            <option value="">Pilih Group</option>
                            <option value="A" {{ old('group', $employee->group) == 'A' ? 'selected' : '' }}>Group A</option>
                            <option value="B" {{ old('group', $employee->group) == 'B' ? 'selected' : '' }}>Group B</option>
                            <option value="C" {{ old('group', $employee->group) == 'C' ? 'selected' : '' }}>Group C</option>
                            <option value="D" {{ old('group', $employee->group) == 'D' ? 'selected' : '' }}>Group D</option>
                        </select>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">Department <span class="text-danger">*</span></label>
                        <input type="text" name="department" class="form-control form-control-custom" required value="{{ old('department', $employee->department) }}" placeholder="Contoh: Produksi">
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">Position <span class="text-danger">*</span></label>
                        <input type="text" name="position" class="form-control form-control-custom" required value="{{ old('position', $employee->position) }}" placeholder="Contoh: Operator">
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">Status Default <span class="text-danger">*</span></label>
                        <select name="default_status" class="form-control form-control-custom" required>
                            <option value="">Pilih Status</option>
                            <option value="Team Leader" {{ old('default_status', $employee->default_status) == 'Team Leader' ? 'selected' : '' }}>Team Leader</option>
                            <option value="Operator" {{ old('default_status', $employee->default_status) == 'Operator' ? 'selected' : '' }}>Operator</option>
                            <option value="Driver Forklift" {{ old('default_status', $employee->default_status) == 'Driver Forklift' ? 'selected' : '' }}>Driver Forklift</option>
                        </select>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">Jenis Pekerjaan Utama <span class="text-danger">*</span></label>
                        <select name="primary_job_type" class="form-control form-control-custom" required>
                            <option value="">Pilih Pekerjaan</option>
                            <option value="Scan" {{ old('primary_job_type', $employee->primary_job_type) == 'Scan' ? 'selected' : '' }}>Scan</option>
                            <option value="Strapping" {{ old('primary_job_type', $employee->primary_job_type) == 'Strapping' ? 'selected' : '' }}>Strapping</option>
                            <option value="Tempel Stiker" {{ old('primary_job_type', $employee->primary_job_type) == 'Tempel Stiker' ? 'selected' : '' }}>Tempel Stiker</option>
                            <option value="Susun Tire" {{ old('primary_job_type', $employee->primary_job_type) == 'Susun Tire' ? 'selected' : '' }}>Susun Tire</option>
                            <option value="Pressing" {{ old('primary_job_type', $employee->primary_job_type) == 'Pressing' ? 'selected' : '' }}>Pressing</option>
                            <option value="Pasang Product Tage OE" {{ old('primary_job_type', $employee->primary_job_type) == 'Pasang Product Tage OE' ? 'selected' : '' }}>Pasang Product Tage OE</option>
                        </select>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">Tanggal Masuk <span class="text-danger">*</span></label>
                        <input type="date" name="hire_date" class="form-control form-control-custom" required value="{{ old('hire_date', $employee->hire_date->format('Y-m-d')) }}">
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">No. Telepon</label>
                        <input type="tel" name="phone" class="form-control form-control-custom" value="{{ old('phone', $employee->phone) }}" placeholder="Contoh: 08123456789">
                    </div>
                    
                    <div class="col-12">
                        <label class="form-label">Alamat</label>
                        <textarea name="address" class="form-control form-control-custom" rows="3" placeholder="Masukkan alamat lengkap">{{ old('address', $employee->address) }}</textarea>
                    </div>
                    

                    
                    <div class="col-12">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary-custom btn-custom">
                                <i data-lucide="save" class="me-2" size="18"></i>Update Data
                            </button>
                            <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary btn-custom">
                                Batal
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="bento-card">
            <h6 class="mb-3">Informasi Karyawan</h6>
            <div class="small">
                <div class="mb-2"><strong>ID Karyawan:</strong> {{ $employee->employee_id }}</div>
                <div class="mb-2"><strong>Tanggal Masuk:</strong> {{ $employee->hire_date->format('d/m/Y') }}</div>
                <div class="mb-2"><strong>Masa Kerja:</strong> {{ $employee->hire_date->diffInDays(now()) }} hari</div>

            </div>
        </div>
        
        <div class="bento-card mt-3">
            <h6 class="mb-3">Riwayat Pekerjaan</h6>
            <div class="small text-muted">
                <div class="mb-2"><strong>Plant:</strong> {{ $employee->plant }}</div>
                <div class="mb-2"><strong>Group:</strong> {{ $employee->group }}</div>
                <div class="mb-2"><strong>Department:</strong> {{ $employee->department ?? '-' }}</div>
                <div class="mb-2"><strong>Position:</strong> {{ $employee->position ?? '-' }}</div>
                <div class="mb-0"><strong>Status Default:</strong> {{ $employee->default_status }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
