<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->date('tanggal');
            $table->enum('tipe', ['masuk', 'pulang']);
            $table->time('jam');
            $table->string('foto')->nullable(); 
            $table->timestamps();

            // Index agar pencarian cepat
            $table->index(['user_id', 'tanggal', 'tipe']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};