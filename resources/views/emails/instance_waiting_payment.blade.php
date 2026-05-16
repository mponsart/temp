<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Activation de votre espace MonAsso – Paiement en attente</title>
    <link href="https://fonts.googleapis.com/css2?family=Titillium+Web:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body, * { font-family: 'Titillium Web', Arial, sans-serif !important; }
    </style>
</head>
<body style="background: #F6F3FF; color: #222;">
    <div style="max-width: 600px; margin: 0 auto; background: #fff; border-radius: 16px; padding: 32px; border-top: 6px solid #A259FF;">
        <h1 style="color: #A259FF;">Bienvenue sur MonAsso !</h1>
        <p>Bonjour,</p>
        <p>Votre demande de création d’espace associatif a bien été prise en compte.</p>
        <ul>
            <li><strong>Sous-domaine choisi&nbsp;:</strong> {{ $instance->subdomain }}.monasso.eu</li>
            <li><strong>Nom de l'association&nbsp;:</strong> {{ $instance->association_name }}</li>
        </ul>
        <p><strong>Pour activer votre espace, finalisez simplement le paiement sécurisé sur la page Stripe qui vient de s’ouvrir.</strong></p>
        <p>Dès validation du paiement, votre espace sera immédiatement accessible à l’adresse ci-dessus.</p>
        <p>Si vous n’êtes pas à l’origine de cette demande, vous pouvez ignorer ce message.</p>
        <p style="font-size:12px;color:#888;">Ce message est généré automatiquement. Merci de ne pas y répondre.</p>
    </div>
</body>
</html>
