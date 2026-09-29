<aside id="student-sidebar" class="hidden md:flex w-[260px] h-screen bg-[#1E293B] text-slate-400 border-r border-slate-700/50 flex-col">

    <!-- Sidebar header -->
    <div class="flex items-center justify-between border-b border-slate-700/50 px-5 py-6">
        <div class="student-sidebar-title">
            <h1 class="text-sm font-bold tracking-wide leading-none text-white">UPTM RENTAL</h1>
            <span class="mt-1 block text-[10px] font-semibold tracking-wider text-emerald-400">FRAUD DETECTION</span>
        </div>
        <button type="button" id="student-sidebar-toggle" aria-controls="student-sidebar" aria-expanded="true" aria-label="Collapse sidebar" title="Collapse sidebar" class="shrink-0 p-2 rounded-lg hover:bg-slate-800/80 hover:text-white focus-visible:outline focus-visible:outline-2 focus-visible:outline-blue-500">
            <i class="fa-solid fa-angles-left text-xs" aria-hidden="true"></i>
        </button>
    </div>


    <!-- Primary navigation -->
    <nav aria-label="Student navigation" class="student-navigation">
        @php
            $navigationGroups = [
                'Explore' => [
                    ['student.dashboard', 'Dashboard', 'fa-table-cells-large', request()->routeIs('student.dashboard')],
                    ['student.listings', 'Room Listings', 'fa-house', request()->routeIs('student.listings', 'student.listings.show')],
                    ['student.saved', 'Saved Listings', 'fa-bookmark', request()->routeIs('student.saved')],
                ],
                'Your activity' => [
                    ['student.listings.create', 'Post a Room', 'fa-circle-plus', request()->routeIs('student.listings.create', 'student.listings.edit')],
                    ['student.message.inbox', 'Messages', 'fa-comment-dots', request()->routeIs('student.message.inbox*', 'student.messages')],
                ],
            ];
        @endphp
        @foreach($navigationGroups as $group => $items)
            <div class="student-nav-group">
                <p class="student-nav-heading">{{ $group }}</p>
                @foreach($items as [$destination, $label, $icon, $active])
                    <a href="{{ route($destination) }}" class="student-nav-link {{ $active ? 'is-active' : '' }}" aria-label="{{ $label }}" title="{{ $label }}" @if($active) aria-current="page" @endif>
                        <i class="fa-solid {{ $icon }}" aria-hidden="true"></i>
                        <span class="student-nav-label">{{ $label }}</span>
                        @if($destination === 'student.message.inbox')
                            <span id="unread-message-count" hidden class="hidden student-message-badge" aria-live="polite"></span>
                        @endif
                    </a>
                @endforeach
            </div>
        @endforeach
    </nav>

    @include('student.account-menu')

</aside>

<script>
    (() => {
        const sidebar = document.getElementById('student-sidebar');
        const toggle = document.getElementById('student-sidebar-toggle');
        const icon = toggle.querySelector('i');
        const setCollapsed = (collapsed) => {
            sidebar.dataset.collapsed = String(collapsed);
            toggle.setAttribute('aria-expanded', String(!collapsed));
            toggle.setAttribute('aria-label', collapsed ? 'Expand sidebar' : 'Collapse sidebar');
            toggle.title = collapsed ? 'Expand sidebar' : 'Collapse sidebar';
            icon.classList.toggle('fa-angles-left', !collapsed);
            icon.classList.toggle('fa-angles-right', collapsed);
        };

        try { setCollapsed(localStorage.getItem('student-sidebar-collapsed') === 'true'); } catch (_) {}
        toggle.addEventListener('click', () => {
            const collapsed = sidebar.dataset.collapsed !== 'true';
            setCollapsed(collapsed);
            try { localStorage.setItem('student-sidebar-collapsed', String(collapsed)); } catch (_) {}
        });
    })();
</script>

<script>
    (() => {
        const system = window.matchMedia('(prefers-color-scheme: dark)');
        let preference = 'light';
        try { preference = localStorage.getItem('student-theme') || 'light'; } catch (_) {}
        const apply = () => {
            document.body.dataset.theme = preference === 'dark' || (preference === 'system' && system.matches) ? 'dark' : 'light';
        };
        window.studentTheme = {
            get preference() { return preference; },
            set(value) {
                preference = ['light', 'dark', 'system'].includes(value) ? value : 'light';
                try { localStorage.setItem('student-theme', preference); } catch (_) {}
                apply();
            },
        };
        apply();
        system.addEventListener('change', apply);
    })();
</script>

<script>
(() => {
    const badge = document.getElementById('unread-message-count');
    let busy = false;

    async function updateUnread() {
        if (busy || document.hidden) return;
        busy = true;
        try {
            const response = await fetch(@json(route('student.messages.unread-count')), {headers: {'Accept': 'application/json'}, cache: 'no-store'});
            if (!response.ok || response.redirected) return;
            const {count} = await response.json();
            badge.hidden = count === 0;
            badge.classList.toggle('hidden', count === 0);
            badge.textContent = count > 99 ? '99+' : String(count);
            badge.setAttribute('aria-label', `${count} unread messages`);
        } catch (_) { /* Keep the last count while offline. */ }
        finally { busy = false; }
    }
    updateUnread();
    setInterval(updateUnread, 5000);
    window.addEventListener('messages-read', updateUnread);
    document.addEventListener('visibilitychange', updateUnread);
})();
</script>

@include('student.account-menu-script')
