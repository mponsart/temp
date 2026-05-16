<?php
namespace App\Http\Controllers;

use App\Models\Instance;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;

class SubscribeController extends Controller
{
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
        $ftpHost = config('services.ftp.host');
        $ftpUser = config('services.ftp.user');
        $ftpPass = config('services.ftp.pass');
        $ftpBasePath = config('services.ftp.base_path', '/');
        $folderExists = false;
        if ($ftpHost && $ftpUser && $ftpPass) {
            $conn = @ftp_connect($ftpHost);
            if ($conn && @ftp_login($conn, $ftpUser, $ftpPass)) {
                $dirList = @ftp_nlist($conn, $ftpBasePath);
                if ($dirList && in_array($ftpBasePath . $sub, $dirList)) {
                    $folderExists = true;
                } elseif ($dirList && in_array($sub, array_map(function($d) use ($ftpBasePath) { return str_replace($ftpBasePath, '', $d); }, $dirList))) {
                    $folderExists = true;
                }
                ftp_close($conn);
            }
        }

        if ($exists || $folderExists) {
            return response()->json(['available' => false, 'error' => 'Sous-domaine déjà pris.']);
        }
        return response()->json(['available' => true]);
    }

    public function createCheckoutSession(Request $request)
    {
        $data = $request->validate([
            'subdomain' => 'required|regex:/^[a-z0-9]{3,32}$/|unique:instances,subdomain',
            'email' => 'required|email',
            'association_name' => 'nullable|string|max:255',
        ]);

        $instance = Instance::create([
            'subdomain' => $data['subdomain'],
            'email' => $data['email'],
            'association_name' => $data['association_name'] ?? null,
            'status' => 'pending',
        ]);

        // Création de la session Stripe Checkout
        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
        try {
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
            return response()->json(['url' => $session->url]);
        } catch (\Exception $e) {
            \Log::error('Stripe error: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de la création de la session Stripe.'], 500);
        }
    }
}
