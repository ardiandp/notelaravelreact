<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - HRSI Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; }
        .login-card { border: none; border-radius: 1rem; box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15); }
        .login-card .card-header { background: transparent; border: none; text-align: center; padding-top: 2rem; }
        .login-card .card-body { padding: 2rem; }
        .btn-primary { border-radius: 2rem; padding: 0.5rem 1.5rem; }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center min-vh-100 align-items-center">
            <div class="col-md-5">
                <div class="text-center mb-4">
                    <div class="d-inline-flex align-items-center justify-content-center bg-white rounded-circle shadow" style="width: 70px; height: 70px;">
                        <span class="h3 mb-0 text-primary fw-bold">N</span>
                    </div>
                    <h3 class="mt-3 text-white fw-light">HRSI Admin</h3>
                </div>

                <div class="card login-card">
                    <div class="card-header">
                        <h4 class="mb-0 fw-bold">Welcome Back</h4>
                        <p class="text-muted small">Sign in to your account</p>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label small">Email</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="admin@admin.com" required autofocus>
                                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="password" required>
                                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" name="remember" class="form-check-input" id="remember">
                                <label class="form-check-label small" for="remember">Remember me</label>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Sign In</button>
                        </form>
                    </div>
                </div>

                <div class="card mt-3 border-0" style="border-radius: 1rem; box-shadow: 0 0.25rem 0.5rem rgba(0,0,0,0.1);">
                    <div class="card-body py-3 px-4">
                        <h6 class="fw-bold mb-2"><i class="fas fa-key me-1"></i> Akun Testing</h6>
                        <p class="text-muted small mb-2">Semua password: <code>password</code></p>
                        <table class="table table-sm small mb-0">
                            <thead><tr><th>Role</th><th>Email</th></tr></thead>
                            <tbody>
                                <tr><td><span class="badge bg-danger">Super Admin</span></td><td><code>admin@admin.com</code></td></tr>
                                <tr><td><span class="badge bg-warning text-dark">HRD</span></td><td><code>siti@hrsi.test</code></td></tr>
                                <tr><td><span class="badge bg-info text-dark">Manager</span></td><td><code>budi@hrsi.test</code> / <code>dodi@hrsi.test</code></td></tr>
                                <tr><td><span class="badge bg-secondary">Karyawan</span></td><td><code>ahmad@hrsi.test</code> / <code>dewi@hrsi.test</code> / <code>rudi@hrsi.test</code><br><code>ani@hrsi.test</code> / <code>fitri@hrsi.test</code></td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <p class="text-center text-white-50 mt-3 small">
                    <i class="fas fa-arrow-left me-1"></i>
                    <a href="/" class="text-white-50">Back to Home</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
