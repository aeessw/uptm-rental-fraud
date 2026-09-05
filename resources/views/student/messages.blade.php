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

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 9999px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
</head>

<body class="bg-slate-50/80 font-sans text-slate-800 antialiased overflow-hidden">

<div class="flex h-screen w-full overflow-hidden">

    @include('student.sidebar')

    <main class="flex-1 flex flex-col min-w-0 h-full overflow-hidden">

        <header class="bg-white/90 backdrop-blur-md border-b border-slate-200/60 px-8 py-4 flex items-center justify-between shrink-0 z-10">
            <div>
                <h2 class="text-base font-bold text-slate-900 tracking-tight">
                    Messages
                </h2>
            </div>

            <div class="flex items-center space-x-3">
                <div class="text-right">
                    <p class="text-xs font-bold text-slate-900 leading-none mb-1">
                        {{ Auth::user()->name }}
                    </p>
                    <p class="text-[11px] text-slate-400 font-medium leading-none">
                        {{ Auth::user()->email }}
                    </p>
                </div>

                <div class="w-9 h-9 rounded-full bg-brand-50 text-brand-600 font-bold flex items-center justify-center text-xs border border-brand-100 ring-2 ring-brand-500/10">
                    {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                </div>
            </div>
        </header>

        <div class="flex-1 p-6 lg:p-8 overflow-hidden bg-slate-50/80">
            <div class="max-w-7xl h-full mx-auto grid grid-cols-12 gap-6">

                <div class="col-span-12 md:col-span-5 lg:col-span-4 bg-white rounded-2xl border border-slate-200/60 shadow-xs flex flex-col overflow-hidden">

                    <div class="p-5 border-b border-slate-100 bg-white/50 backdrop-blur-sm">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-sm font-bold text-slate-900">Inbox</h3>
                                <p class="text-[10px] text-slate-500 mt-0.5">Your ongoing conversations</p>
                            </div>

                            <div class="flex h-7 min-w-[28px] items-center justify-center rounded-full bg-brand-50 px-2 text-[10px] font-bold text-brand-600 border border-brand-100">
                                {{ $users->count() }}
                            </div>
                        </div>
                    </div>

                    <div class="flex-1 overflow-y-auto custom-scrollbar bg-white">
                        @if($users->count() > 0)
                            @foreach($users as $chatUser)
                                <a href="{{ route('student.messages', ['userId' => $chatUser->id]) }}"
                                   class="block border-b border-slate-100 px-5 py-4 transition-all duration-200 hover:bg-slate-50/80 {{ isset($user) && $user->id == $chatUser->id ? 'bg-brand-50/40 border-l-4 border-l-brand-600' : 'border-l-4 border-l-transparent' }}">

                                    <div class="flex items-center space-x-3.5">
                                        <div class="relative shrink-0">
                                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-100 text-xs font-bold text-slate-600 border border-slate-200/60 {{ isset($user) && $user->id == $chatUser->id ? 'bg-brand-100 text-brand-700 border-brand-200' : '' }}">
                                                {{ strtoupper(substr($chatUser->name, 0, 2)) }}
                                            </div>
                                        </div>

                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-center justify-between mb-1">
                                                <h4 class="truncate text-xs font-bold text-slate-900 group-hover:text-brand-600 transition">
                                                    {{ $chatUser->name }}
                                                </h4>
                                                <span class="text-[9px] font-medium text-slate-400 shrink-0 ml-2">
                                                    {{ $chatUser->last_message_at ? $chatUser->last_message_at->format('h:i A') : '' }}
                                                </span>
                                            </div>

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
                                Keep all discussions within this channel to ensure student identity verification and prevent fraud.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-span-12 md:col-span-7 lg:col-span-8 bg-white rounded-2xl border border-slate-200/60 shadow-xs flex flex-col overflow-hidden">

                    <div class="p-5 border-b border-slate-100 flex items-center space-x-3.5 bg-white shrink-0">
                        @if($user)
                            <div class="w-10 h-10 rounded-full bg-emerald-50 text-emerald-600 font-bold flex items-center justify-center text-xs border border-emerald-100 shrink-0 ring-2 ring-emerald-500/10">
                                {{ strtoupper(substr($user->name, 0, 2)) }}
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-slate-900">{{ $user->name }}</h4>
                            </div>
                        @else
                            <div class="w-10 h-10 rounded-full bg-slate-50 text-slate-400 font-bold flex items-center justify-center text-xs border border-slate-200 shrink-0">
                                <i class="fa-regular fa-comments"></i>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-slate-900">No conversation selected</h4>
                                <p class="text-[10px] text-slate-500 mt-0.5">Select a conversation from the inbox.</p>
                            </div>
                        @endif
                    </div>

                    <div id="chatBox" class="flex-1 overflow-y-auto p-6 space-y-4 bg-slate-50/60 custom-scrollbar relative shadow-inner">
                        @if(session('success'))
                            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl text-xs flex items-center space-x-2">
                                <i class="fa-solid fa-circle-check text-emerald-600"></i>
                                <span class="font-medium">{{ session('success') }}</span>
                            </div>
                        @endif

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
                                <p class="text-[11px] text-slate-500 mt-1.5 max-w-sm leading-relaxed">
                                    Select a student from the inbox to view your secure conversation.
                                </p>
                            </div>
                        @elseif($messages->isEmpty())
                            <div class="flex flex-col items-center justify-center h-full text-center py-10">
                                <div class="w-12 h-12 bg-white text-brand-600 rounded-2xl border border-slate-200/60 shadow-sm flex items-center justify-center mb-3">
                                    <i class="fa-regular fa-paper-plane text-lg"></i>
                                </div>
                                <h3 class="text-xs font-bold text-slate-800">No messages yet</h3>
                                <p class="text-[11px] text-slate-500 mt-1 max-w-sm leading-relaxed">
                                    Start the conversation by sending a message below.
                                </p>
                            </div>
                        @else
                            @php $lastDate = null; @endphp
                            @foreach($messages as $msg)
                                @php $msgDate = $msg->created_at->format('Y-m-d'); @endphp

                                @if($lastDate !== $msgDate)
                                    <div class="my-5 flex items-center justify-center">
                                        <span class="rounded-full bg-white border border-slate-200 px-3.5 py-1 text-[10px] font-bold text-slate-500 shadow-xs">
                                            @if($msg->created_at->isToday()) Today
                                            @elseif($msg->created_at->isYesterday()) Yesterday
                                            @else {{ $msg->created_at->format('d M Y') }}
                                            @endif
                                        </span>
                                    </div>
                                    @php $lastDate = $msgDate; @endphp
                                @endif

                                @if($msg->sender_id == Auth::id())
                                    <div class="flex flex-col items-end space-y-1">
                                        <div class="relative max-w-md rounded-2xl rounded-tr-sm bg-brand-600 px-4 py-3 shadow-sm text-xs leading-relaxed text-white border border-brand-700/50">
                                            <span class="inline-block pr-14 break-words">
                                                {{ $msg->message }}
                                            </span>
                                            <span class="absolute bottom-1.5 right-2.5 flex items-center space-x-1 text-[9px] font-medium text-brand-200 select-none">
                                                <span>{{ $msg->created_at->format('h:i A') }}</span>
                                                @if(isset($msg->is_read) && $msg->is_read)
                                                    <i class="fa-solid fa-check-double text-cyan-300"></i>
                                                @elseif(isset($msg->is_delivered) && $msg->is_delivered)
                                                    <i class="fa-solid fa-check-double text-brand-200/70"></i>
                                                @else
                                                    <i class="fa-solid fa-check text-brand-200/70"></i>
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                @else
                                    <div class="flex flex-col items-start space-y-1">
                                        <div class="relative max-w-md rounded-2xl rounded-tl-sm bg-white border border-slate-200/80 px-4 py-3 shadow-sm text-xs leading-relaxed text-slate-800">
                                            <span class="inline-block pr-12 break-words font-medium">
                                                {{ $msg->message }}
                                            </span>
                                            <span class="absolute bottom-1.5 right-2.5 text-[9px] font-medium text-slate-400 select-none">
                                                {{ $msg->created_at->format('h:i A') }}
                                            </span>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        @endif
                    </div>

                    <div class="p-5 border-t border-slate-100 bg-white shrink-0">
                        @if($user)
                            <form action="{{ route('student.messages.store') }}" method="POST" class="space-y-3">
                                @csrf
                                <input type="hidden" name="receiver_id" value="{{ $user->id }}">

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

                            <p class="text-[10px] font-medium text-slate-400 text-center mt-3 flex items-center justify-center space-x-1.5">
                                <i class="fa-solid fa-shield-halved text-emerald-500"></i>
                                <span>Messages are securely encrypted.</span>
                            </p>
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

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const chatBox = document.getElementById("chatBox");
        if (chatBox) {
            chatBox.scrollTop = chatBox.scrollHeight;
        }
    });
</script>
</body>
</html>