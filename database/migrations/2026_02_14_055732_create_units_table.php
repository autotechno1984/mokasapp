<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('tenant_id');
            $table->foreignId('user_id');
            $table->foreignId('masterbarang_id');
            $table->date('tgl_beli');
            $table->date('tgl_jual')->nullable();
            $table->decimal('harga_beli',14,2);
            $table->decimal('harga_jual',14,2)->nullable();
            $table->decimal('biaya',14,2);
            $table->enum('status',['siap-jual','perbaikan','sewa','tahan','terjual']);
            $table->boolean('unit_titip')->default(0);
            $table->unsignedBigInteger('gudang_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
