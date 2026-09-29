<script>
(() => {
    const toggle = document.getElementById('mpp-notifications-toggle');
    const panel = document.getElementById('mpp-notifications-panel');
    const badge = document.getElementById('mpp-notification-count');
    const list = document.getElementById('mpp-notifications-list');
    const markAll = document.getElementById('mpp-notifications-read');
    const error = document.getElementById('mpp-notification-error');
    let busy = false;
    async function markRead(key) {
        const response = await fetch(@json(route('mpp.notifications.read')), {
            method: 'POST', headers: {'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': @json(csrf_token())},
            body: JSON.stringify(key ? {key} : {}),
        });
        if (!response.ok || response.redirected) throw new Error('Unable to save read status. Please try again.');
    }
    async function refresh() {
        if (busy || document.hidden) return;
        busy = true;
        try {
            const response = await fetch(@json(route('mpp.notifications')), {headers: {'Accept': 'application/json'}, cache: 'no-store'});
            if (!response.ok || response.redirected) throw new Error();
            const data = await response.json();
            badge.hidden = data.count === 0;
            badge.textContent = data.count > 99 ? '99+' : String(data.count);
            toggle.setAttribute('aria-label', `Notifications, ${data.count} unread`);
            markAll.disabled = data.count === 0;
            list.replaceChildren();
            error.textContent = '';
            data.items.forEach((item) => {
                const link = document.createElement('a');
                link.href = item.url;
                link.className = 'block rounded-lg bg-slate-50 p-2 text-xs hover:bg-indigo-50';
                const title = document.createElement('strong');
                title.className = 'block'; title.textContent = item.title;
                const description = document.createElement('span'); description.textContent = item.description;
                link.append(title, description);
                link.addEventListener('click', async (event) => {
                    if (event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;
                    event.preventDefault();
                    try { await markRead(item.key); window.location.assign(item.url); }
                    catch (e) { error.textContent = e.message; }
                });
                list.appendChild(link);
            });
            if (!data.count) list.textContent = 'You are all caught up.';
            if (data.count > data.items.length) {
                const more = document.createElement('p'); more.className = 'text-xs text-slate-500';
                more.textContent = `Showing the latest ${data.items.length} of ${data.count} unread notifications.`;
                list.appendChild(more);
            }
        } catch (_) { error.textContent = 'Notifications could not be loaded. Please try again.'; }
        finally { busy = false; }
    }
    toggle.addEventListener('click', () => { panel.hidden = !panel.hidden; toggle.setAttribute('aria-expanded', String(!panel.hidden)); if (!panel.hidden) refresh(); });
    markAll.addEventListener('click', async () => {
        markAll.disabled = true;
        try { await markRead(); await refresh(); }
        catch (e) { error.textContent = e.message; }
        finally { markAll.disabled = false; }
    });
    refresh();
    setInterval(refresh, 30000);
    document.addEventListener('visibilitychange', refresh);
})();
</script>
