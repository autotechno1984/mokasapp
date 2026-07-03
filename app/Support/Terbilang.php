<?php

namespace App\Support;

class Terbilang
{
    /**
     * Ubah angka menjadi kata-kata Bahasa Indonesia.
     * Contoh: 1250000 => "satu juta dua ratus lima puluh ribu".
     */
    public static function make(float $number): string
    {
        $number = (int) floor(abs($number));

        if ($number === 0) {
            return 'nol';
        }

        return trim(preg_replace('/\s+/', ' ', self::convert($number)));
    }

    /**
     * Versi lengkap dengan satuan rupiah, mis. "satu juta rupiah".
     */
    public static function rupiah(float $number): string
    {
        return trim(self::make($number)) . ' rupiah';
    }

    private static function convert(int $number): string
    {
        $angka = [
            '', 'satu', 'dua', 'tiga', 'empat', 'lima',
            'enam', 'tujuh', 'delapan', 'sembilan', 'sepuluh', 'sebelas',
        ];

        if ($number < 12) {
            return ' ' . $angka[$number];
        }

        if ($number < 20) {
            return self::convert($number - 10) . ' belas';
        }

        if ($number < 100) {
            return self::convert(intdiv($number, 10)) . ' puluh' . self::convert($number % 10);
        }

        if ($number < 200) {
            return ' seratus' . self::convert($number - 100);
        }

        if ($number < 1000) {
            return self::convert(intdiv($number, 100)) . ' ratus' . self::convert($number % 100);
        }

        if ($number < 2000) {
            return ' seribu' . self::convert($number - 1000);
        }

        if ($number < 1_000_000) {
            return self::convert(intdiv($number, 1000)) . ' ribu' . self::convert($number % 1000);
        }

        if ($number < 1_000_000_000) {
            return self::convert(intdiv($number, 1_000_000)) . ' juta' . self::convert($number % 1_000_000);
        }

        if ($number < 1_000_000_000_000) {
            return self::convert(intdiv($number, 1_000_000_000)) . ' miliar' . self::convert($number % 1_000_000_000);
        }

        return self::convert(intdiv($number, 1_000_000_000_000)) . ' triliun' . self::convert($number % 1_000_000_000_000);
    }
}
