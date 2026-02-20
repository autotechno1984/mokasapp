<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('masterbarangs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tipe_id');
            $table->foreignId('kategori_id');
            $table->foreignId('merek_id');
            $table->foreignId('model_id');
            $table->string('nama_barang');
            $table->boolean('isactive')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('masterbarangs');
    }
};
