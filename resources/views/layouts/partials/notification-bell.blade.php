<!-- Notification Bell with Dropdown -->
<div class="flex items-center space-x-2" x-data="{ open: false, notifications: [], unread: 0 }" 
     x-init="fetchNotifications(); setInterval(() => fetchNotifications(), 30000)">
    
    <!-- Sound Toggle -->
    <button id="sound-toggle" class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition" 
            onclick="toggleSound()" title="Toggle Sound">
        🔊
    </button>
    
    <!-- Notification Bell -->
    <div class="relative">
        <button @click="open = !open; if(open) markAllRead()" 
                class="relative p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
            </svg>
            <span x-show="unread > 0" 
                  x-text="unread > 99 ? '99+' : unread"
                  class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full min-w-[20px] h-5 flex items-center justify-center px-1 animate-pulse">
            </span>
        </button>
        
        <!-- Dropdown -->
        <div x-show="open" 
             @click.away="open = false"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             class="absolute right-0 mt-2 w-96 bg-white rounded-2xl shadow-xl border border-gray-100 z-50 overflow-hidden">
            
            <!-- Header -->
            <div class="p-4 border-b flex justify-between items-center bg-gray-50">
                <h3 class="font-bold text-gray-900">Notifications</h3>
                <div class="flex gap-2">
                    <button @click="markAllRead()" class="text-xs text-blue-500 hover:text-blue-600">Mark all read</button>
                    <a href="/notifications" class="text-xs text-gray-500 hover:text-gray-600">View all</a>
                </div>
            </div>
            
            <!-- Notification List -->
            <div class="max-h-96 overflow-y-auto">
                <template x-if="notifications.length === 0">
                    <div class="p-8 text-center text-gray-500">
                        <div class="text-4xl mb-3">🔔</div>
                        <p class="text-sm">No notifications yet</p>
                    </div>
                </template>
                
                <template x-for="notif in notifications.slice(0, 10)" :key="notif.id">
                    <div :class="notif.read_at ? 'bg-white' : 'bg-blue-50'"
                         class="p-4 border-b last:border-0 hover:bg-gray-50 cursor-pointer transition flex items-start gap-3">
                        <!-- Icon -->
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg flex-shrink-0"
                             :class="{
                                'bg-blue-100': notif.data?.type === 'report_submitted',
                                'bg-green-100': notif.data?.type === 'report_approved',
                                'bg-purple-100': notif.data?.type === 'new_message',
                                'bg-orange-100': notif.data?.type === 'checkin_reminder',
                                'bg-red-100': notif.data?.type === 'safety_incident',
                                'bg-yellow-100': notif.data?.type === 'low_stock',
                                'bg-gray-100': !notif.data?.type
                             }">
                            <span x-text="getNotifIcon(notif.data?.type)"></span>
                        </div>
                        
                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-900" x-text="notif.data?.title || 'Notification'"></p>
                            <p class="text-xs text-gray-600 mt-1 line-clamp-2" x-text="notif.data?.message || ''"></p>
                            <span class="text-xs text-gray-400 mt-2 block" x-text="timeAgo(notif.created_at)"></span>
                        </div>
                        
                        <!-- Unread dot -->
                        <div x-show="!notif.read_at" class="w-2 h-2 bg-blue-500 rounded-full flex-shrink-0 mt-2"></div>
                    </div>
                </template>
            </div>
            
            <!-- Footer -->
            <div class="p-3 border-t bg-gray-50 text-center">
                <a href="/notifications" class="text-sm text-blue-500 hover:text-blue-600 font-medium">
                    See All Notifications →
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>

<script>
function getNotifIcon(type) {
    const icons = {
        'report_submitted': '📝',
        'report_approved': '✅',
        'new_message': '💬',
        'checkin_reminder': '📍',
        'safety_incident': '🦺',
        'low_stock': '📦',
        'test': '🔔',
    };
    return icons[type] || '🔔';
}

function timeAgo(dateString) {
    if (!dateString) return '';
    const date = new Date(dateString);
    const seconds = Math.floor((new Date() - date) / 1000);
    if (seconds < 60) return 'Just now';
    if (seconds < 3600) return Math.floor(seconds / 60) + 'm ago';
    if (seconds < 86400) return Math.floor(seconds / 3600) + 'h ago';
    return Math.floor(seconds / 86400) + 'd ago';
}

async function fetchNotifications() {
    try {
        const res = await fetch('/notifications/latest');
        const data = await res.json();
        // Update Alpine component
        const el = document.querySelector('[x-data]');
        if (el && el.__x) {
            el.__x.$data.notifications = data.notifications || [];
            el.__x.$data.unread = data.unread_count || 0;
        }
    } catch (e) {}
}

async function markAllRead() {
    try {
        await fetch('/notifications/mark-all-read', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
        });
    } catch (e) {}
}

function toggleSound() {
    const btn = document.getElementById('sound-toggle');
    if (window.notificationManager) {
        window.notificationManager.soundEnabled = !window.notificationManager.soundEnabled;
        btn.textContent = window.notificationManager.soundEnabled ? '🔊' : '🔇';
    }
}
</script>
