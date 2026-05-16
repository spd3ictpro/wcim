@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-6 py-6">
    @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative text-sm" x-data x-init="setTimeout(() => $el.remove(), 3000)">{{ session('success') }}</div>
    @endif

    <h1 class="font-['Manrope',sans-serif] text-xl font-bold text-[#004b87] mb-1">📊 Consumption Report</h1>
    <p class="text-sm text-gray-500 mb-6">Monthly product usage with forecast.</p>

    <div class="bg-white rounded-lg shadow border border-blue-200 overflow-x-auto">
        <table class="w-full text-sm [&_th]:border-r [&_th]:border-blue-100 [&_th:last-child]:border-r-0 [&_td]:border-r [&_td]:border-blue-100 [&_td:last-child]:border-r-0">
            <thead>
                <tr class="bg-slate-100 border-b">
                    <th class="px-3 py-2 text-left font-semibold text-gray-600">Product</th>
                    <th class="px-3 py-2 text-left font-semibold text-gray-600">Code</th>
                    @foreach($monthLabels as $label)
                    <th class="px-3 py-2 text-center font-semibold text-gray-600">{{ $label }}</th>
                    @endforeach
                    <th class="px-3 py-2 text-center font-semibold text-blue-700 bg-blue-50/50">Forecast</th>
                    <th class="px-3 py-2 text-center font-semibold text-gray-600 w-20">Apply</th>
                </tr>
            </thead>
            <tbody>
                @forelse($report as $row)
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-3 py-2.5 font-medium">{{ $row->product }}</td>
                    <td class="px-3 py-2.5 font-mono text-xs">{{ $row->code }}</td>
                    @foreach($monthLabels as $label)
                    <td class="px-3 py-2.5 text-center">{{ $row->months[$label] ?? 0 }}</td>
                    @endforeach
                    <td class="px-3 py-2.5 text-center font-semibold text-blue-700 bg-blue-50/50">{{ $row->forecast_label }}</td>
                    <td class="px-3 py-2.5 text-center">
                        @if($row->forecast_units > 0)
                        <form action="{{ route('analytics.apply-forecast', ['product' => $row->id, 'units' => $row->forecast_units]) }}" method="POST">
                            @csrf
                            <button type="submit" class="text-[#004b87] hover:text-[#003461] text-xs font-semibold underline">→ Req</button>
                        </form>
                        @else
                        <span class="text-gray-400 text-xs">—</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="{{ 4 + $monthLabels->count() }}" class="px-3 py-8 text-center text-gray-500">No consumption data yet. Start using products to see trends here.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6 bg-white rounded-lg shadow border border-blue-200 p-5">
        <h3 class="text-xs font-semibold text-gray-500 uppercase mb-2">How Forecast Works</h3>
        <p class="text-xs text-gray-600 leading-relaxed">
            Forecast is calculated as the <strong>average consumption over the last 3 months</strong>, rounded up to the nearest box/unit.
            Click <strong>"→ Req"</strong> to set this value as the product's Requirement for the next indent.
        </p>
    </div>
</div>
@endsection
