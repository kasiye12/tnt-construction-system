@extends('layouts.mobile')

@section('title', 'Daily Report')

@section('content')
<div class="animate-in">
    <div class="card">
        <div class="card-header">📝 {{ date('l, F d, Y') }}</div>
        
        <form action="/mobile/reports" method="POST">
            @csrf
            
            <div class="input-group">
                <label>Site *</label>
                <select name="site_id" required>
                    <option value="">Select site</option>
                    @foreach($sites as $site)
                    <option value="{{ $site->id }}">{{ $site->site_name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="input-group">
                <label>Workforce Count</label>
                <input type="number" name="workforce_count" placeholder="Number of workers" min="0">
            </div>
            
            <div class="input-group">
                <label>Progress: <span id="progVal">0%</span></label>
                <input type="range" name="progress_percentage" min="0" max="100" value="0" 
                       oninput="document.getElementById('progVal').textContent = this.value + '%'">
                <div class="progress-bar" style="margin-top:8px;">
                    <div class="progress-fill" id="progFill" style="width:0%"></div>
                </div>
            </div>
            
            <div class="input-group">
                <label>Work Summary</label>
                <textarea name="summary_text" rows="3" placeholder="Describe today's work..."></textarea>
            </div>
            
            <div class="input-group">
                <label>Challenges</label>
                <textarea name="challenges" rows="2" placeholder="Any issues or delays..."></textarea>
            </div>
            
            <button type="submit" class="btn btn-primary" style="margin-top:8px;">
                📤 Submit Report
            </button>
        </form>
    </div>
</div>
@endsection
