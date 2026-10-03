<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications - UPTM Rental</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
</head>
<body class="bg-slate-50 text-slate-800 antialiased">
<div class="min-h-screen flex">
@include('student.sidebar')
<main class="student-notifications-page flex min-w-0 flex-1 flex-col">
    <div class="w-full space-y-5 p-4 sm:p-6 lg:p-8">
        <header><h1>Notifications</h1><p class="mt-2 text-sm text-slate-500">View approval and rejection updates for submitted listings.</p></header>
        @if(session('success'))<p role="status" class="rounded-xl bg-emerald-50 p-4 text-sm text-emerald-800">{{ session('success') }}</p>@endif
        @if($errors->any())<p role="alert" class="rounded-xl bg-rose-50 p-4 text-sm text-rose-800">{{ $errors->first() }}</p>@endif
        <form id="remove-notifications" method="POST" action="{{ route('student.notifications.destroy') }}">@csrf @method('DELETE')</form>
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm" aria-labelledby="notifications-heading">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-5 py-4">
                <h2 id="notifications-heading">All notifications <span class="ml-2 rounded-full bg-indigo-50 px-2 py-1 text-sm text-indigo-700">{{ $count }} unread</span></h2>
                <div class="flex items-center gap-2">
                    <form method="POST" action="{{ route('student.notifications.read') }}">@csrf<button type="submit" @disabled($count === 0) class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-indigo-700 hover:bg-indigo-50 disabled:opacity-40">Mark all read</button></form>
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
                    @php($rejected = $notification->notification_decision === 'rejected')
                    <article class="relative flex items-start gap-3 px-5 py-4 {{ $notification->notification_read_at ? '' : 'bg-indigo-50/40' }}">
                        <input hidden type="checkbox" name="keys[]" value="{{ $notification->notification_id }}" form="remove-notifications" class="notification-selection relative z-10 mt-3 h-4 w-4 shrink-0 accent-indigo-600" aria-label="Select notification for {{ $notification->listing_title }}">
                        <span class="hidden sm:flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $rejected ? 'bg-rose-50 text-rose-700' : 'bg-emerald-50 text-emerald-700' }}"><i class="fa-solid {{ $rejected ? 'fa-circle-xmark' : 'fa-circle-check' }}" aria-hidden="true"></i></span>
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2"><h3>{{ $rejected ? 'Your listing was not approved' : 'Your listing has been approved' }}</h3>@unless($notification->notification_read_at)<span class="h-2 w-2 shrink-0 rounded-full bg-indigo-500" role="img" aria-label="Unread notification" title="Unread notification"></span>@endunless</div>
                            <p class="mt-1 break-words text-sm text-slate-600">{{ $notification->listing_title }}</p>
                            @if($rejected && $notification->notification_reason)<p class="mt-1 break-words text-sm text-rose-700">Reason: {{ $notification->notification_reason }}</p>@endif
                            <time datetime="{{ \Illuminate\Support\Carbon::parse($notification->notification_created_at)->toIso8601String() }}" class="mt-2 block text-xs text-slate-500">{{ $rejected ? 'Rejected' : 'Approved' }} &middot; {{ \Illuminate\Support\Carbon::parse($notification->notification_created_at)->diffForHumans() }}</time>
                            <div>
                                <form method="POST" action="{{ route('student.notifications.read') }}">@csrf<input type="hidden" name="key" value="{{ $notification->notification_id }}"><button name="open" value="1" aria-label="View listing: {{ $notification->listing_title }}" class="absolute inset-0 cursor-pointer rounded-xl focus-visible:outline focus-visible:outline-2 focus-visible:outline-indigo-500"><span class="sr-only">View listing</span></button></form>
                            </div>
                        </div>
                        @unless($notification->notification_read_at)<form class="relative z-10 ml-auto shrink-0 self-center" method="POST" action="{{ route('student.notifications.read') }}">@csrf<input type="hidden" name="key" value="{{ $notification->notification_id }}"><button class="rounded-xl px-3 py-2 text-sm text-slate-600 hover:bg-slate-100">Mark as read</button></form>@endunless
                    </article>
                @empty
                    <div class="p-12 text-center"><i class="fa-regular fa-bell mb-4 text-2xl text-slate-400" aria-hidden="true"></i><h3>No notifications yet</h3><p class="mt-2 text-sm text-slate-500">Approval and rejection updates for submitted listings will appear here.</p></div>
                @endforelse
            </div>
        </section>
        {{ $notifications->links() }}
    </div>
</main>
</div>
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