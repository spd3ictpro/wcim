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
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:1',
            'input_mode' => 'required|in:unit,box',
            'notes' => 'nullable|string|max:500',
        ]);

        $product = Product::findOrFail($validated['product_id']);
        $perBox = $product->per_box;
        $qty = (int) $validated['quantity'];

        if ($validated['input_mode'] === 'box' && $perBox) {
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
            'note' => $validated['notes'] ?? '',
            'type' => 'received',
        ]);

        return redirect()->route('stock.receive')->with('success', "Received {$qty} {$validated['input_mode']}(s) of {$product->product}.");
    }
}
