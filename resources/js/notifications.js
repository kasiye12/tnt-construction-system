/**
 * TNT Construction - Complete Notification System
 */

class NotificationManager {
    constructor() {
        this.soundEnabled = true;
        this.permission = 'default';
        this.unreadCount = 0;
        this.sounds = {
            message: '/sounds/message.mp3',
            notification: '/sounds/notification.wav',
            alert: '/sounds/alert.mp3',
        };
        this.init();
    }

    init() {
        this.requestPermission();
        this.loadUnreadCount();
        this.initSoundToggle();
        this.startPolling();
    }

    // Request browser notification permission
    async requestPermission() {
        if (!('Notification' in window)) {
            console.log('Notifications not supported');
            return;
        }
        
        const permission = await Notification.requestPermission();
        this.permission = permission;
        
        if (permission === 'granted') {
            this.showToast('✅ Notifications enabled!', 'success');
        }
    }

    // Show browser notification with sound
    show(title, body, icon = '/icons/icon-192x192.png', sound = true) {
        // Show browser notification
        if (this.permission === 'granted') {
            const notification = new Notification(title, {
                body: body,
                icon: icon,
                badge: '/icons/icon-72x72.png',
                tag: 'tnt-notification',
                requireInteraction: false,
                silent: false,
                vibrate: [200, 100, 200],
            });

            notification.onclick = () => {
                window.focus();
                notification.close();
            };

            setTimeout(() => notification.close(), 5000);
        }

        // Play sound
        if (sound && this.soundEnabled) {
            this.playSound('notification');
        }

        // Show in-app toast
        this.showToast(body, 'info');
        
        // Update badge
        this.unreadCount++;
        this.updateBadge();
    }

    // Play notification sound
    playSound(type = 'notification') {
        try {
            const audio = new Audio(this.sounds[type] || this.sounds.notification);
            audio.volume = 0.5;
            audio.play().catch(e => console.log('Sound play failed:', e));
        } catch (e) {
            console.log('Audio not supported');
        }
    }

    // Show in-app toast message
    showToast(message, type = 'info') {
        const colors = {
            success: 'bg-green-500',
            error: 'bg-red-500',
            warning: 'bg-yellow-500',
            info: 'bg-blue-500',
        };

        const icons = {
            success: '✅',
            error: '❌',
            warning: '⚠️',
            info: '🔔',
        };

        const toast = document.createElement('div');
        toast.className = `fixed top-4 right-4 ${colors[type]} text-white px-6 py-4 rounded-xl shadow-lg z-50 flex items-center gap-3 animate-slide-in min-w-[300px]`;
        toast.innerHTML = `
            <span class="text-xl">${icons[type]}</span>
            <span class="flex-1 text-sm font-medium">${message}</span>
            <button onclick="this.parentElement.remove()" class="text-white/80 hover:text-white">✕</button>
        `;
        document.body.appendChild(toast);
        
        // Auto remove
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateX(100%)';
            toast.style.transition = 'all 0.3s ease';
            setTimeout(() => toast.remove(), 300);
        }, 4000);
    }

    // Update notification badge
    updateBadge() {
        const badge = document.getElementById('notification-badge');
        if (badge) {
            if (this.unreadCount > 0) {
                badge.textContent = this.unreadCount > 99 ? '99+' : this.unreadCount;
                badge.classList.remove('hidden');
                // Pulse animation
                badge.classList.add('animate-pulse');
                setTimeout(() => badge.classList.remove('animate-pulse'), 1000);
            } else {
                badge.classList.add('hidden');
            }
        }
        
        // Update document title
        if (this.unreadCount > 0) {
            document.title = `(${this.unreadCount}) TNT Construction`;
        } else {
            document.title = 'TNT Construction System';
        }
    }

    // Load unread count from server
    async loadUnreadCount() {
        try {
            const res = await fetch('/notifications/unread-count');
            const data = await res.json();
            this.unreadCount = data.count || 0;
            this.updateBadge();
        } catch (e) {
            console.error('Failed to load notifications');
        }
    }

    // Initialize sound toggle button
    initSoundToggle() {
        const toggle = document.getElementById('sound-toggle');
        if (toggle) {
            toggle.addEventListener('click', () => {
                this.soundEnabled = !this.soundEnabled;
                toggle.textContent = this.soundEnabled ? '🔊' : '🔇';
                this.showToast(
                    this.soundEnabled ? 'Sound enabled' : 'Sound muted',
                    this.soundEnabled ? 'success' : 'warning'
                );
            });
        }
    }

    // Poll for new notifications every 30 seconds
    startPolling() {
        setInterval(() => this.loadUnreadCount(), 30000);
        
        // Also listen for real-time events if Echo is available
        if (window.Echo && window.userId) {
            window.Echo.channel('user.' + window.userId)
                .listen('.notification.sent', (e) => {
                    this.show(
                        e.notification.title || 'New Notification',
                        e.notification.message || e.notification.body,
                        '/icons/icon-192x192.png',
                        true
                    );
                });
        }
    }

    // Send test notification
    test() {
        this.show(
            '🔔 Test Notification',
            'This is a test notification from TNT Construction System!',
            '/icons/icon-192x192.png',
            true
        );
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    window.notificationManager = new NotificationManager();
    
    // Expose to window for manual testing
    window.testNotification = () => window.notificationManager.test();
    window.toggleSound = () => {
        window.notificationManager.soundEnabled = !window.notificationManager.soundEnabled;
    };
});

// Add animation styles
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from { opacity: 0; transform: translateX(100%); }
        to { opacity: 1; transform: translateX(0); }
    }
    .animate-slide-in {
        animation: slideIn 0.3s ease forwards;
    }
    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.2); }
    }
    .animate-pulse {
        animation: pulse 0.3s ease;
    }
`;
document.head.appendChild(style);
