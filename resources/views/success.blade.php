<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Votre espace MonAsso est prêt !</title>
    <link href="https://fonts.googleapis.com/css2?family=Titillium+Web:wght@400;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Titillium Web', 'ui-sans-serif', 'system-ui'],
                    },
                    colors: {
                        primary: '#A259FF',
                        accent: '#22C55E',
                        lavender: '#F6F3FF',
                    },
                }
            }
        }
    </script>
</head>
<body class="bg-lavender min-h-screen flex items-center justify-center font-sans">
    <div class="bg-white p-8 rounded-2xl shadow-soft w-full max-w-md border-t-8 border-primary text-center">
        <div class="flex justify-center mb-4">
            <img src="https://www.groupe-speed.cloud/logo.svg" alt="MonAsso" class="h-12 drop-shadow-md">
        </div>
        <h1 class="text-2xl font-extrabold mb-2 text-primary">Votre espace est prêt !</h1>
        <p class="text-gray-700 mb-4">Votre paiement a bien été validé.<br>Votre espace MonAsso est maintenant actif et prêt à l’emploi.</p>
        <div class="bg-lavender rounded-xl p-4 mb-4 text-left text-sm">
            <div class="mb-2"><span class="font-bold text-primary">Sous-domaine :</span> <span class="text-gray-800">{{ session('subdomain') ?? '...' }}.monasso.eu</span></div>
            <div class="mb-2"><span class="font-bold text-primary">Email :</span> <span class="text-gray-800">{{ session('email') ?? '...' }}</span></div>
            <div class="mb-2"><span class="font-bold text-primary">Nom de l’association :</span> <span class="text-gray-800">{{ session('association_name') ?? '...' }}</span></div>
            <div class="mb-2"><span class="font-bold text-primary">Montant :</span> <span class="text-gray-800">{{ session('amount') ?? 'Voir reçu Stripe' }}</span></div>
            <div><span class="font-bold text-primary">Référence paiement :</span> <span class="text-gray-800">{{ session('payment_id') ?? 'Voir reçu Stripe' }}</span></div>
        </div>
        <p class="text-gray-700 mb-4">Vous pouvez dès maintenant accéder à votre espace&nbsp;:</p>
        <a href="https://{{ session('subdomain') ?? '#' }}.monasso.eu" class="inline-block mb-4 px-6 py-3 bg-accent text-white rounded-xl font-bold shadow hover:bg-accent/90 transition">Accéder à mon espace</a>
        <p class="text-gray-600 text-xs mb-2">Si vous avez un souci, contactez le support : <a href="mailto:support@groupe-speed.cloud" class="text-primary underline">support@groupe-speed.cloud</a></p>
        <a href="/" class="inline-block mt-2 px-6 py-3 bg-primary text-white rounded-xl font-bold shadow hover:bg-primary/90 transition">Retour à l'accueil</a>
    </div>
</body>
</html>