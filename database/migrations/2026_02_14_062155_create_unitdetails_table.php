<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('unitdetails', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_id');
            $table->string('no_polisi');
            $table->string('no_mesin');
            $table->string('no_rangka');
            $table->integer('tahun');
            $table->integer('km');
            $table->string('warna');
            $table->string('nama_bpkb')->nullable();
            $table->string('alamat_bpkb')->nullable();
            $table->string('no_bpkb')->nullable();
            $table->date('masa_berlaku_pajak');
            $table->date('masa_berlaku_stnk');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('unitdetails');
    }
};
