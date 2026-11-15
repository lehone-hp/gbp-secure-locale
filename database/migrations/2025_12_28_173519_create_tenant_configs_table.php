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
        Schema::create('tenant_configs', function (Blueprint $table) {
            $table->id();
            $table->string('domain')->unique()->comment('Domain name (e.g., domain1.com)');
            $table->string('payment_gateway')->comment('Payment gateway: braintree, stripe, paypal');
            $table->boolean('is_active')->default(true)->comment('Whether this tenant config is active');
            $table->json('settings')->nullable()->comment('Additional tenant-specific settings');
            $table->timestamps();
            
            $table->index('domain');
            $table->index(['domain', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenant_configs');
    }
};
