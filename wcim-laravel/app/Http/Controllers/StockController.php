<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\UsageLog;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function index()
    {
        $products = Product::orderBy('product')->get();
        $recentLogs = UsageLog::where('type', 'received')
            ->with('product')
            ->latest()
            ->take(10)
            ->get();

        return view('stock.receive', compact('products', 'recentLogs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.input_mode' => 'required|in:unit,box',
            'items.*.notes' => 'nullable|string|max:500',
        ]);

        $count = 0;
        foreach ($validated['items'] as $item) {
            $product = Product::findOrFail($item['product_id']);
            $perBox = $product->per_box;
            $qty = (int) $item['quantity'];

            if ($item['input_mode'] === 'box' && $perBox) {
                $units = $qty * $perBox;
            } else {
                $units = $qty;
            }

            $oldStock = $product->stock;
            $product->stock += $units;
            $product->save();

            UsageLog::create([
                'product_id' => $product->id,
                'old_stock' => $oldStock,
                'new_stock' => $product->stock,
                'change' => $units,
                'note' => $item['notes'] ?? '',
                'type' => 'received',
            ]);

            $count++;
        }

        return redirect()->route('stock.receive')->with('success', "Successfully received {$count} item(s).");
    }
}
