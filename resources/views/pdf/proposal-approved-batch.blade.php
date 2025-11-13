<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Proposal Anggaran Disetujui Koarmada II</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111; }
        .header { text-align: center; margin-bottom: 12px; }
        .header h1 { font-size: 18px; margin: 0 0 4px; }
        .header .sub { font-size: 12px; color: #555; }
        table.items { width: 100%; border-collapse: collapse; margin-top: 8px; }
        table.items th, table.items td { border: 1px solid #ccc; padding: 6px; }
        table.items th { background: #f0f0f0; }
        .totals { text-align: right; margin-top: 8px; font-weight: bold; }
        .muted { color: #666; }
    </style>
    @php
        function rupiah($n){ return 'Rp '.number_format((float)$n, 2, ',', '.'); }
        $grand = 0;
    @endphp
    <style>
        @page { margin: 24px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Gabungan Proposal Disetujui</h1>
        <div class="sub">Dicetak pada {{ $printedAt->format('Y-m-d H:i') }} | Total proposal: {{ $proposals->count() }}</div>
    </div>

    <table class="items">
        <thead>
            <tr>
                <th style="width:5%">No</th>
                <th style="width:18%">Satuan (Unit Kerja)</th>
                <th style="width:35%">Uraian</th>
                <th style="width:8%">Qty</th>
                <th style="width:10%">Unit</th>
                <th style="width:12%">Harga Satuan</th>
                <th style="width:12%">Subtotal</th>
            </tr>
        </thead>
        <tbody>
        @php $hasAny = false; $row = 0; @endphp
        @foreach($proposals as $proposal)
            @foreach($proposal->items as $it)
                @php $hasAny = true; $grand += (float) $it->subtotal; @endphp
                <tr>
                    <td style="text-align:center">{{ ++$row }}</td>
                    <td>{{ $proposal->satuan->nama ?? ($proposal->satuan->name ?? '-') }}</td>
                    <td>{{ $it->uraian }}</td>
                    <td style="text-align:right">{{ number_format((float)$it->qty, 2, ',', '.') }}</td>
                    <td>{{ $it->satuan }}</td>
                    <td style="text-align:right">{{ rupiah($it->harga_satuan) }}</td>
                    <td style="text-align:right">{{ rupiah($it->subtotal) }}</td>
                </tr>
            @endforeach
        @endforeach
        @if(!$hasAny)
            <tr><td colspan="6" class="muted">Tidak ada item dari proposal yang dipilih.</td></tr>
        @endif
        </tbody>
    </table>
    <div class="totals">Grand Total: {{ rupiah($grand) }}</div>
</body>
</html>
