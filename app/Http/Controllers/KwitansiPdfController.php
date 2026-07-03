<?php

namespace App\Http\Controllers;

use App\Models\Kwitansi;
use Illuminate\Support\Str;
use Spatie\Browsershot\Browsershot;
use Spatie\LaravelPdf\Facades\Pdf;
use Spatie\LaravelPdf\PdfBuilder;

class KwitansiPdfController extends Controller
{
    public function __invoke(int $kwitansi): PdfBuilder
    {
        // Scope tenant manual — pola sama dengan halaman lain (currentTenant tidak di-bind).
        $k = Kwitansi::with(['tenant', 'unit.masterbarang.merek', 'unit.masterbarang.tipe', 'unit.unitdetail'])
            ->where('tenant_id', auth()->user()->tenant_id)
            ->findOrFail($kwitansi);

        $filename = 'kwitansi-' . Str::slug($k->nomor) . '.pdf';

        return Pdf::view('pdf.kwitansi', ['k' => $k])
            ->format('a5')
            ->landscape()
            ->name($filename)
            ->withBrowsershot(function (Browsershot $browsershot) {
                // Endpoint Chromium remote (mis. Cloudflare Browser Rendering).
                // Diisi lewat env; kalau kosong, pakai Chromium lokal default.
                $host = config('services.pdf.browser_host');

                if ($host) {
                    $browsershot->setRemoteInstance(
                        $host,
                        (int) config('services.pdf.browser_port', 9222),
                    );
                }
            });
    }
}
