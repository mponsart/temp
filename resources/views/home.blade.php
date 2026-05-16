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
<body class="bg-lavender min-h-screen flex items-center justify-center">
    <div class="bg-white p-10 rounded-3xl shadow-soft w-full max-w-md border-t-8 border-primary text-center flex flex-col items-center">
        <div class="flex flex-col items-center mb-6">
            <img src="https://www.groupe-speed.cloud/logo.svg" alt="MonAsso" class="h-16 mb-2 drop-shadow-md">
            <span class="text-primary font-bold text-lg tracking-wide">Groupe Speed Cloud</span>
        </div>
        <h1 class="text-2xl font-extrabold mb-2 text-primary">Bienvenue sur MonAsso</h1>
        <p class="text-gray-500 mb-8">Votre espace associatif en ligne, simple et sécurisé.</p>
        @if(env('STRIPE_PORTAL_URL'))
            <a href="{{ env('STRIPE_PORTAL_URL') }}" class="w-full bg-primary text-white font-bold py-3 rounded-xl hover:bg-primary/90 transition text-lg mb-4 shadow-lg" target="_blank">Gérer ma facturation</a>
        @endif
        <div class="flex flex-col gap-3 w-full">
            <a href="/login" class="w-full bg-gray-700 text-white font-bold py-3 rounded-xl hover:bg-gray-800 transition text-lg">Je suis client</a>
            <a href="/subscribe" class="w-full bg-accent text-white font-bold py-3 rounded-xl hover:bg-accent/90 transition text-lg">Nouveau client</a>
        </div>
    </div>
</body>
</html>
