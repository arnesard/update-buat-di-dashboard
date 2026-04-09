<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Penerimaan Produksi</title>

    <!-- Bootstrap 5 CSS -->
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">

    <style>
        :root {
            --primary-color: #3b82f6;
            --secondary-color: #64748b;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --dark-color: #1e293b;
            --light-bg: #f8fafc;
            --card-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --card-hover-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        body {
            background: radial-gradient(circle at 20% 20%, rgba(59, 130, 246, 0.20), transparent 45%),
                radial-gradient(circle at 80% 10%, rgba(37, 99, 235, 0.18), transparent 40%),
                linear-gradient(180deg, #f8fafc, #eef2ff);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-card {
            border-radius: 20px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 18px 50px rgba(15, 23, 42, 0.12);
            overflow: hidden;
            max-width: 440px;
            width: 100%;
            background: white;
        }

        .brand-badge {
            width: 80px;
            height: 80px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: white;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.05);
            padding: 10px;
        }

        .form-control-custom {
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            padding: 0.875rem 1rem;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: var(--light-bg);
        }

        .form-control-custom:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            background: white;
        }

        .btn-login {
            background: linear-gradient(135deg, var(--primary-color), #2563eb);
            color: white;
            border: none;
            border-radius: 12px;
            padding: 0.875rem 2rem;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
            width: 100%;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(59, 130, 246, 0.3);
            color: white;
        }

        .alert-custom {
            border-radius: 12px;
            border: none;
            padding: 1rem;
        }
    </style>
</head>

<body>
    <div class="login-card">
        <div class="p-4 p-md-5">
            <div class="d-flex flex-column align-items-center text-center mb-4">
                <div class="brand-badge mb-1">
                    <img src="{{ asset('images/logo-gt.png') }}" alt="GT Logo" class="img-fluid" style="max-height: 60px;">
                </div>
                <div class="text-muted mb-3" style="font-size: 0.65rem; font-weight: 400; letter-spacing: 0.05em;">PT GAJAH TUNGGAL TBK</div>
                <h3 class="fw-bold mb-1">Penerimaan Produksi Gudang Ban B</h3>
                <p class="text-muted mb-0">Silakan login untuk melanjutkan</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger alert-custom alert-dismissible fade show" role="alert">
                    {{ $errors->first() }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form id="loginForm" action="{{ route('login') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="name" class="form-label fw-semibold">Username</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0" style="width: 46px; justify-content: center;">
                            <i data-lucide="user" class="text-muted" style="width: 18px; height: 18px;"></i>
                        </span>
                        <input type="text" class="form-control form-control-custom border-start-0" id="name"
                            name="name" value="{{ old('name') }}" required placeholder="Masukkan nama" autocomplete="username">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label fw-semibold">Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0" style="width: 46px; justify-content: center;">
                            <i data-lucide="lock" class="text-muted" style="width: 18px; height: 18px;"></i>
                        </span>
                        <input type="password" class="form-control form-control-custom border-start-0" id="password"
                            name="password" required placeholder="Masukkan password" autocomplete="current-password">
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="remember" name="remember">
                        <label class="form-check-label" for="remember">Ingat saya</label>
                    </div>
                </div>

                <button id="loginBtn" type="submit" class="btn btn-login">
                    <span id="loginBtnText">Masuk</span>
                    <span id="loginSpinner" class="spinner-border spinner-border-sm ms-2 d-none" role="status" aria-hidden="true"></span>
                </button>

                <div class="text-center mt-4">
                    <small class="text-muted">Belum punya akun? Hubungi Administrator</small>
                </div>
            </form>
        </div>
    </div>

    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/lucide.min.js') }}"></script>
    <script>
        lucide.createIcons();

        const form = document.getElementById('loginForm');
        const btn = document.getElementById('loginBtn');
        const spinner = document.getElementById('loginSpinner');
        const text = document.getElementById('loginBtnText');

        if (form && btn && spinner && text) {
            form.addEventListener('submit', function() {
                btn.disabled = true;
                spinner.classList.remove('d-none');
                text.textContent = 'Memproses...';
            });
        }
    </script>
</body>

</html>
