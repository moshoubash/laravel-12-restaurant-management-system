<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->onDelete('cascade');
            $table->integer('table_number');
            $table->integer('capacity')->default(4);
            $table->string('section')->nullable();
            $table->string('status')->default('free');
            $table->string('qr_code')->nullable();
            $table->float('x_position')->nullable();
            $table->float('y_position')->nullable();
            $table->string('shape')->default('rectangle');
            $table->float('width')->default(60);
            $table->float('height')->default(60);
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();

            $table->unique(['branch_id', 'table_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tables');
    }
};
