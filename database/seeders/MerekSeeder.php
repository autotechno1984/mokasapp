<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MerekSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('mereks')->insert([
            ['nama' => 'HONDA', 'isactive' => 1],
            ['nama' => 'YAMAHA', 'isactive' => 1],
            ['nama' => 'SUZUKI', 'isactive' => 1],
            ['nama' => 'KAWASAKI', 'isactive' => 1],
            ['nama' => 'ISUZU', 'isactive' => 1],
            ['nama' => 'DAIHATSU', 'isactive' => 1],
            ['nama' => 'NISSAN', 'isactive' => 1],
            ['nama' => 'TOYOTA', 'isactive' => 1],
            ['nama' => 'MITSUBISHI', 'isactive' => 1],
            ['nama' => 'BYD', 'isactive' => 1],
            ['nama' => 'SAMSUNG', 'isactive' => 1],
            ['nama' => 'KIA', 'isactive' => 1],
            ['nama' => 'VOLKSWAGEN', 'isactive' => 1],
            ['nama' => 'APPLE', 'isactive' => 1],

        ]);
    }
}
