<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Reports - UPTM Rental</title>

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
<div class="min-h-screen">@include('mpp.sidebar')
<main class="fraud-reports-page min-w-0"><div class="mpp-page-content space-y-4 p-4 md:p-6">
    @include('mpp.page-heading', ['title' => 'Fraud Reports', 'description' => 'Investigate reported listings and review moderation history.'])
    @if(session('success'))<p role="status" class="rounded-lg bg-emerald-50 p-3 text-emerald-700">{{ session('success') }}</p>@endif
    @if($errors->any())<p role="alert" class="rounded-lg bg-rose-50 p-3 text-rose-700">{{ $errors->first() }} Reopen View Details to correct the moderation form.</p>@endif
    @if(request()->filled('listing_id'))<p class="text-sm text-indigo-600">Showing reports for Listing #{{ request('listing_id') }}. <a class="underline" href="{{ route('mpp.reports') }}">Show all reported listings</a></p>@endif
    <div class="grid gap-3 sm:grid-cols-3">
    @foreach(['Reported Listings' => $reportedListings->count(), 'Active Reported' => $reportedListings->where('listing_status', 'active')->count(), 'Hidden' => $reportedListings->where('listing_status', 'hidden')->count()] as $label => $count)<div class="rounded-xl border border-slate-200 bg-white px-4 py-2.5"><p class="text-xs text-slate-500">{{ $label }}</p><p class="mt-1 text-lg font-semibold">{{ $count }}</p></div>@endforeach
    </div>
    <p class="text-xs text-slate-500">Reported listings are shown here for MPP review. Hidden listings can be restored by MPP after review.</p>
    <div class="grid gap-3 rounded-xl border border-slate-200 bg-white p-4 sm:grid-cols-[2fr_1fr_1.5fr_auto]">
        <input id="fraudSearch" aria-label="Search reported listings" placeholder="Search listings, owners or reasons..." class="min-w-0 rounded-lg border border-slate-200 bg-slate-50 p-3 text-sm">
        <select id="fraudStatus" aria-label="Filter status" class="rounded-lg border border-slate-200 bg-slate-50 p-3 text-sm"><option value="">All Status</option><option value="active">Active</option><option value="hidden">Hidden</option></select>
        <select id="fraudCount" aria-label="Filter report count" class="rounded-lg border border-slate-200 bg-slate-50 p-3 text-sm"><option value="">All Report Counts</option><option value="low">1&ndash;2 Reports</option><option value="high">3+ Reports / Threshold Reached</option></select>
        <button id="fraudClear" type="button" class="rounded-lg border border-slate-200 px-4 py-2 text-sm">Clear</button>
    </div>
    <section class="overflow-x-auto rounded-xl border border-slate-200 bg-white"><table id="fraudReportsTable" class="w-full min-w-[950px] table-fixed text-left text-sm"><thead class="bg-slate-50 text-xs uppercase text-slate-500"><tr><th class="w-[22%] p-4">Listing</th><th class="w-[21%] p-4">Owner</th><th class="w-[14%] p-4">Reports</th><th class="w-[20%] p-4">Report Reasons</th><th class="w-[10%] p-4">Status</th><th class="w-[13%] p-4">Action</th></tr></thead><tbody class="divide-y divide-slate-100">
    @foreach($reportedListings as $listing)
        <tr class="fraud-row" data-search="{{ strtolower($listing->listing_title.' '.($listing->user?->user_name ?? '').' '.($listing->user?->user_email ?? '').' '.$listing->reports->pluck('report_reason')->implode(' ')) }}" data-status="{{ $listing->listing_status }}" data-count="{{ $listing->actual_reports_count }}">
            <td class="p-4"><p class="truncate font-semibold" title="{{ $listing->listing_title }}">{{ $listing->listing_title }}</p><p class="mt-1 text-xs text-slate-500">Listing #{{ $listing->getKey() }}</p></td>
            <td class="p-4"><p class="truncate" title="{{ $listing->user?->user_name }}">{{ $listing->user?->user_name ?? 'Deleted account' }}</p><p class="mt-1 truncate text-xs text-slate-500" title="{{ $listing->user?->user_email }}">{{ $listing->user?->user_email }}</p></td>
            <td class="p-4"><span class="inline-flex items-center gap-1 rounded-full px-2 py-1 font-semibold {{ $listing->actual_reports_count >= 3 ? 'bg-rose-100 text-rose-700' : ($listing->actual_reports_count === 2 ? 'bg-orange-100 text-orange-700' : 'bg-amber-100 text-amber-700') }}"><i class="fa-solid fa-flag" aria-hidden="true"></i>{{ $listing->actual_reports_count }} reports</span></td>
            <td class="p-4">
                @php
                    $reasonGroups = $listing->reports->groupBy(fn ($report) => mb_strtolower(preg_replace('/\s+/u', ' ', trim($report->report_reason))))->sortByDesc(fn ($items) => $items->count());
                @endphp
                <div class="space-y-1">
                @foreach($reasonGroups->take(3) as $items)
                    @php($reasonLabel = ucfirst(preg_replace('/\s+/u', ' ', trim($items->first()->report_reason))))
                    <p class="truncate text-xs text-slate-600" title="{{ $reasonLabel }}">{{ $reasonLabel }} ({{ $items->count() }})</p>
                @endforeach
                @if($reasonGroups->count() > 3)<button type="button" class="text-xs font-semibold text-indigo-600" onclick="document.getElementById('listing-details-{{ $listing->getKey() }}').showModal()">+{{ $reasonGroups->count() - 3 }} more</button>@endif
                </div>
            </td>
            <td class="p-4"><span class="rounded-full px-2 py-1 text-xs font-semibold {{ $listing->listing_status === 'hidden' ? 'bg-rose-100 text-rose-700' : 'bg-emerald-50 text-emerald-700' }}">{{ ucfirst($listing->listing_status) }}</span></td>
            <td class="p-4"><button type="button" class="rounded-lg bg-indigo-50 px-3 py-2 font-semibold text-indigo-700" onclick="document.getElementById('listing-details-{{ $listing->getKey() }}').showModal()">View Details</button></td>
        </tr>
    @endforeach
    </tbody></table><p id="fraudEmpty" hidden class="p-8 text-center text-sm text-slate-500">No reported listings match these filters.</p></section>
</div></main></div>
@include('mpp.listing-details-modals', ['listings' => $reportedListings, 'investigationMode' => true])
<script>
(() => {
 const search = document.getElementById('fraudSearch'), status = document.getElementById('fraudStatus'), count = document.getElementById('fraudCount');
 function filter() { let visible = 0; const words = search.value.toLowerCase().trim().split(/\s+/).filter(Boolean); document.querySelectorAll('.fraud-row').forEach(row => { const total = Number(row.dataset.count); const match = words.every(word => row.dataset.search.includes(word)) && (!status.value || status.value === row.dataset.status) && (!count.value || (count.value === 'high' ? total >= 3 : total < 3)); row.hidden = !match; if (match) visible++; }); document.getElementById('fraudEmpty').hidden = visible > 0; }
 search.addEventListener('input', filter); [status,count].forEach(el => el.addEventListener('change', filter)); document.getElementById('fraudClear').addEventListener('click', () => {search.value = '';status.value = '';count.value = '';filter();search.focus();}); filter();
})();
</script></body></html>
