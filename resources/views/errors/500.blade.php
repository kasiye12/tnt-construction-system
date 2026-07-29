@extends('layouts.app')

@section('title', 'Server Error')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center">
    <div class="text-center">
        <div class="text-8xl mb-4">⚠️</div>
        <h1 class="text-4xl font-bold text-gray-900 mb-2">500</h1>
        <p class="text-xl text-gray-600 mb-6">Internal Server Error</p>
        <p class="text-gray-500 mb-8">Something went wrong. Please try again later.</p>
        <a href="{{ url('/dashboard') }}" class="bg-sky-500 text-white px-6 py-3 rounded-xl font-medium hover:bg-sky-600 transition">
            ← Back to Dashboard
        </a>
    </div>
</div>
@endsection
