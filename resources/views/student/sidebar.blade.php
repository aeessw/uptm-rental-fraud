<aside class="w-64 bg-[#1E293B] text-slate-400 flex flex-col justify-between shrink-0 hidden md:flex">
    <div>
        <div class="p-6 border-b border-slate-700/50">
            <h1 class="font-bold text-white text-sm tracking-wide leading-none">UPTM RENTAL</h1>
            <span class="text-[10px] text-emerald-400 font-semibold tracking-wider block mt-1">FRAUD DETECTION</span>
        </div>

        <nav class="p-4 space-y-1 text-xs font-medium">
            <a href="{{ route('student.dashboard') }}" 
               class="flex items-center space-x-3 px-4 py-3 rounded-xl transition {{ request()->routeIs('student.dashboard') ? 'bg-slate-700/60 text-white' : 'hover:bg-slate-800/80 hover:text-white' }}">
                <i class="fa-solid fa-border-all w-4 text-center"></i>
                <span>Dashboard</span>
            </a>

            <a href="{{ route('student.listings') }}" 
               class="flex items-center space-x-3 px-4 py-3 rounded-xl transition {{ request()->routeIs('student.listings') ? 'bg-slate-700/60 text-white' : 'hover:bg-slate-800/80 hover:text-white' }}">
                <i class="fa-solid fa-house w-4 text-center"></i>
                <span>Room Listings</span>
            </a>

            <a href="{{ route('student.listings.create') }}" 
               class="flex items-center space-x-3 px-4 py-3 rounded-xl transition {{ request()->routeIs('student.listings.create') ? 'bg-slate-700/60 text-white' : 'hover:bg-slate-800/80 hover:text-white' }}">
                <i class="fa-solid fa-circle-plus w-4 text-center"></i>
                <span>Post a Room</span>
            </a>

            <a href="{{ route('student.message.inbox') }}" 
               class="flex items-center space-x-3 px-4 py-3 rounded-xl transition {{ request()->routeIs('student.message.inbox*') ? 'bg-slate-700/60 text-white' : 'hover:bg-slate-800/80 hover:text-white' }}">
                <i class="fa-regular fa-envelope w-4 text-center"></i>
                <span>Messages</span>
            </a>
        </nav>
    </div>

    <div class="p-4 border-t border-slate-700/50">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="w-full flex items-center space-x-3 px-4 py-3 text-slate-400 hover:bg-red-500/10 hover:text-red-400 rounded-xl transition font-medium text-xs">
                <i class="fa-solid fa-right-from-bracket w-4 text-center"></i>
                <span>Logout</span>
            </button>
        </form>
    </div>
</aside>