<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'Kode' => 'STARTER',
                'nama' => 'Starter',
                'harga_bulanan' => 349000,
                'harga_tahunan' => 3490000,
                'max_user' => 2,
                'max_cabang' => 1,
                'fitur' => json_encode([
                    'Manajemen Produk',
                    'Kasir / POS',
                    'Laporan Penjualan Dasar',
                    'Manajemen Stok',
                ]),
                'is_active' => true,
            ],
            [
                'Kode' => 'PROFESSIONAL',
                'nama' => 'Professional',
                'harga_bulanan' => 599000,
                'harga_tahunan' => 5990000,
                'max_user' => 5,
                'max_cabang' => 3,
                'fitur' => json_encode([
                    'Manajemen Produk',
                    'Kasir / POS',
                    'Laporan Penjualan Lengkap',
                    'Manajemen Stok',
                    'Multi Cabang',
                    'Manajemen Karyawan',
                    'Promo & Diskon',
                ]),
                'is_active' => true,
            ],
            [
                'Kode' => 'ENTERPRISE',
                'nama' => 'Enterprise',
                'harga_bulanan' => 999000,
                'harga_tahunan' => 9990000,
                'max_user' => 20,
                'max_cabang' => 10,
                'fitur' => json_encode([
                    'Manajemen Produk',
                    'Kasir / POS',
                    'Laporan Penjualan Lengkap',
                    'Manajemen Stok',
                    'Multi Cabang',
                    'Manajemen Karyawan',
                    'Promo & Diskon',
                    'API Integrasi',
                    'Dukungan Prioritas',
                    'Custom Branding',
                ]),
                'is_active' => true,
            ],
        ];

        foreach ($plans as $plan) {
            Plan::updateOrCreate(['Kode' => $plan['Kode']], $plan);
        }
    }
}
