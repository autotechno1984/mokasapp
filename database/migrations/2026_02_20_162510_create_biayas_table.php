<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('biayas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id');
            $table->string('kategori')->comment('biaya gaji','biaya sewa','biaya air','biaya listrik','biaya internet','biaya telepon','biaya lainnya');
            $table->date('tanggal');
            $table->string('keterangan');
            $table->decimal('jumlah',12,2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('biayas');
    }
};
