@extends('layouts.mobile')

@section('title', 'Daily Report')

@section('content')
<div class="space-y-4">
    <div class="card">
        <h2 class="text-lg font-bold mb-4">📝 Daily Report</h2>
        <p class="text-sm text-gray-500 mb-4">{{ now()->format('l, F d, Y') }}</p>

        <form action="/mobile/reports" method="POST" id="report-form">
            @csrf
            
            <label class="block text-sm font-medium mb-1">Site *</label>
            <select name="site_id" required>
                <option value="">Select site</option>
                @foreach($sites as $site)
                <option value="{{ $site->id }}">{{ $site->site_name }}</option>
                @endforeach
            </select>

            <label class="block text-sm font-medium mb-1">Workforce Count</label>
            <input type="number" name="workforce_count" placeholder="Number of workers" min="0">

            <label class="block text-sm font-medium mb-1">Progress (%)</label>
            <input type="range" name="progress_percentage" min="0" max="100" value="0" 
                   oninput="document.getElementById('progress-val').textContent = this.value + '%'">
            <div class="text-center font-bold text-lg" id="progress-val">0%</div>

            <label class="block text-sm font-medium mb-1">Work Summary</label>
            <textarea name="summary_text" rows="4" placeholder="Describe today's work..."></textarea>

            <label class="block text-sm font-medium mb-1">Challenges</label>
            <textarea name="challenges" rows="3" placeholder="Any issues or delays..."></textarea>

            <button type="submit" class="btn btn-primary mt-4">
                📤 Submit Report
            </button>
        </form>
    </div>
</div>
@endsection
