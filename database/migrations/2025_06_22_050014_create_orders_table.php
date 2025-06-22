<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('order_id');
            $table->string('email')->nullable();
            $table->string('customer_name')->nullable();
            $table->decimal('total_price', 10, 2)->nullable();
            $table->string('financial_status')->nullable();
            $table->string('fulfillment_status')->nullable();
            $table->string('delivery_status')->nullable();       // e.g., in_transit, delivered
            $table->string('tracking_number')->nullable();
            $table->string('tracking_url')->nullable();
            $table->json('raw_data'); // optional full JSON for reference
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
