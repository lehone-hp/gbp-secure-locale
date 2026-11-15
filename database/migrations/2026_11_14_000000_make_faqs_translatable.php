<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Wrap existing plain-text values as English translations before the JSON conversion
        DB::table('faqs')->get()->each(function ($faq) {
            DB::table('faqs')->where('id', $faq->id)->update([
                'question' => json_encode(['en' => $faq->question]),
                'answer' => json_encode(['en' => $faq->answer]),
            ]);
        });

        Schema::table('faqs', function (Blueprint $table) {
            $table->json('question')->change();
            $table->json('answer')->change();
        });
    }

    public function down(): void
    {
        Schema::table('faqs', function (Blueprint $table) {
            $table->string('question')->change();
            $table->longText('answer')->change();
        });

        DB::table('faqs')->get()->each(function ($faq) {
            DB::table('faqs')->where('id', $faq->id)->update([
                'question' => json_decode($faq->question, true)['en'] ?? '',
                'answer' => json_decode($faq->answer, true)['en'] ?? '',
            ]);
        });
    }
};
