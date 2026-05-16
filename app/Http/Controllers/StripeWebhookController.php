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
        \Log::info('[WEBHOOK] Stripe reçu', ['payload' => $request->all()]);
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
                // Stocke les infos en session pour la page de succès
                session([
                    'subdomain' => $instance->subdomain,
                    'email' => $instance->email,
                    'association_name' => $instance->association_name,
                    'amount' => isset($data['amount_total']) ? ($data['amount_total'] / 100) . ' €' : null,
                    'payment_id' => $data['payment_intent'] ?? null,
                ]);
                // Plus d’envoi de mail de bienvenue ici
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
                $instance->delete();
                Log::info('Instance supprimée de la base: ' . $instance->subdomain);
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
