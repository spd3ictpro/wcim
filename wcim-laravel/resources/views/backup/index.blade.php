@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-6 py-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="font-['Manrope',sans-serif] text-xl font-bold text-[#004b87]">💾 Backup & Restore</h1>
            <p class="text-sm text-gray-500">Database Management</p>
        </div>
        <form action="{{ route('backup.logout') }}" method="POST">
            @csrf
            <button type="submit" class="text-sm text-red-600 hover:text-red-800 underline">Lock</button>
        </form>
    </div>
    @if(session('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative text-sm" x-data x-init="setTimeout(() => $el.remove(), 3000)">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded text-sm">{{ session('error') }}</div>
        @endif

        {{-- Database Info --}}
        <div class="bg-white rounded-lg shadow border border-blue-200 p-5 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="font-['Inter',sans-serif] text-sm font-semibold text-gray-700">Current Database</h3>
                    <p class="font-['Inter',sans-serif] text-xs text-gray-500 mt-1">File: <code class="bg-gray-100 px-1 rounded">database/database.sqlite</code> ({{ $dbSizeFormatted }})</p>
                    <p class="font-['Inter',sans-serif] text-xs text-gray-500">Last backup: {{ $latestBackup }}</p>
                </div>
                <span class="text-4xl">💾</span>
            </div>
        </div>

        {{-- Export / Import --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow border border-blue-200 p-5">
                <h3 class="font-['Inter',sans-serif] text-sm font-semibold text-gray-700 mb-1">📥 Export Backup</h3>
                <p class="font-['Inter',sans-serif] text-xs text-gray-500 mb-4">Download the current database as a backup file.</p>
                <a href="{{ route('backup.export') }}" class="inline-block bg-[#004b87] hover:bg-[#003461] text-white font-semibold px-5 py-2 rounded text-sm transition">Download Backup</a>
            </div>

            <div class="bg-white rounded-lg shadow border border-blue-200 p-5">
                <h3 class="font-['Inter',sans-serif] text-sm font-semibold text-gray-700 mb-1">📤 Import Backup</h3>
                <p class="font-['Inter',sans-serif] text-xs text-gray-500 mb-4">Restore from a previously downloaded backup file.</p>
                <form action="{{ route('backup.import') }}" method="POST" enctype="multipart/form-data" onsubmit="return confirm('⚠️ This will REPLACE ALL current data with the backup. A backup of your current data will be saved automatically. Continue?')">
                    @csrf
                    <input type="file" name="backup_file" accept=".sqlite,.db" required class="w-full text-sm mb-3">
                    <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white font-semibold px-5 py-2 rounded text-sm transition">Restore Backup</button>
                </form>
            </div>
        </div>

        {{-- Stored Backups --}}
        <div class="bg-white rounded-lg shadow border border-blue-200 p-5 mb-6">
            <h3 class="font-['Inter',sans-serif] text-sm font-semibold text-gray-700 mb-3">📂 Stored Backups</h3>
            @if($backups->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left py-2 px-2 font-semibold text-gray-500">File</th>
                            <th class="text-left py-2 px-2 font-semibold text-gray-500">Date</th>
                            <th class="text-right py-2 px-2 font-semibold text-gray-500">Size</th>
                            <th class="text-center py-2 px-2 font-semibold text-gray-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($backups as $backup)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="py-2 px-2 text-xs font-mono">{{ $backup->name }}</td>
                            <td class="py-2 px-2 text-xs">{{ $backup->date }}</td>
                            <td class="py-2 px-2 text-xs text-right">{{ $backup->size }}</td>
                            <td class="py-2 px-2 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('backup.download', $backup->name) }}" class="text-blue-600 hover:text-blue-800 text-xs font-semibold">Download</a>
                                    <form action="{{ route('backup.delete', $backup->name) }}" method="POST" onsubmit="return confirm('Delete this backup?')">
                                        @csrf
                                        <button type="submit" class="text-red-600 hover:text-red-800 text-xs font-semibold">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p class="text-gray-500 text-sm text-center py-4">No backups stored yet. Export your first backup to see it here.</p>
            @endif
        </div>

        {{-- Change Password --}}
        <div class="bg-white rounded-lg shadow border border-blue-200 p-5">
            <h3 class="font-['Inter',sans-serif] text-sm font-semibold text-gray-700 mb-3">🔑 Change Password</h3>
            <form action="{{ route('backup.password') }}" method="POST" class="max-w-md">
                @csrf
                <div class="grid grid-cols-1 gap-3">
                    <input type="password" name="current_password" placeholder="Current password" required class="border rounded px-3 py-2 text-sm">
                    <input type="password" name="new_password" placeholder="New password (min 6 characters)" required minlength="6" class="border rounded px-3 py-2 text-sm">
                    <input type="password" name="confirm_password" placeholder="Confirm new password" required class="border rounded px-3 py-2 text-sm">
                    <button type="submit" class="bg-gray-700 hover:bg-gray-800 text-white font-semibold px-4 py-2 rounded text-sm transition w-fit">Update Password</button>
                </div>
            </form>
        </div>
</div>
@endsection
