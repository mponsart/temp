<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Souscrire à Paheko SaaS</title>
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
        <form id="subscribe-form" class="space-y-6" autocomplete="off" novalidate>
            <div class="relative">
                <input type="text" id="subdomain" name="subdomain" required pattern="[a-z0-9]{3,32}" autocomplete="off"
                    class="peer w-full border border-gray-300 rounded-xl px-4 pt-6 pb-2 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition placeholder-transparent bg-lavender/60"
                    placeholder="Sous-domaine" aria-describedby="subdomain-status">
                <label for="subdomain" class="absolute left-4 top-2 text-xs text-gray-500 transition-all peer-focus:text-primary peer-focus:top-1 peer-focus:text-xs peer-placeholder-shown:top-4 peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-400 pointer-events-none">Sous-domaine souhaité</label>
                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-primary font-bold">.monasso.eu</span>
                <p id="subdomain-status" class="text-xs mt-1"></p>
            </div>
            <div class="relative">
                <input type="text" id="association_name" name="association_name" required
                    class="peer w-full border border-gray-300 rounded-xl px-4 pt-6 pb-2 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition placeholder-transparent bg-lavender/60"
                    placeholder="Nom de l'association">
                <label for="association_name" class="absolute left-4 top-2 text-xs text-gray-500 transition-all peer-focus:text-primary peer-focus:top-1 peer-focus:text-xs peer-placeholder-shown:top-4 peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-400 pointer-events-none">Nom de l'association</label>
            </div>
            <div class="relative">
                <input type="email" id="email" name="email" required autocomplete="email"
                    class="peer w-full border border-gray-300 rounded-xl px-4 pt-6 pb-2 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition placeholder-transparent bg-lavender/60"
                    placeholder="Votre email">
                <label for="email" class="absolute left-4 top-2 text-xs text-gray-500 transition-all peer-focus:text-primary peer-focus:top-1 peer-focus:text-xs peer-placeholder-shown:top-4 peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-400 pointer-events-none">Votre email</label>
            </div>
            <div class="bg-lavender/80 rounded-xl p-4 text-sm text-gray-700 border border-primary/10 shadow-inner">
                <div class="font-semibold text-primary mb-1">Récapitulatif :</div>
                <ul class="list-disc list-inside space-y-1">
                    <li>Votre sous-domaine : <span id="recap-subdomain" class="font-bold text-primary"></span><span class="text-primary">.monasso.eu</span></li>
                    <li>Nom de l'association : <span id="recap-association" class="font-bold"></span></li>
                    <li>Email de contact : <span id="recap-email" class="font-bold"></span></li>
                </ul>
            </div>
            <button type="submit" id="submit-btn" class="w-full bg-primary text-white font-bold py-3 rounded-xl hover:bg-primary/90 transition flex items-center justify-center shadow-lg text-lg">
                <span id="btn-text">Souscrire</span>
                <svg id="loader" class="animate-spin ml-2 h-5 w-5 text-white hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path></svg>
            </button>
        </form>
        <div id="error-message" class="text-red-600 mt-4 text-center font-semibold"></div>
        <div id="success-message" class="text-accent mt-4 text-center hidden font-semibold"></div>
        <p class="text-xs text-gray-400 mt-6 text-center">Vos données sont protégées et votre espace est isolé et sécurisé.<br>Support 7j/7 - Activation immédiate après paiement.</p>
    </div>
    <script>
        const subdomainInput = document.getElementById('subdomain');
        const associationInput = document.getElementById('association_name');
        const emailInput = document.getElementById('email');
        const status = document.getElementById('subdomain-status');
        const form = document.getElementById('subscribe-form');
        const errorMessage = document.getElementById('error-message');
        const successMessage = document.getElementById('success-message');
        const submitBtn = document.getElementById('submit-btn');
        const btnText = document.getElementById('btn-text');
        const loader = document.getElementById('loader');
        const recapSub = document.getElementById('recap-subdomain');
        const recapAsso = document.getElementById('recap-association');
        const recapEmail = document.getElementById('recap-email');
        let subdomainAvailable = false;
        let lastChecked = '';

        function updateRecap() {
            recapSub.textContent = subdomainInput.value.trim().toLowerCase();
            recapAsso.textContent = associationInput.value.trim();
            recapEmail.textContent = emailInput.value.trim();
        }
        [subdomainInput, associationInput, emailInput].forEach(input => {
            input.addEventListener('input', updateRecap);
        });
        updateRecap();

        subdomainInput.addEventListener('input', async () => {
            const value = subdomainInput.value.trim().toLowerCase();
            if (!/^[a-z0-9]{3,32}$/.test(value)) {
                status.textContent = '3 à 32 lettres ou chiffres, sans espace.';
                status.className = 'text-xs text-gray-500 mt-1';
                subdomainAvailable = false;
                return;
            }
            lastChecked = value;
            status.textContent = 'Vérification...';
            status.className = 'text-xs text-gray-500 mt-1';
            try {
                const res = await fetch(`/api/check-subdomain?subdomain=${encodeURIComponent(value)}`);
                const json = await res.json();
                if (lastChecked !== value) return; // ignore outdated
                if (json.available) {
                    status.textContent = 'Sous-domaine disponible !';
                    status.className = 'text-xs text-accent mt-1';
                    subdomainAvailable = true;
                } else {
                    status.textContent = json.error || 'Sous-domaine déjà pris.';
                    status.className = 'text-xs text-red-600 mt-1';
                    subdomainAvailable = false;
                }
            } catch (e) {
                status.textContent = 'Erreur réseau.';
                status.className = 'text-xs text-red-600 mt-1';
                subdomainAvailable = false;
            }
        });

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            errorMessage.textContent = '';
            successMessage.classList.add('hidden');
            if (!subdomainAvailable) {
                errorMessage.textContent = 'Veuillez choisir un sous-domaine disponible.';
                subdomainInput.focus();
                return;
            }
            if (!associationInput.value.trim()) {
                errorMessage.textContent = 'Veuillez renseigner le nom de l\'association.';
                associationInput.focus();
                return;
            }
            if (!emailInput.value.trim() || !/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(emailInput.value.trim())) {
                errorMessage.textContent = 'Veuillez renseigner un email valide.';
                emailInput.focus();
                return;
            }
            const email = emailInput.value.trim();
            const subdomain = subdomainInput.value.trim().toLowerCase();
            const association_name = associationInput.value.trim();
            submitBtn.disabled = true;
            btnText.textContent = 'Redirection vers Stripe...';
            loader.classList.remove('hidden');
            try {
                const res = await fetch('/subscribe/checkout-session', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') },
                    body: JSON.stringify({ subdomain, email, association_name })
                });
                const json = await res.json();
                if (json.url) {
                    window.location = json.url;
                } else {
                    errorMessage.textContent = json.error || 'Erreur lors de la création de la session Stripe.';
                    submitBtn.disabled = false;
                    btnText.textContent = 'Souscrire';
                    loader.classList.add('hidden');
                }
            } catch (e) {
                errorMessage.textContent = 'Erreur réseau.';
                submitBtn.disabled = false;
                btnText.textContent = 'Souscrire';
                loader.classList.add('hidden');
            }
        });
    </script>
</body>
</html>
