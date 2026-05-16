<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #333; }
        h1 { font-size: 16px; margin-bottom: 4px; color: #1e293b; }
        .subtitle { font-size: 10px; color: #64748b; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background: #f1f5f9; padding: 8px 10px; text-align: left; border-bottom: 2px solid #cbd5e1; font-size: 10px; text-transform: uppercase; color: #475569; }
        td { padding: 6px 10px; border-bottom: 1px solid #e2e8f0; }
        tr:nth-child(even) { background: #f8fafc; }
        .footer { margin-top: 30px; font-size: 9px; color: #94a3b8; text-align: center; border-top: 1px solid #e2e8f0; padding-top: 10px; }
        .no-items { text-align: center; color: #94a3b8; padding: 30px 0; }
        .sig-line { border-bottom: 1px solid #333; height: 36px; margin-bottom: 4px; }
        .sig-label { font-size: 9px; color: #64748b; }
    </style>
</head>
<body>
    <h1>Wound Care Order Report</h1>
    <p class="subtitle">Generated: {{ $generated_at }}</p>

    @if($items->count() > 0)
    <table>
        <thead>
            <tr>
                <th style="width: 50%;">Product ID</th>
                <th style="width: 25%;">Code</th>
                <th style="width: 25%; text-align: right;">Order Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
            <tr>
                <td>{{ $item->product_id ?: '-' }}</td>
                <td>{{ $item->code }}</td>
                <td style="text-align: right; font-weight: bold;">
                    @php
                        $diff = $item->requirement - $item->stock;
                        if (preg_match('/(\d+)\/BOX/i', $item->unit, $m)) {
                            $boxes = (int) ceil($diff / $m[1]);
                            echo $boxes . ' boxes';
                        } else {
                            echo $diff . ' units';
                        }
                    @endphp
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <p class="no-items">No items require ordering at this time.</p>
    @endif

    <table style="width: 100%; margin-top: 30px;">
        <tr>
            <td style="width: 50%; padding-right: 15px;">
                <p style="font-size: 10px; color: #475569; margin-bottom: 4px; font-weight: bold; text-transform: uppercase;">Prepared By</p>
                <div class="sig-line"></div>
                <table style="width: 100%; margin: 0;"><tr>
                    <td style="border: none; padding: 0; font-size: 9px; color: #94a3b8;">Name &amp; Signature</td>
                    <td style="border: none; padding: 0; font-size: 9px; color: #94a3b8; text-align: right;">Date: ________</td>
                </tr></table>
            </td>
            <td style="width: 50%; padding-left: 15px;">
                <p style="font-size: 10px; color: #475569; margin-bottom: 4px; font-weight: bold; text-transform: uppercase;">Approved By</p>
                <div class="sig-line"></div>
                <table style="width: 100%; margin: 0;"><tr>
                    <td style="border: none; padding: 0; font-size: 9px; color: #94a3b8;">Name &amp; Signature</td>
                    <td style="border: none; padding: 0; font-size: 9px; color: #94a3b8; text-align: right;">Date: ________</td>
                </tr></table>
            </td>
        </tr>
    </table>

    <div class="footer">
        Wound Care Inventory Management System
    </div>
</body>
</html>
