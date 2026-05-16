@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto px-6 py-6">
    @if(session('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative text-sm" x-data x-init="setTimeout(() => $el.remove(), 3000)">
                {{ session('success') }}
            </div>
        @endif

        {{-- Indent Items --}}
        <div class="bg-white rounded-lg shadow overflow-hidden border border-blue-200 mb-6">
            <table class="w-full text-sm [&_th]:border-r [&_th]:border-blue-100 [&_th:last-child]:border-r-0 [&_td]:border-r [&_td]:border-blue-100 [&_td:last-child]:border-r-0">
                <thead>
                    <tr class="bg-slate-100 border-b">
                        <th class="px-4 py-2 text-left font-semibold text-gray-600">Product</th>
                        <th class="px-4 py-2 text-left font-semibold text-gray-600">Code</th>
                        <th class="px-4 py-2 text-center font-semibold text-gray-600">Order</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($indent->items as $item)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-2.5">{{ $item->product_name }}</td>
                        <td class="px-4 py-2.5 font-mono text-xs">{{ $item->code }}</td>
                        <td class="px-4 py-2.5 text-center font-semibold text-red-600">{{ $item->order_display }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-4 py-8 text-center text-gray-500">No items in this indent.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Notes --}}
            <div class="bg-white rounded-lg shadow p-5 border border-blue-200">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">Notes</h3>
                <form action="{{ route('analytics.update-notes', $indent) }}" method="POST">
                    @csrf @method('PATCH')
                    <textarea name="notes" rows="3" class="w-full border rounded px-3 py-2 text-sm resize-none">{{ $indent->notes }}</textarea>
                    <button type="submit" class="mt-2 bg-[#004b87] hover:bg-[#003461] text-white text-xs font-semibold px-4 py-1.5 rounded transition">Save Notes</button>
                </form>
            </div>

            {{-- Actions --}}
            <div class="bg-white rounded-lg shadow p-5 border border-blue-200">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">Actions</h3>
                <a href="{{ route('analytics.pdf', $indent) }}" class="inline-block bg-[#004b87] hover:bg-[#003461] text-white text-sm font-semibold px-4 py-2 rounded transition">📄 Re-download PDF</a>
            </div>
        </div>
</div>
@endsection
