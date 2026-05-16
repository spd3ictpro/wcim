@extends('layout')

@section('content')
<div class="min-h-screen bg-gray-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-lg border border-blue-200 p-8 w-full max-w-md">
        <div class="text-center mb-6">
            <span class="text-4xl">🔒</span>
            <h1 class="font-['Manrope',sans-serif] text-xl font-bold text-[#004b87] mt-2">Backup & Restore</h1>
            <p class="font-['Inter',sans-serif] text-sm text-gray-500 mt-1">Enter password to access backup features</p>
        </div>

        @if(session('error'))
            <div class="mb-4 bg-red-50 border border-red-200 text-red-700 rounded px-3 py-2 text-sm">{{ session('error') }}</div>
        @endif

        <form action="{{ route('backup.authenticate') }}" method="POST">
            @csrf
            <input type="password" name="password" placeholder="Password" required autofocus class="w-full border rounded-lg px-4 py-2.5 text-sm mb-4 focus:outline-none focus:ring-2 focus:ring-blue-400">
            <button type="submit" class="w-full bg-[#004b87] hover:bg-[#003461] text-white font-semibold py-2.5 rounded-lg text-sm transition">Enter Backup</button>
        </form>

        <div class="mt-6 text-center">
            <a href="{{ route('dashboard') }}" class="text-sm text-blue-600 hover:text-blue-800 underline">← Back to Dashboard</a>
        </div>
    </div>
</div>
@endsection
