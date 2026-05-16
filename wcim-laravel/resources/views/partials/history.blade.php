@if($logs->count() > 0)
<table class="w-full text-sm">
    <thead>
        <tr class="border-b">
            <th class="text-left py-2 px-2 font-semibold text-gray-600">Date</th>
            <th class="text-right py-2 px-2 font-semibold text-gray-600">Old Stock</th>
            <th class="text-right py-2 px-2 font-semibold text-gray-600">New Stock</th>
            <th class="text-right py-2 px-2 font-semibold text-gray-600">Change</th>
        </tr>
    </thead>
    <tbody>
        @foreach($logs as $log)
        <tr class="border-b">
            <td class="py-1.5 px-2">{{ $log->created_at->format('d/m/Y H:i') }}</td>
            <td class="py-1.5 px-2 text-right">{{ $log->old_stock }}</td>
            <td class="py-1.5 px-2 text-right">{{ $log->new_stock }}</td>
            <td class="py-1.5 px-2 text-right font-semibold {{ $log->change > 0 ? 'text-green-600' : ($log->change < 0 ? 'text-red-600' : 'text-gray-500') }}">
                {{ $log->change > 0 ? '+' : '' }}{{ $log->change }}
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@else
<p class="text-gray-500 text-sm text-center py-4">No stock changes recorded yet.</p>
@endif
