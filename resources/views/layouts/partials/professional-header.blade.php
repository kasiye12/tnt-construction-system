<header class="bg-white border-b border-gray-200 sticky top-0 z-40">
    <div class="px-6 py-4">
        <div class="flex items-center justify-between">
            <!-- Search -->
            <div class="flex-1 max-w-lg">
                <div class="relative">
                    <input type="text" placeholder="Search..." 
                           class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500 text-sm">
                    <svg class="absolute left-3 top-2.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>
            
            <!-- Right Actions -->
            <div class="flex items-center space-x-3">
                <!-- Notification Bell -->
                <div class="relative" x-data="{ open: false, unread: 0 }" x-init="fetchUnreadCount(); setInterval(() => fetchUnreadCount(), 15000)">
                    <button @click="open = !open" 
                            class="relative p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                        <span x-show="unread > 0" x-text="unread > 9 ? '9+' : unread"
                              class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full min-w-[20px] h-5 flex items-center justify-center px-1">
                        </span>
                    </button>
                    
                    <!-- Notification Dropdown -->
                    <div x-show="open" @click.away="open = false"
                         x-transition
                         class="absolute right-0 mt-2 w-96 bg-white rounded-2xl shadow-xl border border-gray-100 z-50 overflow-hidden">
                        <div class="p-4 border-b flex justify-between items-center bg-gray-50">
                            <h3 class="font-bold text-gray-900">🔔 Notifications</h3>
                            <div class="flex gap-2">
                                <a href="/notifications" class="text-xs text-blue-500 hover:text-blue-600">View All</a>
                            </div>
                        </div>
                        <div class="max-h-80 overflow-y-auto" id="notif-dropdown-list">
                            <div class="p-8 text-center text-gray-500">
                                <div class="animate-spin text-2xl mb-2">⏳</div>
                                <p class="text-sm">Loading notifications...</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- User Menu -->
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 bg-sky-500 rounded-lg flex items-center justify-center text-white font-bold">
                        {{ strtoupper(substr(Auth::user()->full_name ?? 'U', 0, 1)) }}
                    </div>
                    <span class="text-sm font-medium hidden md:block">{{ Auth::user()->full_name }}</span>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-gray-400 hover:text-red-500 text-sm" title="Logout">🚪</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>

<script>
// Fetch unread count
async function fetchUnreadCount() {
    try {
        const res = await fetch('/notifications/unread-count');
        const data = await res.json();
        const el = document.querySelector('[x-data]');
        if (el && el.__x) {
            el.__x.$data.unread = data.count || 0;
        }
    } catch(e) {}
}

// Load notifications when dropdown opens
document.addEventListener('alpine:init', () => {
    Alpine.data('notifications', () => ({
        async loadNotifications() {
            try {
                const res = await fetch('/notifications/latest');
                const data = await res.json();
                const list = document.getElementById('notif-dropdown-list');
                if (list && data.notifications) {
                    if (data.notifications.length === 0) {
                        list.innerHTML = '<div class="p-8 text-center text-gray-500"><div class="text-3xl mb-2">🔔</div><p class="text-sm">No notifications</p></div>';
                    } else {
                        list.innerHTML = data.notifications.map(n => {
                            const d = typeof n.data === 'string' ? JSON.parse(n.data) : n.data;
                            const icons = {report_submitted:'📝',report_approved:'✅',report_rejected:'❌',new_message:'💬',checkin:'📍',safety_incident:'🦺'};
                            const bg = n.read_at ? 'bg-white' : 'bg-blue-50';
                            return `<div class="p-4 border-b ${bg} hover:bg-gray-50 cursor-pointer">
                                <div class="flex items-start gap-3">
                                    <span class="text-xl">${icons[d?.type] || '🔔'}</span>
                                    <div class="flex-1">
                                        <p class="text-sm font-semibold">${d?.title || 'Notification'}</p>
                                        <p class="text-xs text-gray-600 mt-1">${d?.message || ''}</p>
                                        <span class="text-xs text-gray-400 mt-2 block">${timeAgo(n.created_at)}</span>
                                    </div>
                                    ${!n.read_at ? '<span class="w-2 h-2 bg-blue-500 rounded-full mt-2"></span>' : ''}
                                </div>
                            </div>`;
                        }).join('');
                    }
                }
            } catch(e) {
                console.error('Failed to load notifications:', e);
            }
        }
    }));
});

function timeAgo(dateString) {
    if (!dateString) return '';
    const date = new Date(dateString);
    const seconds = Math.floor((new Date() - date) / 1000);
    if (seconds < 60) return 'Just now';
    if (seconds < 3600) return Math.floor(seconds / 60) + 'm ago';
    if (seconds < 86400) return Math.floor(seconds / 3600) + 'h ago';
    return Math.floor(seconds / 86400) + 'd ago';
}

// Load notifications when bell is clicked
document.querySelector('[x-data] button')?.addEventListener('click', function() {
    setTimeout(() => {
        if (window.Alpine) {
            const el = document.querySelector('[x-data]');
            if (el && el.__x && el.__x.$data.open) {
                fetch('/notifications/latest')
                    .then(res => res.json())
                    .then(data => {
                        const list = document.getElementById('notif-dropdown-list');
                        if (list && data.notifications) {
                            if (data.notifications.length === 0) {
                                list.innerHTML = '<div class="p-8 text-center text-gray-500"><div class="text-3xl mb-2">🔔</div><p class="text-sm">No notifications</p></div>';
                            } else {
                                list.innerHTML = data.notifications.map(n => {
                                    const d = typeof n.data === 'string' ? JSON.parse(n.data) : n.data;
                                    const icons = {report_submitted:'📝',report_approved:'✅',report_rejected:'❌',new_message:'💬',checkin:'📍',safety_incident:'🦺'};
                                    const bg = n.read_at ? 'bg-white' : 'bg-blue-50';
                                    return `<div class="p-4 border-b ${bg} hover:bg-gray-50 cursor-pointer">
                                        <div class="flex items-start gap-3">
                                            <span class="text-xl">${icons[d?.type] || '🔔'}</span>
                                            <div class="flex-1">
                                                <p class="text-sm font-semibold">${d?.title || 'Notification'}</p>
                                                <p class="text-xs text-gray-600 mt-1">${d?.message || ''}</p>
                                                <span class="text-xs text-gray-400 mt-2 block">${timeAgo(n.created_at)}</span>
                                            </div>
                                        </div>
                                    </div>`;
                                }).join('');
                            }
                        }
                    });
            }
        }
    }, 300);
});
</script>
