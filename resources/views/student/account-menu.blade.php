                    <div class="student-account relative mt-auto border-t border-slate-700/50 p-2" id="dashboard-account">
                        <button type="button" id="dashboard-account-toggle" aria-expanded="false" aria-controls="dashboard-account-panel" aria-label="Open account menu" class="student-account-trigger flex w-full items-center gap-3 rounded-xl p-2 text-left text-slate-200 transition hover:bg-slate-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-blue-400">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-indigo-500 text-sm font-semibold text-white">{{ strtoupper(substr(Auth::user()->user_name ?? 'S', 0, 1)) }}</span>
                            <span class="student-account-label min-w-0 flex-1">
                                <span class="block truncate text-xs font-semibold">{{ Auth::user()->user_name }}</span>
                                <span class="block truncate text-xs text-slate-400">{{ Auth::user()->user_email }}</span>
                            </span>
                            <i class="student-account-label fa-solid fa-chevron-up text-xs" aria-hidden="true"></i>
                        </button>
                        <div id="dashboard-account-panel" hidden class="account-panel fixed z-50 w-72 text-slate-800 max-w-[calc(100vw-2rem)] overflow-hidden rounded-xl border border-slate-200 bg-white text-sm shadow-xl">
                            <div class="account-identity flex items-center gap-3 px-4 py-4">
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-indigo-500 text-lg text-white">{{ strtoupper(substr(Auth::user()->user_name ?? 'S', 0, 1)) }}</span>
                                <div class="min-w-0 text-slate-500">
                                    <p class="truncate font-semibold" title="{{ Auth::user()->user_name }}">{{ Auth::user()->user_name }}</p>
                                    <p class="truncate text-xs" title="{{ Auth::user()->user_email }}">{{ Auth::user()->user_email }}</p>
                                </div>
                            </div>
                            <div class="py-2">
                                <a href="{{ route('student.profile') }}" class="account-menu-item"><i class="fa-regular fa-user" aria-hidden="true"></i> Account settings</a>
                                <button type="button" id="dashboard-notifications-toggle" aria-expanded="false" aria-controls="dashboard-notifications-panel" class="account-menu-item"><i class="fa-regular fa-bell" aria-hidden="true"></i> Notifications <span id="dashboard-notification-count" hidden class="hidden ml-auto rounded-full bg-[#D72035] px-2 text-xs text-white"></span></button>
                            </div>
                            <div id="dashboard-notifications-panel" hidden class="mx-2 mb-3 rounded-xl border border-slate-200 p-3">
                                <div class="flex items-center justify-between gap-2"><span class="text-xs font-semibold">Notifications</span><button type="button" id="mark-all-notifications-read" class="text-xs text-indigo-600">Mark all as read</button></div>
                                <p id="student-notification-error" role="status" class="mt-2 text-xs text-rose-600"></p>
                                <div id="dashboard-notifications-list" class="mt-2 space-y-2" style="max-height: 180px; overflow-y: auto;"></div>
                            </div>
                            <div class="account-appearance" role="group" aria-label="Theme">
                                <p class="mb-3 text-xs font-semibold text-slate-500">Appearance</p>
                                <div class="account-theme-options">
                                @foreach(['light' => 'sun', 'dark' => 'moon', 'system' => 'laptop'] as $theme => $icon)
                                    <button type="button" data-theme-choice="{{ $theme }}" aria-pressed="false" class="account-menu-item account-theme-option"><i class="fa-solid fa-{{ $icon }}" aria-hidden="true"></i> {{ ucfirst($theme) }}<i class="theme-check fa-solid fa-check ml-auto" aria-hidden="true" hidden></i></button>
                                @endforeach
                                </div>
                            </div>
                            <form action="{{ route('logout') }}" method="POST" class="account-signout border-t border-slate-100 py-2">
                                @csrf
                                <button type="submit" class="account-menu-item"><i class="fa-solid fa-arrow-right-from-bracket" aria-hidden="true"></i> Log out</button>
                            </form>
                        </div>

                    </div>
