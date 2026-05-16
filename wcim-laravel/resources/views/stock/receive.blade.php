@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-6 py-6">
    @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative text-sm" x-data x-init="setTimeout(() => $el.remove(), 3000)">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded text-sm">{{ session('error') }}</div>
    @endif

    <h1 class="font-['Manrope',sans-serif] text-xl font-bold text-[#004b87] mb-1">📦 Receive Stock</h1>
    <p class="text-sm text-gray-500 mb-6">Log incoming stock deliveries.</p>

    <div class="bg-white rounded-lg shadow border border-blue-200 p-6 mb-6">
        <form action="{{ route('stock.receive.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Product</label>
                    <select name="product_id" required class="w-full border rounded px-3 py-2 text-sm">
                        <option value="">Select product...</option>
                        @foreach($products as $p)
                        <option value="{{ $p->id }}">{{ $p->product }} ({{ $p->unit_display }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Quantity</label>
                    <div class="flex gap-2">
                        <input type="number" name="quantity" required min="1" class="flex-1 border rounded px-3 py-2 text-sm">
                        <select name="input_mode" class="w-20 border rounded px-2 py-2 text-sm">
                            <option value="unit">Unit</option>
                            <option value="box">Box</option>
                        </select>
                    </div>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Notes (optional)</label>
                    <input type="text" name="notes" placeholder="e.g. Supplier delivery, order reference..." class="w-full border rounded px-3 py-2 text-sm">
                </div>
            </div>
            <button type="submit" class="mt-4 bg-[#004b87] hover:bg-[#003461] text-white font-semibold px-6 py-2 rounded text-sm transition">Receive Stock</button>
        </form>
    </div>

    {{-- Recent Receives --}}
    <div class="bg-white rounded-lg shadow border border-blue-200 p-5">
        <h2 class="text-sm font-semibold text-gray-700 mb-3">Recent Receives</h2>
        @if($recentLogs->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b">
                        <th class="text-left py-2 px-2 font-semibold text-gray-500 text-xs">Date</th>
                        <th class="text-left py-2 px-2 font-semibold text-gray-500 text-xs">Product</th>
                        <th class="text-right py-2 px-2 font-semibold text-gray-500 text-xs">Qty</th>
                        <th class="text-left py-2 px-2 font-semibold text-gray-500 text-xs">Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentLogs as $log)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="py-2 px-2 text-xs">{{ $log->created_at->format('d M, h:i A') }}</td>
                        <td class="py-2 px-2 text-xs font-medium">{{ $log->product->product ?? '-' }}</td>
                        <td class="py-2 px-2 text-xs text-right font-semibold text-green-600">+{{ $log->change }}</td>
                        <td class="py-2 px-2 text-xs text-gray-500">{{ $log->note ?: '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <p class="text-gray-500 text-sm text-center py-4">No stock received yet.</p>
        @endif
    </div>
</div>
@endsection
