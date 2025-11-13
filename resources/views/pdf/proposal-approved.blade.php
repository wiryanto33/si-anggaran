<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Bukti Persetujuan Proposal</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111; }
        .header { text-align: center; margin-bottom: 12px; }
        .header h1 { font-size: 18px; margin: 0 0 4px; }
        .header .sub { font-size: 12px; color: #555; }
        .meta { width: 100%; border-collapse: collapse; margin: 12px 0; }
        .meta td { padding: 4px 6px; vertical-align: top; }
        .meta td.label { width: 28%; color: #444; }
        .badge { display: inline-block; padding: 2px 6px; border: 1px solid #1b873b; color: #1b873b; font-weight: bold; border-radius: 3px; font-size: 11px; }
        .box { border: 1px solid #ddd; padding: 8px; border-radius: 4px; background: #fafafa; }
        table.items { width: 100%; border-collapse: collapse; margin-top: 8px; }
        table.items th, table.items td { border: 1px solid #ccc; padding: 6px; }
        table.items th { background: #f0f0f0; }
        .totals { text-align: right; margin-top: 8px; font-weight: bold; }
        .footer { margin-top: 18px; font-size: 11px; color: #555; }
        .sign { margin-top: 36px; }
        .sign .col { width: 45%; text-align: center; display: inline-block; }
        .muted { color: #666; }
        .notes h4 { margin: 6px 0 4px; font-size: 12px; }
    </style>
    @php
        function rupiah($n){ return 'Rp '.number_format((float)$n, 2, ',', '.'); }
    @endphp
</head>
<body>
    <div class="header">
        <h1>Bukti Persetujuan Proposal</h1>
        <div class="sub">Dicetak pada {{ $printedAt->format('Y-m-d H:i') }}</div>
        <div class="badge">Status: DISETUJUI</div>
    </div>

    <table class="meta">
        <tr>
            <td class="label">Kode Usulan</td><td>: {{ $proposal->kode_usulan }}</td>
            <td class="label">Judul</td><td>: {{ $proposal->judul }}</td>
        </tr>
        <tr>
            <td class="label">Satuan</td><td>: {{ $proposal->satuan->nama ?? '-' }}</td>
            <td class="label">Perencana</td><td>: {{ $proposal->perencana->name ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Tahun</td><td>: {{ $proposal->tahun }}</td>
            <td class="label">Tanggal Pengajuan</td><td>: {{ optional($proposal->tanggal_pengajuan)->format('Y-m-d') }}</td>
        </tr>
        <tr>
            <td class="label">Total Rencana</td><td colspan="3">: {{ rupiah($proposal->total_rencana) }}</td>
        </tr>
    </table>

    {{-- <div class="notes">
        <h4>Catatan Verifikator</h4>
        <div class="box">{!! nl2br(e($proposal->catatan_verifikator ?: '-')) !!}</div>
        <h4>Catatan Pimpinan</h4>
        <div class="box">{!! nl2br(e($proposal->catatan_pimpinan ?: '-')) !!}</div>
    </div> --}}

    <table class="items">
        <thead>
            <tr>
                <th style="width:45%">Uraian</th>
                <th style="width:10%">Qty</th>
                <th style="width:15%">Satuan</th>
                <th style="width:15%">Harga Satuan</th>
                <th style="width:15%">Subtotal</th>
            </tr>
        </thead>
        <tbody>
        @forelse($proposal->items as $it)
            <tr>
                <td>{{ $it->uraian }}</td>
                <td style="text-align:right">{{ number_format((float)$it->qty, 2, ',', '.') }}</td>
                <td>{{ $it->satuan }}</td>
                <td style="text-align:right">{{ rupiah($it->harga_satuan) }}</td>
                <td style="text-align:right">{{ rupiah($it->subtotal) }}</td>
            </tr>
        @empty
            <tr><td colspan="5" class="muted">Tidak ada item</td></tr>
        @endforelse
        </tbody>
    </table>
    <div class="totals">Total: {{ rupiah($proposal->total_rencana) }}</div>

    <div class="footer muted">
        Dokumen ini dihasilkan secara otomatis dari sistem sebagai bukti bahwa proposal telah disetujui.
    </div>

    <div class="sign">
        <div class="col">
            <div>Perencana</div>
            <div style="height:60px"></div>
            <div><strong>{{ $proposal->perencana->name ?? '-' }}</strong></div>
        </div>
        <div class="col">
            <div>Mengetahui, <br> Pimpinan</div>
            <div style="height:60px"></div>
            <div><strong>&nbsp;</strong></div>
        </div>
    </div>
</body>
</html>

