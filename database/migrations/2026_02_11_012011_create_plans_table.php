<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('Kode');
            $table->string('nama');
            $table->decimal('harga_bulanan',12,2);
            $table->decimal('harga_tahunan',12,2);
            $table->integer('max_user')->default(1);
            $table->integer('max_cabang')->default(1);
            $table->json('fitur');
            $table->boolean('is_active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
