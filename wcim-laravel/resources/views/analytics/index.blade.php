@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-6 py-6">
    @if(session('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative text-sm" x-data x-init="setTimeout(() => $el.remove(), 3000)">
                {{ session('success') }}
            </div>
        @endif

        {{-- Stats --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow p-4 border-t-4 border-slate-600">
                <h3 class="text-xs font-semibold text-gray-500 uppercase">Total Indents</h3>
                <p class="text-2xl font-bold text-slate-700">{{ $stats['total_indents'] }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-4 border-t-4 border-blue-600">
                <h3 class="text-xs font-semibold text-gray-500 uppercase">Total Items Ordered</h3>
                <p class="text-2xl font-bold text-blue-700">{{ $stats['total_items_ordered'] }}</p>
            </div>
        </div>

        {{-- Indent History --}}
        <div class="bg-white rounded-lg shadow overflow-hidden border border-blue-200">
            <table class="w-full text-sm [&_th]:border-r [&_th]:border-blue-100 [&_th:last-child]:border-r-0 [&_td]:border-r [&_td]:border-blue-100 [&_td:last-child]:border-r-0">
                <thead>
                    <tr class="bg-slate-100 border-b">
                        <th class="px-4 py-2 text-left font-semibold text-gray-600">Month</th>
                        <th class="px-4 py-2 text-left font-semibold text-gray-600">Submitted On</th>
                        <th class="px-4 py-2 text-center font-semibold text-gray-600">Items</th>
                        <th class="px-4 py-2 text-center font-semibold text-gray-600">Notes</th>
                        <th class="px-4 py-2 text-center font-semibold text-gray-600">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($indents as $monthLabel => $monthIndents)
                        @foreach($monthIndents as $indent)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium">{{ $monthLabel }}</td>
                            <td class="px-4 py-3">{{ $indent->indent_date->format('d F Y') }}</td>
                            <td class="px-4 py-3 text-center">{{ $indent->total_items }}</td>
                            <td class="px-4 py-3 text-center text-xs text-gray-500 max-w-[200px] truncate">{{ $indent->notes ?: '—' }}</td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('analytics.show', $indent) }}" class="text-blue-600 hover:text-blue-800 text-xs font-semibold">View</a>
                                    <a href="{{ route('analytics.pdf', $indent) }}" class="text-blue-600 hover:text-blue-800 text-xs font-semibold">PDF</a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-gray-500">No indents recorded yet. Generate your first indent from the dashboard.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
</div>
@endsection
