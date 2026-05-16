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
                        splitRight: '#EDE9FE',
                        splitLeft: '#FFFFFF',
                    },
                    boxShadow: {
                        'soft': '0 4px 32px 0 rgba(162,89,255,0.10)',
                    }
                }
            }
        }
    </script>
    <style>
        html, body, * { font-family: 'Titillium Web', ui-sans-serif, system-ui, sans-serif !important; }
    </style>
</head>
<body class="min-h-screen flex items-stretch bg-monassoLight">
    <div class="absolute top-0 left-0 w-full flex justify-center pt-8 z-20">
        <img src="https://www.groupe-speed.cloud/logo.svg" alt="MonAsso" class="h-14 drop-shadow-md">
    </div>
    <div class="flex flex-1 h-screen pt-28 pb-8">
        <!-- Split gauche : Nouveau client -->
        <div class="w-1/2 flex flex-col justify-center items-center bg-splitLeft relative rounded-l-3xl shadow-soft">
            <div class="relative z-10 flex flex-col items-center w-full max-w-md px-8">
                <h2 class="text-3xl md:text-4xl font-extrabold text-monasso mb-6 text-center tracking-wide">Nouveau client</h2>
                <p class="text-gray-700 mb-8 text-center text-lg">Créez votre espace associatif en quelques clics.<br>Paiement sécurisé, activation immédiate.</p>
                <a href="/subscribe" class="w-full bg-accent text-white font-bold py-3 rounded-2xl hover:bg-accent/90 transition text-lg shadow-lg mb-2 text-center">Continuer</a>
            </div>
        </div>
        <!-- Split droite : Déjà client -->
        <div class="w-1/2 flex flex-col justify-center items-center bg-splitRight relative rounded-r-3xl shadow-soft">
            <div class="relative z-10 flex flex-col items-center w-full max-w-md px-8">
                <h2 class="text-3xl md:text-4xl font-extrabold text-monassoDark mb-2 text-center tracking-wide">Déjà client MonAsso</h2>
                <p class="text-gray-700 mb-8 text-center text-lg">Gérez votre facturation ou changez d’offre en toute autonomie.</p>
                @if(env('STRIPE_PORTAL_URL'))
                <a href="{{ env('STRIPE_PORTAL_URL') }}" target="_blank" class="w-full bg-white text-monassoDark font-bold py-3 rounded-2xl hover:bg-monasso/10 transition text-lg shadow-lg mb-3 text-center border-2 border-monasso">Gérer ma facturation</a>
                @endif
                <a href="mailto:support@groupe-speed.cloud" class="w-full bg-monasso text-white font-bold py-3 rounded-2xl hover:bg-monassoDark transition text-lg shadow-lg text-center">Changer mon offre</a>
            </div>
        </div>
    </div>
</body>
</html>
