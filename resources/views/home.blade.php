<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil MonAsso</title>
    <link href="https://fonts.googleapis.com/css2?family=Titillium+Web:wght@400;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        html, body, * { font-family: 'Titillium Web', ui-sans-serif, system-ui, sans-serif !important; }
    </style>
</head>
<body class="bg-lavender min-h-screen flex items-center justify-center font-sans">
    <div class="bg-white p-8 rounded-2xl shadow-soft w-full max-w-md border-t-8 border-primary text-center">
        <div class="flex justify-center mb-4">
            <img src="https://www.groupe-speed.cloud/logo.svg" alt="MonAsso" class="h-12 drop-shadow-md">
        </div>
        <h1 class="text-2xl font-extrabold mb-6 text-primary">Bienvenue sur MonAsso</h1>
        <div class="flex flex-col gap-4">
            <a href="/login" class="w-full bg-primary text-white font-bold py-3 rounded-xl hover:bg-primary/90 transition text-lg">Je suis client</a>
            <a href="/subscribe" class="w-full bg-accent text-white font-bold py-3 rounded-xl hover:bg-accent/90 transition text-lg">Nouveau client</a>
        </div>
    </div>
</body>
</html>
