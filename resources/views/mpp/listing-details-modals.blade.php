@foreach($listings as $listing)
<dialog id="listing-details-{{ $listing->getKey() }}" aria-labelledby="listing-heading-{{ $listing->getKey() }}" class="listing-review-dialog m-auto w-[calc(100%-2rem)] max-w-3xl max-h-[90vh] overflow-hidden rounded-2xl border border-slate-200 bg-white p-0 text-slate-800 shadow-xl backdrop:bg-slate-950/60">
    <div class="listing-review-body overflow-y-auto p-5">
    <div class="flex items-start justify-between gap-4">
        <div><p class="text-xs text-slate-500">{{ ($investigationMode ?? false) ? 'Report Details for Listing' : 'Listing Details' }} #{{ $listing->getKey() }}</p><h2 id="listing-heading-{{ $listing->getKey() }}" class="mt-1 text-xl font-bold">{{ $listing->listing_title }}</h2><div class="mt-2 flex flex-wrap gap-2 text-xs">
<span class="rounded-full px-2 py-1 {{ $listing->listing_status === 'active' ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">{{ ucfirst($listing->listing_status) }}</span>
@unless($investigationMode ?? false)
<span class="rounded-full px-2 py-1 {{ $listing->listing_availability === 'available' ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">{{ ucfirst($listing->listing_availability ?? 'unavailable') }}</span>
@endunless
<span class="rounded-full px-2 py-1 font-semibold {{ $listing->report_count >= 3 ? 'bg-rose-100 text-rose-700' : ($listing->report_count == 2 ? 'bg-orange-100 text-orange-700' : ($listing->report_count == 1 ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-600')) }}">{{ $listing->report_count }} {{ $listing->report_count == 1 ? 'Report' : 'Reports' }}</span></div></div>
        <form method="dialog"><button autofocus class="h-10 w-10 rounded-lg hover:bg-slate-100" aria-label="{{ ($investigationMode ?? false) ? 'Close report details' : 'Close listing details' }}"><i class="fa-solid fa-xmark" aria-hidden="true"></i></button></form>
    </div>
    @unless($investigationMode ?? false)
    <dl class="mt-4 grid gap-x-5 gap-y-3 rounded-xl bg-slate-50 p-4 sm:grid-cols-[1.5fr_1fr_1fr] text-sm">
        <div><dt class="text-slate-500">Owner</dt><dd class="break-words">{{ $listing->user?->user_name ?? 'Deleted account' }}<span class="mt-1 block truncate text-xs text-slate-500" title="{{ $listing->user?->user_email }}">{{ $listing->user?->user_email }}</span></dd></div>
        <div><dt class="text-slate-500">Location</dt><dd>{{ $listing->listing_location }}</dd></div>
        <div><dt class="text-slate-500">Rent</dt><dd>RM{{ number_format($listing->listing_rent, 2) }}/month</dd></div>
        <div><dt class="text-slate-500">Room Type</dt><dd>{{ $listing->room_type }}</dd></div>
        <div><dt class="text-slate-500">Pax</dt><dd>{{ $listing->pax ? $listing->pax . ' pax' : 'Not specified' }}</dd></div>
        <div><dt class="text-slate-500">Availability</dt><dd>{{ ucfirst($listing->listing_availability ?? 'unavailable') }}</dd></div>
    </dl>
    <h3 class="mt-4 font-semibold">Description</h3>
    <p class="mt-2 whitespace-pre-line break-words text-sm text-slate-600">{{ $listing->listing_description }}</p>
    <h3 class="mt-4 font-semibold">Photos</h3>
    <div class="mt-2 flex flex-wrap gap-2">
        @foreach(collect([$listing->listing_photo])->merge($listing->photos->pluck('photo_path'))->filter()->unique()->take(4) as $photo)
            <button type="button" class="listing-photo-open rounded-lg focus-visible:outline focus-visible:outline-2 focus-visible:outline-indigo-500" data-photo="{{ asset('storage/' . $photo) }}" data-caption="{{ $listing->listing_title }}" aria-label="Enlarge photo of {{ $listing->listing_title }}"><img src="{{ asset('storage/' . $photo) }}" alt="Room photo for {{ $listing->listing_title }}" loading="lazy" class="h-24 w-32 rounded-lg object-cover"></button>
        @endforeach
        @if(!$listing->listing_photo && $listing->photos->isEmpty())<div class="flex w-full items-center justify-center gap-2 rounded-xl border border-dashed border-slate-200 bg-slate-50 p-5 text-sm text-slate-500"><i class="fa-regular fa-image" aria-hidden="true"></i>No photos available.</div>@endif
    </div>
    @endunless
    <section class="mt-4 border-t border-slate-100 pt-4"><h3 class="font-semibold">{{ ($investigationMode ?? false) ? 'Reports and moderation' : 'Moderation' }}</h3><div class="mt-3 grid gap-4 sm:grid-cols-2">
        @if($investigationMode ?? false)
        <div><h4 class="text-sm font-semibold">Individual reports ({{ $listing->report_count }})</h4><ul class="mt-3 space-y-3 text-sm">@foreach($listing->reports as $report)<li class="rounded-lg bg-slate-50 p-3"><p class="whitespace-pre-wrap break-words font-semibold">{{ $report->report_reason }}</p><p class="mt-2 text-xs text-slate-500">Reported by {{ $report->user?->user_name ?? 'Deleted account' }} &middot; Report #{{ $report->getKey() }}</p>@if($report->user?->user_email)<p class="break-words text-xs text-slate-500">{{ $report->user->user_email }}</p>@endif<time class="text-xs text-slate-500">{{ $report->report_created_at?->format('d M Y, g:i A') }}</time></li>@endforeach</ul></div>
        @else
        <div><h4 class="text-sm font-semibold">Reports</h4>@if($listing->report_count > 0)<p class="mt-2 text-sm text-slate-600">{{ $listing->report_count }} {{ $listing->report_count === 1 ? 'report' : 'reports' }} received</p><a class="mt-2 inline-flex items-center gap-2 text-sm text-indigo-600" href="{{ route('mpp.reports', ['listing_id' => $listing->getKey()]) }}">View reports <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>@else<p class="mt-2 text-sm text-slate-500">No reports received.</p>@endif</div>
        @endif
        <div><h4 class="text-sm font-semibold">Moderation history</h4><ul class="mt-2 space-y-2 text-sm">@forelse($listing->moderationHistory->take(3) as $entry)<li class="rounded-lg bg-slate-50 p-2"><p class="break-words">{{ $entry->displayDetails() }}</p><p class="text-xs text-slate-500">By {{ $entry->user?->user_name ?? 'System' }} &middot; {{ $entry->audit_created_at->format('d M Y, g:i A') }}</p></li>@empty<li class="text-slate-500">No moderation activity recorded.</li>@endforelse</ul>@if($listing->moderationHistory->isNotEmpty())<a class="mt-2 inline-flex items-center gap-2 text-sm text-indigo-600" href="{{ route('mpp.audit.logs', ['listing_id' => $listing->getKey()]) }}">View full audit log <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>@endif</div>
    </div></section>
    </div>
    <div class="flex flex-wrap shrink-0 justify-end gap-3 border-t border-slate-200 bg-white px-5 py-3">
        <form method="dialog"><button class="rounded-lg border border-slate-200 px-4 py-2 text-sm">Close</button></form>
        @if(($investigationMode ?? false) && $listing->user?->user_role === 'student')
            @if($listing->user->user_suspended)
                <span class="rounded-lg bg-rose-50 px-4 py-2 text-sm text-rose-700">Account Suspended</span>
            @else
                <button type="button" class="rounded-lg border border-rose-600 px-4 py-2 text-sm font-semibold text-rose-700" onclick="document.getElementById('listing-details-{{ $listing->getKey() }}').close();document.getElementById('suspend-owner-{{ $listing->getKey() }}').showModal()">Suspend Account</button>
            @endif
        @endif
        @if(in_array($listing->listing_status, ['active', 'hidden']))
            @if($listing->listing_status === 'hidden')
            <form method="POST" action="{{ route('mpp.listings.restore', $listing->getKey()) }}" onsubmit="return confirm('Restore this listing after review? Existing reports will remain recorded.')">@csrf<button class="rounded-lg bg-indigo-600 px-4 py-2 text-sm text-white">Restore Listing</button></form>
            @else
            <button type="button" class="rounded-lg bg-rose-600 px-4 py-2 text-sm text-white" onclick="document.getElementById('listing-details-{{ $listing->getKey() }}').close();document.getElementById('hide-listing-{{ $listing->getKey() }}').showModal()">Hide Listing</button>
            @endif
        @endif
    </div>
</dialog>
<dialog id="hide-listing-{{ $listing->getKey() }}" class="m-auto w-full max-w-lg rounded-2xl bg-white p-6 text-slate-800 backdrop:bg-slate-900/50" aria-labelledby="hide-title-{{ $listing->getKey() }}"><h2 id="hide-title-{{ $listing->getKey() }}" class="text-lg font-semibold">Hide this listing?</h2><p class="my-3">{{ $listing->listing_title }}</p><form method="POST" action="{{ route('mpp.listings.remove', $listing->getKey()) }}">@csrf<label for="hide-reason-{{ $listing->getKey() }}" class="block text-sm">Moderation reason *</label><select id="hide-reason-{{ $listing->getKey() }}" name="reason" required onchange="this.form.elements.note.required = this.value === 'Other'" class="my-2 w-full rounded-lg border p-2"><option value="">Select reason</option>@foreach(['Fraud / Scam','Misleading information','Duplicate listing','Inappropriate Content','Other'] as $reason)<option>{{ $reason }}</option>@endforeach</select><label for="hide-note-{{ $listing->getKey() }}" class="block text-sm">Note (required for Other)</label><textarea id="hide-note-{{ $listing->getKey() }}" name="note" maxlength="100" class="my-2 w-full rounded-lg border p-2"></textarea><div class="mt-3 flex justify-end gap-2"><button type="button" onclick="this.closest('dialog').close()" class="rounded-lg px-3 py-2">Cancel</button><button class="rounded-lg bg-rose-600 px-3 py-2 text-white">Hide Listing</button></div></form></dialog>
@if(($investigationMode ?? false) && $listing->user?->user_role === 'student' && !$listing->user->user_suspended)
<dialog id="suspend-owner-{{ $listing->getKey() }}" aria-labelledby="owner-suspend-title-{{ $listing->getKey() }}" class="m-auto w-full max-w-lg rounded-2xl bg-white p-6 text-slate-800 shadow-xl backdrop:bg-slate-900/50" style="max-height:85dvh;overflow-y:auto">
    <h2 id="owner-suspend-title-{{ $listing->getKey() }}" class="text-lg font-bold">Suspend Account</h2><p class="mb-5 mt-2 break-words text-sm">You are about to suspend <strong>{{ $listing->user->user_name }}</strong>.</p>
    <form method="POST" action="{{ route('mpp.users.suspend', $listing->user->getKey()) }}">@csrf
        <label class="block text-sm font-semibold" for="owner-reason-{{ $listing->getKey() }}">Reason *</label>
        <select required id="owner-reason-{{ $listing->getKey() }}" name="reason" class="mt-2 w-full rounded-lg border border-slate-200 p-3" onchange="this.form.elements.note.required = this.value === 'Other'"><option value="">Select a reason</option>@foreach(['Repeated suspicious listings', 'Misleading information', 'Fraud-related activity', 'Violation of platform rules', 'Other'] as $reason)<option value="{{ $reason }}">{{ $reason }}</option>@endforeach</select>
        <label for="owner-note-{{ $listing->getKey() }}" class="mt-4 block text-sm font-semibold">Additional note</label><p class="text-xs text-slate-500">Required for Other. Maximum 100 characters.</p><textarea id="owner-note-{{ $listing->getKey() }}" name="note" maxlength="100" rows="3" class="mt-2 w-full rounded-lg border border-slate-200 p-3"></textarea>
        <label class="my-4 flex items-start gap-2 text-sm"><input type="checkbox" name="hide_listings" value="1" class="mt-1">Hide all active listings from this student</label>
        <div class="flex justify-end gap-3"><button type="button" onclick="this.closest('dialog').close();document.getElementById('listing-details-{{ $listing->getKey() }}').showModal()" class="rounded-lg px-4 py-2">Cancel</button><button type="submit" class="rounded-lg bg-rose-600 px-4 py-2 text-white">Suspend Account</button></div>
    </form>
</dialog>
@endif
@endforeach

@unless($investigationMode ?? false)
<dialog id="listing-photo-preview" aria-label="Enlarged listing photo" class="m-auto rounded-2xl bg-slate-950 p-3 text-white shadow-xl backdrop:bg-black/80" style="width:min(1100px,94vw);max-width:94vw;max-height:94dvh;">
    <div class="mb-3 flex items-center justify-between gap-3"><p id="listing-photo-caption" class="truncate text-sm"></p><form method="dialog"><button autofocus class="rounded-lg px-3 py-2 hover:bg-slate-800" aria-label="Close photo preview"><i class="fa-solid fa-xmark" aria-hidden="true"></i></button></form></div>
    <img id="listing-photo-full" alt="" style="display:block;width:100%;height:75dvh;object-fit:contain;">
</dialog>
<script>
(() => {
    const preview = document.getElementById('listing-photo-preview');
    const photo = document.getElementById('listing-photo-full');
    document.querySelectorAll('.listing-photo-open').forEach(button => {
        button.addEventListener('click', () => {
            photo.src = button.dataset.photo;
            photo.alt = 'Listing photo: ' + button.dataset.caption;
            document.getElementById('listing-photo-caption').textContent = button.dataset.caption;
            preview.showModal();
        });
    });
    preview.addEventListener('click', event => {
        const rect = preview.getBoundingClientRect();
        if (event.target === preview && (event.clientX < rect.left || event.clientX > rect.right || event.clientY < rect.top || event.clientY > rect.bottom)) preview.close();
    });
})();
</script>
@endunless
