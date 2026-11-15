<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_addons', function (Blueprint $table) {
            $table->string('payment_link', 2048)->nullable()->after('type');
        });
    }

    public function down(): void
    {
        Schema::table('service_addons', function (Blueprint $table) {
            $table->dropColumn('payment_link');
        });
    }
};
