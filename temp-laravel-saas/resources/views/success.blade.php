<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Merci pour votre souscription !</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Montserrat', 'ui-sans-serif', 'system-ui'],
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
        <h1 class="text-2xl font-extrabold mb-2 text-primary">Merci pour votre souscription !</h1>
        <p class="text-gray-700 mb-4">Votre paiement a bien été reçu.<br>Votre espace Paheko est en cours de création.<br>
        Vous recevrez un email dès qu'il sera prêt.</p>
        <a href="/" class="inline-block mt-4 px-6 py-3 bg-primary text-white rounded-xl font-bold shadow hover:bg-primary/90 transition">Retour à l'accueil</a>
    </div>
</body>
</html>