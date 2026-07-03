<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Kwitansi {{ $k->nomor }}</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            color: #1a1a1a;
            font-size: 12px;
            margin: 0;
            padding: 24px 28px;
        }
        .sheet { position: relative; border: 2px solid #111; border-radius: 6px; padding: 18px 22px; }
        .watermark {
            position: absolute; top: 50%; left: 50%;
            transform: translate(-50%, -50%) rotate(-18deg);
            font-size: 90px; font-weight: 800; letter-spacing: 6px;
            color: rgba(220, 38, 38, 0.16);
            border: 8px solid rgba(220, 38, 38, 0.16);
            padding: 6px 30px; border-radius: 12px;
        }
        .head { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #111; padding-bottom: 10px; margin-bottom: 12px; }
        .brand { font-size: 18px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; }
        .brand small { display: block; font-size: 10px; font-weight: 400; text-transform: none; letter-spacing: 0; color: #555; margin-top: 2px; }
        .title { text-align: right; }
        .title h1 { margin: 0; font-size: 22px; letter-spacing: 3px; }
        .title .no { font-size: 11px; color: #444; }
        table.rows { width: 100%; border-collapse: collapse; margin-top: 4px; }
        table.rows td { padding: 6px 4px; vertical-align: top; }
        table.rows td.label { width: 150px; color: #444; }
        table.rows td.sep { width: 10px; }
        .terbilang { background: #f3f4f6; border-radius: 6px; padding: 8px 12px; font-style: italic; }
        .amount-box { display: inline-block; border: 2px solid #111; border-radius: 6px; padding: 8px 16px; font-size: 16px; font-weight: 800; }
        .foot { display: flex; justify-content: space-between; align-items: flex-end; margin-top: 22px; }
        .sign { text-align: center; width: 200px; }
        .sign .line { margin-top: 52px; border-top: 1px solid #111; padding-top: 4px; }
        .muted { color: #666; font-size: 10px; }
    </style>
</head>
<body>
    <div class="sheet">
        @if($k->isBatal())
            <div class="watermark">BATAL</div>
        @endif

        <div class="head">
            <div class="brand">
                {{ $k->tenant?->nama_tenant ?? 'Showroom' }}
                @php $alamat = data_get($k->tenant?->data, 'alamat') ?? data_get($k->tenant?->settings, 'alamat'); @endphp
                @php $telp = data_get($k->tenant?->data, 'telepon') ?? data_get($k->tenant?->settings, 'telepon'); @endphp
                @if($alamat)<small>{{ $alamat }}</small>@endif
                @if($telp)<small>Telp: {{ $telp }}</small>@endif
            </div>
            <div class="title">
                <h1>KWITANSI</h1>
                <div class="no">No: {{ $k->nomor }}</div>
            </div>
        </div>

        <table class="rows">
            <tr>
                <td class="label">Telah terima dari</td>
                <td class="sep">:</td>
                <td><strong>{{ $k->nama_penerima }}</strong></td>
            </tr>
            <tr>
                <td class="label">Uang sejumlah</td>
                <td class="sep">:</td>
                <td><span class="terbilang">{{ ucwords($k->terbilang()) }}</span></td>
            </tr>
            <tr>
                <td class="label">Untuk pembayaran</td>
                <td class="sep">:</td>
                <td>
                    {{ $k->untuk_pembayaran }}
                    @if($k->unit)
                        <br><span class="muted">
                            {{ $k->unit->masterbarang?->merek?->nama ?? '' }}
                            {{ $k->unit->masterbarang?->tipe?->nama ?? '' }}
                            @if($k->unit->unitdetail?->nopol) — {{ $k->unit->unitdetail->nopol }} @endif
                        </span>
                    @endif
                </td>
            </tr>
            @if($k->metode)
            <tr>
                <td class="label">Metode</td>
                <td class="sep">:</td>
                <td>{{ $k->metode }}</td>
            </tr>
            @endif
        </table>

        <div class="foot">
            <div>
                <div class="amount-box">Rp {{ number_format($k->jumlah, 0, ',', '.') }}</div>
                @if($k->catatan)<div class="muted" style="margin-top:8px;">{{ $k->catatan }}</div>@endif
            </div>
            <div class="sign">
                {{ $k->tanggal->translatedFormat('d F Y') }}
                <div class="line">Penerima</div>
            </div>
        </div>
    </div>
</body>
</html>
