<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained()->onDelete('cascade');
            $table->foreignId('inventory_item_id')->nullable()->constrained()->onDelete('set null');
            $table->string('item_name');
            $table->decimal('quantity', 12, 2);
            $table->string('unit');
            $table->decimal('unit_cost', 10, 2);
            $table->decimal('total_cost', 12, 2);
            $table->decimal('received_quantity', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_order_items');
    }
};
