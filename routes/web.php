<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ShopifyWebhookController;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/shopify/order-webhook', [ShopifyWebhookController::class, 'handleOrder']);
Route::post('/shopify/fulfillment-webhook', [ShopifyWebhookController::class, 'handleFulfillment']);
