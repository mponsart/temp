<?php
namespace App\Http\Controllers;

use App\Models\Instance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\PahekoInstanceService;
use Illuminate\Support\Facades\Mail;
use App\Mail\InstanceWelcomeMail;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $event = $request->all();
        $type = $event['type'] ?? null;
        $data = $event['data']['object'] ?? [];
        $service = new PahekoInstanceService();

        if ($type === 'checkout.session.completed') {
            $instanceId = $data['metadata']['instance_id'] ?? null;
            if ($instanceId && ($instance = Instance::find($instanceId))) {
                // Sauvegarde l'ID de la souscription Stripe
                $instance->stripe_subscription_id = $data['subscription'] ?? null;
                $instance->status = 'active';
                $instance->save();
                $service->deploy($instance);
                // Envoi de l'email de bienvenue avec gestion d'erreur
                try {
                    Mail::to($instance->email)->send(new InstanceWelcomeMail($instance));
                    Log::info('Instance activée, déployée et email envoyé: ' . $instance->subdomain);
                } catch (\Throwable $e) {
                    Log::error('Erreur envoi mail bienvenue: ' . $e->getMessage(), ['instance' => $instance->id, 'email' => $instance->email]);
                    // Optionnel : réessayer une fois
                    try {
                        Mail::to($instance->email)->send(new InstanceWelcomeMail($instance));
                        Log::info('Réessai envoi mail bienvenue réussi: ' . $instance->subdomain);
                    } catch (\Throwable $e2) {
                        Log::critical('Echec définitif envoi mail bienvenue: ' . $e2->getMessage(), ['instance' => $instance->id, 'email' => $instance->email]);
                    }
                }
            }
        }
        if ($type === 'invoice.payment_failed') {
            $subscriptionId = $data['subscription'] ?? $data['id'] ?? null;
            if ($subscriptionId && ($instance = Instance::where('stripe_subscription_id', $subscriptionId)->first())) {
                $service->suspend($instance);
                $instance->status = 'suspended';
                $instance->save();
                Log::info('Instance suspendue: ' . $instance->subdomain);
            }
        }
        if ($type === 'customer.subscription.deleted') {
            $subscriptionId = $data['id'] ?? null;
            if ($subscriptionId && ($instance = Instance::where('stripe_subscription_id', $subscriptionId)->first())) {
                $service->delete($instance);
                $instance->status = 'deleted';
                $instance->save();
                Log::info('Instance supprimée: ' . $instance->subdomain);
            }
        }
        if ($type === 'invoice.paid') {
            $subscriptionId = $data['subscription'] ?? $data['id'] ?? null;
            if ($subscriptionId && ($instance = Instance::where('stripe_subscription_id', $subscriptionId)->first())) {
                $service->unsuspend($instance);
                $instance->status = 'active';
                $instance->save();
                Log::info('Instance réactivée: ' . $instance->subdomain);
            }
        }
        return response('OK', 200);
    }
}
