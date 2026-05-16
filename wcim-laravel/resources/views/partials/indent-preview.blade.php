@if($items->count() > 0)
<table class="w-full text-sm">
    <thead>
        <tr class="border-b">
            <th class="text-left py-2 px-2 font-semibold text-gray-600">Product</th>
            <th class="text-left py-2 px-2 font-semibold text-gray-600">Product ID</th>
            <th class="text-left py-2 px-2 font-semibold text-gray-600">Code</th>
            <th class="text-right py-2 px-2 font-semibold text-gray-600">Stock</th>
            <th class="text-right py-2 px-2 font-semibold text-gray-600">Requirement</th>
            <th class="text-right py-2 px-2 font-semibold text-red-600">Order</th>
        </tr>
    </thead>
    <tbody>
        @foreach($items as $item)
        <tr class="border-b">
            <td class="py-1.5 px-2">{{ $item->product }} ({{ $item->size }})</td>
            <td class="py-1.5 px-2 text-xs">{{ $item->product_id }}</td>
            <td class="py-1.5 px-2">{{ $item->code }}</td>
            <td class="py-1.5 px-2 text-right">{{ $item->stock }}</td>
            <td class="py-1.5 px-2 text-right">{{ $item->requirement }}</td>
            <td class="py-1.5 px-2 text-right font-semibold text-red-600">{{ $item->order_display }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
<p class="text-xs text-gray-500 mt-2">{{ $items->count() }} item(s) to order</p>
@else
<p class="text-gray-500 text-sm text-center py-4">No items require ordering at this time.</p>
@endif
