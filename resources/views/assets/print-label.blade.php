<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Label QR Aset - {{ $asset->code }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=JetBrains+Mono:wght@600;700&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        body {
            background-color: #f3f4f6;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }
        .action-bar {
            margin-bottom: 24px;
            display: flex;
            gap: 12px;
        }
        .btn {
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 13px;
            cursor: pointer;
            border: none;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-primary {
            background-color: #2563eb;
            color: white;
        }
        .btn-secondary {
            background-color: #e5e7eb;
            color: #1f2937;
        }

        /* Printable Sticker Tag */
        .sticker-card {
            width: 380px;
            background: white;
            border: 2px solid #1e293b;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1);
        }
        .sticker-header {
            background: #1e293b;
            color: white;
            padding: 12px 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .sticker-brand {
            font-size: 15px;
            font-weight: 800;
            letter-spacing: -0.5px;
        }
        .sticker-brand span {
            color: #38bdf8;
        }
        .sticker-badge {
            background: rgba(255,255,255,0.2);
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
        }
        .sticker-body {
            padding: 16px;
            display: flex;
            gap: 16px;
            align-items: center;
        }
        .qr-wrapper {
            background: white;
            padding: 6px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .qr-wrapper svg {
            width: 120px !important;
            height: 120px !important;
        }
        .asset-info {
            flex: 1;
        }
        .asset-code {
            font-family: 'JetBrains Mono', monospace;
            font-size: 14px;
            font-weight: 700;
            color: #2563eb;
            background: #eff6ff;
            padding: 3px 6px;
            border-radius: 4px;
            display: inline-block;
            margin-bottom: 6px;
        }
        .asset-name {
            font-size: 14px;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.2;
            margin-bottom: 6px;
        }
        .asset-category {
            font-size: 11px;
            color: #64748b;
            font-weight: 600;
        }
        .asset-loc {
            font-size: 11px;
            color: #475569;
            margin-top: 4px;
        }
        .sticker-footer {
            background: #f8fafc;
            border-top: 1px dashed #cbd5e1;
            padding: 8px 16px;
            font-size: 9px;
            color: #64748b;
            text-align: center;
            font-weight: 600;
            letter-spacing: 0.2px;
        }

        @media print {
            body {
                background: none;
                padding: 0;
            }
            .action-bar {
                display: none;
            }
            .sticker-card {
                box-shadow: none;
                border: 2px solid black;
                margin: 0;
            }
        }
    </style>
</head>
<body>

    <div class="action-bar">
        <button onclick="window.print()" class="btn btn-primary">
            🖨️ Cetak Label Sekarang
        </button>
        <button onclick="window.close()" class="btn btn-secondary">
            Tutup
        </button>
    </div>

    <!-- Sticker Element -->
    <div class="sticker-card">
        <div class="sticker-header">
            <div class="sticker-brand">Asset<span>Track</span></div>
            <div class="sticker-badge">{{ $asset->category }}</div>
        </div>
        <div class="sticker-body">
            <div class="qr-wrapper">
                {!! $qrCodeSvg !!}
            </div>
            <div class="asset-info">
                <div class="asset-code">{{ $asset->code }}</div>
                <div class="asset-name">{{ $asset->name }}</div>
                <div class="asset-loc">📍 {{ $asset->location }}</div>
            </div>
        </div>
        <div class="sticker-footer">
            Pindai QR Code untuk Cek Status & Peminjaman Barang &bull; PROPERTI PERUSAHAAN
        </div>
    </div>

</body>
</html>
