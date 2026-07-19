<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loyalty_programs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('name')->default('Default Program');
            $table->decimal('points_per_currency', 8, 2)->default(1);
            $table->integer('minimum_points_redeem')->default(100);
            $table->integer('points_per_visit')->default(10);
            $table->integer('birthday_points')->default(50);
            $table->integer('review_points')->default(20);
            $table->boolean('is_active')->default(true);
            $table->json('tiers')->nullable();
            $table->timestamps();
        });

        Schema::create('loyalty_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('set null');
            $table->integer('points');
            $table->string('type');
            $table->string('description')->nullable();
            $table->string('reference')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loyalty_transactions');
        Schema::dropIfExists('loyalty_programs');
    }
};
