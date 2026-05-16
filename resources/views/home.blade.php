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
                        splitRight: '#F4F0FF',
                        splitLeft: '#FFFFFF',
                    },
                }
            }
        }
    </script>
    <style>
        html, body, * { font-family: 'Titillium Web', ui-sans-serif, system-ui, sans-serif !important; }
    </style>
</head>
<body class="min-h-screen flex flex-col bg-monassoLight">
    <header class="w-full flex justify-center pt-10 pb-2">
        <img src="https://www.groupe-speed.cloud/logo.svg" alt="MonAsso" class="h-12">
    </header>
    <main class="flex-1 flex items-center justify-center">
        <div class="w-full max-w-5xl mx-auto flex flex-col md:flex-row rounded-3xl overflow-hidden shadow-none bg-white border border-monasso/10">
            <!-- Nouveau client -->
            <section class="flex-1 flex flex-col justify-center items-center bg-splitLeft py-16 px-8">
                <h2 class="text-3xl font-extrabold text-monasso mb-4 text-center">Nouveau client</h2>
                <p class="text-gray-700 mb-8 text-center text-base max-w-xs">Créez votre espace associatif en quelques clics.<br>Paiement sécurisé, activation immédiate.</p>
                <a href="/subscribe" class="w-64 bg-accent text-white font-bold py-3 rounded-xl hover:bg-accent/90 transition text-lg text-center">Continuer</a>
            </section>
            <!-- Déjà client -->
            <section class="flex-1 flex flex-col justify-center items-center bg-splitRight py-16 px-8">
                <h2 class="text-3xl font-extrabold text-monassoDark mb-4 text-center">Déjà client MonAsso</h2>
                <p class="text-gray-700 mb-4 text-center text-base max-w-xs font-semibold">Vous avez déjà un espace MonAsso&nbsp;?</p>
                <p class="text-gray-700 mb-8 text-center text-base max-w-xs">Accédez à votre facturation en un clic&nbsp;:</p>
                <div class="flex flex-col gap-4 w-64">
                    @if(env('STRIPE_PORTAL_URL'))
                    <a href="{{ env('STRIPE_PORTAL_URL') }}" target="_blank" class="bg-monasso text-white font-bold py-3 rounded-xl hover:bg-monassoDark transition text-lg text-center">Gérer ma facturation</a>
                    @endif
                </div>
            </section>
        </div>
    </main>
</body>
</html>
