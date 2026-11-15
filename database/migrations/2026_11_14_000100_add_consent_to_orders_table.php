<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->timestamp('terms_accepted_at')->nullable()->after('notes');
            $table->boolean('immediate_start_accepted')->default(false)->after('terms_accepted_at');
            $table->string('policy_version', 20)->nullable()->after('immediate_start_accepted');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['terms_accepted_at', 'immediate_start_accepted', 'policy_version']);
        });
    }
};
