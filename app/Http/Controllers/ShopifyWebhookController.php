<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ShopifyWebhookController extends Controller
{
    public function handleOrder(Request $request)
    {
        $data = $request->all();

        Order::create([
            'order_id' => $data['id'],
            'email' => $data['email'] ?? null,
            'customer_name' => ($data['customer']['first_name'] ?? '') . ' ' . ($data['customer']['last_name'] ?? ''),
            'total_price' => $data['total_price'] ?? 0,
            'financial_status' => $data['financial_status'] ?? null,
            'fulfillment_status' => $data['fulfillment_status'] ?? null,
            'delivery_status' => null,
            'tracking_number' => null,
            'tracking_url' => null,
            'raw_data' => json_encode($data),
        ]);

        return response()->json(['status' => 'order stored']);
    }

    public function handleFulfillment(Request $request)
    {
        $data = $request->all();

        $orderId = $data['order_id'] ?? null;
        if (!$orderId) {
            return response()->json(['error' => 'Missing order_id'], 400);
        }

        $deliveryStatus = $data['shipment_status'] ?? null;
        $trackingNumber = $data['tracking_numbers'][0] ?? null;
        $trackingUrl = $data['tracking_urls'][0] ?? null;

        $order = Order::where('order_id', $orderId)->first();
        if ($order) {
            $order->update([
                'delivery_status' => $deliveryStatus,
                'tracking_number' => $trackingNumber,
                'tracking_url' => $trackingUrl,
            ]);
        }

        return response()->json(['status' => 'fulfillment updated']);
    }

    function updateDeliveryStatusInShopify($orderId)
    {
        $storeUrl = 'fw7yrz-8z.myshopify.com';
        $accessToken = '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';

        // Step 1: Get the fulfillment ID
        $response = Http::withHeaders([
            'X-Shopify-Access-Token' => $accessToken,
        ])->get("https://{$storeUrl}/admin/api/2025-04/orders/{$orderId}/fulfillments.json");

        $fulfillments = $response->json()['fulfillments'] ?? [];

        if (count($fulfillments) === 0) {
            return 'No fulfillment found for this order.';
        }

        $fulfillmentId = $fulfillments[0]['id'];

        // Step 2: Update shipment_status to 'delivered'
        $update = Http::withHeaders([
            'X-Shopify-Access-Token' => $accessToken,
            'Content-Type' => 'application/json',
        ])->put("https://{$storeUrl}/admin/api/2025-04/orders/{$orderId}/fulfillments/{$fulfillmentId}.json", [
            'fulfillment' => [
                'id' => $fulfillmentId,
                'shipment_status' => 'delivered',
            ]
        ]);

        return $update->successful() ? 'Updated to delivered!' : $update->json();
    }
}
