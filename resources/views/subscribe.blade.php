<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Souscrire à MonAsso</title>
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
                        primary: '#A259FF', // Violet MonAsso
                        accent: '#22C55E', // Vert accent
                        lavender: '#F6F3FF', // Fond lavande très clair
                    },
                    boxShadow: {
                        'soft': '0 4px 32px 0 rgba(162,89,255,0.10)',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-lavender min-h-screen flex items-center justify-center font-sans">
    <div class="bg-white p-8 rounded-2xl shadow-soft w-full max-w-md border-t-8 border-primary">
        <div class="flex justify-center mb-4">
            <img src="https://www.groupe-speed.cloud/logo.svg" alt="MonAsso" class="h-12 drop-shadow-md">
        </div>
        <h1 class="text-2xl font-extrabold mb-2 text-center text-primary tracking-tight">Créer mon espace Paheko</h1>
        <ol class="flex items-center justify-center mb-6 text-gray-400 text-xs gap-2">
            <li class="flex items-center gap-1">
                <span class="w-6 h-6 flex items-center justify-center rounded-full bg-primary text-white font-bold">1</span>
                <span class="font-semibold text-primary">Sous-domaine</span>
                <span class="mx-1">→</span>
            </li>
            <li class="flex items-center gap-1">
                <span class="w-6 h-6 flex items-center justify-center rounded-full bg-primary text-white font-bold">2</span>
                <span class="font-semibold text-primary">Paiement</span>
                <span class="mx-1">→</span>
            </li>
            <li class="flex items-center gap-1">
                <span class="w-6 h-6 flex items-center justify-center rounded-full bg-primary text-white font-bold">3</span>
                <span class="font-semibold text-primary">Déploiement</span>
            </li>
        </ol>
        <form method="POST" action="/subscribe/checkout-session" class="space-y-6" autocomplete="off" novalidate>
            @csrf
            <div class="relative">
                <input type="text" name="subdomain" required pattern="[a-z0-9]{3,32}" class="peer w-full border border-gray-300 rounded-xl px-4 pt-6 pb-2 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition placeholder-transparent bg-lavender/60" placeholder="Sous-domaine">
                <label class="absolute left-4 top-2 text-xs text-gray-500 transition-all peer-focus:text-primary peer-focus:top-1 peer-focus:text-xs peer-placeholder-shown:top-4 peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-400 pointer-events-none">Sous-domaine souhaité</label>
                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-primary font-bold">.monasso.eu</span>
            </div>
            <div class="relative">
                <input type="text" name="association_name" required class="peer w-full border border-gray-300 rounded-xl px-4 pt-6 pb-2 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition placeholder-transparent bg-lavender/60" placeholder="Nom de l'association">
                <label class="absolute left-4 top-2 text-xs text-gray-500 transition-all peer-focus:text-primary peer-focus:top-1 peer-focus:text-xs peer-placeholder-shown:top-4 peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-400 pointer-events-none">Nom de l'association</label>
            </div>
            <div class="relative">
                <input type="email" name="email" required autocomplete="email" class="peer w-full border border-gray-300 rounded-xl px-4 pt-6 pb-2 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition placeholder-transparent bg-lavender/60" placeholder="Votre email">
                <label class="absolute left-4 top-2 text-xs text-gray-500 transition-all peer-focus:text-primary peer-focus:top-1 peer-focus:text-xs peer-placeholder-shown:top-4 peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-400 pointer-events-none">Votre email</label>
            </div>
            <button type="submit" class="w-full bg-primary text-white font-bold py-3 rounded-xl hover:bg-primary/90 transition flex items-center justify-center shadow-lg text-lg">
                Souscrire
            </button>
            @if(session('error'))
                <div class="text-red-600 mt-4 text-center font-semibold">{{ session('error') }}</div>
            @endif
            @if(session('success'))
                <div class="text-accent mt-4 text-center font-semibold">{{ session('success') }}</div>
            @endif
        </form>
        <p class="text-xs text-gray-400 mt-6 text-center">Vos données sont protégées et votre espace est isolé et sécurisé.<br>Support 7j/7 - Activation immédiate après paiement.</p>
    </div>
</body>
</html>
