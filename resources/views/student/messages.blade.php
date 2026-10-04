<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - UPTM Rental Fraud Detection</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#EEF2FF',
                            100: '#E0E7FF',
                            200: '#C7D2FE',
                            500: '#6366F1',
                            600: '#4F46E5',
                            700: '#4338CA',
                            900: '#1E1B4B',
                        }
                    }
                }
            }
        }
    </script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous">

    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 9999px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
</head>

<body class="bg-slate-50/80 font-sans text-slate-800 antialiased overflow-hidden">

<div class="flex h-screen w-full overflow-hidden">

    @include('student.sidebar')

    <main class="flex-1 flex flex-col min-w-0 h-full overflow-hidden">


        <div class="flex-1 p-6 lg:p-8 overflow-hidden bg-slate-50/80">
            <div class="max-w-7xl h-full mx-auto grid grid-cols-12 gap-6">

                <div class="col-span-12 md:col-span-5 lg:col-span-4 bg-white rounded-2xl border border-slate-200/60 shadow-xs flex flex-col overflow-hidden">

                    <div class="p-5 border-b border-slate-100 bg-white/50 backdrop-blur-sm">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-sm font-bold text-slate-900">Inbox</h3>
                                <p class="text-[10px] text-slate-500 mt-0.5">Your ongoing conversations</p>
                            </div>

                        </div>
                    </div>

                    <div class="flex items-center gap-2 border-b border-slate-100 bg-white px-4 py-3">
                        <div class="relative min-w-0 flex-1">
                            <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-[11px] text-slate-400"></i>
                            <input type="search" id="conversation-search" placeholder="Search name" aria-label="Search conversations by name" class="w-full rounded-lg border border-slate-200 bg-white py-2 pl-8 pr-3 text-xs font-medium text-slate-800 placeholder-slate-400 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20">
                        </div>
                        <select id="conversation-filter" aria-label="Filter conversations" class="rounded-lg border border-slate-200 bg-white px-2.5 py-2 text-xs font-semibold text-slate-600 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20">
                            <option value="all">All</option>
                            <option value="unread">Unread</option>
                            <option value="read">Read</option>
                        </select>
                    </div>

                    <p id="conversation-switch-status" role="status" class="hidden px-4 py-2 text-xs text-slate-500"></p>
                    <div id="conversation-list" class="flex-1 overflow-y-auto custom-scrollbar bg-white">
                        @if($users->count() > 0)
                            @php
                                $profileColors = [
                                    'bg-emerald-100 text-emerald-700 border-emerald-200',
                                    'bg-sky-100 text-sky-700 border-sky-200',
                                    'bg-violet-100 text-violet-700 border-violet-200',
                                    'bg-amber-100 text-amber-700 border-amber-200',
                                    'bg-rose-100 text-rose-700 border-rose-200',
                                    'bg-cyan-100 text-cyan-700 border-cyan-200',
                                    'bg-indigo-100 text-indigo-700 border-indigo-200',
                                    'bg-orange-100 text-orange-700 border-orange-200',
                                ];
                            @endphp
                            @foreach($users as $chatUser)
                                @php $avatarClass = $profileColors[abs((int) $chatUser->getKey()) % count($profileColors)]; @endphp
                                <a href="{{ route('student.messages', ['userId' => $chatUser->getKey()]) }}"
                                              data-conversation-item
                                              data-conversation-name="{{ strtolower($chatUser->user_name) }}"
                                              data-unread="{{ ($chatUser->unread_count ?? 0) > 0 ? 'true' : 'false' }}"
                                   class="block border-b border-slate-100 px-5 py-4 transition-all duration-200 hover:bg-slate-50/80 {{ isset($user) && $user->getKey() == $chatUser->getKey() ? 'bg-brand-50/40 border-l-4 border-l-brand-600' : 'border-l-4 border-l-transparent' }}">

                                    <div class="flex items-center space-x-3.5">
                                        <div class="relative shrink-0">
                                            <div class="flex h-10 w-10 items-center justify-center rounded-full border text-xs font-bold {{ $avatarClass }} {{ isset($user) && $user->getKey() == $chatUser->getKey() ? 'ring-2 ring-brand-200/70' : '' }}">
                                                {{ strtoupper(substr($chatUser->user_name, 0, 1)) }}
                                            </div>
                                        </div>

                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-center justify-between mb-1">
                                                <h4 class="truncate text-xs font-bold text-slate-900 group-hover:text-brand-600 transition">
                                                    {{ $chatUser->user_name }}
                                                </h4>
                                                <span class="text-[9px] font-medium text-slate-400 shrink-0 ml-2">
                                                    @if($chatUser->last_message_at)
                                                        @if($chatUser->last_message_at->isToday())
                                                            {{ $chatUser->last_message_at->format('h:i A') }}
                                                        @elseif($chatUser->last_message_at->isYesterday())
                                                            Yesterday
                                                        @else
                                                            {{ $chatUser->last_message_at->format('d/m/y') }}
                                                        @endif
                                                    @endif
                                                </span>
                                            </div>

                                            @if($chatUser->user_suspended)
                                                <p class="mb-1 text-[11px] font-semibold text-rose-700">Account Suspended</p>
                                            @endif
                                            <div class="flex items-center justify-between space-x-2">
                                                <p class="truncate text-[11px] {{ ($chatUser->unread_count ?? 0) > 0 ? 'font-semibold text-slate-900' : 'text-slate-500' }}">
                                                    {{ $chatUser->last_message }}
                                                </p>

                                                @if(isset($chatUser->unread_count) && $chatUser->unread_count > 0)
                                                    <span class="flex h-4 min-w-[16px] items-center justify-center rounded-full bg-brand-600 px-1 text-[9px] font-bold text-white shadow-xs shrink-0">
                                                        {{ $chatUser->unread_count }}
                                                    </span>
                                                @endif
                                            </div>

                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        @else
                            <div class="flex flex-col items-center justify-center px-6 py-20 text-center">
                                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-400 mb-4 shadow-sm border border-slate-200/50">
                                    <i class="fa-regular fa-comments text-lg"></i>
                                </div>
                                <h3 class="text-xs font-bold text-slate-800">No conversations yet</h3>
                                <p class="mt-1 max-w-[200px] text-[10px] leading-relaxed text-slate-500">
                                    Start a conversation by messaging a room owner.
                                </p>
                            </div>
                        @endif
                    </div>

                    <div class="border-t border-slate-100 bg-slate-50/50 p-4 shrink-0">
                        <div class="flex items-start space-x-2.5">
                            <i class="fa-solid fa-shield-halved mt-0.5 text-[11px] text-emerald-500"></i>
                            <p class="text-[10px] font-medium leading-relaxed text-slate-500">
                                Keep conversations on the platform to protect privacy and help prevent fraud.
                            </p>
                        </div>
                    </div>
                </div>

                <div id="conversation-panel" data-access-state="{{ (int) $isSuspended }}-{{ (int) $isBlocked }}-{{ (int) $isBlockedBy }}-{{ (int) $canViewListing }}-{{ (int) ($user?->user_suspended ?? false) }}" data-read-url="{{ $user ? route('student.messages.read', $user->getKey()) : '' }}" class="col-span-12 md:col-span-7 lg:col-span-8 bg-white rounded-2xl border border-slate-200/60 shadow-xs flex flex-col overflow-hidden">

                    <div class="p-5 border-b border-slate-100 flex items-center justify-between gap-3.5 bg-white shrink-0">
                        @if($user)
                            <div class="flex min-w-0 items-center gap-3.5">
                            @php
                                $selectedUserColors = [
                                    'bg-emerald-100 text-emerald-700 border-emerald-200 ring-emerald-500/10',
                                    'bg-sky-100 text-sky-700 border-sky-200 ring-sky-500/10',
                                    'bg-violet-100 text-violet-700 border-violet-200 ring-violet-500/10',
                                    'bg-amber-100 text-amber-700 border-amber-200 ring-amber-500/10',
                                    'bg-rose-100 text-rose-700 border-rose-200 ring-rose-500/10',
                                    'bg-cyan-100 text-cyan-700 border-cyan-200 ring-cyan-500/10',
                                    'bg-indigo-100 text-indigo-700 border-indigo-200 ring-indigo-500/10',
                                    'bg-orange-100 text-orange-700 border-orange-200 ring-orange-500/10',
                                ];
                                $selectedAvatarClass = $selectedUserColors[abs((int) $user->getKey()) % count($selectedUserColors)];
                            @endphp
                            <div class="w-10 h-10 rounded-full border font-bold flex items-center justify-center text-xs shrink-0 ring-2 {{ $selectedAvatarClass }}">
                                {{ strtoupper(substr($user->user_name, 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <h4 class="truncate text-sm font-bold text-slate-900">{{ $user->user_name }}</h4>
                                @if($user->user_suspended)
                                    <span class="mt-1 inline-flex rounded-full bg-rose-50 px-2 py-1 text-xs font-semibold text-rose-700">Account Suspended</span>

                                @endif
                            </div>
                            </div>
                            <div class="ml-auto flex shrink-0 items-center gap-1">
                            <button id="message-search-toggle" type="button" aria-label="Search conversation" title="Search conversation" aria-expanded="false" aria-controls="message-search-panel" class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-50">
                                <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                            </button>
                                <details id="conversation-actions" class="relative">
                                    <summary aria-label="Conversation actions" title="Conversation actions" class="flex h-10 w-10 cursor-pointer list-none items-center justify-center rounded-lg text-slate-500 hover:bg-slate-50 [&::-webkit-details-marker]:hidden">
                                        <i class="fa-solid fa-ellipsis-vertical" aria-hidden="true"></i>
                                    </summary>
                                    <div class="absolute right-0 top-full z-30 mt-1 w-48 rounded-xl border border-slate-200 bg-white p-1.5 shadow-lg">
                                        @if($conversationListing)
                                            <button type="button" aria-label="Report user or listing" onclick="this.closest('details').open = false; document.getElementById('chat-report-modal').classList.remove('hidden')" class="w-full rounded-lg px-3 py-2 text-left text-xs font-semibold text-slate-600 hover:bg-slate-50">Report user</button>
                                        @endif
                                        @unless($user->user_suspended)
                                            <form method="POST" action="{{ $isBlocked ? route('student.users.unblock', $user) : route('student.users.block', $user) }}">
                                                @csrf
                                                <button type="submit" class="w-full rounded-lg px-3 py-2 text-left text-xs font-semibold text-slate-600 hover:bg-slate-50">{{ $isBlocked ? 'Unblock user' : 'Block user' }}</button>
                                            </form>
                                        @endunless
                                        <form method="POST" action="{{ route('student.messages.delete', $user->getKey()) }}" onsubmit="return confirm('Delete conversation?\nThis conversation will be removed from your inbox.');">
                                            @csrf
                                            <button type="submit" class="w-full rounded-lg px-3 py-2 text-left text-xs font-semibold text-rose-600 hover:bg-rose-50">Delete conversation</button>
                                        </form>
                                    </div>
                                </details>
                            </div>
                        @else
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-full bg-slate-50 text-slate-400 font-bold flex items-center justify-center text-xs border border-slate-200 shrink-0">
                                    <i class="fa-regular fa-comments"></i>
                                </div>
                                <div>
                                    <h4 class="text-xs font-bold text-slate-900">No conversation selected</h4>
                                    <p class="text-[10px] text-slate-500 mt-0.5">Select a conversation from the inbox.</p>
                                </div>
                            </div>
                        @endif
                    </div>

                    @if($user && $conversationListing)
                        <div class="shrink-0 border-b border-slate-100 bg-white px-5 py-3">
                            <a @if($canViewListing) href="{{ route('student.listings.show', $conversationListing->getKey()) }}" @else aria-disabled="true" @endif class="group flex items-center gap-3 rounded-xl border border-slate-200 bg-white p-2.5 shadow-sm {{ $canViewListing ? 'transition hover:border-brand-200 hover:bg-brand-50/30' : '' }}">
                                <div class="h-16 w-20 shrink-0 overflow-hidden rounded-lg bg-slate-100">
                                    @if($conversationListing->listing_photo)
                                        <img src="{{ asset('storage/' . $conversationListing->listing_photo) }}" alt="{{ $conversationListing->listing_title }}" class="h-full w-full object-cover">
                                    @else
                                        <div class="flex h-full items-center justify-center text-slate-300"><i class="fa-solid fa-house" aria-hidden="true"></i></div>
                                    @endif
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h3 class="truncate text-xs font-bold text-slate-900">{{ $conversationListing->listing_title }}</h3>
                                    <div class="mt-1 flex flex-wrap items-center gap-x-2 gap-y-0.5 text-[10px] font-medium text-slate-500">
                                        <span><i class="fa-solid fa-location-dot mr-1 text-slate-400" aria-hidden="true"></i>{{ $conversationListing->listing_location }}</span>
                                        <span><i class="fa-solid fa-door-open mr-1 text-slate-400" aria-hidden="true"></i>{{ $conversationListing->room_type }}</span>
                                    </div>
                                    <p class="mt-1 text-xs font-extrabold text-brand-600">RM {{ number_format($conversationListing->listing_rent, 2) }} <span class="font-medium text-slate-400">/month</span></p>
                                </div>
                                <span class="shrink-0 rounded-lg border border-slate-200 px-2.5 py-1.5 text-[10px] font-bold text-slate-600">
                                    @if($canViewListing) View room <i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i> @else Room unavailable @endif
                                </span>
                            </a>
                        </div>
                    @endif

                    @if($user)
                        <div id="message-search-panel" class="hidden flex flex-wrap items-center gap-2 border-b border-slate-100 bg-white px-5 py-3">
                            <input id="message-search" type="search" aria-label="Search messages in this conversation" placeholder="Search this conversation" class="min-w-0 flex-1 rounded-lg border border-slate-200 px-3 py-2 text-xs">
                            <span id="message-search-count" role="status" class="text-xs text-slate-500"></span>
                            <button id="message-search-next" type="button" disabled class="rounded-lg px-3 py-2 text-xs text-brand-600 disabled:opacity-50">Next match</button>
                            <button id="message-search-close" type="button" aria-label="Close conversation search" title="Close search" class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-50">
                                <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                            </button>
                        </div>
                    @endif
                    <div data-through-id="{{ $messages->where('receiver_id', Auth::id())->max('message_id') ?? 0 }}" id="chatBox" class="flex-1 overflow-y-auto p-6 space-y-4 bg-slate-50/60 custom-scrollbar relative shadow-inner">
                        @if(session('error'))
                            <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl text-xs flex items-center space-x-2">
                                <i class="fa-solid fa-circle-exclamation text-rose-500"></i>
                                <span class="font-medium">{{ session('error') }}</span>
                            </div>
                        @endif

                        @if($errors->any())
                            <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl text-xs space-y-1.5">
                                @foreach($errors->all() as $error)
                                    <div class="flex items-center space-x-1.5">
                                        <i class="fa-solid fa-circle-exclamation text-rose-500"></i>
                                        <span class="font-medium">{{ $error }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        @if(!$user)
                            <div class="flex flex-col items-center justify-center h-full text-center py-10">
                                <div class="w-14 h-14 bg-white text-brand-600 rounded-2xl border border-slate-200/60 shadow-sm flex items-center justify-center mb-4">
                                    <i class="fa-regular fa-comments text-xl"></i>
                                </div>
                                <h3 class="text-sm font-bold text-slate-800">No conversation selected</h3>
                            </div>
                        @elseif($messages->isEmpty())
                            <div class="flex flex-col items-center justify-center h-full text-center py-10">
                                <div class="w-12 h-12 bg-white text-brand-600 rounded-2xl border border-slate-200/60 shadow-sm flex items-center justify-center mb-3">
                                    <i class="fa-regular fa-paper-plane text-lg"></i>
                                </div>
                                <h3 class="text-xs font-bold text-slate-800">No messages yet</h3>
                                <p class="text-[11px] text-slate-500 mt-1 max-w-sm leading-relaxed">
                                    {{ $isSuspended ? 'Messaging is unavailable while an account is suspended.' : 'Start the conversation by sending a message below.' }}
                                </p>
                            </div>
                        @else
                            @php $lastDate = null; @endphp
                            @foreach($messages as $msg)
                                @php $msgDate = $msg->message_created_at->format('Y-m-d'); @endphp

                                @if($lastDate !== $msgDate)
                                    <div class="my-5 flex items-center justify-center">
                                        <span class="rounded-full bg-white border border-slate-200 px-3.5 py-1 text-[10px] font-bold text-slate-500 shadow-xs">
                                            @if($msg->message_created_at->isToday()) Today
                                            @elseif($msg->message_created_at->isYesterday()) Yesterday
                                            @else {{ $msg->message_created_at->format('d/m/y') }}
                                            @endif
                                        </span>
                                    </div>
                                    @php $lastDate = $msgDate; @endphp
                                @endif

                                @if($msg->sender_id == Auth::id())
                                    <div class="flex flex-col items-end space-y-1">
                                        <div class="relative max-w-md rounded-2xl rounded-tr-sm bg-[#EEF2F6] px-4 py-3 shadow-sm text-xs leading-relaxed text-slate-700 border border-[#D9E2EC]">
                                            <span data-message-text class="inline-block pr-14 break-words">
                                                {{ $msg->message_content }}
                                            </span>
                                            <span class="absolute bottom-1.5 right-2.5 flex items-center space-x-1 text-[9px] font-medium text-slate-500 select-none">
                                                <span>{{ $msg->message_created_at->format('h:i A') }}</span>
                                                @if($msg->message_read_at)
                                                    <i class="fa-solid fa-check-double text-blue-500" title="Read" aria-label="Read"></i>

                                                @else
                                                    <i class="fa-solid fa-check-double text-slate-400" title="Unread" aria-label="Unread"></i>
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                @else
                                    <div class="flex flex-col items-start space-y-1">
                                        <div class="relative max-w-md rounded-2xl rounded-tl-sm bg-white border border-slate-200/80 px-4 py-3 shadow-sm text-xs leading-relaxed text-slate-800">
                                            <span data-message-text class="inline-block pr-12 break-words font-medium">
                                                {{ $msg->message_content }}
                                            </span>
                                            <span class="absolute bottom-1.5 right-2.5 text-[9px] font-medium text-slate-400 select-none">
                                                {{ $msg->message_created_at->format('h:i A') }}
                                            </span>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        @endif
                    </div>

                    <div class="p-5 border-t border-slate-100 bg-white shrink-0">
                        @if($user)
                            @if($isSuspended)
                                <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3" role="status">
                                    <p class="text-xs font-bold text-rose-800">{{ $user->user_suspended ? 'Account Suspended' : 'Your Account Is Suspended' }}</p>
                                    <p class="mt-1 text-xs leading-relaxed text-rose-700">This account has been suspended by MPP Admin.</p>
                                </div>
                            @elseif($isBlocked)
                                <div class="rounded-xl border border-rose-100 bg-rose-50 px-4 py-3 text-center">
                                    <p class="text-xs font-bold text-rose-700">You blocked this user.</p>
                                    <p class="mt-1 text-[11px] text-rose-600">Unblock them to continue messaging.</p>
                                    <div class="mt-3 flex justify-center gap-2">
                                        <form action="{{ route('student.messages.delete', $user->getKey()) }}" method="POST" onsubmit="return confirm('Delete conversation?\nThis conversation will be removed from your inbox.');">
                                            @csrf
                                            <button type="submit" class="rounded-lg border border-rose-200 bg-white px-3 py-2 text-xs font-bold text-rose-700 transition hover:bg-rose-100">Delete chat</button>
                                        </form>
                                        <form action="{{ route('student.messages.unblock', $user->getKey()) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="rounded-lg bg-brand-600 px-3 py-2 text-xs font-bold text-white transition hover:bg-brand-700">Unblock</button>
                                        </form>
                                    </div>
                                </div>
                            @elseif($isBlockedBy)
                                <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-center" role="status">
                                    <p class="text-xs font-semibold text-slate-700">Messaging is unavailable for this conversation.</p>
                                </div>
                            @else
                                <form id="message-compose-form" action="{{ route('student.messages.store') }}" method="POST" class="space-y-3">
                                    @csrf
                                    <p id="message-send-error" role="alert" class="hidden text-xs text-rose-700"></p>
                                    <input type="hidden" name="receiver_id" value="{{ $user->getKey() }}">
                                    <input type="hidden" name="listing_id" value="{{ request('listing_id') ?: $messages->firstWhere('listing_id', '!=', null)?->listing_id }}">

                                    @if($messages->isEmpty())
                                    <div id="quick-replies" class="flex flex-wrap gap-2" aria-label="Quick replies">
                                        @foreach(['Is this still available?', 'Can I view the room?', 'What is included in the rent?', 'When can I move in?'] as $reply)
                                            <button type="button" data-quick-reply class="rounded-full border border-slate-200 bg-slate-50 px-3 py-2 text-xs text-slate-700 hover:bg-brand-50">{{ $reply }}</button>
                                        @endforeach
                                    </div>
                                    @endif
                                    <div class="flex items-center space-x-3">
                                        <input type="text" name="message" maxlength="5000" placeholder="Type your message..."
                                               class="flex-1 bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-xs text-slate-800 placeholder:text-slate-400 focus:outline-none focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 transition-all font-medium"
                                               required autocomplete="off">

                                        <button type="submit"
                                                class="bg-brand-600 hover:bg-brand-700 active:bg-brand-800 text-white font-bold text-xs px-5 py-3 rounded-xl shadow-sm hover:shadow transition-all flex items-center space-x-2 shrink-0">
                                            <span>Send</span>
                                            <i class="fa-solid fa-paper-plane text-[11px]"></i>
                                        </button>
                                    </div>
                                </form>
                            @endif

                        @else
                            <div class="text-center py-2">
                                <p class="text-xs font-medium text-slate-400">
                                    Select a conversation to start messaging.
                                </p>
                            </div>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </main>
</div>

@if($user && $conversationListing)
    <div id="chat-report-modal" class="fixed inset-0 z-[200] hidden flex items-center justify-center bg-slate-950/50 p-4 backdrop-blur-sm">
        <div class="w-full max-w-md rounded-2xl bg-white p-5 shadow-2xl">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Report user/listing</h2>
                    <p class="mt-1 text-xs text-slate-500">Report suspicious activity related to {{ $conversationListing->listing_title }}.</p>
                </div>
                <button type="button" onclick="document.getElementById('chat-report-modal').classList.add('hidden')" class="text-slate-400 transition hover:text-slate-700" aria-label="Close report dialog" title="Close report dialog">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form action="{{ route('student.listings.report', $conversationListing->getKey()) }}" method="POST" class="mt-5 space-y-4">
                @csrf
                <input type="hidden" name="blocked_user_id" value="{{ $user->getKey() }}">
                <fieldset>
                    <legend class="block text-xs font-bold text-slate-700">Action</legend>
                    <div class="mt-2 space-y-2">
                        <label class="flex cursor-pointer items-start gap-2 rounded-lg border border-slate-200 p-2.5 text-xs text-slate-700">
                            <input type="radio" name="action" value="report" checked class="mt-0.5 text-brand-600 focus:ring-brand-500">
                            <span><strong>Report only</strong><br><span class="text-[11px] text-slate-500">Send the report without blocking this user.</span></span>
                        </label>
                        <label class="flex cursor-pointer items-start gap-2 rounded-lg border border-slate-200 p-2.5 text-xs text-slate-700">
                            <input type="radio" name="action" value="report_and_block" class="mt-0.5 text-rose-600 focus:ring-rose-500">
                            <span><strong>Report and block</strong><br><span class="text-[11px] text-slate-500">Report this listing and stop messages from this user.</span></span>
                        </label>
                    </div>
                </fieldset>
                <label for="chat-report-reason" class="block text-xs font-bold text-slate-700">Reason</label>
                <textarea id="chat-report-reason" name="reason" rows="4" maxlength="1000" required placeholder="Describe the suspicious activity..." class="w-full resize-none rounded-xl border border-slate-200 px-3 py-2.5 text-xs text-slate-800 placeholder:text-slate-400 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20"></textarea>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="document.getElementById('chat-report-modal').classList.add('hidden')" class="rounded-xl border border-slate-200 px-4 py-2 text-xs font-bold text-slate-600 transition hover:bg-slate-50">Cancel</button>
                    <button type="submit" class="rounded-xl bg-rose-600 px-4 py-2 text-xs font-bold text-white transition hover:bg-rose-700">Submit report</button>
                </div>
            </form>
        </div>
    </div>
@endif



<script>
    document.addEventListener('DOMContentLoaded', function() {
        const chatBox = document.getElementById("chatBox");
        if (chatBox) {
            chatBox.scrollTop = chatBox.scrollHeight;
        }
    });
</script>
<script>
(() => {
    const searchInput = document.getElementById('conversation-search');
    const filterSelect = document.getElementById('conversation-filter');

    function filterConversations() {
        const search = (searchInput?.value || '').trim().toLowerCase();
        const filter = filterSelect?.value || 'all';
        const items = Array.from(document.querySelectorAll('[data-conversation-item]'));
        let visibleCount = 0;

        items.forEach((item) => {
            const matchesName = item.dataset.conversationName.includes(search);
            const matchesFilter = filter === 'all' || item.dataset.unread === (filter === 'unread' ? 'true' : 'false');
            const visible = matchesName && matchesFilter;
            item.classList.toggle('hidden', !visible);
            if (visible) visibleCount += 1;
        });

        let emptyState = document.getElementById('conversation-filter-empty');
        if (!emptyState && visibleCount === 0 && items.length > 0) {
            emptyState = document.createElement('p');
            emptyState.id = 'conversation-filter-empty';
            emptyState.className = 'px-5 py-10 text-center text-xs font-medium text-slate-400';
            document.getElementById('conversation-list').appendChild(emptyState);
        }
        if (emptyState) {
            emptyState.textContent = filter === 'unread' ? 'No unread conversations.' : (filter === 'read' ? 'No read conversations.' : 'No conversations found.');
            emptyState.classList.toggle('hidden', visibleCount > 0 || items.length === 0);
        }
    }

    searchInput?.addEventListener('input', filterConversations);
    filterSelect?.addEventListener('change', filterConversations);
    window.addEventListener('conversation-list-updated', filterConversations);
    filterConversations();
})();
</script>
<script>
(() => {
    let readUrl = document.getElementById('conversation-panel').dataset.readUrl;
    let switching = false;
    let switchController;
    let activeUrl = window.location.href;
    const drafts = new Map();
    const csrf = @json(csrf_token());
    let busy = false;
    let lastRead = 0;
    let sending = false;
    let revision = 0;
    async function acknowledge() {
        const box = document.getElementById('chatBox');
        if (switching) return;
        const readRevision = revision;
        const through = Number(box.dataset.throughId);
        if (document.getElementById('message-search')?.value.trim() || !readUrl || document.hidden || !document.hasFocus() || through <= lastRead || box.scrollHeight - box.scrollTop - box.clientHeight > 80) return;
        const response = await fetch(readUrl, {
            method: 'POST', headers: {'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf},
            body: JSON.stringify({through_id: through})
        });
        if (response.ok && readRevision === revision) {
            lastRead = through;
            window.dispatchEvent(new Event('messages-read'));
        }
    }
    function renderConversation(html) {
            const doc = new DOMParser().parseFromString(html, 'text/html');
            const box = document.getElementById('chatBox');
            const nextBox = doc.getElementById('chatBox');
            const list = document.getElementById('conversation-list');
            const nextList = doc.getElementById('conversation-list');
            if (!nextBox || !nextList) return;
            const panel = document.getElementById('conversation-panel');
            const nextPanel = doc.getElementById('conversation-panel');
            if (nextPanel && panel.dataset.accessState !== nextPanel.dataset.accessState) {
                const input = panel.querySelector('input[name="message"]');
                if (input) drafts.set(activeUrl, input.value);
                const previousScroll = box.scrollTop;
                panel.replaceWith(nextPanel);
                const restoredInput = nextPanel.querySelector('input[name="message"]');
                if (restoredInput) restoredInput.value = drafts.get(activeUrl) || '';
                nextPanel.querySelector('#chatBox').scrollTop = previousScroll;
                list.innerHTML = nextList.innerHTML;
                document.getElementById('chat-report-modal')?.remove();
                const modal = doc.getElementById('chat-report-modal');
                if (modal) document.body.appendChild(modal);
                window.dispatchEvent(new Event('conversation-list-updated'));
                window.dispatchEvent(new Event('conversation-switched'));
                return;
            }
            const atBottom = box.scrollHeight - box.scrollTop - box.clientHeight < 80;
            if (list.innerHTML !== nextList.innerHTML) {
                list.innerHTML = nextList.innerHTML;
                window.dispatchEvent(new Event('conversation-list-updated'));
            }
            if (box.innerHTML !== nextBox.innerHTML) {
                const previousScroll = box.scrollTop;
                box.innerHTML = nextBox.innerHTML;
                box.scrollTop = atBottom ? box.scrollHeight : previousScroll;
            }
            if (!doc.getElementById('quick-replies')) document.getElementById('quick-replies')?.remove();
            window.dispatchEvent(new Event('conversation-messages-updated'));
            box.dataset.throughId = nextBox.dataset.throughId;
    }
    async function refresh() {
        if (busy || sending || switching || document.hidden) return;
        const requestRevision = revision;
        busy = true;
        try {
            const response = await fetch(activeUrl, {headers: {'Accept': 'text/html'}, cache: 'no-store'});
            if (!response.ok || response.redirected) return;
            const html = await response.text();
            if (requestRevision !== revision || sending) return;
            renderConversation(html);
            await acknowledge();
        } catch (_) { /* Retry on the next interval after a connection failure. */ }
        finally { busy = false; }
    }
    document.addEventListener('DOMContentLoaded', () => {
        const box = document.getElementById('chatBox');
        box.scrollTop = box.scrollHeight;
        acknowledge().catch(() => {});
        document.addEventListener('scroll', event => {
            if (event.target.id === 'chatBox') acknowledge().catch(() => {});
        }, true);
    });
    document.addEventListener('submit', async event => {
        const compose = event.target;
        if (compose.id !== 'message-compose-form') return;
        event.preventDefault();
        if (sending || switching || !compose.reportValidity()) return;
        const input = compose.querySelector('input[name="message"]');
        if (!input.value.trim()) { input.focus(); return; }
        const draft = input.value;
        const button = compose.querySelector('button[type="submit"]');
        const error = document.getElementById('message-send-error');
        sending = true;
        revision++;
        button.disabled = true;
        button.setAttribute('aria-busy', 'true');
        error.classList.add('hidden');
        try {
            const response = await fetch(compose.action, {
                method: 'POST', body: new FormData(compose),
                headers: {'Accept': 'application/json', 'X-CSRF-TOKEN': csrf}
            });
            const data = await response.json().catch(() => ({}));
            if (!response.ok || response.redirected) throw new Error(data.message || 'Unable to send. Please refresh if your session has expired.');
            if (input.value === draft) input.value = '';
            document.getElementById('quick-replies')?.remove();
            if (data.html) renderConversation(data.html);
            const box = document.getElementById('chatBox');
            box.scrollTop = box.scrollHeight;
            input.focus();
        } catch (failure) {
            error.textContent = failure.message || 'Connection interrupted. Your draft is still here; check the conversation before retrying.';
            error.classList.remove('hidden');
        } finally {
            sending = false;
            revision++;
            button.disabled = false;
            button.removeAttribute('aria-busy');
            document.getElementById('conversation-switch-status').classList.add('hidden');
        }
    });
    async function switchConversation(url, push = true) {
        const status = document.getElementById('conversation-switch-status');
        if (sending) {
            status.textContent = 'Please wait for your message to finish sending.';
            status.classList.remove('hidden');
            if (!push) history.replaceState(null, '', activeUrl);
            return;
        }
        switchController?.abort();
        const controller = new AbortController();
        switchController = controller;
        const token = ++revision;
        switching = true;
        const oldPanel = document.getElementById('conversation-panel');
        const draft = oldPanel.querySelector('input[name="message"]');
        if (draft) drafts.set(activeUrl, draft.value);
        oldPanel.inert = true;
        oldPanel.setAttribute('aria-busy', 'true');
        status.textContent = 'Loading conversation...';
        status.classList.remove('hidden');
        try {
            const response = await fetch(url, {headers: {'Accept': 'text/html'}, cache: 'no-store', signal: controller.signal});
            if (!response.ok || response.redirected) throw new Error('Unable to open this conversation. Please try again or refresh to sign in.');
            const doc = new DOMParser().parseFromString(await response.text(), 'text/html');
            if (token !== revision) return;
            const panel = doc.getElementById('conversation-panel');
            const nextList = doc.getElementById('conversation-list');
            if (!panel || !nextList) throw new Error('Unable to open this conversation. Please try again.');
            oldPanel.replaceWith(panel);
            const list = document.getElementById('conversation-list');
            const scroll = list.scrollTop;
            list.innerHTML = nextList.innerHTML;
            list.scrollTop = scroll;
            document.getElementById('chat-report-modal')?.remove();
            const modal = doc.getElementById('chat-report-modal');
            if (modal) document.body.appendChild(modal);
            activeUrl = url;
            if (push && location.href !== url) history.pushState(null, '', url);
            readUrl = panel.dataset.readUrl;
            lastRead = 0;
            const input = panel.querySelector('input[name="message"]');
            if (input) input.value = drafts.get(url) || '';
            const box = panel.querySelector('#chatBox');
            box.scrollTop = box.scrollHeight;
            window.dispatchEvent(new Event('conversation-list-updated'));
            window.dispatchEvent(new Event('conversation-switched'));
            status.classList.add('hidden');
        } catch (error) {
            if (token !== revision || error.name === 'AbortError') return;
            status.textContent = error.message || 'Connection interrupted. Please try again.';
            if (!push) history.replaceState(null, '', activeUrl);
        } finally {
            if (token === revision) {
                switching = false;
                const panel = document.getElementById('conversation-panel');
                panel.inert = false;
                panel.removeAttribute('aria-busy');
                acknowledge().catch(() => {});
            }
        }
    }
    document.addEventListener('click', event => {
        const link = event.target.closest('[data-conversation-item]');
        if (!link || event.button !== 0 || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;
        event.preventDefault();
        switchConversation(link.href);
    });
    window.addEventListener('popstate', () => switchConversation(location.href, false));
    setInterval(refresh, 5000);
    window.addEventListener('focus', refresh);
    document.addEventListener('visibilitychange', () => { if (!document.hidden) refresh(); });
})();
</script>
<script>
(() => {
    document.addEventListener('click', event => {
        const actions = document.getElementById('conversation-actions');
        if (actions && !actions.contains(event.target)) actions.open = false;
    });
    document.addEventListener('keydown', event => {
        const actions = document.getElementById('conversation-actions');
        if (event.key === 'Escape' && actions?.open) {
            actions.open = false;
            actions.querySelector('summary').focus();
        }
    });
    let cleanup = () => {};
    function initializeConversationControls() {
    cleanup();
    document.querySelectorAll('[data-quick-reply]').forEach(button => {
        button.addEventListener('click', () => {
            const input = document.querySelector('input[name="message"]');
            if (!input) return;
            input.value = button.textContent.trim();
            input.focus();
        });
    });
    const search = document.getElementById('message-search');
    const count = document.getElementById('message-search-count');
    const next = document.getElementById('message-search-next');
    if (!search) return;
    const toggle = document.getElementById('message-search-toggle');
    const panel = document.getElementById('message-search-panel');
    function setSearchOpen(open) {
        panel.classList.toggle('hidden', !open);
        toggle.setAttribute('aria-expanded', String(open));
        toggle.setAttribute('aria-label', open ? 'Close conversation search' : 'Search conversation');
        toggle.title = open ? 'Close conversation search' : 'Search conversation';
        if (open) search.focus();
        else {
            search.value = '';
            update();
            toggle.focus();
        }
    }
    toggle.addEventListener('click', () => setSearchOpen(toggle.getAttribute('aria-expanded') !== 'true'));
    document.getElementById('message-search-close').addEventListener('click', () => setSearchOpen(false));
    panel.addEventListener('keydown', event => {
        if (event.key === 'Escape') { event.preventDefault(); setSearchOpen(false); }
    });
    let matches = [], current = -1;
    function update(scroll = false) {
        const query = search.value.trim().toLocaleLowerCase();
        matches = [];
        document.querySelectorAll('[data-message-text]').forEach(el => {
            const hit = query && el.textContent.toLocaleLowerCase().includes(query);
            el.style.outline = hit ? '2px solid #818cf8' : '';
            el.style.outlineOffset = hit ? '4px' : '';
            if (hit) matches.push(el);
        });
        next.disabled = matches.length === 0;
        count.textContent = query ? `${matches.length} matches` : '';
        current = -1;
        if (scroll && matches.length) move();
    }
    function move() {
        current = (current + 1) % matches.length;
        const box = document.getElementById('chatBox');
        const rect = matches[current].getBoundingClientRect();
        box.scrollTop += rect.top - box.getBoundingClientRect().top - box.clientHeight / 2;
        count.textContent = `${current + 1} of ${matches.length}`;
    }
    search.addEventListener('input', () => update(true));
    next.addEventListener('click', () => { if (matches.length) move(); });
    const onMessagesUpdated = () => update();
    window.addEventListener('conversation-messages-updated', onMessagesUpdated);
    cleanup = () => window.removeEventListener('conversation-messages-updated', onMessagesUpdated);
    }
    initializeConversationControls();
    window.addEventListener('conversation-switched', initializeConversationControls);
})();
</script>
</body>
</html>
