@extends('layouts.mobile')

@section('title', 'Check In')

@section('content')
<div class="space-y-4">
    <div class="card text-center">
        <div class="text-6xl mb-4">{{ $todayCheckin && !$todayCheckin->check_out_time ? '📍' : '🏠' }}</div>
        <h2 class="text-xl font-bold">
            {{ $todayCheckin ? ($todayCheckin->check_out_time ? 'Checked Out' : 'On Site') : 'Check In' }}
        </h2>
        
        @if($todayCheckin)
        <div class="mt-4 text-left space-y-2">
            <div class="flex justify-between">
                <span class="text-gray-600">Check In:</span>
                <span class="font-semibold">{{ $todayCheckin->check_in_time->format('H:i A') }}</span>
            </div>
            @if($todayCheckin->check_out_time)
            <div class="flex justify-between">
                <span class="text-gray-600">Check Out:</span>
                <span class="font-semibold">{{ $todayCheckin->check_out_time->format('H:i A') }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">Hours:</span>
                <span class="font-semibold">{{ $todayCheckin->hours_worked }} hrs</span>
            </div>
            @endif
        </div>
        @endif
    </div>

    @if(!$todayCheckin || ($todayCheckin && $todayCheckin->check_out_time))
    <button onclick="checkIn()" class="btn btn-primary" id="checkin-btn">
        📍 Check In Now
    </button>
    @endif

    @if($todayCheckin && !$todayCheckin->check_out_time)
    <button onclick="checkOut()" class="btn btn-danger" id="checkout-btn">
        🚪 Check Out
    </button>
    @endif

    <!-- Recent Check-ins -->
    <div class="card">
        <h3 class="font-bold mb-3">Recent Activity</h3>
        @foreach($recentCheckins as $checkin)
        <div class="py-2 border-b last:border-0">
            <div class="flex justify-between">
                <span class="text-sm">{{ $checkin->created_at->format('M d, Y') }}</span>
                <span class="text-sm font-semibold">
                    {{ $checkin->check_out_time ? '✅ Completed' : '🟢 Active' }}
                </span>
            </div>
            <div class="text-xs text-gray-500">
                {{ $checkin->check_in_time->format('H:i') }} - 
                {{ $checkin->check_out_time ? $checkin->check_out_time->format('H:i') : 'Present' }}
            </div>
        </div>
        @endforeach
    </div>
</div>

<script>
function getLocation() {
    return new Promise((resolve, reject) => {
        if (!navigator.geolocation) {
            reject('Geolocation not supported');
        }
        navigator.geolocation.getCurrentPosition(
            position => resolve({
                latitude: position.coords.latitude,
                longitude: position.coords.longitude
            }),
            error => reject(error)
        );
    });
}

async function checkIn() {
    try {
        document.getElementById('checkin-btn').disabled = true;
        const location = await getLocation();
        
        const response = await fetch('/mobile/do-checkin', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(location)
        });
        
        const data = await response.json();
        alert(data.message);
        location.reload();
    } catch (error) {
        alert('❌ Could not get location. Please enable GPS.');
    }
}

async function checkOut() {
    if (!confirm('Are you sure you want to check out?')) return;
    
    const response = await fetch('/mobile/do-checkout', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    });
    
    const data = await response.json();
    alert(data.message);
    location.reload();
}
</script>
@endsection
