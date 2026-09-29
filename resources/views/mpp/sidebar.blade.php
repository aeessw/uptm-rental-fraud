<aside id="mpp-sidebar" class="fixed left-0 top-0 z-50 hidden w-[260px] flex-col bg-[#1E293B] text-slate-400 md:flex">
    <div class="mpp-sidebar-header flex shrink-0 items-center justify-between border-b border-slate-700/50 px-5 py-6">
        <div class="mpp-brand">
            <h1 class="text-sm font-bold tracking-wide leading-none text-white">UPTM RENTAL</h1>
            <span class="mt-1 block text-[10px] font-semibold tracking-wider text-emerald-400">FRAUD DETECTION</span>
        </div>
        <button id="mpp-sidebar-toggle" type="button" aria-controls="mpp-sidebar" aria-expanded="true" aria-label="Collapse sidebar" title="Collapse sidebar" class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg hover:bg-slate-700 hover:text-white">
            <i class="fa-solid fa-angles-left text-xs" aria-hidden="true"></i>
        </button>
    </div>
    <nav aria-label="MPP navigation" class="mpp-navigation">
        @php
            $mppNavigation = [
                'Moderation' => [
                    ['mpp.dashboard', 'Dashboard', 'fa-table-cells-large'],
                    ['mpp.listings', 'Room Listings', 'fa-house'],
                    ['mpp.reports', 'Fraud Reports', 'fa-flag'],
                ],
                'Administration' => [
                    ['mpp.students', 'Student Accounts', 'fa-users'],
                    ['mpp.audit.logs', 'Audit Logs', 'fa-clock-rotate-left'],
                ],
            ];
        @endphp
        @foreach($mppNavigation as $group => $items)
            <div class="student-nav-group">
                <p class="student-nav-heading">{{ $group }}</p>
                @foreach($items as [$destination, $label, $icon])
                    <a href="{{ route($destination) }}" class="mpp-nav-link {{ request()->routeIs($destination.'*') ? 'is-active' : '' }}" aria-label="{{ $label }}" title="{{ $label }}" @if(request()->routeIs($destination.'*')) aria-current="page" @endif>
                        <i class="fa-solid {{ $icon }}" aria-hidden="true"></i><span class="mpp-nav-label">{{ $label }}</span>
                    </a>
                @endforeach
            </div>
        @endforeach
    </nav>
    <div id="mpp-account" class="relative shrink-0 border-t border-slate-700/50 p-3">
        <button id="mpp-account-toggle" type="button" aria-label="Open MPP account menu" aria-expanded="false" aria-controls="mpp-account-panel" class="mpp-account-trigger flex w-full items-center gap-3 rounded-xl p-2 text-left hover:bg-slate-700">
            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-indigo-500 text-sm font-semibold text-white">{{ strtoupper(substr(Auth::user()->user_name ?? 'M', 0, 1)) }}</span>
            <span class="mpp-account-label min-w-0 flex-1"><span class="block truncate text-xs font-semibold text-white">{{ Auth::user()->user_name }}</span><span class="block truncate text-xs">{{ Auth::user()->user_email }}</span></span>
            <i class="mpp-account-label fa-solid fa-chevron-up text-xs" aria-hidden="true"></i>
        </button>
        <div id="mpp-account-panel" hidden class="account-panel absolute bottom-full left-2 mb-2 w-72 max-w-[calc(100vw-2rem)] rounded-2xl border border-slate-200 bg-white p-1.5 text-slate-800 shadow-xl">
            <div class="account-identity flex items-center gap-3 px-4 py-4">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-indigo-500 text-lg text-white">{{ strtoupper(substr(Auth::user()->user_name ?? 'M', 0, 1)) }}</span>
                <div class="min-w-0 text-slate-500"><p class="truncate font-semibold" title="{{ Auth::user()->user_name }}">{{ Auth::user()->user_name }}</p><p class="truncate text-xs" title="{{ Auth::user()->user_email }}">{{ Auth::user()->user_email }}</p></div>
            </div>
            <button id="mpp-notifications-toggle" type="button" class="account-menu-item" aria-expanded="false" aria-controls="mpp-notifications-panel"><i class="fa-regular fa-bell" aria-hidden="true"></i> Notifications <span id="mpp-notification-count" hidden class="ml-auto rounded-full bg-rose-600 px-2 text-xs text-white"></span></button>
            <div id="mpp-notifications-panel" hidden class="mx-2 mb-3 rounded-xl border border-slate-200 p-3">
                <div class="flex items-center justify-between gap-2"><span class="text-xs font-semibold">Unread notifications</span><button id="mpp-notifications-read" type="button" class="text-xs text-indigo-600">Mark all as read</button></div>
                <p id="mpp-notification-error" role="status" class="mt-2 text-xs text-rose-600"></p>
                <div id="mpp-notifications-list" class="mt-2 space-y-2" style="max-height: 180px; overflow-y: auto;"></div>
            </div>
            <div class="account-appearance" role="group" aria-label="Theme">
                <p class="mb-3 text-xs font-semibold text-slate-500">Appearance</p>
                <div class="account-theme-options">
                    @foreach(['light' => 'sun', 'dark' => 'moon', 'system' => 'laptop'] as $theme => $icon)
                        <button type="button" data-mpp-theme="{{ $theme }}" aria-pressed="false" class="account-menu-item account-theme-option"><i class="fa-solid fa-{{ $icon }}" aria-hidden="true"></i>{{ ucfirst($theme) }}<i class="theme-check fa-solid fa-check ml-auto" aria-hidden="true" hidden></i></button>
                    @endforeach
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST" class="account-signout border-t border-slate-100 py-2">@csrf
                <button type="submit" class="account-menu-item"><i class="fa-solid fa-arrow-right-from-bracket" aria-hidden="true"></i> Log out</button>
            </form>
        </div>
    </div>
</aside>
<script>
(() => {
    const sidebar = document.getElementById('mpp-sidebar');
    const toggle = document.getElementById('mpp-sidebar-toggle');
    const account = document.getElementById('mpp-account');
    const accountToggle = document.getElementById('mpp-account-toggle');
    const panel = document.getElementById('mpp-account-panel');
    const closeAccount = () => { panel.hidden = true; accountToggle.setAttribute('aria-expanded', 'false'); };
    function setCollapsed(collapsed) {
        sidebar.dataset.collapsed = String(collapsed);
        toggle.setAttribute('aria-expanded', String(!collapsed));
        toggle.setAttribute('aria-label', collapsed ? 'Expand sidebar' : 'Collapse sidebar');
        toggle.title = collapsed ? 'Expand sidebar' : 'Collapse sidebar';
        toggle.querySelector('i').classList.toggle('fa-angles-left', !collapsed);
        toggle.querySelector('i').classList.toggle('fa-angles-right', collapsed);
        closeAccount();
    }
    try { setCollapsed(localStorage.getItem('mpp-sidebar-collapsed') === 'true'); } catch (_) {}
    toggle.addEventListener('click', () => {
        const collapsed = sidebar.dataset.collapsed !== 'true';
        setCollapsed(collapsed);
        try { localStorage.setItem('mpp-sidebar-collapsed', String(collapsed)); } catch (_) {}
    });
    accountToggle.addEventListener('click', () => { panel.hidden = !panel.hidden; accountToggle.setAttribute('aria-expanded', String(!panel.hidden)); });
    document.addEventListener('click', (event) => { if (!account.contains(event.target)) closeAccount(); });
    document.addEventListener('keydown', (event) => { if (event.key === 'Escape' && !panel.hidden) { closeAccount(); accountToggle.focus(); } });
    window.addEventListener('resize', closeAccount);
    const systemTheme = window.matchMedia('(prefers-color-scheme: dark)');
    const themeButtons = panel.querySelectorAll('[data-mpp-theme]');
    let preference = 'light';
    try { preference = localStorage.getItem('mpp-theme') || 'light'; } catch (_) {}
    const applyTheme = () => {
        document.body.dataset.theme = preference === 'dark' || (preference === 'system' && systemTheme.matches) ? 'dark' : 'light';
        themeButtons.forEach((button) => {
            const selected = button.dataset.mppTheme === preference;
            button.setAttribute('aria-pressed', String(selected));
            button.querySelector('.theme-check').hidden = !selected;
        });
    };
    themeButtons.forEach((button) => button.addEventListener('click', () => {
        preference = button.dataset.mppTheme;
        try { localStorage.setItem('mpp-theme', preference); } catch (_) {}
        applyTheme();
    }));
    systemTheme.addEventListener('change', applyTheme);
    applyTheme();
})();
</script>

@include('mpp.notifications-script')
