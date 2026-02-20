<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('penjualans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id');
            $table->foreignId('unit_id');
            $table->dateTime('tgl_jual');
            $table->string('nama_konsumen');
            $table->string('alamat')->nullable();
            $table->string('kontak')->nullable();
            $table->decimal('harga_jual',14,2);
            $table->enum('status_pembelian',['cash','kredit','cash-bertahap']);
            $table->string('leasing')->nullable();
            $table->string('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penjualans');
    }
};
