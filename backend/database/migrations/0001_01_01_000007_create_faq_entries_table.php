<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('faq_entries', function (Blueprint $table) {
            $table->id();
            $table->text('keywords'); // Comma separated
            $table->text('answer');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('faq_entries');
    }
};
