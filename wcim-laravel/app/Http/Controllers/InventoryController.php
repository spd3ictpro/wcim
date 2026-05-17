<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\UsageLog;
use App\Models\Indent;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('product', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        if ($request->filled('filter')) {
            if ($request->filter === 'needs_order') {
                $query->whereColumn('requirement', '>', 'stock');
            } elseif ($request->filter === 'expiring_soon') {
                $query->whereBetween('expiry', [now(), now()->addDays(90)]);
            } elseif ($request->filter !== 'all') {
                $query->where('category', $request->filter);
            }
        }

        $products = $query->orderBy('product')->get();
        $categories = Product::distinct()->pluck('category');

        $stats = [
            'total' => Product::count(),
            'needs_order' => Product::whereColumn('requirement', '>', 'stock')->count(),
            'default_input_mode' => Product::value('input_mode') ?? 'unit',
        ];

        $currentMonthIndent = Indent::whereMonth('indent_date', now()->month)
            ->whereYear('indent_date', now()->year)
            ->first();

        return view('dashboard', compact('products', 'categories', 'stats', 'currentMonthIndent'));
    }

    public function show(Product $product)
    {
        return response()->json([
            'id' => $product->id,
            'category' => $product->category,
            'type' => $product->type,
            'product' => $product->product,
            'brand' => $product->brand,
            'size' => $product->size,
            'price' => $product->price,
            'unit' => $product->unit,
            'cost_per_unit' => $product->cost_per_unit,
            'product_id' => $product->product_id,
            'code' => $product->code,
            'requirement' => $product->requirement,
            'stock' => $product->stock,
            'expiry' => $product->expiry?->format('Y-m-d') ?? '',
            'image_url' => $product->image ? asset('storage/' . $product->image) : null,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category' => 'required|string',
            'type' => 'required|string',
            'product' => 'required|string',
            'brand' => 'required|string',
            'size' => 'required|string',
            'price' => 'nullable|numeric',
            'unit' => 'required|string',
            'cost_per_unit' => 'nullable|numeric',
            'product_id' => 'nullable|string',
            'code' => 'required|string',
            'requirement' => 'nullable|integer',
            'stock' => 'nullable|integer',
            'expiry' => 'nullable|date',
        ]);

        Product::create([
            ...$validated,
            'price' => $validated['price'] ?? 0,
            'cost_per_unit' => $validated['cost_per_unit'] ?? 0,
            'requirement' => $validated['requirement'] ?? 0,
            'stock' => $validated['stock'] ?? 0,
        ]);

        return redirect()->back()->with('success', 'Product added successfully.');
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'field' => 'required|string',
            'value' => 'required',
        ]);

        $oldStock = $product->stock;
        $field = $validated['field'];

        if (in_array($field, ['requirement', 'stock'])) {
            $value = (int) $validated['value'];
            $product->$field = $value;

            if ($field === 'stock' && $oldStock !== $value) {
                $change = $value - $oldStock;
                UsageLog::create([
                    'product_id' => $product->id,
                    'old_stock' => $oldStock,
                    'new_stock' => $value,
                    'change' => $change,
                    'type' => $change < 0 ? 'consumed' : 'adjusted',
                ]);
            }
        } else {
            $product->$field = $validated['value'];
        }

        $product->save();

        return response()->json(['success' => true]);
    }

    public function updateDetails(Request $request, Product $product)
    {
        $validated = $request->validate([
            'category' => 'required|string',
            'type' => 'required|string',
            'product' => 'required|string',
            'brand' => 'required|string',
            'size' => 'required|string',
            'price' => 'nullable|numeric',
            'unit' => 'required|string',
            'cost_per_unit' => 'nullable|numeric',
            'product_id' => 'nullable|string',
            'code' => 'required|string',
            'requirement' => 'nullable|integer',
            'stock' => 'nullable|integer',
            'expiry' => 'nullable|date',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $path = $request->file('image')->store('products', 'public');
            $validated['image'] = $path;
        }

        $validated['price'] = $validated['price'] ?? 0;
        $validated['cost_per_unit'] = $validated['cost_per_unit'] ?? 0;
        $validated['requirement'] = $validated['requirement'] ?? 0;
        $validated['stock'] = $validated['stock'] ?? 0;

        $product->update($validated);

        return redirect()->back()->with('success', 'Product updated successfully.');
    }

    public function updateInputMode(Request $request, Product $product)
    {
        $validated = $request->validate([
            'input_mode' => 'required|in:unit,box',
            'stock_value' => 'required|numeric',
        ]);

        $oldStock = $product->stock;
        $perBox = $product->per_box;

        if ($validated['input_mode'] === 'box' && $perBox) {
            $newStock = (int) ($validated['stock_value'] * $perBox);
        } else {
            $newStock = (int) $validated['stock_value'];
        }

        $product->input_mode = $validated['input_mode'];
        $product->stock = $newStock;
        $product->save();

        if ($oldStock !== $newStock) {
            UsageLog::create([
                'product_id' => $product->id,
                'old_stock' => $oldStock,
                'new_stock' => $newStock,
                'change' => $newStock - $oldStock,
                'type' => 'adjusted',
            ]);
        }

        return response()->json(['success' => true, 'stock' => $newStock]);
    }

    public function batchUpdateInputMode(Request $request)
    {
        $validated = $request->validate([
            'input_mode' => 'required|in:unit,box',
        ]);

        Product::query()->update(['input_mode' => $validated['input_mode']]);

        return response()->json(['success' => true]);
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->back()->with('success', 'Product deleted.');
    }

    public function deleteImage(Product $product)
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        $product->image = null;
        $product->save();

        return response()->json(['success' => true]);
    }

    public function history(Product $product)
    {
        $logs = $product->usageLogs()->latest()->get();
        return view('partials.history', compact('product', 'logs'))->render();
    }

    public function previewIndent()
    {
        $items = Product::whereColumn('requirement', '>', 'stock')
            ->orderBy('category')
            ->get();

        return view('partials.indent-preview', compact('items'));
    }

    public function generatePdf()
    {
        $items = Product::whereColumn('requirement', '>', 'stock')
            ->orderBy('category')
            ->get(['product_id', 'code', 'requirement', 'stock', 'unit']);

        $pdf = Pdf::loadView('reports.pdf', [
            'items' => $items,
            'generated_at' => now()->format('Y-m-d H:i:s'),
        ]);

        return $pdf->download('wound-care-order-' . now()->format('Y-m-d') . '.pdf');
    }

}
