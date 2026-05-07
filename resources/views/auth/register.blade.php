<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un compte — Ecommerce Dakar</title>
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
            width: 100%; max-width: 460px; overflow: hidden;
        }
        .auth-header {
            background: linear-gradient(135deg, #7c3aed, #a855f7);
            padding: 35px 40px 25px; text-align: center; color: white;
        }
        .auth-logo {
            width: 65px; height: 65px; background: rgba(255,255,255,.2);
            border-radius: 18px; display: flex; align-items: center;
            justify-content: center; margin: 0 auto 14px; font-size: 28px;
        }
        .auth-body { padding: 30px 40px 35px; }
        .form-label { font-weight: 600; font-size: .88rem; color: #374151; margin-bottom: 6px; }
        .form-control {
            border: 2px solid #e5e7eb; border-radius: 12px;
            padding: 11px 16px; font-size: .93rem; transition: all .2s;
        }
        .form-control:focus { border-color: #7c3aed; box-shadow: 0 0 0 3px rgba(124,58,237,.1); }
        .input-group .form-control { border-right: none; border-radius: 12px 0 0 12px; }
        .input-group-text {
            border: 2px solid #e5e7eb; border-left: none;
            border-radius: 0 12px 12px 0; background: white; cursor: pointer;
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
        .alert-danger { border-radius: 12px; border: none; background: #fef2f2; color: #dc2626; font-size: .85rem; }
        .link-purple { color: #7c3aed; font-weight: 600; text-decoration: none; }
        .link-purple:hover { color: #6d28d9; text-decoration: underline; }
        .password-strength { height: 4px; border-radius: 4px; margin-top: 8px; transition: all .3s; background: #e5e7eb; }
        .strength-text { font-size: .75rem; margin-top: 4px; }
        .floating-shapes { position: fixed; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; z-index: 0; }
        .shape { position: absolute; border-radius: 50%; opacity: .08; background: white; }
        .auth-card { position: relative; z-index: 1; }
    </style>
</head>
<body>
<div class="floating-shapes">
    <div class="shape" style="width:300px;height:300px;top:-100px;right:-100px"></div>
    <div class="shape" style="width:200px;height:200px;bottom:50px;left:-50px"></div>
</div>

<div class="auth-card">
    <div class="auth-header">
        <div class="auth-logo">✨</div>
        <h4 class="fw-bold mb-1">Créer un compte</h4>
        <p style="opacity:.85;font-size:.9rem">Rejoignez notre boutique en ligne</p>
    </div>

    <div class="auth-body">
        @if($errors->any())
            <div class="alert alert-danger mb-3">
                <i class="bi bi-exclamation-circle me-2"></i>
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="mb-3">
                <label class="form-label">Nom complet</label>
                <div class="input-group">
                    <span class="input-group-text border-2 border-end-0 rounded-start-3">
                        <i class="bi bi-person" style="color:#7c3aed"></i>
                    </span>
                    <input type="text" name="name"
                           class="form-control border-start-0"
                           style="border-radius:0 12px 12px 0"
                           value="{{ old('name') }}" required autofocus
                           placeholder="Votre nom complet">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Adresse email</label>
                <div class="input-group">
                    <span class="input-group-text border-2 border-end-0 rounded-start-3">
                        <i class="bi bi-envelope" style="color:#7c3aed"></i>
                    </span>
                    <input type="email" name="email"
                           class="form-control border-start-0"
                           style="border-radius:0 12px 12px 0"
                           value="{{ old('email') }}" required
                           placeholder="votre@email.com">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Mot de passe</label>
                <div class="input-group">
                    <span class="input-group-text border-2 border-end-0 rounded-start-3">
                        <i class="bi bi-lock" style="color:#7c3aed"></i>
                    </span>
                    <input type="password" name="password" id="password"
                           class="form-control border-start-0 border-end-0"
                           style="border-radius:0" required
                           placeholder="Min. 8 caractères"
                           oninput="checkStrength(this.value)">
                    <span class="input-group-text" onclick="togglePass('password','eye1')"
                          style="border-radius:0 12px 12px 0">
                        <i class="bi bi-eye" id="eye1" style="color:#9ca3af"></i>
                    </span>
                </div>
                <div class="password-strength" id="strengthBar"></div>
                <div class="strength-text text-muted" id="strengthText"></div>
            </div>

            <div class="mb-4">
                <label class="form-label">Confirmer le mot de passe</label>
                <div class="input-group">
                    <span class="input-group-text border-2 border-end-0 rounded-start-3">
                        <i class="bi bi-shield-lock" style="color:#7c3aed"></i>
                    </span>
                    <input type="password" name="password_confirmation" id="password2"
                           class="form-control border-start-0 border-end-0"
                           style="border-radius:0" required
                           placeholder="Répétez le mot de passe">
                    <span class="input-group-text" onclick="togglePass('password2','eye2')"
                          style="border-radius:0 12px 12px 0">
                        <i class="bi bi-eye" id="eye2" style="color:#9ca3af"></i>
                    </span>
                </div>
            </div>

            <button type="submit" class="btn btn-auth">
                <i class="bi bi-person-plus me-2"></i>Créer mon compte
            </button>
        </form>

        <div class="text-center mt-3" style="font-size:.9rem;color:#6b7280">
            Déjà un compte ?
            <a href="{{ route('login') }}" class="link-purple ms-1">Se connecter</a>
        </div>
    </div>
</div>

<script>
function togglePass(id, iconId) {
    const input = document.getElementById(id);
    const icon = document.getElementById(iconId);
    input.type = input.type === 'password' ? 'text' : 'password';
    icon.className = input.type === 'password' ? 'bi bi-eye' : 'bi bi-eye-slash';
}

function checkStrength(val) {
    const bar = document.getElementById('strengthBar');
    const text = document.getElementById('strengthText');
    let strength = 0;
    if (val.length >= 8) strength++;
    if (/[A-Z]/.test(val)) strength++;
    if (/[0-9]/.test(val)) strength++;
    if (/[^A-Za-z0-9]/.test(val)) strength++;
    const configs = [
        { color: '#ef4444', width: '25%', label: 'Très faible' },
        { color: '#f97316', width: '50%', label: 'Faible' },
        { color: '#eab308', width: '75%', label: 'Moyen' },
        { color: '#22c55e', width: '100%', label: 'Fort 💪' },
    ];
    if (val.length === 0) { bar.style.width = '0'; text.textContent = ''; return; }
    const cfg = configs[strength - 1] || configs[0];
    bar.style.background = cfg.color;
    bar.style.width = cfg.width;
    text.textContent = cfg.label;
    text.style.color = cfg.color;
}
</script>
</body>
</html>