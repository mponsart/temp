<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil MonAsso</title>
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
                        monasso: '#A259FF',
                        monassoLight: '#F6F3FF',
                        monassoDark: '#7C3AED',
                        accent: '#22C55E',
                        bgSoft: '#F9F8FC',
                    },
                }
            }
        }
    </script>
    <style>
        html, body, * { font-family: 'Titillium Web', ui-sans-serif, system-ui, sans-serif !important; }
    </style>
</head>
<body class="min-h-screen flex flex-col bg-bgSoft">
    <header class="w-full flex justify-center pt-10 pb-2">
        <img src="https://www.groupe-speed.cloud/logo.svg" alt="MonAsso" class="h-12">
    </header>
    <main class="flex-1 flex items-center justify-center">
        <div class="w-full max-w-md mx-auto bg-white rounded-2xl shadow-lg border border-monasso/10 flex flex-col items-center py-10 px-6">
            <h1 class="text-3xl font-extrabold text-monasso mb-2 text-center tracking-wide">Espace client MonAsso</h1>
            <p class="text-gray-700 text-center mb-8">Bienvenue sur votre espace client. <br>Gérez votre abonnement ou créez un nouvel espace associatif en toute simplicité.</p>
            <div class="flex flex-col gap-5 w-full max-w-xs">
                <a href="/subscribe" class="bg-accent text-white font-bold py-3 rounded-xl hover:bg-accent/90 transition text-lg text-center shadow-sm">Nouveau client – Créer mon espace</a>
                @if(env('STRIPE_PORTAL_URL'))
                <a href="{{ env('STRIPE_PORTAL_URL') }}" target="_blank" class="bg-monasso text-white font-bold py-3 rounded-xl hover:bg-monassoDark transition text-lg text-center shadow-sm">Déjà client – Gérer ma facturation</a>
                @endif
            </div>
        </div>
    </main>
</body>
</html>
