<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Reports Received by Student - UPTM Rental</title>

<!-- Google Font -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

<link
    href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
    rel="stylesheet"
>

<!-- Tailwind CSS -->
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
<main class="student-details-page min-w-0"><div class="mpp-page-content space-y-4 p-4 md:p-6">
<a href="{{ route('mpp.students') }}" class="inline-flex items-center gap-2 font-semibold text-indigo-600"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Back to Student Accounts</a>
@if(session('success'))<p role="status" class="rounded-lg bg-emerald-50 p-3 text-emerald-700">{{ session('success') }}</p>@endif
@if($errors->any())<p role="alert" class="rounded-lg bg-rose-50 p-3 text-rose-700">{{ $errors->first() }}</p>@endif
<section class="px-1 py-2">
    @include('mpp.page-heading', ['title' => 'Reports received by student', 'description' => 'Review complaints submitted against the listings belonging to the selected student.'])
    <p class="mt-3 font-semibold">{{ $student->user_name }}</p>
    <p class="break-words text-slate-500">{{ $student->user_email }}</p>
    <span class="mt-2 inline-flex rounded-full px-3 py-1 text-sm {{ $student->user_suspended ? 'bg-rose-50 text-rose-700' : 'bg-emerald-50 text-emerald-700' }}">{{ $student->user_suspended ? 'Suspended' : 'Active' }}</span>
</section>
@if($reports->total() > 0)
<section class="rounded-2xl border border-slate-200 bg-white p-5">
    <h2 class="mb-3">Report summary</h2>
    <div class="grid items-center gap-4 md:grid-cols-[1fr_1fr_2fr]">
        <div><p class="text-slate-500">Total reports</p><p class="mt-1 font-semibold">{{ $reports->total() }}</p></div>
        <div><p class="text-slate-500">Listings reported</p><p class="mt-1 font-semibold">{{ $reportedListingCount }}</p></div>
        @if($mostReportedListing)
        <div class="flex flex-wrap items-center justify-between gap-3"><div><p class="text-slate-500">Most reported listing</p><p class="mt-1 font-semibold">{{ $mostReportedListing->listing_title }}</p><p class="mt-1 text-rose-700"><i class="fa-solid fa-flag mr-1" aria-hidden="true"></i> {{ $mostReportedListing->reports_count }} reports</p></div><a class="student-action student-action-view" href="{{ route('mpp.listings', ['user_id' => $student->getKey(), 'listing_id' => $mostReportedListing->getKey()]) }}">View Listing</a></div>
        @endif
    </div>
    @if($thresholdEvent)
        <p class="mt-3 border-t border-slate-100 pt-3 text-sm text-slate-500">The most reported listing was automatically hidden after reaching the report threshold on {{ $thresholdEvent->audit_created_at->format('d M Y, g:i A') }}.</p>
    @endif
</section>
<h2>Report records</h2>
<section class="overflow-x-auto rounded-2xl border border-slate-200 bg-white">
<table class="w-full min-w-[900px] text-left text-sm"><thead class="bg-slate-50 text-slate-500"><tr>@foreach(['Submitted by','Reason','Related listing','Listing status','Date & time','Action'] as $heading)<th class="p-4 font-semibold">{{ $heading }}</th>@endforeach</tr></thead><tbody class="divide-y divide-slate-100">
@forelse($reports as $report)<tr><td class="p-4"><span>{{ $report->user?->user_name ?? 'Deleted account' }}</span><small class="mt-1 block break-words text-slate-500">{{ $report->user?->user_email }}</small></td><td class="max-w-sm break-words p-4">{{ $report->report_reason }}</td><td class="p-4"><a class="text-indigo-600" href="{{ route('mpp.listings', ['user_id' => $student->getKey(), 'listing_id' => $report->listing_id]) }}">{{ $report->listing?->listing_title ?? 'Listing unavailable' }}</a></td><td class="p-4"><span class="rounded-full px-2 py-1 {{ $report->listing?->listing_status === 'active' ? 'bg-emerald-50 text-emerald-700' : ($report->listing?->listing_status === 'hidden' ? 'bg-rose-100 text-rose-700 font-semibold' : 'bg-slate-100 text-slate-600') }}">{{ ucfirst($report->listing?->listing_status ?? 'Unavailable') }}</span></td><td class="whitespace-nowrap p-4">{{ $report->report_created_at?->format('d M Y') }} &middot; {{ $report->report_created_at?->format('g:i A') }}</td><td class="p-4"><button type="button" class="student-action student-action-view" onclick="document.getElementById('report-details-{{ $report->getKey() }}').showModal()">View Details</button></td></tr>@empty<tr><td colspan="6" class="p-8 text-center text-slate-500">No reports received for this account.</td></tr>@endforelse
</tbody></table></section>
@if($reports->hasPages())<div class="rounded-xl border border-slate-200 bg-white p-4">{{ $reports->links() }}</div>@endif
@else
<section class="rounded-2xl border border-slate-200 bg-white px-5 py-10 text-center">
    <i class="fa-regular fa-flag mb-3 text-xl text-slate-400" aria-hidden="true"></i>
    <h2>No reports received</h2>
    <p class="mt-2 text-slate-500">No listings from this student have been reported.</p>
</section>
@endif
</div></main></div>
@foreach($reports as $report)
<dialog id="report-details-{{ $report->getKey() }}" aria-labelledby="report-title-{{ $report->getKey() }}" class="m-auto w-full max-w-lg rounded-2xl bg-white p-6 text-slate-800 shadow-xl backdrop:bg-slate-900/50" style="max-height:85dvh;overflow-y:auto"><h2 id="report-title-{{ $report->getKey() }}" class="mb-4 text-base font-semibold">Report #{{ $report->getKey() }}</h2><dl class="space-y-3 text-sm">@foreach(['Account reported' => $student->user_name, 'Submitted by' => $report->user?->user_name ?? 'Deleted account', 'Reporter email' => $report->user?->user_email ?? 'Unavailable', 'Reason' => $report->report_reason, 'Related listing' => $report->listing?->listing_title ?? 'Listing unavailable', 'Listing status' => ucfirst($report->listing?->listing_status ?? 'Unavailable'), 'Submitted at' => $report->report_created_at?->format('d M Y, g:i A')] as $label => $value)<div><dt class="text-slate-500">{{ $label }}</dt><dd class="whitespace-pre-wrap break-words">{{ $value }}</dd></div>@endforeach</dl><form method="dialog" class="mt-5 text-right"><button class="student-action student-action-view">Close</button></form></dialog>
@endforeach

</body></html>
