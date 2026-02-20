<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('unitbiayas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_id');
            $table->string('kategori')->comment('perbaikan','pajak');
            $table->string('keterangan',250);
            $table->decimal('amount',14,2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('unitbiayas');
    }
};
