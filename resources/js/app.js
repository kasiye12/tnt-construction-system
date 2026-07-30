import './echo';
import './notifications';

// Request notification permission
document.addEventListener('DOMContentLoaded', () => {
    if ('Notification' in window && Notification.permission === 'default') {
        Notification.requestPermission();
    }
    console.log('✅ TNT Construction App Ready');
});
