<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paiement annulé</title>
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
        <h1 class="text-2xl font-extrabold mb-2 text-primary">Paiement annulé</h1>
        <p class="text-gray-700 mb-4">Votre paiement a été annulé.<br>Votre espace Paheko n'a pas été créé.<br>
        Vous pouvez réessayer à tout moment.</p>
        <a href="/subscribe" class="inline-block mt-4 px-6 py-3 bg-primary text-white rounded-xl font-bold shadow hover:bg-primary/90 transition">Réessayer</a>
    </div>
</body>
</html>