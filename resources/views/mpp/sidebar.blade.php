<aside
    class="fixed left-0 top-0 z-50 hidden h-screen w-64
           flex-col justify-between
           bg-[#1E293B] text-slate-400
           md:flex"
>

    <!-- ================= TOP SIDEBAR ================= -->
    <div>

        <!-- ================= LOGO ================= -->
        <div class="border-b border-slate-700/50 p-6">

            <h1 class="text-sm font-bold tracking-wide leading-none text-white">
                UPTM RENTAL
            </h1>

            <span
                class="mt-1 block text-[10px] font-semibold
                       tracking-wider text-emerald-400"
            >
                FRAUD DETECTION
            </span>

        </div>


        <!-- ================= NAVIGATION ================= -->
        <nav class="space-y-1 p-4 text-xs font-medium">

            <!-- Dashboard -->
            <a
                href="{{ route('mpp.dashboard') }}"
                class="flex items-center space-x-3 rounded-xl px-4 py-3 transition
                {{ request()->routeIs('mpp.dashboard')
                    ? 'bg-slate-700/60 text-white'
                    : 'hover:bg-slate-800/80 hover:text-white' }}"
            >

                <i class="fa-solid fa-border-all w-4 text-center"></i>

                <span>Dashboard</span>

            </a>


            <!-- Listings -->
            <a
                href="{{ route('mpp.listings') }}"
                class="flex items-center space-x-3 rounded-xl px-4 py-3 transition
                {{ request()->routeIs('mpp.listings*')
                    ? 'bg-slate-700/60 text-white'
                    : 'hover:bg-slate-800/80 hover:text-white' }}"
            >

                <i class="fa-solid fa-house w-4 text-center"></i>

                <span>Listings</span>

            </a>


            <!-- Reports -->
            <a
                href="{{ route('mpp.reports') }}"
                class="flex items-center space-x-3 rounded-xl px-4 py-3 transition
                {{ request()->routeIs('mpp.reports*')
                    ? 'bg-slate-700/60 text-white'
                    : 'hover:bg-slate-800/80 hover:text-white' }}"
            >

                <i class="fa-solid fa-triangle-exclamation w-4 text-center"></i>

                <span>Reports</span>

            </a>


            <!-- Student Accounts -->
            <a
                href="{{ route('mpp.students') }}"
                class="flex items-center space-x-3 rounded-xl px-4 py-3 transition
                {{ request()->routeIs('mpp.students*')
                    ? 'bg-slate-700/60 text-white'
                    : 'hover:bg-slate-800/80 hover:text-white' }}"
            >

                <i class="fa-solid fa-users w-4 text-center"></i>

                <span>Student Accounts</span>

            </a>


            <!-- Audit Logs -->
            <a
                href="{{ route('mpp.audit.logs') }}"
                class="flex items-center space-x-3 rounded-xl px-4 py-3 transition
                {{ request()->routeIs('mpp.audit.logs*')
                    ? 'bg-slate-700/60 text-white'
                    : 'hover:bg-slate-800/80 hover:text-white' }}"
            >

                <i class="fa-solid fa-clipboard-list w-4 text-center"></i>

                <span>Audit Logs</span>

            </a>

        </nav>

    </div>


    <!-- ================= LOGOUT ================= -->
    <div class="border-t border-slate-700/50 p-4">

        <form action="{{ route('logout') }}" method="POST">

            @csrf

            <button
                type="submit"
                class="flex w-full items-center space-x-3
                       rounded-xl px-4 py-3
                       text-xs font-medium text-slate-400
                       transition
                       hover:bg-red-500/10
                       hover:text-red-400"
            >

                <i class="fa-solid fa-right-from-bracket w-4 text-center"></i>

                <span>Logout</span>

            </button>

        </form>

    </div>

</aside>