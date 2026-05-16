<?php
namespace App\Http\Controllers;

use App\Models\Instance;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Http;

class SubscribeController extends Controller
{

    /**
     * Suspendre une instance : crée un .htaccess de redirection dans le dossier utilisateur via cPanel UAPI
     */
    public function suspendInstance($subdomain)
    {
        $cpanelUrl = env('CPANEL_API_URL');
        $cpanelUser = env('CPANEL_API_USER');
        $cpanelToken = env('CPANEL_API_TOKEN');
        $usersPath = env('CPANEL_USERS_PATH', '/home/gowo3083/app.monasso.eu/users');
        $targetDir = rtrim($usersPath, '/') . '/' . $subdomain;
        $htaccessPath = $targetDir . '/.htaccess';
        $content = "RewriteEngine On\nRewriteRule ^(.*)$ https://monasso.eu/errors/suspended-instance [R=302,L]";

        if ($cpanelUrl && $cpanelUser && $cpanelToken) {
            try {
                $response = \Illuminate\Support\Facades\Http::withHeaders([
                    'Authorization' => 'cpanel ' . $cpanelUser . ':' . $cpanelToken,
                ])->post($cpanelUrl . '/execute/Fileman/save_file', [
                    'file' => $htaccessPath,
                    'data' => $content,
                    'encoding' => 'utf-8',
                ]);
                if (!$response->ok()) {
                    \Log::error('cPanel UAPI: Erreur lors de la suspension (.htaccess)', ['response' => $response->body()]);
                }
            } catch (\Throwable $e) {
                \Log::error('cPanel UAPI: Exception suspension - ' . $e->getMessage());
            }
        }
    }

    /**
     * Réactiver une instance : supprime le .htaccess de redirection via cPanel UAPI
     */
    public function unsuspendInstance($subdomain)
    {
        $cpanelUrl = env('CPANEL_API_URL');
        $cpanelUser = env('CPANEL_API_USER');
        $cpanelToken = env('CPANEL_API_TOKEN');
        $usersPath = env('CPANEL_USERS_PATH', '/home/gowo3083/app.monasso.eu/users');
        $targetDir = rtrim($usersPath, '/') . '/' . $subdomain;
        $htaccessPath = $targetDir . '/.htaccess';

        if ($cpanelUrl && $cpanelUser && $cpanelToken) {
            try {
                $response = \Illuminate\Support\Facades\Http::withHeaders([
                    'Authorization' => 'cpanel ' . $cpanelUser . ':' . $cpanelToken,
                ])->post($cpanelUrl . '/execute/Fileman/file_delete', [
                    'files' => json_encode([$htaccessPath]),
                ]);
                if (!$response->ok()) {
                    \Log::error('cPanel UAPI: Erreur lors de la réactivation (suppression .htaccess)', ['response' => $response->body()]);
                }
            } catch (\Throwable $e) {
                \Log::error('cPanel UAPI: Exception réactivation - ' . $e->getMessage());
            }
        }
    }
    public function showForm()
    {
        return view('subscribe');
    }

    public function checkSubdomain(Request $request)
    {
        $sub = strtolower($request->input('subdomain'));
        if (!preg_match('/^[a-z0-9]{3,32}$/', $sub)) {
            return response()->json(['available' => false, 'error' => 'Format invalide']);
        }
        $exists = Instance::where('subdomain', $sub)->exists();

        // Vérifie si un dossier existe déjà sur le FTP
        // Vérification via cPanel UAPI
        $cpanelUrl = env('CPANEL_API_URL');
        $cpanelUser = env('CPANEL_API_USER');
        $cpanelToken = env('CPANEL_API_TOKEN');
        $usersPath = env('CPANEL_USERS_PATH', '/home/gowo3083/app.monasso.eu/users');
        $folderExists = false;
        if ($cpanelUrl && $cpanelUser && $cpanelToken && $usersPath) {
            try {
                $fullPath = rtrim($usersPath, '/') . '/' . $sub;
                $response = Http::withHeaders([
                    'Authorization' => 'cpanel ' . $cpanelUser . ':' . $cpanelToken,
                ])->get($cpanelUrl . '/execute/Fileman/list_files', [
                    'dir' => $usersPath,
                    'types' => 'dir',
                ]);
                if ($response->ok() && isset($response['data'])) {
                    foreach ($response['data'] as $item) {
                        if (isset($item['file']) && $item['file'] === $sub && $item['type'] === 'dir') {
                            $folderExists = true;
                            break;
                        }
                    }
                } else {
                    \Log::error('cPanel UAPI: Erreur de réponse', ['response' => $response->body()]);
                }
            } catch (\Throwable $e) {
                \Log::error('cPanel UAPI: Exception - ' . $e->getMessage());
            }
        }

        if ($exists || $folderExists) {
            return response()->json(['available' => false, 'error' => 'Sous-domaine déjà pris.']);
        }
        return response()->json(['available' => true]);
    }

    public function createCheckoutSession(Request $request)
    {
        \Log::info('createCheckoutSession: Début', ['input' => $request->all()]);
        try {
            $data = $request->validate([
                'subdomain' => 'required|regex:/^[a-z0-9]{3,32}$/|unique:instances,subdomain',
                'email' => 'required|email',
                'association_name' => 'nullable|string|max:255',
            ]);
            \Log::info('createCheckoutSession: Validation OK', ['data' => $data]);
        } catch (\Exception $e) {
            \Log::error('createCheckoutSession: Erreur validation', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Erreur de validation : ' . $e->getMessage()], 422);
        }

        try {
            $instance = Instance::create([
                'subdomain' => $data['subdomain'],
                'email' => $data['email'],
                'association_name' => $data['association_name'] ?? null,
                'status' => 'pending',
            ]);
            \Log::info('createCheckoutSession: Instance créée', ['instance_id' => $instance->id]);
        } catch (\Exception $e) {
            \Log::error('createCheckoutSession: Erreur création instance', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Erreur lors de la création de l\'instance : ' . $e->getMessage()], 500);
        }

        // Création de la session Stripe Checkout
        try {
            \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
            $session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'mode' => 'subscription',
                'customer_email' => $data['email'],
                'line_items' => [[
                    'price' => config('services.stripe.price_id'),
                    'quantity' => 1,
                ]],
                'success_url' => config('services.stripe.success_url'),
                'cancel_url' => config('services.stripe.cancel_url'),
                'metadata' => [
                    'instance_id' => $instance->id,
                    'subdomain' => $data['subdomain'],
                    'association_name' => $data['association_name'] ?? '',
                ],
            ]);
            \Log::info('createCheckoutSession: Session Stripe créée', ['session_id' => $session->id]);
            return response()->json(['url' => $session->url]);
        } catch (\Exception $e) {
            \Log::error('createCheckoutSession: Erreur Stripe', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Erreur lors de la création de la session Stripe : ' . $e->getMessage()], 500);
        }
    }
}
