<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\SubscribeController;
use App\Http\Controllers\StripeWebhookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\InstanceWelcomeMail;

Route::view('/', 'home');

Route::get('/subscribe', [SubscribeController::class, 'showForm']);
Route::get('/api/check-subdomain', [SubscribeController::class, 'checkSubdomain']);
Route::post('/subscribe/checkout-session', [SubscribeController::class, 'createCheckoutSession']);
Route::post('/webhook/stripe', [StripeWebhookController::class, 'handle']);
Route::post('/send-confirmation-mail', function(Request $request) {
    $data = $request->only(['subdomain', 'email', 'association_name']);
    $instance = (object) $data;
    try {
        Mail::to($instance->email)->send(new InstanceWelcomeMail($instance));
        return back()->with('success', 'Mail de confirmation envoyé !');
    } catch (\Throwable $e) {
        return back()->with('error', 'Erreur lors de l\'envoi du mail : ' . $e->getMessage());
    }
})->name('send.confirmation.mail');

// Pages de succès et d'annulation Stripe
Route::view('/subscribe/success', 'success');
Route::view('/subscribe/cancel', 'cancel');
Route::get('/subscribe/success', [SubscribeController::class, 'stripeSuccess']);
