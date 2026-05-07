<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion — Ecommerce Dakar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #1e293b 0%, #7c3aed 50%, #a855f7 100%);
            display: flex; align-items: center; justify-content: center;
            font-family: 'Segoe UI', sans-serif; padding: 20px;
        }
        .auth-card {
            background: white; border-radius: 24px;
            box-shadow: 0 25px 60px rgba(0,0,0,.3);
            width: 100%; max-width: 440px; overflow: hidden;
        }
        .auth-header {
            background: linear-gradient(135deg, #7c3aed, #a855f7);
            padding: 40px 40px 30px; text-align: center; color: white;
        }
        .auth-logo {
            width: 70px; height: 70px; background: rgba(255,255,255,.2);
            border-radius: 20px; display: flex; align-items: center;
            justify-content: center; margin: 0 auto 16px; font-size: 32px;
        }
        .auth-body { padding: 35px 40px 40px; }
        .form-label { font-weight: 600; font-size: .88rem; color: #374151; margin-bottom: 6px; }
        .form-control {
            border: 2px solid #e5e7eb; border-radius: 12px;
            padding: 12px 16px; font-size: .93rem; transition: all .2s;
        }
        .form-control:focus { border-color: #7c3aed; box-shadow: 0 0 0 3px rgba(124,58,237,.1); }
        .input-group .form-control { border-right: none; border-radius: 12px 0 0 12px; }
        .input-group-text {
            border: 2px solid #e5e7eb; border-left: none;
            border-radius: 0 12px 12px 0; background: white;
            cursor: pointer; transition: all .2s;
        }
        .input-group:focus-within .form-control,
        .input-group:focus-within .input-group-text { border-color: #7c3aed; }
        .btn-auth {
            background: linear-gradient(135deg, #7c3aed, #a855f7);
            color: white; border: none; border-radius: 12px;
            padding: 13px; font-weight: 700; font-size: 1rem;
            width: 100%; transition: all .2s; margin-top: 8px;
        }
        .btn-auth:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(124,58,237,.4); color: white; }
        .divider { text-align: center; margin: 20px 0; color: #9ca3af; font-size: .85rem; position: relative; }
        .divider::before, .divider::after {
            content: ''; position: absolute; top: 50%;
            width: 42%; height: 1px; background: #e5e7eb;
        }
        .divider::before { left: 0; }
        .divider::after { right: 0; }
        .alert-danger { border-radius: 12px; border: none; background: #fef2f2; color: #dc2626; font-size: .85rem; }
        .form-check-input:checked { background-color: #7c3aed; border-color: #7c3aed; }
        .link-purple { color: #7c3aed; font-weight: 600; text-decoration: none; }
        .link-purple:hover { color: #6d28d9; text-decoration: underline; }
        .floating-shapes { position: fixed; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; overflow: hidden; z-index: 0; }
        .shape { position: absolute; border-radius: 50%; opacity: .08; background: white; }
        .auth-card { position: relative; z-index: 1; }
    </style>
</head>
<body>
<div class="floating-shapes">
    <div class="shape" style="width:300px;height:300px;top:-100px;left:-100px"></div>
    <div class="shape" style="width:200px;height:200px;bottom:50px;right:-50px"></div>
    <div class="shape" style="width:150px;height:150px;top:50%;left:10%"></div>
</div>

<div class="auth-card">
    <div class="auth-header">
        <div class="auth-logo">🛍️</div>
        <h4 class="fw-bold mb-1">Bon retour !</h4>
        <p style="opacity:.85;font-size:.9rem">Connectez-vous à votre compte</p>
    </div>

    <div class="auth-body">
        @if(session('status'))
            <div class="alert alert-success rounded-3 mb-3">{{ session('status') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger mb-3">
                <i class="bi bi-exclamation-circle me-2"></i>
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label">Adresse email</label>
                <div class="input-group">
                    <span class="input-group-text border-2 border-end-0 rounded-start-3">
                        <i class="bi bi-envelope" style="color:#7c3aed"></i>
                    </span>
                    <input type="email" name="email" class="form-control border-start-0"
                           style="border-radius:0 12px 12px 0"
                           value="{{ old('email') }}" required autofocus
                           placeholder="votre@email.com">
                </div>
            </div>

            <div class="mb-3">
                <div class="d-flex justify-content-between">
                    <label class="form-label">Mot de passe</label>
                    @if(Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="link-purple" style="font-size:.82rem">
                            Mot de passe oublié ?
                        </a>
                    @endif
                </div>
                <div class="input-group">
                    <span class="input-group-text border-2 border-end-0 rounded-start-3">
                        <i class="bi bi-lock" style="color:#7c3aed"></i>
                    </span>
                    <input type="password" name="password" id="password"
                           class="form-control border-start-0 border-end-0"
                           style="border-radius:0" required placeholder="••••••••">
                    <span class="input-group-text" onclick="togglePassword()" style="border-radius:0 12px 12px 0">
                        <i class="bi bi-eye" id="eyeIcon" style="color:#9ca3af"></i>
                    </span>
                </div>
            </div>

            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" name="remember" id="remember">
                    <label class="form-check-label" for="remember" style="font-size:.88rem;color:#6b7280">
                        Se souvenir de moi
                    </label>
                </div>
            </div>

            <button type="submit" class="btn btn-auth">
                <i class="bi bi-box-arrow-in-right me-2"></i>Se connecter
            </button>
        </form>

        <div class="divider">ou</div>

        <div class="text-center" style="font-size:.9rem;color:#6b7280">
            Pas encore de compte ?
            <a href="{{ route('register') }}" class="link-purple ms-1">Créer un compte</a>
        </div>
    </div>
</div>

<script>
function togglePassword() {
    const pwd = document.getElementById('password');
    const icon = document.getElementById('eyeIcon');
    if (pwd.type === 'password') {
        pwd.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        pwd.type = 'password';
        icon.className = 'bi bi-eye';
    }
}
</script>
</body>
</html>