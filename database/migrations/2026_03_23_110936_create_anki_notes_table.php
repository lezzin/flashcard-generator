<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('anki_notes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('anki_id')->nullable()->index();
            $table->string('fields_hash')->index();
            $table->string('model_name');
            $table->string('type');
            $table->json('fields');
            $table->json('improved_fields')->nullable();
            $table->json('keywords')->nullable();
            $table->boolean('is_valid')->nullable();
            $table->boolean('is_recoverable')->nullable();
            $table->text('invalidation_reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anki_notes');
    }
};
