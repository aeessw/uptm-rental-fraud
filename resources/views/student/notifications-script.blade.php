<script>
(() => {
    const link = document.getElementById('student-notifications-toggle');
    const badge = document.getElementById('student-notification-count');
    let busy = false;
    async function refresh() {
        if (busy || document.hidden) return;
        busy = true;
        try {
            const response = await fetch(@json(route('student.notifications')), {headers: {'Accept': 'application/json'}, cache: 'no-store'});
            if (!response.ok || response.redirected) return;
            const data = await response.json();
            badge.hidden = data.count === 0;
            badge.textContent = data.count > 99 ? '99+' : String(data.count);
            link.setAttribute('aria-label', `Notifications, ${data.count} unread`);
        } catch (_) { /* Keep the last count while offline. */ }
        finally { busy = false; }
    }
    refresh();
    setInterval(refresh, 30000);
    document.addEventListener('visibilitychange', refresh);
})();
</script>
