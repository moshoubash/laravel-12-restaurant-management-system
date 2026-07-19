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
        Schema::table('design_configs', function (Blueprint $table) {
            $table->string('locale', 5)->default('en')->after('receipt_footer');
        });
    }

    public function down(): void
    {
        Schema::table('design_configs', function (Blueprint $table) {
            $table->dropColumn('locale');
        });
    }
};
