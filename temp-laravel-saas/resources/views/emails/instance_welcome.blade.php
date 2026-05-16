<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Votre espace Paheko est prêt !</title>
</head>
<body style="font-family: Montserrat, Arial, sans-serif; background: #F6F3FF; color: #222;">
    <div style="max-width: 600px; margin: 0 auto; background: #fff; border-radius: 16px; padding: 32px; border-top: 6px solid #A259FF;">
        <h1 style="color: #A259FF;">Bienvenue sur MonAsso !</h1>
        <p>Bonjour,</p>
        <p>Votre espace Paheko est prêt à l'emploi&nbsp;:</p>
        <ul>
            <li><strong>Sous-domaine&nbsp;:</strong> {{ $instance->subdomain }}.monasso.eu</li>
            <li><strong>Nom de l'association&nbsp;:</strong> {{ $instance->association_name }}</li>
        </ul>
        <p>Vous pouvez dès maintenant vous connecter et commencer à gérer votre association.</p>
        <p>
            <a href="https://{{ $instance->subdomain }}.monasso.eu" style="display:inline-block;padding:12px 32px;background:#A259FF;color:#fff;border-radius:8px;text-decoration:none;font-weight:bold;">Accéder à mon espace</a>
        </p>
        <p>Besoin d'aide&nbsp;? Notre équipe support est disponible 7j/7.</p>
        <p style="font-size:12px;color:#888;">Ce message est généré automatiquement. Merci de ne pas y répondre.</p>
    </div>
</body>
</html>
