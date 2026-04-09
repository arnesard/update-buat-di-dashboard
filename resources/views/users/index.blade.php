@extends('layouts.app')

@section('title', 'Manajemen User')

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
        --danger-gradient: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        --violet-gradient: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
    }

    body { background-color: #f8fafc !important; }

    .text-slate-500 { color: var(--slate-500) !important; }
    .text-slate-600 { color: var(--slate-600) !important; }
    .text-slate-900 { color: var(--slate-900) !important; }
    .bg-slate-50 { background-color: var(--slate-50) !important; }
    .bg-sky-light { background-color: var(--sky-light) !important; }
    .text-sky { color: var(--sky-600) !important; }
    
    .btn-sky {
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
    
    .border-bottom-slate-100 { border-bottom: 1px solid var(--slate-100); }

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
        text-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .card-vibrant:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px -5px rgba(0,0,0,0.15) !important;
    }
    .bg-gradient-violet { background: var(--violet-gradient); }
    .bg-gradient-sky { background: var(--primary-gradient); }

    .icon-box-white {
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(4px);
        color: white;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(14, 165, 233, 0.02) !important;
    }
    
    .avatar-vibrant {
        background: var(--violet-gradient);
        color: white;
        box-shadow: 0 2px 5px rgba(139, 92, 246, 0.2);
    }

    .badge-self {
        background-color: rgba(14, 165, 233, 0.1);
        color: var(--sky-600);
        font-size: 10px;
    }

    .btn-action {
        width: 32px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        border: 1px solid var(--slate-200);
        background: white;
        color: var(--slate-500);
        transition: all 0.2s ease;
        cursor: pointer;
        padding: 0;
    }
    .btn-action:hover {
        background: var(--slate-50);
        color: var(--slate-900);
        border-color: var(--slate-500);
    }
    .btn-action.btn-action-danger:hover {
        background: #fef2f2;
        color: #dc2626;
        border-color: #fca5a5;
    }

    .password-toggle {
        cursor: pointer;
        border: none;
        background: none;
        color: var(--slate-500);
        padding: 0 0.75rem;
    }
    .password-toggle:hover { color: var(--slate-900); }
</style>
@endpush

@section('content')
<div class="p-3 p-md-4 max-w-7xl mx-auto">
    {{-- Header --}}
    <div class="mb-4 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
        <div>
            <h1 class="h2 fw-bold tracking-tight text-slate-900 mb-1">Kelola User</h1>
            <p class="text-muted mb-0">Kelola akun pengguna yang dapat login ke sistem</p>
        </div>
        <button type="button" class="btn btn-sky rounded-3 px-4 py-2 d-inline-flex align-items-center gap-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#userModal" onclick="openCreateUser()">
            <i data-lucide="plus" size="18"></i>
            <span class="fw-semibold">Tambah User</span>
        </button>
    </div>

    {{-- Statistics Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6">
            <div class="card h-100 card-vibrant bg-gradient-violet rounded-4 shadow">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-xs font-bold text-uppercase tracking-wider mb-1">Total User</p>
                            <p class="h2 fw-bold mb-0">{{ $users->count() }}</p>
                        </div>
                        <div class="p-3 icon-box-white rounded-circle">
                            <i data-lucide="shield" size="28"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6">
            <div class="card h-100 card-vibrant bg-gradient-sky rounded-4 shadow">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-xs font-bold text-uppercase tracking-wider mb-1">Login Sebagai</p>
                            <p class="h2 fw-bold mb-0">{{ auth()->user()->name }}</p>
                        </div>
                        <div class="p-3 icon-box-white rounded-circle">
                            <i data-lucide="user-check" size="28"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- User Table Card --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-white border-bottom p-4">
            <div class="row g-3 align-items-center">
                <div class="col-md-6">
                    <h5 class="fw-bold text-slate-900 mb-1">Daftar User</h5>
                    <p class="text-xs text-muted mb-0">Semua akun yang terdaftar dalam sistem</p>
                </div>
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-slate-50 border-end-0">
                            <i data-lucide="search" size="16" class="text-muted"></i>
                        </span>
                        <input type="text" class="form-control bg-slate-50 border-start-0 ps-0 shadow-none" id="searchInput" placeholder="Cari user..." onkeyup="filterTable()">
                    </div>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="px-4 pt-3">
                <div class="alert alert-success border-0 rounded-3 shadow-sm alert-dismissible fade show" role="alert">
                    <i data-lucide="check-circle" class="me-2" size="18"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="px-4 pt-3">
                <div class="alert alert-danger border-0 rounded-3 shadow-sm alert-dismissible fade show" role="alert">
                    <i data-lucide="alert-circle" class="me-2" size="18"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="px-4 pt-3">
                <div class="alert alert-danger border-0 rounded-3 shadow-sm alert-dismissible fade show" role="alert">
                    <i data-lucide="alert-circle" class="me-2" size="18"></i>
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        @endif
        
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="userTable">
                    <thead class="bg-slate-50 text-slate-500 font-bold text-uppercase text-xs tracking-wider border-bottom text-muted">
                        <tr>
                            <th class="px-4 py-3 border-0">#</th>
                            <th class="px-4 py-3 border-0">Nama</th>
                            <th class="px-4 py-3 border-0">Dibuat</th>
                            <th class="px-4 py-3 border-0 text-center" style="width: 100px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        @forelse($users as $index => $user)
                        <tr class="border-bottom-slate-100 user-row">
                            <td class="px-4 py-3 text-slate-500 fw-medium">{{ $index + 1 }}</td>
                            <td class="px-4 py-3 fw-semibold text-slate-900">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm avatar-vibrant rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 32px; height: 32px; font-size: 11px;">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        {{ $user->name }}
                                        @if($user->id === auth()->id())
                                            <span class="badge badge-self rounded-pill ms-2">Anda</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-slate-500 text-xs">
                                {{ $user->created_at ? $user->created_at->format('d M Y, H:i') : '-' }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="d-flex gap-1 justify-content-center">
                                    <button class="btn-action" title="Edit" onclick="openEditUser({{ $user->id }}, '{{ addslashes($user->name) }}')">
                                        <i data-lucide="pencil" size="14"></i>
                                    </button>
                                    @if($user->id !== auth()->id())
                                    <button class="btn-action btn-action-danger" title="Hapus" onclick="openDeleteUser({{ $user->id }}, '{{ addslashes($user->name) }}')">
                                        <i data-lucide="trash-2" size="14"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">
                                <i data-lucide="shield" class="mb-3 opacity-25" size="48"></i>
                                <p class="mb-0">Belum ada user terdaftar</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Modal Tambah/Edit User --}}
<div class="modal fade" id="userModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header">
                <div>
                    <h5 id="userModalTitle" class="modal-title fw-bold mb-0">Tambah User</h5>
                    <small id="userModalDesc" class="text-muted">Buat akun pengguna baru</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="userForm" method="POST">
                @csrf
                <div id="userFormMethod"></div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Username</label>
                            <input type="text" class="form-control" name="name" id="user_name" required autocomplete="off">
                            <small class="text-muted">Digunakan untuk login ke sistem</small>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control" name="password" id="user_password" autocomplete="new-password">
                                <button class="password-toggle input-group-text" type="button" onclick="togglePassword('user_password', this)">
                                    <i data-lucide="eye" size="16"></i>
                                </button>
                            </div>
                            <small id="passwordHint" class="text-muted">Minimal 6 karakter</small>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Konfirmasi Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control" name="password_confirmation" id="user_password_confirmation" autocomplete="new-password">
                                <button class="password-toggle input-group-text" type="button" onclick="togglePassword('user_password_confirmation', this)">
                                    <i data-lucide="eye" size="16"></i>
                                </button>
                            </div>
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

{{-- Modal Hapus User --}}
<div class="modal fade" id="deleteUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header">
                <h5 class="modal-title fw-bold mb-0">Hapus User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">Apakah Anda yakin ingin menghapus user <strong id="deleteUserName"></strong>? Akun ini tidak akan bisa login lagi.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light border rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                <form id="deleteUserForm" method="POST" class="m-0">
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
function openCreateUser() {
    document.getElementById('userModalTitle').textContent = 'Tambah User';
    document.getElementById('userModalDesc').textContent = 'Buat akun pengguna baru';
    document.getElementById('userForm').action = "{{ route('users.store') }}";
    document.getElementById('userFormMethod').innerHTML = '';
    document.getElementById('userForm').reset();
    document.getElementById('user_password').setAttribute('required', 'required');
    document.getElementById('user_password_confirmation').setAttribute('required', 'required');
    document.getElementById('passwordHint').textContent = 'Minimal 6 karakter';
    if (window.lucide) lucide.createIcons();
}

function openEditUser(id, name) {
    document.getElementById('userModalTitle').textContent = 'Edit User';
    document.getElementById('userModalDesc').textContent = 'Perbarui data pengguna';
    document.getElementById('userForm').action = "/web_receiving/public/users/" + id;
    document.getElementById('userFormMethod').innerHTML = '@method("PUT")';
    document.getElementById('user_name').value = name;
    document.getElementById('user_password').value = '';
    document.getElementById('user_password_confirmation').value = '';
    document.getElementById('user_password').removeAttribute('required');
    document.getElementById('user_password_confirmation').removeAttribute('required');
    document.getElementById('passwordHint').textContent = 'Kosongkan jika tidak ingin mengubah password';
    
    var modal = new bootstrap.Modal(document.getElementById('userModal'));
    modal.show();
    if (window.lucide) lucide.createIcons();
}

function openDeleteUser(id, name) {
    document.getElementById('deleteUserName').textContent = name;
    document.getElementById('deleteUserForm').action = "/web_receiving/public/users/" + id;
    var modal = new bootstrap.Modal(document.getElementById('deleteUserModal'));
    modal.show();
    if (window.lucide) lucide.createIcons();
}

function togglePassword(inputId, btn) {
    const input = document.getElementById(inputId);
    if (input.type === 'password') {
        input.type = 'text';
        btn.innerHTML = '<i data-lucide="eye-off" size="16"></i>';
    } else {
        input.type = 'password';
        btn.innerHTML = '<i data-lucide="eye" size="16"></i>';
    }
    if (window.lucide) lucide.createIcons();
}

function filterTable() {
    const searchText = document.getElementById('searchInput').value.toLowerCase();
    const rows = document.querySelectorAll('.user-row');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchText) ? '' : 'none';
    });
}

document.addEventListener('DOMContentLoaded', function() {
    if (window.lucide) lucide.createIcons();
});
</script>
@endpush
