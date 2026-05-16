@extends('layouts.app')

@section('content')
<div class="max-w-full px-6 py-6" x-data="inventoryApp()">
        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" x-data x-init="setTimeout(() => $el.remove(), 3000)">
                {{ session('success') }}
            </div>
        @endif

        {{-- Monthly Indent Status Banner --}}
        @php $monthLabel = now()->format('F Y'); @endphp
        @if($currentMonthIndent)
        <div class="mb-4 bg-blue-50 border border-blue-200 text-blue-800 rounded-lg px-5 py-3 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="text-lg">✅</span>
                <div>
                    <p class="font-semibold text-sm">{{ $monthLabel }} — Submitted on {{ \Carbon\Carbon::parse($currentMonthIndent->indent_date)->format('d F Y') }}</p>
                    <p class="text-xs text-blue-600">{{ $currentMonthIndent->total_items }} item(s) ordered. <a href="{{ route('analytics.show', $currentMonthIndent) }}" class="underline">View in Analytics</a></p>
                </div>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('analytics.pdf', $currentMonthIndent) }}" class="bg-[#004b87] hover:bg-[#003461] text-white text-xs font-semibold px-3 py-1.5 rounded transition">📄 Re-download PDF</a>
            </div>
        </div>
        @else
        <div class="mb-4 bg-green-50 border border-green-200 text-green-800 rounded-lg px-5 py-3">
            <div class="flex items-center gap-3">
                <span class="text-lg">📋</span>
                <div>
                    <p class="font-semibold text-sm">{{ $monthLabel }} — Indent Open</p>
                    <p class="text-xs text-green-600">{{ $stats['needs_order'] }} item(s) need ordering.</p>
                </div>
            </div>
        </div>
        @endif

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow p-4 border-t-4 border-slate-600">
                <h3 class="text-xs font-semibold text-gray-500 uppercase">Total SKU Items</h3>
                <p class="text-2xl font-bold text-slate-700">{{ $stats['total'] }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-4 border-t-4 border-red-500">
                <h3 class="text-xs font-semibold text-gray-500 uppercase">Items to Indent</h3>
                <p class="text-2xl font-bold text-red-600">{{ $stats['needs_order'] }}</p>
            </div>
        </div>

        {{-- Add New Product Button --}}
        <button @click="showAddModal = true" class="bg-[#004b87] hover:bg-[#003461] text-white font-semibold py-2 px-5 rounded text-sm transition mb-6">
            + Add New Product
        </button>

        {{-- Search, Filter & Actions --}}
        <div class="flex flex-col sm:flex-row gap-3 mb-4 items-start sm:items-center justify-between">
            <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                <form method="GET" action="{{ route('dashboard') }}" class="flex gap-2">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search products..." class="border rounded px-3 py-2 text-sm w-64">
                    <select name="filter" onchange="this.form.submit()" class="border rounded px-3 py-2 text-sm">
                        <option value="all" {{ request('filter') === 'all' || !request('filter') ? 'selected' : '' }}>All Categories</option>
                        <option value="needs_order" {{ request('filter') === 'needs_order' ? 'selected' : '' }}>Needs Order</option>
                        <option value="expiring_soon" {{ request('filter') === 'expiring_soon' ? 'selected' : '' }}>Expiring Soon</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}" {{ request('filter') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                    @if(request('search') || request('filter'))
                        <a href="{{ route('dashboard') }}" class="text-sm text-gray-500 hover:text-gray-700 underline self-center">Clear</a>
                    @endif
                </form>
            </div>
            <div class="flex gap-2">
                @if($currentMonthIndent)
                <button onclick="confirm('Indent for {{ $monthLabel }} already submitted. Generate a new one? This will overwrite.') ? window.location.href='{{ route('analytics.pdf', $currentMonthIndent) }}' : null" class="bg-blue-100 hover:bg-blue-200 text-blue-800 font-semibold py-2 px-4 rounded text-sm transition">⚠️ Already Submitted (View PDF)</button>
                @else
                <button @click="loadIndentPreview(); showIndentModal = true" class="bg-[#004b87] hover:bg-[#003461] text-white font-semibold py-2 px-4 rounded text-sm transition">📄 Generate Indent</button>
                @endif
                <a href="{{ route('analytics.index') }}" class="bg-white border border-blue-300 hover:bg-blue-50 text-blue-700 font-semibold py-2 px-4 rounded text-sm transition">📊 Analytics</a>
                <a href="{{ route('backup.index') }}" class="bg-white border border-blue-300 hover:bg-blue-50 text-blue-700 font-semibold py-2 px-4 rounded text-sm transition">💾 Backup</a>
            </div>
        </div>

        {{-- Inventory Table --}}
        <div class="bg-white rounded-lg shadow overflow-hidden border border-blue-200">
            <table class="w-full text-sm [&_th]:border-r [&_th]:border-blue-100 [&_th:last-child]:border-r-0 [&_td]:border-r [&_td]:border-blue-100 [&_td:last-child]:border-r-0">
                <thead>
                    <tr class="bg-slate-100 border-b">
                        <th class="w-8 px-3 py-2"></th>
                        <th class="px-3 py-2 text-left font-semibold text-gray-600">Product</th>
                        <th class="px-3 py-2 text-left font-semibold text-gray-600">Size</th>
                        <th class="px-3 py-2 text-left font-semibold text-gray-600">Unit</th>
                        <th class="px-3 py-2 text-center font-semibold text-blue-700">Req</th>
                        <th class="px-3 py-2 text-center font-semibold text-blue-700">
                            Stock
                            <div class="inline-flex ml-1.5" @click.stop>
                                <button @click="setGlobalMode('unit')" class="text-[10px] px-1.5 py-0.5 rounded-l font-semibold transition border border-blue-400" :class="globalStockMode === 'unit' ? 'bg-blue-700 text-white' : 'bg-white text-blue-700'">U</button>
                                <button @click="setGlobalMode('box')" class="text-[10px] px-1.5 py-0.5 rounded-r font-semibold transition border-t border-b border-r border-blue-400" :class="globalStockMode === 'box' ? 'bg-blue-700 text-white' : 'bg-white text-blue-700'">B</button>
                            </div>
                        </th>
                        <th class="px-3 py-2 text-center font-semibold text-red-700">Order</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $index => $product)
                    <tbody x-data="{ inputMode: '{{ $product->input_mode }}', stockValue: {{ $product->per_box && $product->input_mode === 'box' ? $product->stock / $product->per_box : $product->stock }}, expanded: false }">
                    {{-- Main Row --}}
                    <tr class="border-b cursor-pointer hover:bg-sky-100 transition-colors" style="{{ $index % 2 === 0 ? '' : 'background-color: #e0f0ff;' }}"
                        @click="expanded = !expanded">
                        <td class="px-3 py-3">
                            <span class="text-gray-400 transition-transform duration-200"
                                  :class="{ 'rotate-90': expanded }">▶</span>
                        </td>
                        <td class="px-3 py-3 font-semibold text-gray-800 max-w-[220px] truncate">{{ $product->product }}</td>
                        <td class="px-3 py-3 text-gray-600">{{ $product->size }}</td>
                        <td class="px-3 py-3 text-gray-600">{{ $product->unit_display }}</td>
                        <td class="px-3 py-3 bg-blue-50/50">
                            <div class="flex items-center justify-center gap-1.5" @click.stop>
                                <input type="number" value="{{ $product->per_box ? intdiv($product->requirement, $product->per_box) : $product->requirement }}"
                                    onchange="updateField({{ $product->id }}, 'requirement', this.value, {{ $product->per_box ?? 1 }})"
                                    class="w-16 text-center border rounded px-1 py-0.5 text-sm bg-white">
                                <span class="text-xs font-semibold text-blue-700 w-8 text-left">{{ $product->per_box ? 'Box' : 'Unit' }}</span>
                            </div>
                        </td>
                        <td class="px-3 py-3">
                            <div class="flex items-center justify-center gap-1" @click.stop>
                                <input type="number" x-model.number="stockValue"
                                    @change="updateStock({{ $product->id }}, inputMode, stockValue)"
                                    class="w-16 text-center border rounded px-1 py-0.5 text-sm bg-white">
                                <button @click="toggleInputMode({{ $product->id }}, inputMode === 'unit' ? 'box' : 'unit', stockValue)"
                                    :class="(inputMode === 'box' ? 'bg-slate-600 text-white' : 'bg-gray-200 text-gray-600') + ({{ $product->per_box ? 'true' : 'false' }} ? '' : ' invisible')"
                                    class="text-xs px-1.5 py-0.5 rounded font-semibold transition"
                                    x-text="inputMode === 'unit' ? 'Unit' : 'Box'"></button>
                            </div>
                        </td>
                        <td class="px-3 py-3 text-center font-semibold text-red-600">
                            {{ $product->order_display }}
                        </td>
                    </tr>

                    {{-- Detail Row --}}
                    <tr x-show="expanded" :style="expanded ? '' : 'display: none;'" class="bg-gray-50 border-b">
                        <td colspan="7" class="px-6 py-4">
                            @if($product->image)
                            <div class="mb-4">
                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->product }}" class="h-32 w-32 object-cover rounded-lg border">
                            </div>
                            @endif
                            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4 text-sm">
                                <div>
                                    <span class="text-xs font-semibold text-gray-500 uppercase">Category</span>
                                    <p class="text-gray-800">{{ $product->category }}</p>
                                </div>
                                <div>
                                    <span class="text-xs font-semibold text-gray-500 uppercase">Type</span>
                                    <p class="text-gray-800">{{ $product->type }}</p>
                                </div>
                                <div>
                                    <span class="text-xs font-semibold text-gray-500 uppercase">Brand</span>
                                    <p class="text-gray-800">{{ $product->brand }}</p>
                                </div>
                                <div>
                                    <span class="text-xs font-semibold text-gray-500 uppercase">Price</span>
                                    <p class="text-gray-800">{{ $product->price > 0 ? 'RM ' . number_format($product->price, 2) : '-' }}</p>
                                </div>
                                <div>
                                    <span class="text-xs font-semibold text-gray-500 uppercase">Cost/Unit</span>
                                    <p class="text-gray-800">{{ $product->cost_per_unit > 0 ? 'RM ' . number_format($product->cost_per_unit, 2) : '-' }}</p>
                                </div>
                                <div class="sm:col-span-2">
                                    <span class="text-xs font-semibold text-gray-500 uppercase">Product ID</span>
                                    <p class="text-gray-800 text-xs">{{ $product->product_id ?: '-' }}</p>
                                </div>
                                <div>
                                    <span class="text-xs font-semibold text-gray-500 uppercase">Code</span>
                                    <p class="text-gray-800 font-mono">{{ $product->code }}</p>
                                </div>
                                <div>
                                    <span class="text-xs font-semibold text-gray-500 uppercase">Expiry</span>
                                    <p class="text-gray-800">
                                        @if($product->expiry)
                                            @if($product->expiry->lt(now()))
                                                <span class="text-red-600 font-semibold">EXPIRED ({{ $product->expiry->format('d/m/Y') }})</span>
                                            @elseif($product->expiry->lt(now()->addDays(90)))
                                                <span class="text-amber-600 font-semibold">{{ $product->expiry->format('d/m/Y') }}</span>
                                            @else
                                                {{ $product->expiry->format('d/m/Y') }}
                                            @endif
                                        @else
                                            -
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="mt-4 pt-3 border-t border-gray-200 flex items-center gap-3" @click.stop>
                                <button @click="openEditModal({{ $product->id }})" class="text-xs px-3 py-1.5 bg-gray-200 hover:bg-gray-300 rounded font-semibold transition">✏️ Edit</button>
                                <button @click="loadHistory({{ $product->id }})" class="text-xs px-3 py-1.5 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded font-semibold transition">📋 History</button>
                                <form action="{{ route('products.destroy', $product) }}" method="POST" onsubmit="return confirm('Delete this product?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-xs px-3 py-1.5 bg-red-100 hover:bg-red-200 text-red-700 rounded font-semibold transition">🗑️ Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                    @empty
                    <tr>
                        <td colspan="7" class="px-3 py-8 text-center text-gray-500">No products found. Add a new product to get started.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    {{-- Indent Preview Modal --}}
    <div x-show="showIndentModal" x-cloak class="fixed inset-0 bg-black/60 flex items-center justify-center z-50" @click.self="showIndentModal = false">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl mx-4 p-6" @click.stop>
            <h3 class="text-lg font-bold text-gray-800 mb-2">Indent Order Preview</h3>
            <p class="text-xs text-gray-500 mb-4">Review items before generating PDF report.</p>
            <div id="indent-preview-content" class="bg-gray-50 rounded p-4 max-h-[400px] overflow-y-auto"></div>
            <div class="flex justify-end gap-3 mt-4">
                <button @click="showIndentModal = false" class="px-4 py-2 border rounded text-sm hover:bg-gray-100">Cancel</button>
                <button @click="generatePdf()" class="bg-slate-700 hover:bg-slate-800 text-white px-4 py-2 rounded text-sm font-semibold transition">Confirm & Download PDF</button>
            </div>
        </div>
    </div>

    {{-- History Modal --}}
    <div x-show="showHistoryModal" x-cloak class="fixed inset-0 bg-black/60 flex items-center justify-center z-50" @click.self="showHistoryModal = false">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-lg mx-4 p-6" @click.stop>
            <h3 class="text-lg font-bold text-gray-800 mb-2">Stock Usage History</h3>
            <div id="history-content" class="bg-gray-50 rounded p-4 max-h-[400px] overflow-y-auto"></div>
            <div class="flex justify-end mt-4">
                <button @click="showHistoryModal = false" class="px-4 py-2 border rounded text-sm hover:bg-gray-100">Close</button>
            </div>
        </div>
    </div>

    {{-- Edit Product Modal --}}
    <div x-show="showEditModal" x-cloak class="fixed inset-0 bg-black/60 flex items-center justify-center z-50" @click.self="showEditModal = false">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl mx-4 p-6" @click.stop>
            <h3 class="text-lg font-bold text-gray-800 mb-4">Edit Product</h3>
            <form :action="editFormAction" method="POST" enctype="multipart/form-data">
                @csrf
                @if($errors->any())
                    <div class="mb-3 bg-red-50 border border-red-200 text-red-700 rounded px-3 py-2 text-xs">
                        @foreach($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Category</label>
                        <input type="text" name="category" x-model="editProduct.category" required class="w-full border rounded px-2 py-1.5">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Type / Function</label>
                        <input type="text" name="type" x-model="editProduct.type" required class="w-full border rounded px-2 py-1.5">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Product</label>
                        <input type="text" name="product" x-model="editProduct.product" required class="w-full border rounded px-2 py-1.5">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Brand</label>
                        <input type="text" name="brand" x-model="editProduct.brand" required class="w-full border rounded px-2 py-1.5">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Size</label>
                        <input type="text" name="size" x-model="editProduct.size" required class="w-full border rounded px-2 py-1.5">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Unit (e.g. 10/BOX)</label>
                        <input type="text" name="unit" x-model="editProduct.unit" required class="w-full border rounded px-2 py-1.5">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Price</label>
                        <input type="number" step="0.01" name="price" x-model="editProduct.price" class="w-full border rounded px-2 py-1.5">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Cost/Unit</label>
                        <input type="number" step="0.01" name="cost_per_unit" x-model="editProduct.cost_per_unit" class="w-full border rounded px-2 py-1.5">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Product ID</label>
                        <input type="text" name="product_id" x-model="editProduct.product_id" class="w-full border rounded px-2 py-1.5">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Code</label>
                        <input type="text" name="code" x-model="editProduct.code" required class="w-full border rounded px-2 py-1.5">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Expiry</label>
                        <input type="date" name="expiry" x-model="editProduct.expiry" class="w-full border rounded px-2 py-1.5">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Requirement</label>
                        <input type="number" name="requirement" x-model="editProduct.requirement" class="w-full border rounded px-2 py-1.5">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Stock</label>
                        <input type="number" name="stock" x-model="editProduct.stock" class="w-full border rounded px-2 py-1.5">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Product Image</label>
                        <template x-if="editProduct.image_url">
                            <img :src="editProduct.image_url" class="h-20 w-20 object-cover rounded border mb-2">
                        </template>
                        <input type="file" name="image" accept="image/jpeg,image/png,image/jpg,image/gif,image/webp" class="w-full border rounded px-2 py-1.5 text-sm">
                    </div>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" @click="showEditModal = false" class="px-4 py-2 border rounded text-sm hover:bg-gray-100">Cancel</button>
                    <button type="submit" class="bg-[#004b87] hover:bg-[#003461] text-white px-4 py-2 rounded text-sm font-semibold transition">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Add Product Modal --}}
    <div x-show="showAddModal" x-cloak class="fixed inset-0 bg-black/60 flex items-center justify-center z-50" @click.self="showAddModal = false">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl mx-4 p-6" @click.stop>
            <h3 class="text-lg font-bold text-gray-800 mb-4">Add New Product</h3>
            <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @if($errors->any())
                    <div class="mb-3 bg-red-50 border border-red-200 text-red-700 rounded px-3 py-2 text-xs">
                        @foreach($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Category</label>
                        <input type="text" name="category" required class="w-full border rounded px-2 py-1.5">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Type / Function</label>
                        <input type="text" name="type" required class="w-full border rounded px-2 py-1.5">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Product</label>
                        <input type="text" name="product" required class="w-full border rounded px-2 py-1.5">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Brand</label>
                        <input type="text" name="brand" required class="w-full border rounded px-2 py-1.5">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Size</label>
                        <input type="text" name="size" required class="w-full border rounded px-2 py-1.5">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Unit (e.g. 10/BOX)</label>
                        <input type="text" name="unit" required class="w-full border rounded px-2 py-1.5">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Price</label>
                        <input type="number" step="0.01" name="price" class="w-full border rounded px-2 py-1.5">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Cost/Unit</label>
                        <input type="number" step="0.01" name="cost_per_unit" class="w-full border rounded px-2 py-1.5">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Product ID</label>
                        <input type="text" name="product_id" class="w-full border rounded px-2 py-1.5">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Code</label>
                        <input type="text" name="code" required class="w-full border rounded px-2 py-1.5">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Expiry</label>
                        <input type="date" name="expiry" class="w-full border rounded px-2 py-1.5">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Requirement</label>
                        <input type="number" name="requirement" class="w-full border rounded px-2 py-1.5">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Stock</label>
                        <input type="number" name="stock" class="w-full border rounded px-2 py-1.5">
                    </div>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" @click="showAddModal = false" class="px-4 py-2 border rounded text-sm hover:bg-gray-100">Cancel</button>
                    <button type="submit" class="bg-[#004b87] hover:bg-[#003461] text-white px-4 py-2 rounded text-sm font-semibold transition">Add Item</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function inventoryApp() {
    return {
        globalStockMode: '{{ $stats['default_input_mode'] }}',
        showAddModal: false,
        showIndentModal: false,
        showHistoryModal: false,
        showEditModal: false,
        editProduct: {},
        editFormAction: '',

        setGlobalMode(mode) {
            fetch('/products/batch-input-mode', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ input_mode: mode })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            });
        },

        openEditModal(productId) {
            fetch(`/products/${productId}`)
                .then(r => r.json())
                .then(data => {
                    this.editProduct = data;
                    this.editFormAction = `/products/${productId}/update-details`;
                    this.showEditModal = true;
                });
        },

        toggleInputMode(productId, newMode, stockValue) {
            fetch(`/products/${productId}/input-mode`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ input_mode: newMode, stock_value: stockValue })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            });
        },

        updateStock(productId, inputMode, stockValue) {
            fetch(`/products/${productId}/input-mode`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ input_mode: inputMode, stock_value: stockValue })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            });
        },

        loadHistory(productId) {
            fetch(`/products/${productId}/history`)
                .then(r => r.text())
                .then(html => {
                    document.getElementById('history-content').innerHTML = html;
                    this.showHistoryModal = true;
                });
        },

        generatePdf() {
            fetch('/indent/save-and-download', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                },
            })
            .then(r => r.blob())
            .then(blob => {
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'wound-care-order-{{ now()->format("Y-m-d") }}.pdf';
                a.click();
                window.URL.revokeObjectURL(url);
                this.showIndentModal = false;
                location.reload();
            });
        },

        loadIndentPreview() {
            fetch('/indent/preview')
                .then(r => r.text())
                .then(html => {
                    document.getElementById('indent-preview-content').innerHTML = html;
                });
        }
    }
}

function updateField(productId, field, value, factor) {
    factor = factor || 1;
    let val = parseInt(value) * factor;
    fetch(`/products/${productId}`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
        body: JSON.stringify({ field, value: val })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}
</script>
@endsection
