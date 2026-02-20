<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kategoris = [
            ['nama' => 'R2', 'isactive' => true],
            ['nama' => 'R4', 'isactive' => true],
            ['nama' => 'HANDPHONE', 'isactive' => true],
            ['nama' => 'LAPTOP', 'isactive' => true],
        ];

        foreach ($kategoris as $kategori) {
            \App\Models\Kategori::updateOrCreate(['nama' => $kategori['nama']], $kategori);
        }
    }
}
