<script>
(() => {
    const account = document.getElementById('dashboard-account');
    const trigger = document.getElementById('dashboard-account-toggle');
    const menu = document.getElementById('dashboard-account-panel');
    const notifications = document.getElementById('dashboard-notifications-panel');
    const notificationToggle = document.getElementById('dashboard-notifications-toggle');
    const closePanels = () => {
        menu.hidden = true;
        notifications.hidden = true;
        trigger.setAttribute('aria-expanded', 'false');
        notificationToggle.setAttribute('aria-expanded', 'false');
    };
    const positionPanels = () => {
        const rect = trigger.getBoundingClientRect();
        [menu].forEach((panel) => {
            panel.style.left = `${Math.max(8, Math.min(rect.left, window.innerWidth - 296))}px`;
            panel.style.bottom = `${Math.max(8, window.innerHeight - rect.top + 8)}px`;
            panel.style.maxHeight = `${Math.max(120, rect.top - 16)}px`;
            panel.style.overflowY = 'auto';
        });
    };
    window.addEventListener('resize', () => { closePanels(); positionPanels(); });
    document.addEventListener('scroll', (event) => { if (!account.contains(event.target)) closePanels(); }, true);
    // Scrolling within either popup should keep it open.
    [menu, notifications].forEach((panel) => panel.addEventListener('scroll', (event) => event.stopPropagation()));
    document.getElementById('student-sidebar-toggle').addEventListener('click', closePanels);
    trigger.addEventListener('click', () => {
        positionPanels();
        const opening = menu.hidden;
        closePanels();
        menu.hidden = !opening;
        trigger.setAttribute('aria-expanded', String(opening));
    });
    document.addEventListener('click', (event) => {
        if (!account.contains(event.target)) closePanels();
    });
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && (!menu.hidden || !notifications.hidden)) {
            closePanels();
            trigger.focus();
        }
    });
    const choices = account.querySelectorAll('[data-theme-choice]');
    const updateChoices = () => choices.forEach((button) => {
        const selected = button.dataset.themeChoice === window.studentTheme.preference;
        button.setAttribute('aria-pressed', String(selected));
        button.querySelector('.theme-check').hidden = !selected;
    });
    choices.forEach((button) => button.addEventListener('click', () => {
        window.studentTheme.set(button.dataset.themeChoice);
        updateChoices();
    }));
    updateChoices();
})();

(() => {
    const toggle = document.getElementById('dashboard-notifications-toggle');
    const panel = document.getElementById('dashboard-notifications-panel');
    const countBadge = document.getElementById('dashboard-notification-count');
    const list = document.getElementById('dashboard-notifications-list');
    const markAllButton = document.getElementById('mark-all-notifications-read');
    const listingUrl = @json(route('student.listings.show', ['listing' => '__LISTING_ID__']));
    const error = document.getElementById('student-notification-error');
    const seenKey = @json('student-seen-listings-'.Auth::id());
    let seenListings = [];
    try { seenListings = JSON.parse(localStorage.getItem(seenKey) || '[]'); if (!Array.isArray(seenListings)) seenListings = []; } catch (_) {}
    let currentListingIds = [];
    let busy = false;

    toggle.addEventListener('click', () => {
        const isOpen = !panel.hidden;
        panel.hidden = isOpen;
        toggle.setAttribute('aria-expanded', String(!isOpen));
        if (!isOpen) updateNotifications();
    });

    markAllButton.addEventListener('click', async () => {
        markAllButton.disabled = true;
        try {
            const response = await fetch(@json(route('student.messages.read-all')), {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': @json(csrf_token()),
                },
            });
            if (!response.ok || response.redirected) throw new Error();
            seenListings = [...new Set([...seenListings, ...currentListingIds])].slice(-1000);
            try { localStorage.setItem(seenKey, JSON.stringify(seenListings)); } catch (_) {}
            window.dispatchEvent(new Event('messages-read'));
            await updateNotifications();
        } catch (_) {
            error.textContent = 'Unable to mark notifications as read. Please try again.';
        } finally {
            markAllButton.disabled = false;
        }
    });

    function renderNotifications(data) {
        const listings = (data.new_listings || []).filter((listing) => !seenListings.includes(listing.id));
        currentListingIds = listings.map((listing) => listing.id);
        const messages = data.unread_messages || [];
        const total = Number(data.count || 0) + listings.length;
        countBadge.hidden = total === 0;
        countBadge.classList.toggle('hidden', total === 0);
        countBadge.textContent = total > 99 ? '99+' : String(total);
        list.innerHTML = '';
        error.textContent = '';
        markAllButton.disabled = total === 0;
        toggle.setAttribute('aria-label', `Notifications, ${total} unread`);

        messages.forEach((message) => {
            const messageLink = document.createElement('a');
            messageLink.href = @json(route('student.message.inbox'));
            messageLink.className = 'block rounded-xl bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-700 transition hover:bg-brand-50 hover:text-brand-700';
            messageLink.textContent = `New message from ${message.sender}`;
            list.appendChild(messageLink);
        });

        if (data.count > messages.length) {
            const messageSummary = document.createElement('a');
            messageSummary.href = @json(route('student.message.inbox'));
            messageSummary.className = 'block px-3 py-1 text-xs text-slate-500 hover:text-brand-700';
            messageSummary.textContent = `+ ${data.count - messages.length} more unread message${data.count - messages.length === 1 ? '' : 's'}`;
            list.appendChild(messageSummary);
        }

        listings.forEach((listing) => {
            const listingLink = document.createElement('a');
            listingLink.href = listingUrl.replace('__LISTING_ID__', listing.id);
            listingLink.className = 'block rounded-xl bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-700 transition hover:bg-brand-50 hover:text-brand-700';
            listingLink.textContent = `New listing: ${listing.title}`;
            list.appendChild(listingLink);
        });

        if (total === 0) {
            const emptyState = document.createElement('p');
            emptyState.className = 'px-3 py-2 text-xs text-slate-500';
            emptyState.textContent = 'You are all caught up.';
            list.appendChild(emptyState);
        }
    }

    async function updateNotifications() {
        if (busy || document.hidden) return;
        busy = true;
        try {
            const response = await fetch(@json(route('student.messages.unread-count')), {headers: {'Accept': 'application/json'}, cache: 'no-store'});
            if (!response.ok || response.redirected) throw new Error();
            renderNotifications(await response.json());
        } catch (_) {
            error.textContent = 'Notifications could not be loaded. Please try again.';
        } finally {
            busy = false;
        }
    }

    updateNotifications();
    setInterval(updateNotifications, 5000);
    window.addEventListener('messages-read', updateNotifications);
    document.addEventListener('visibilitychange', updateNotifications);
})();
</script>
