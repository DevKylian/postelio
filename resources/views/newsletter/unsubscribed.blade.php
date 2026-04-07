<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Désabonnement — Postelio</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/postelio-logo-gradient.svg') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: #F8FAFC; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 24px; }
        .card { background: #fff; border-radius: 24px; box-shadow: 0 4px 24px rgba(0,0,0,0.07); padding: 48px 40px; max-width: 440px; width: 100%; text-align: center; }
        .icon { width: 64px; height: 64px; background: #F1F5F9; border-radius: 20px; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px; font-size: 28px; }
        h1 { font-size: 22px; font-weight: 700; color: #111827; margin-bottom: 10px; letter-spacing: -0.3px; }
        p { font-size: 15px; color: #64748B; line-height: 1.6; margin-bottom: 28px; }
        .btn { display: inline-block; padding: 13px 28px; background: #111827; color: #fff; font-size: 14px; font-weight: 700; text-decoration: none; border-radius: 50px; transition: opacity .2s; }
        .btn:hover { opacity: .85; }
        .note { margin-top: 20px; font-size: 12px; color: #94A3B8; }
        .note a { color: #F59E0B; text-decoration: none; font-weight: 600; }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon">✅</div>
        <h1>Vous êtes désabonné</h1>
        <p>
            Vous ne recevrez plus nos newsletters, {{ $user->name }}.<br>
            Vous pouvez vous réabonner à tout moment depuis votre profil.
        </p>
        <a href="{{ route('dashboard') }}" class="btn">Retour au tableau de bord</a>
        <p class="note">
            Vous pouvez gérer vos préférences email dans <a href="{{ route('profile') }}">votre profil</a>.
        </p>
    </div>
</body>
</html>
