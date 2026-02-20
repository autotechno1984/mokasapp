<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tipe;

class TipeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tipes = [
            ['nama' => 'CUB', 'isactive' => true],
            ['nama' => 'MATIC', 'isactive' => true],
            ['nama' => 'SPORT', 'isactive' => true],
            ['nama' => 'SEDAN', 'isactive' => true],
        ];

        foreach ($tipes as $tipe) {
            Tipe::updateOrCreate(['nama' => $tipe['nama']], $tipe);
        }
    }
}
