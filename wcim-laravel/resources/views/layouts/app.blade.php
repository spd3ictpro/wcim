<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>WCIM — Wound Care Inventory Management</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
        .sidebar-link {  }
    </style>
</head>
<body class="bg-gray-50 min-h-screen font-['Inter',sans-serif]">

<div class="flex h-screen overflow-hidden">
    {{-- Sidebar --}}
    <aside class="w-56 bg-white border-r border-blue-100 flex-shrink-0 flex flex-col">
        {{-- Logo Area --}}
        <div class="px-4 py-4 border-b border-blue-100">
            <div class="flex items-center gap-2.5">
                <img src="{{ asset('images/logo kkm.jpg') }}" alt="KKM" class="h-9 w-auto">
                <div class="w-px h-7 bg-blue-200"></div>
                <img src="{{ asset('images/kkjp logo.png') }}" alt="KKJP" class="h-9 w-auto">
            </div>
            <div class="mt-2 pl-1">
                <span class="font-['Manrope',sans-serif] text-base font-extrabold text-[#004b87]">WCIM</span>
                <span class="text-[10px] text-blue-500 block leading-tight">Wound Care Inventory</span>
            </div>
        </div>

        {{-- Nav --}}
        <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
            <a href="{{ route('dashboard') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('dashboard') ? 'bg-[#004b87] text-white' : 'text-gray-600 hover:bg-blue-50 hover:text-[#004b87]' }}">
                <span>📋</span> Dashboard
            </a>

            {{-- Analytics with sub-items --}}
            <div x-data="{ open: {{ request()->routeIs('analytics.*') ? 'true' : 'false' }} }">
                <button @click="open = !open"
                   class="flex items-center justify-between w-full px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('analytics.*') ? 'bg-[#004b87] text-white' : 'text-gray-600 hover:bg-blue-50 hover:text-[#004b87]' }}">
                    <span class="flex items-center gap-3"><span>📊</span> Analytics</span>
                    <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="{ 'rotate-90': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </button>
                <div x-show="open" x-cloak class="ml-6 mt-1 space-y-1">
                    <a href="{{ route('analytics.index') }}"
                       class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium transition {{ request()->routeIs('analytics.index') || request()->routeIs('analytics.show') ? 'bg-blue-100 text-[#004b87]' : 'text-gray-500 hover:text-[#004b87]' }}">
                        <span class="text-[10px]">└─</span> Indent History
                    </a>
                    <a href="{{ route('analytics.consumption') }}"
                       class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium transition {{ request()->routeIs('analytics.consumption') ? 'bg-blue-100 text-[#004b87]' : 'text-gray-500 hover:text-[#004b87]' }}">
                        <span class="text-[10px]">└─</span> Consumption
                    </a>
                </div>
            </div>

            <a href="{{ route('stock.receive') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('stock.*') ? 'bg-[#004b87] text-white' : 'text-gray-600 hover:bg-blue-50 hover:text-[#004b87]' }}">
                <span>📦</span> Receive Stock
            </a>

            {{-- Maintenance with sub-items --}}
            <div x-data="{ open: {{ request()->routeIs('import.*') || request()->routeIs('backup.*') ? 'true' : 'false' }} }">
                <button @click="open = !open"
                   class="flex items-center justify-between w-full px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('import.*') || request()->routeIs('backup.*') ? 'bg-[#004b87] text-white' : 'text-gray-600 hover:bg-blue-50 hover:text-[#004b87]' }}">
                    <span class="flex items-center gap-3"><span>⚙️</span> Maintenance</span>
                    <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="{ 'rotate-90': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </button>
                <div x-show="open" x-cloak class="ml-6 mt-1 space-y-1">
                    <a href="{{ route('import.index') }}"
                       class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium transition {{ request()->routeIs('import.*') ? 'bg-blue-100 text-[#004b87]' : 'text-gray-500 hover:text-[#004b87]' }}">
                        <span class="text-[10px]">└─</span> Import CSV
                    </a>
                    <a href="{{ route('backup.index') }}"
                       class="flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium transition {{ request()->routeIs('backup.*') ? 'bg-blue-100 text-[#004b87]' : 'text-gray-500 hover:text-[#004b87]' }}">
                        <span class="text-[10px]">└─</span> Backup
                    </a>
                </div>
            </div>
        </nav>

        {{-- Footer --}}
        <div class="px-4 py-3 border-t border-blue-100 text-[10px] text-gray-400">
            {{ now()->format('d F Y') }}
        </div>
    </aside>

    {{-- Main Content --}}
    <main class="flex-1 overflow-y-auto bg-gray-50">
        @yield('content')
    </main>
</div>

</body>
</html>
