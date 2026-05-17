@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-6 py-6" x-data="receiveStock()">
    @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative text-sm" x-data x-init="setTimeout(() => $el.remove(), 3000)">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded text-sm">{{ session('error') }}</div>
    @endif

    <h1 class="font-['Manrope',sans-serif] text-xl font-bold text-[#004b87] mb-1">📦 Receive Stock</h1>
    <p class="text-sm text-gray-500 mb-6">Log incoming stock deliveries.</p>

    {{-- Add Form --}}
    <div class="bg-white rounded-lg shadow border border-blue-200 p-6 mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Product</label>
                <select x-model="product_id" id="product-select" class="w-full border rounded px-3 py-2 text-sm">
                    <option value="">Select product...</option>
                    @foreach($products as $p)
                    <option value="{{ $p->id }}">{{ $p->product }} ({{ $p->unit_display }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Quantity</label>
                <div class="flex gap-2">
                    <input type="number" x-model.number="quantity" min="1" class="flex-1 border rounded px-3 py-2 text-sm">
                    <select x-model="input_mode" class="w-20 border rounded px-2 py-2 text-sm">
                        <option value="unit">Unit</option>
                        <option value="box">Box</option>
                    </select>
                </div>
            </div>
            <div class="sm:col-span-2">
                <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Notes (optional)</label>
                <input type="text" x-model="notes" placeholder="e.g. Supplier delivery, order reference..." class="w-full border rounded px-3 py-2 text-sm">
            </div>
        </div>
        <button @click="addItem()" type="button" class="mt-4 bg-[#004b87] hover:bg-[#003461] text-white font-semibold px-6 py-2 rounded text-sm transition">Add to List</button>
    </div>

    {{-- Pending Items --}}
    <div class="bg-white rounded-lg shadow border border-blue-200 p-5 mb-6" x-show="pendingItems.length > 0" x-cloak>
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-sm font-semibold text-gray-700">Pending Items (<span x-text="pendingItems.length"></span>)</h2>
            <form id="receive-form" action="{{ route('stock.receive.store') }}" method="POST">
                @csrf
                <button @click="submitAll()" type="button" class="bg-green-600 hover:bg-green-700 text-white font-semibold px-5 py-2 rounded text-sm transition">Receive All</button>
            </form>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b">
                        <th class="text-left py-2 px-2 font-semibold text-gray-500 text-xs">#</th>
                        <th class="text-left py-2 px-2 font-semibold text-gray-500 text-xs">Product</th>
                        <th class="text-right py-2 px-2 font-semibold text-gray-500 text-xs">Qty</th>
                        <th class="text-left py-2 px-2 font-semibold text-gray-500 text-xs">Mode</th>
                        <th class="text-left py-2 px-2 font-semibold text-gray-500 text-xs">Notes</th>
                        <th class="py-2 px-2"></th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(item, index) in pendingItems" :key="index">
                        <tr class="border-b hover:bg-gray-50">
                            <td class="py-2 px-2 text-xs text-gray-400" x-text="index + 1"></td>
                            <td class="py-2 px-2 text-xs font-medium" x-text="item.product_name"></td>
                            <td class="py-2 px-2 text-xs text-right font-semibold text-green-600" x-text="'+' + item.quantity"></td>
                            <td class="py-2 px-2 text-xs capitalize" x-text="item.input_mode"></td>
                            <td class="py-2 px-2 text-xs text-gray-500" x-text="item.notes || '-'"></td>
                            <td class="py-2 px-2 text-right">
                                <button @click="removeItem(index)" type="button" class="text-red-500 hover:text-red-700 text-xs font-semibold">Remove</button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
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

<script>
function receiveStock() {
    return {
        pendingItems: [],
        product_id: '',
        quantity: 1,
        input_mode: 'unit',
        notes: '',
        addItem() {
            if (!this.product_id || this.quantity < 1) return;
            const select = document.getElementById('product-select');
            const selected = select.options[select.selectedIndex];
            this.pendingItems.push({
                product_id: this.product_id,
                product_name: selected.textContent,
                quantity: this.quantity,
                input_mode: this.input_mode,
                notes: this.notes,
            });
            this.product_id = '';
            this.quantity = 1;
            this.input_mode = 'unit';
            this.notes = '';
        },
        removeItem(index) {
            this.pendingItems.splice(index, 1);
        },
        submitAll() {
            if (this.pendingItems.length === 0) return;
            const form = document.getElementById('receive-form');
            form.querySelectorAll('.dynamic-item').forEach(el => el.remove());
            this.pendingItems.forEach((item, i) => {
                ['product_id', 'quantity', 'input_mode', 'notes'].forEach(field => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = `items[${i}][${field}]`;
                    input.value = item[field];
                    input.className = 'dynamic-item';
                    form.appendChild(input);
                });
            });
            form.submit();
        }
    };
}
</script>
@endsection
