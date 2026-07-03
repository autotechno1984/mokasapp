<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('kwitansis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->index();
            $table->foreignId('user_id')->nullable();

            $table->string('nomor');
            $table->date('tanggal');
            $table->string('nama_penerima');          // "Telah terima dari"
            $table->string('untuk_pembayaran');       // Keterangan pembayaran
            $table->decimal('jumlah', 14, 2);
            $table->string('metode')->nullable();     // Tunai / Transfer / dll
            $table->foreignId('unit_id')->nullable(); // opsional: kaitkan ke unit
            $table->enum('status', ['aktif', 'batal'])->default('aktif');
            $table->string('catatan')->nullable();

            $table->timestamps();

            // Nomor kwitansi unik per tenant
            $table->unique(['tenant_id', 'nomor']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kwitansis');
    }
};
