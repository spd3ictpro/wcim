<?php

namespace App\Http\Controllers;

use App\Models\Indent;
use App\Models\IndentItem;
use App\Models\Product;
use App\Models\UsageLog;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function index()
    {
        $indents = Indent::with('items')
            ->orderBy('indent_date', 'desc')
            ->get()
            ->groupBy(fn($i) => $i->indent_date->format('F Y'));

        $stats = [
            'total_indents' => Indent::count(),
            'total_items_ordered' => IndentItem::count(),
        ];

        return view('analytics.index', compact('indents', 'stats'));
    }

    public function show(Indent $indent)
    {
        $indent->load('items');
        return view('analytics.show', compact('indent'));
    }

    public function updateNotes(Request $request, Indent $indent)
    {
        $validated = $request->validate([
            'notes' => 'nullable|string|max:1000',
        ]);

        $indent->update(['notes' => $validated['notes']]);

        return redirect()->back()->with('success', 'Notes updated.');
    }

    public function regeneratePdf(Indent $indent)
    {
        $indent->load('items');
        $productIds = $indent->items->pluck('product_id')->filter();

        $items = Product::whereIn('id', $productIds)->get()
            ->map(fn($p) => (object) [
                'product_id' => $p->product_id,
                'code' => $p->code,
                'requirement' => $p->requirement,
                'stock' => $p->stock,
                'unit' => $p->unit,
            ]);

        $pdf = Pdf::loadView('reports.pdf', [
            'items' => $items,
            'generated_at' => now()->format('Y-m-d H:i:s'),
        ]);

        return $pdf->download('wound-care-order-' . $indent->indent_date->format('Y-m-d') . '.pdf');
    }

    public function consumption()
    {
        $products = Product::with(['usageLogs' => function ($q) {
            $q->where('type', 'consumed')
              ->where('created_at', '>=', now()->subMonths(4));
        }])->orderBy('product')->get();

        $months = collect();
        for ($i = 3; $i >= 1; $i--) {
            $months->push(now()->subMonths($i));
        }

        $report = $products->map(function ($product) use ($months) {
            $monthlyData = $months->mapWithKeys(function ($month) use ($product) {
                $consumed = $product->usageLogs
                    ->filter(fn($log) => $log->created_at->format('Y-m') === $month->format('Y-m'))
                    ->sum(fn($log) => abs($log->change));
                return [$month->format('F Y') => $consumed];
            });

            $values = $monthlyData->values();
            $avg = $values->sum() > 0 ? $values->sum() / $values->count() : 0;
            $perBox = $product->per_box;

            if ($perBox && $avg > 0) {
                $forecastUnits = (int) ceil($avg / $perBox) * $perBox;
                $forecastLabel = ceil($avg / $perBox) . ' Box';
            } elseif ($avg > 0) {
                $forecastUnits = (int) round($avg);
                $forecastLabel = $forecastUnits . ' Units';
            } else {
                $forecastUnits = 0;
                $forecastLabel = '-';
            }

            return (object) [
                'id' => $product->id,
                'product' => $product->product,
                'code' => $product->code,
                'unit' => $product->unit_display,
                'months' => $monthlyData,
                'forecast_units' => $forecastUnits,
                'forecast_label' => $forecastLabel,
            ];
        })->filter(fn($r) => $r->months->values()->sum() > 0);

        $monthLabels = $months->map(fn($m) => $m->format('F Y'));

        return view('analytics.consumption', compact('report', 'monthLabels'));
    }

    public function applyForecast(Product $product, Request $request)
    {
        $units = $request->input('units', 0);
        if ($units > 0) {
            $product->requirement = (int) $units;
            $product->save();
        }
        return redirect()->back()->with('success', "Requirement for {$product->product} set to forecast value.");
    }
}
