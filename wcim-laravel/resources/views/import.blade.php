@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto px-6 py-6">
    @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative text-sm" x-data x-init="setTimeout(() => $el.remove(), 3000)">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded text-sm">{{ session('error') }}</div>
    @endif
    @if(session('import_errors'))
        <div class="mb-4 bg-amber-50 border border-amber-200 text-amber-800 px-4 py-3 rounded text-sm">
            <p class="font-semibold mb-1">Skipped rows:</p>
            <ul class="list-disc list-inside">
                @foreach(session('import_errors') as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <h1 class="font-['Manrope',sans-serif] text-xl font-bold text-[#004b87] mb-1">📥 Import Products from CSV</h1>
    <p class="text-sm text-gray-500 mb-6">Bulk-import products into inventory. Download the sample template to get started.</p>

    <div class="bg-white rounded-lg shadow border border-blue-200 p-6 mb-6">
        <form action="{{ route('import.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-4">
                <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">Upload CSV File</label>
                <input type="file" name="csv_file" accept=".csv" required
                    class="w-full border rounded px-3 py-2 text-sm file:mr-3 file:py-1.5 file:px-3 file:border-0 file:rounded file:text-xs file:font-semibold file:bg-[#004b87] file:text-white hover:file:bg-[#003461]">
                <p class="text-xs text-gray-400 mt-1.5">Max 2MB. CSV files only.</p>
            </div>

            <div class="bg-blue-50 border border-blue-100 rounded p-4 mb-4 text-sm text-blue-800">
                <p class="font-semibold mb-1">Required columns:</p>
                <code class="text-xs bg-white px-2 py-1 rounded border border-blue-200">category, type, product, brand, size, unit, code</code>
                <p class="font-semibold mt-2 mb-1">Optional columns:</p>
                <code class="text-xs bg-white px-2 py-1 rounded border border-blue-200">price, cost_per_unit, product_id, requirement, stock, expiry</code>
                <p class="text-xs mt-2">Headers are matched by name — column order does not matter.</p>
            </div>

            <div class="flex items-center gap-3">
                <button type="submit" class="bg-[#004b87] hover:bg-[#003461] text-white font-semibold px-6 py-2 rounded text-sm transition">Import</button>
                <a href="{{ route('import.sample') }}" class="text-sm text-blue-600 hover:text-blue-800 underline">Download sample CSV</a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow border border-blue-200 p-5">
        <h2 class="text-sm font-semibold text-gray-700 mb-2">Tips</h2>
        <ul class="text-xs text-gray-500 space-y-1 list-disc list-inside">
            <li>Use the sample CSV as a template — it contains realistic wound care products. Replace them with your own data.</li>
            <li>Rows missing any required column will be skipped and reported.</li>
            <li>Duplicate codes are allowed, but you can edit them after import.</li>
            <li>Date format for expiry: <code class="bg-gray-100 px-1 rounded">YYYY-MM-DD</code> (e.g. 2026-12-31).</li>
        </ul>
    </div>
</div>
@endsection
