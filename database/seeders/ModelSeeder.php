<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        DB::table('models')->insert([
            ['nama' => 'BEAT', 'isactive' => '1',],
            ['nama' => 'VARIO', 'isactive' => '1',],
        ]);



    }
}
