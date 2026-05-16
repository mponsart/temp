<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\SubscribeController;
use App\Http\Controllers\StripeWebhookController;

Route::view('/', 'home');

Route::get('/subscribe', [SubscribeController::class, 'showForm']);
Route::get('/api/check-subdomain', [SubscribeController::class, 'checkSubdomain']);
Route::post('/subscribe/checkout-session', [SubscribeController::class, 'createCheckoutSession']);
Route::post('/webhook/stripe', [StripeWebhookController::class, 'handle']);

// Pages de succès et d'annulation Stripe
Route::view('/subscribe/success', 'success');
Route::view('/subscribe/cancel', 'cancel');
Route::get('/subscribe/success', [SubscribeController::class, 'stripeSuccess']);
