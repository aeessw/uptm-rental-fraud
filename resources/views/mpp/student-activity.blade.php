<ul class="divide-y divide-slate-100 text-sm">
@forelse($user->auditActivity->take(5) as $activity)
    <li class="flex items-start gap-3 py-3">
        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-slate-500"><i class="fa-solid {{ $activity->listingId() ? 'fa-house' : 'fa-shield-halved' }}" aria-hidden="true"></i></span>
        <div class="min-w-0">
            <p class="font-semibold">{{ match($activity->audit_action) { 'login' => 'Logged in', 'logout' => 'Logged out', default => ucfirst(str_replace('_', ' ', $activity->audit_action)) } }}</p>
            @if($activity->listingId())<p class="mt-1 break-words text-xs text-slate-500">@if($activity->auditListing){{ $activity->auditListing->listing_title }} &middot; @endif Listing #{{ $activity->listingId() }}</p>
            @elseif(!in_array($activity->audit_action, ['login', 'logout']))<p class="mt-1 break-words text-xs text-slate-500">{{ $activity->displayDetails() }}</p>@endif
            <time class="mt-1 block text-xs text-slate-500">{{ $activity->audit_created_at->format('d M Y') }} &middot; {{ $activity->audit_created_at->format('g:i A') }}</time>
        </div>
    </li>
@empty
    <li class="py-3 text-slate-500">No recorded activity yet.</li>
@endforelse
</ul>
