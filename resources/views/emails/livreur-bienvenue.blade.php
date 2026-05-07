<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body style="font-family:Arial,sans-serif;background:#f4f4f4;padding:20px;margin:0">
<div style="max-width:600px;margin:auto;background:white;border-radius:12px;overflow:hidden;box-shadow:0 2px 20px rgba(0,0,0,0.1)">
    
    {{-- HEADER --}}
    <div style="background:linear-gradient(135deg,#7c3aed,#a855f7);padding:40px 30px;text-align:center">
        <h1 style="color:white;margin:0;font-size:28px">🚀 Bienvenue !</h1>
        <p style="color:rgba(255,255,255,0.85);margin:10px 0 0;font-size:16px">E-Shop SN — Espace Livreur</p>
    </div>

    {{-- BODY --}}
    <div style="padding:35px 30px">
        <p style="font-size:16px;color:#333">Bonjour <strong>{{ $user->name }}</strong>,</p>
        <p style="color:#555;line-height:1.7">
            Votre compte livreur a été créé avec succès sur la plateforme <strong>E-Shop SN</strong>. 
            Vous pouvez dès maintenant vous connecter et commencer à gérer vos livraisons.
        </p>

        {{-- IDENTIFIANTS --}}
        <div style="background:#f8f4ff;border-left:4px solid #7c3aed;border-radius:8px;padding:20px;margin:25px 0">
            <p style="margin:0 0 12px;font-weight:bold;color:#7c3aed;font-size:14px">📋 VOS IDENTIFIANTS DE CONNEXION</p>
            <table style="width:100%;border-collapse:collapse">
                <tr>
                    <td style="padding:8px 0;color:#555;font-size:14px;width:40%"><strong>Email :</strong></td>
                    <td style="padding:8px 0;color:#333;font-size:14px">{{ $user->email }}</td>
                </tr>
                <tr>
                    <td style="padding:8px 0;color:#555;font-size:14px"><strong>Mot de passe :</strong></td>
                    <td style="padding:8px 0;font-size:14px">
                        <span style="background:#fff;border:1px solid #ddd;padding:4px 12px;border-radius:6px;font-family:monospace;font-weight:bold;color:#7c3aed">{{ $plainPassword }}</span>
                    </td>
                </tr>
            </table>
        </div>

        {{-- ALERTE CHANGEMENT MOT DE PASSE --}}
        <div style="background:#fef3c7;border-radius:8px;padding:15px 20px;margin-bottom:25px">
            <p style="margin:0;color:#92400e;font-size:13px">
                ⚠️ <strong>Important :</strong> Pour votre sécurité, veuillez changer votre mot de passe dès votre première connexion.
            </p>
        </div>

        {{-- BOUTON CONNEXION --}}
        <div style="text-align:center;margin:30px 0">
            <a href="{{ url('/login') }}" 
               style="background:linear-gradient(135deg,#7c3aed,#a855f7);color:white;padding:14px 35px;border-radius:10px;text-decoration:none;font-weight:bold;font-size:16px;display:inline-block">
                🔐 Se connecter maintenant
            </a>
        </div>

        {{-- BOUTON CHANGER MOT DE PASSE --}}
        <div style="text-align:center;margin-bottom:25px">
            <a href="{{ url('/forgot-password') }}"
               style="color:#7c3aed;text-decoration:none;font-size:14px;border:1px solid #7c3aed;padding:10px 25px;border-radius:8px;display:inline-block">
                🔑 Changer mon mot de passe
            </a>
        </div>

        <hr style="border:none;border-top:1px solid #eee;margin:25px 0">

        <p style="color:#999;font-size:12px;text-align:center;margin:0">
            Cet email a été envoyé automatiquement par <strong>E-Shop SN</strong>.<br>
            Si vous n'êtes pas concerné, ignorez ce message.
        </p>
    </div>

    {{-- FOOTER --}}
    <div style="background:#f8fafc;padding:20px 30px;text-align:center;border-top:1px solid #eee">
        <p style="margin:0;color:#94a3b8;font-size:12px">© {{ date('Y') }} E-Shop SN — Dakar, Sénégal 🇸🇳</p>
    </div>
</div>
</body>
</html>