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
                        primary: '#A259FF',
                        accent: '#22C55E',
                        lavender: '#F6F3FF',
                        splitRight: '#2B6A7C',
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
        .bg-pineapple {
            background-image: url('https://www.transparenttextures.com/patterns/pineapple.png');
            background-repeat: repeat;
            opacity: 0.08;
        }
    </style>
</head>
<body class="min-h-screen flex items-stretch">
    <div class="flex flex-1 h-screen">
        <!-- Split gauche : Nouveau client -->
        <div class="w-1/2 flex flex-col justify-center items-center bg-splitLeft relative">
            <div class="absolute inset-0 bg-pineapple pointer-events-none"></div>
            <div class="relative z-10 flex flex-col items-center w-full max-w-md px-8">
                <h2 class="text-2xl md:text-3xl font-bold text-primary mb-6 text-center">Nouveau client</h2>
                <p class="text-gray-700 mb-8 text-center">Créez votre espace associatif en quelques clics.<br>Paiement sécurisé, activation immédiate.</p>
                <a href="/subscribe" class="w-full bg-accent text-white font-bold py-3 rounded-xl hover:bg-accent/90 transition text-lg shadow-lg mb-2">Continuer</a>
            </div>
        </div>
        <!-- Split droite : Déjà client -->
        <div class="w-1/2 flex flex-col justify-center items-center bg-splitRight relative">
            <div class="absolute inset-0 bg-pineapple pointer-events-none"></div>
            <div class="relative z-10 flex flex-col items-center w-full max-w-md px-8">
                <h2 class="text-2xl md:text-3xl font-bold text-white mb-2 text-center">Déjà client MonAsso</h2>
                <p class="text-slate-100 mb-8 text-center">Gérez votre facturation ou changez d’offre en toute autonomie.</p>
                @if(env('STRIPE_PORTAL_URL'))
                <a href="{{ env('STRIPE_PORTAL_URL') }}" target="_blank" class="w-full bg-white text-primary font-bold py-3 rounded-xl hover:bg-gray-100 transition text-lg shadow-lg mb-3">Gérer ma facturation</a>
                @endif
                <a href="mailto:support@groupe-speed.cloud" class="w-full bg-primary text-white font-bold py-3 rounded-xl hover:bg-primary/90 transition text-lg shadow-lg">Changer mon offre</a>
            </div>
        </div>
    </div>
</body>
</html>
