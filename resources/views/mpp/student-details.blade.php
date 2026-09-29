@foreach($users as $user)
<dialog id="student-details-{{ $user->getKey() }}" aria-labelledby="student-title-{{ $user->getKey() }}" class="m-auto w-full max-w-xl rounded-2xl bg-white p-6 text-slate-800 shadow-xl backdrop:bg-slate-900/50" style="max-height:85dvh;overflow-y:auto">
    <div class="flex items-center justify-between"><h2 id="student-title-{{ $user->getKey() }}" class="text-lg font-bold">Student details</h2><form method="dialog"><button aria-label="Close student details" class="rounded-lg px-3 py-2"><i class="fa-solid fa-xmark" aria-hidden="true"></i></button></form></div>
    <div class="my-5 flex items-center gap-3"><span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-indigo-100 font-bold text-indigo-700">{{ strtoupper(substr($user->user_name, 0, 2)) }}</span><div class="min-w-0"><p class="break-words font-semibold">{{ $user->user_name }}</p><p class="break-words text-sm text-slate-500">{{ $user->user_email }}</p></div></div>
    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 rounded-xl bg-slate-50 p-4 text-sm">
        <div><dt class="text-slate-500">Status</dt><dd class="font-semibold">{{ $user->user_suspended ? 'Suspended' : 'Active' }}</dd></div>
        <div><dt class="text-slate-500">Account type</dt><dd>Student</dd></div>
        <div><dt class="text-slate-500">Listings posted</dt><dd><a class="text-indigo-600" href="{{ route('mpp.listings', ['user_id' => $user->getKey()]) }}">{{ $user->listings_count }} &middot; View listings</a></dd></div>
        <div><dt class="text-slate-500">Active listings</dt><dd>{{ $user->active_listings_count }}</dd></div>
        <div><dt class="text-slate-500">Reported listings</dt><dd>{{ $user->reported_listings_count }}</dd></div>
        <div><dt class="text-slate-500">Reports received</dt><dd><a class="text-indigo-600" href="{{ route('mpp.reports', ['user_id' => $user->getKey()]) }}">{{ $user->received_reports_count }} &middot; View reports</a></dd></div>
    </dl>
    <h3 class="mb-2 mt-5 font-semibold">Recent audit activity</h3>
    @include('mpp.student-activity', ['user' => $user])
    <div class="mt-6 flex flex-wrap justify-end gap-3"><a class="rounded-lg bg-indigo-50 px-4 py-2 text-sm text-indigo-700" href="{{ route('mpp.listings', ['user_id' => $user->getKey()]) }}">View All Listings</a><a class="rounded-lg bg-indigo-50 px-4 py-2 text-sm text-indigo-700" href="{{ route('mpp.reports', ['user_id' => $user->getKey()]) }}">View Reports</a><a class="rounded-lg bg-indigo-50 px-4 py-2 text-sm text-indigo-700" href="{{ route('mpp.audit.logs', ['user_id' => $user->getKey()]) }}">View Full Audit Activity</a>
    @if(!$user->user_suspended)<button type="button" class="rounded-lg bg-rose-600 px-4 py-2 text-sm text-white" onclick="document.getElementById('student-details-{{ $user->getKey() }}').close(); document.getElementById('suspend-student-{{ $user->getKey() }}').showModal()">Suspend Student</button>@else<form method="POST" action="{{ route('mpp.users.unsuspend', $user->getKey()) }}">@csrf<button class="rounded-lg bg-emerald-600 px-4 py-2 text-sm text-white">Unsuspend Student</button></form>@endif</div>
</dialog>
@if(!$user->user_suspended)
<dialog id="suspend-student-{{ $user->getKey() }}" aria-labelledby="suspend-title-{{ $user->getKey() }}" class="m-auto w-full max-w-lg rounded-2xl bg-white p-6 text-slate-800 shadow-xl backdrop:bg-slate-900/50" style="max-height:85dvh;overflow-y:auto">
    <h2 id="suspend-title-{{ $user->getKey() }}" class="text-lg font-bold">Suspend Student</h2><p class="mb-5 mt-2 break-words text-sm">You are about to suspend <strong>{{ $user->user_name }}</strong>.</p>
    <form method="POST" action="{{ route('mpp.users.suspend', $user->getKey()) }}">@csrf
        <label class="block text-sm font-semibold" for="reason-{{ $user->getKey() }}">Reason *</label>
        <select required id="reason-{{ $user->getKey() }}" name="reason" class="mt-2 w-full rounded-lg border border-slate-200 p-3" onchange="this.form.elements.note.required = this.value === 'Other'"><option value="">Select a reason</option>@foreach(['Repeated suspicious listings', 'Misleading information', 'Fraud-related activity', 'Violation of platform rules', 'Other'] as $reason)<option value="{{ $reason }}">{{ $reason }}</option>@endforeach</select>
        <label for="note-{{ $user->getKey() }}" class="mt-4 block text-sm font-semibold">Additional note</label><p class="text-xs text-slate-500">Required for Other. Maximum 100 characters.</p><textarea id="note-{{ $user->getKey() }}" name="note" maxlength="100" rows="3" class="mt-2 w-full rounded-lg border border-slate-200 p-3"></textarea>
        <label class="my-4 flex items-start gap-2 text-sm"><input type="checkbox" name="hide_listings" value="1" class="mt-1">Hide all active listings from this student</label>
        <div class="flex justify-end gap-3"><button type="button" onclick="this.closest('dialog').close()" class="rounded-lg px-4 py-2">Cancel</button><button type="submit" class="rounded-lg bg-rose-600 px-4 py-2 text-white">Suspend Student</button></div>
    </form>
</dialog>
@endif
@endforeach
