<?php

namespace App\Http\Controllers;

use App\Models\Indent;
use App\Models\Product;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class IndentController extends Controller
{
    public function checkCurrentMonth()
    {
        $indent = Indent::whereMonth('indent_date', now()->month)
            ->whereYear('indent_date', now()->year)
            ->first();

        return response()->json([
            'exists' => $indent ? true : false,
            'indent' => $indent ? [
                'id' => $indent->id,
                'indent_date' => $indent->indent_date->format('d F Y'),
                'total_items' => $indent->total_items,
            ] : null,
        ]);
    }

    public function saveAndDownload()
    {
        $items = Product::whereColumn('requirement', '>', 'stock')
            ->orderBy('product')
            ->get();

        $indent = Indent::create([
            'indent_date' => now(),
            'total_items' => $items->count(),
        ]);

        foreach ($items as $item) {
            $indent->items()->create([
                'product_id' => $item->id,
                'product_name' => $item->product,
                'code' => $item->code,
                'order_display' => $item->order_display,
            ]);
        }

        $backupDir = storage_path('backups');
        if (!File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }
        File::copy(database_path('database.sqlite'), $backupDir . '/auto-backup-' . now()->format('Y-m') . '.sqlite');

        $pdf = Pdf::loadView('reports.pdf', [
            'items' => $items,
            'generated_at' => now()->format('Y-m-d H:i:s'),
        ]);

        return $pdf->download('wound-care-order-' . now()->format('Y-m-d') . '.pdf');
    }
}
