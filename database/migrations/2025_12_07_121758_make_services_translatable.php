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
        Schema::table('services', function (Blueprint $table) {
            $table->json('name')->change();
            $table->json('caption')->change();
            $table->json('description')->change();
        });
        
        Schema::table('service_addons', function (Blueprint $table) {
            $table->json('name')->change();
            $table->json('description')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->string('name')->change();
            $table->string('caption')->change();
            $table->text('description')->change();
        });
        
        Schema::table('service_addons', function (Blueprint $table) {
            $table->string('name')->change();
            $table->text('description')->change();
        });
    }
};
