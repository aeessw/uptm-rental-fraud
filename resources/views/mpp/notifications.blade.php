<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Notifications - UPTM Rental</title>

<!-- Google Font -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

<link
    href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
    rel="stylesheet"
>

<!-- Tailwind -->
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

<!-- Font Awesome -->
<link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous"
>


    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
</head>

<body class="bg-slate-50/80 font-sans antialiased text-slate-800">
@include('mpp.sidebar')
<main class="mpp-notifications-page ml-0 min-w-0 md:ml-[280px]">
    <div class="mpp-page-content space-y-5 p-4 md:p-6">
        @include('mpp.page-heading', ['title' => 'Notifications', 'description' => 'Listing approval requests, fraud reports, and report threshold alerts.'])
        @if(session('success'))<p role="status" class="rounded-xl bg-emerald-50 p-4 text-sm text-emerald-800">{{ session('success') }}</p>@endif
        <form id="remove-notifications" method="POST" action="{{ route('mpp.notifications.destroy') }}">@csrf @method('DELETE')</form>
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm" aria-labelledby="unread-heading">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 p-5">
                <h2 id="unread-heading" class="font-semibold">Unread notifications <span class="ml-2 rounded-full bg-indigo-50 px-2 py-1 text-sm text-indigo-700">{{ $notifications->total() }}</span></h2>
                <div class="flex items-center gap-2">
                    <form method="POST" action="{{ route('mpp.notifications.read') }}">@csrf<button type="submit" @disabled($notifications->total() === 0) class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-indigo-700 hover:bg-indigo-50 disabled:opacity-40">Mark all read</button></form>
                    @if($notifications->isNotEmpty())
                        <button id="toggle-notification-removal" type="button" aria-label="Select notifications to remove" title="Select notifications to remove" aria-expanded="false" aria-controls="notification-selection-toolbar" class="flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 text-slate-500 hover:bg-rose-50 hover:text-rose-700"><i class="fa-regular fa-trash-can" aria-hidden="true"></i></button>
                    @endif
                </div>
            </div>
            @if($notifications->isNotEmpty())
            <div id="notification-selection-toolbar" hidden class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-5 py-3">
                <label class="flex items-center gap-2 text-sm"><input id="select-all-notifications" type="checkbox" class="h-4 w-4 accent-indigo-600"> Select all on this page</label>
                <button id="remove-selected" form="remove-notifications" type="submit" disabled class="rounded-xl bg-rose-50 px-4 py-2 text-sm font-semibold text-rose-700 hover:bg-rose-100 disabled:opacity-40">Remove selected (<span id="selected-count">0</span>)</button>
            </div>
            @endif
            <div class="divide-y divide-slate-100">
                @forelse($notifications as $notification)
                    @php
                        $presentation = match (strtok($notification['key'], ':')) {
                            'approval' => ['icon' => 'fa-clock', 'color' => 'bg-indigo-50 text-indigo-700', 'action' => 'Review listing'],
                            'report' => ['icon' => 'fa-flag', 'color' => 'bg-rose-50 text-rose-700', 'action' => 'View report'],
                            'risk' => ['icon' => 'fa-triangle-exclamation', 'color' => 'bg-amber-50 text-amber-700', 'action' => 'Review reports'],
                            default => ['icon' => 'fa-circle-check', 'color' => 'bg-emerald-50 text-emerald-700', 'action' => 'View update'],
                        };
                    @endphp
                    <article class="relative flex items-center gap-3 bg-indigo-50/40 px-5 py-4">
                        <input hidden type="checkbox" name="keys[]" value="{{ $notification['key'] }}" form="remove-notifications" class="notification-selection relative z-10 h-4 w-4 shrink-0 accent-indigo-600" aria-label="Select notification for {{ $notification['description'] }}">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $presentation['color'] }}"><i class="fa-solid {{ $presentation['icon'] }}" aria-hidden="true"></i></span>
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2"><h3 class="font-semibold">{{ $notification['title'] }}</h3><span class="h-2 w-2 shrink-0 rounded-full bg-indigo-500" role="img" aria-label="Unread notification" title="Unread notification"></span></div>
                            <p class="mt-1 break-words text-sm text-slate-600">{{ $notification['description'] }}</p>
                            @if($notification['created_at'])<time datetime="{{ $notification['created_at'] }}" class="mt-2 block text-xs text-slate-500">{{ \Illuminate\Support\Carbon::parse($notification['created_at'])->diffForHumans() }}</time>@endif
                        </div>
                        <div class="ml-auto flex shrink-0 flex-wrap items-center gap-3">
                            <a href="{{ $notification['url'] }}" class="absolute inset-0 rounded-xl focus-visible:outline focus-visible:outline-2 focus-visible:outline-indigo-500" aria-label="{{ $presentation['action'] }}: {{ $notification['description'] }}"><span class="sr-only">{{ $presentation['action'] }}</span></a>
                            <form class="relative z-10" method="POST" action="{{ route('mpp.notifications.read') }}">@csrf<input type="hidden" name="key" value="{{ $notification['key'] }}"><button type="submit" class="rounded-xl px-3 py-2 text-sm text-slate-600 hover:bg-slate-100">Mark as read</button></form>
                        </div>
                    </article>
                @empty
                    <div class="p-12 text-center"><i class="fa-regular fa-bell mb-4 text-2xl text-slate-400" aria-hidden="true"></i><h3 class="font-semibold">You are all caught up.</h3><p class="mt-2 text-sm text-slate-500">New approval requests and report alerts will appear here.</p></div>
                @endforelse
            </div>
        </section>
        {{ $notifications->links() }}
    </div>
</main>
<script>
(() => {
    const all = document.getElementById('select-all-notifications');
    if (!all) return;
    const selections = [...document.querySelectorAll('.notification-selection')];
    const toggle = document.getElementById('toggle-notification-removal');
    const toolbar = document.getElementById('notification-selection-toolbar');
    let removing = false;
    const update = () => {
        const count = selections.filter(input => input.checked).length;
        document.getElementById('selected-count').textContent = count;
        document.getElementById('remove-selected').disabled = count === 0;
        all.checked = count === selections.length;
        all.indeterminate = count > 0 && count < selections.length;
    };
    toggle.addEventListener('click', () => {
        removing = !removing;
        toolbar.hidden = !removing;
        toggle.setAttribute('aria-expanded', String(removing));
        toggle.setAttribute('aria-label', removing ? 'Cancel notification selection' : 'Select notifications to remove');
        toggle.title = removing ? 'Cancel notification selection' : 'Select notifications to remove';
        selections.forEach(input => { input.hidden = !removing; if (!removing) input.checked = false; });
        update();
    });
    all.addEventListener('change', () => { selections.forEach(input => input.checked = all.checked); update(); });
    selections.forEach(input => input.addEventListener('change', update));
    window.addEventListener('pageshow', update);
    update();
})();
</script>
</body>
</html>
