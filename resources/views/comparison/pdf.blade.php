<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Perbandingan Harga</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            color: #14151d;
        }
        .header {
            border-bottom: 2px solid #16a34a;
            padding-bottom: 10px;
            margin-bottom: 16px;
        }
        .header h1 {
            font-size: 18px;
            margin: 0 0 4px 0;
            color: #14151d;
        }
        .header p {
            margin: 2px 0;
            color: #6b6d7a;
            font-size: 10px;
        }
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 22px;
        }
        .summary-table th, .summary-table td {
            border: 1px solid #e4e4e9;
            padding: 6px 8px;
            font-size: 10.5px;
            text-align: left;
        }
        .summary-table th {
            background-color: #f4f5f7;
            font-weight: bold;
        }
        .badge-cheapest {
            background-color: #dcfce7;
            color: #15803d;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
        }
        .section-title {
            font-size: 13px;
            font-weight: bold;
            margin: 18px 0 8px 0;
            padding: 6px 8px;
            background-color: #f4f5f7;
            border-left: 3px solid #16a34a;
        }
        table.products {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        table.products th, table.products td {
            border: 1px solid #e4e4e9;
            padding: 5px 7px;
            font-size: 9.5px;
            text-align: left;
            vertical-align: top;
        }
        table.products th {
            background-color: #fafafa;
            font-weight: bold;
        }
        table.products td.price {
            font-weight: bold;
            white-space: nowrap;
        }
        table.products td.no {
            width: 24px;
            text-align: center;
        }
        .footer-note {
            margin-top: 18px;
            font-size: 9px;
            color: #9a9ba5;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Perbandingan Harga — ScrapeToko</h1>
        <p>Kata kunci pencarian: <strong>{{ $keyword }}</strong></p>
        <p>Dibuat oleh {{ $generated_by }} pada {{ $generated_at->translatedFormat('d F Y, H:i') }} WIB</p>
    </div>

    <table class="summary-table">
        <thead>
            <tr>
                <th>Toko</th>
                <th>Jenis</th>
                <th>Jumlah Produk</th>
                <th>Harga Termurah</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $shop1_data['shop_name'] }}</td>
                <td>Dikelola</td>
                <td>{{ count($shop1_data['products']) }}</td>
                <td>{{ $shop1_cheapest !== null ? 'Rp '.number_format($shop1_cheapest, 0, ',', '.') : '-' }}</td>
                <td>
                    @if ($shop1_cheapest !== null && $shop2_cheapest !== null && $shop1_cheapest < $shop2_cheapest)
                        <span class="badge-cheapest">Termurah</span>
                    @endif
                </td>
            </tr>
            <tr>
                <td>{{ $shop2_data['shop_name'] }}</td>
                <td>Saingan</td>
                <td>{{ count($shop2_data['products']) }}</td>
                <td>{{ $shop2_cheapest !== null ? 'Rp '.number_format($shop2_cheapest, 0, ',', '.') : '-' }}</td>
                <td>
                    @if ($shop1_cheapest !== null && $shop2_cheapest !== null && $shop2_cheapest < $shop1_cheapest)
                        <span class="badge-cheapest">Termurah</span>
                    @endif
                </td>
            </tr>
        </tbody>
    </table>

    @if ($shop1_cheapest !== null && $shop2_cheapest !== null && $shop1_cheapest !== $shop2_cheapest)
        @php
            $selisih = abs($shop1_cheapest - $shop2_cheapest);
            $lebihMurah = $shop1_cheapest < $shop2_cheapest ? $shop1_data['shop_name'] : $shop2_data['shop_name'];
        @endphp
        <p style="margin-bottom: 16px;">
            Toko <strong>{{ $lebihMurah }}</strong> menjual dengan harga termurah lebih rendah
            <strong>Rp {{ number_format($selisih, 0, ',', '.') }}</strong> dibanding toko lainnya.
        </p>
    @endif

    <div class="section-title">{{ $shop1_data['shop_name'] }} (Toko Dikelola)</div>
    <table class="products">
        <thead>
            <tr>
                <th class="no">No</th>
                <th>Judul Produk</th>
                <th>Harga</th>
                <th>Rating</th>
                <th>Terjual</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($shop1_data['products'] as $i => $product)
                <tr>
                    <td class="no">{{ $i + 1 }}</td>
                    <td>{{ $product['title'] }}</td>
                    <td class="price">{{ $product['price'] }}</td>
                    <td>{{ $product['rating'] ?? '-' }}</td>
                    <td>{{ $product['sold'] ?? '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="5">Tidak ada produk ditemukan.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="section-title">{{ $shop2_data['shop_name'] }} (Toko Saingan)</div>
    <table class="products">
        <thead>
            <tr>
                <th class="no">No</th>
                <th>Judul Produk</th>
                <th>Harga</th>
                <th>Rating</th>
                <th>Terjual</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($shop2_data['products'] as $i => $product)
                <tr>
                    <td class="no">{{ $i + 1 }}</td>
                    <td>{{ $product['title'] }}</td>
                    <td class="price">{{ $product['price'] }}</td>
                    <td>{{ $product['rating'] ?? '-' }}</td>
                    <td>{{ $product['sold'] ?? '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="5">Tidak ada produk ditemukan.</td></tr>
            @endforelse
        </tbody>
    </table>

    <p class="footer-note">Laporan ini dibuat otomatis oleh sistem ScrapeToko berdasarkan data yang berhasil diambil dari Tokopedia pada saat laporan dibuat.</p>
</body>
</html>
