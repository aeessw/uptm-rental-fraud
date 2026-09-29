<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Student Accounts - UPTM Rental</title>

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
<div class="min-h-screen">
@include('mpp.sidebar')
<main class="student-details-page min-w-0">
<div class="mpp-page-content space-y-4 p-4 md:p-6">
@include('mpp.page-heading', ['title' => 'Student details', 'description' => 'Review account information, listings, and recent student activity.'])
    <a href="{{ route('mpp.students') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-indigo-600"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Student Accounts</a>
    @if(session('success'))<p role="status" class="rounded-lg bg-emerald-50 p-3 text-emerald-700">{{ session('success') }}</p>@endif
    @if($errors->any())<p role="alert" class="rounded-lg bg-rose-50 p-3 text-rose-700">{{ $errors->first() }}</p>@endif
    <section class="rounded-2xl border border-slate-200 bg-white p-5">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex min-w-0 items-center gap-3"><span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-indigo-100 font-semibold text-indigo-700">{{ strtoupper(substr($user->user_name, 0, 2)) }}</span><div class="min-w-0"><h2 class="break-words">{{ $user->user_name }}</h2><p class="break-words text-sm text-slate-500">{{ $user->user_email }}</p></div></div>
            <div class="flex items-center gap-3"><span class="rounded-full px-3 py-1 text-sm font-semibold {{ $user->user_suspended ? 'bg-rose-50 text-rose-700' : 'bg-emerald-50 text-emerald-700' }}">{{ $user->user_suspended ? 'Suspended' : 'Active' }}</span><span class="text-sm text-slate-500">Student account</span></div>
        </div>
    </section>
    <div class="grid items-start gap-4 xl:grid-cols-2">
        <section class="rounded-2xl border border-slate-200 bg-white p-5">
            <h2 class="mb-4">Account overview</h2>
            <dl class="grid grid-cols-2 gap-3 text-sm">
                @foreach(['Listings posted' => $user->listings_count, 'Active listings' => $user->active_listings_count, 'Reported listings' => $user->reported_listings_count, 'Reports received' => $user->received_reports_count] as $label => $count)
                <div class="rounded-xl bg-slate-50 p-3"><dt class="text-sm text-slate-500">{{ $label }}</dt><dd class="mt-1 text-base font-semibold">{{ $count }}</dd></div>
                @endforeach
            </dl>
            <div class="mt-4 flex flex-wrap gap-2"><a class="student-action student-action-view" href="{{ route('mpp.listings', ['user_id' => $user->getKey()]) }}">View listings</a><a class="student-action student-action-view" href="{{ route('mpp.reports', ['user_id' => $user->getKey()]) }}">View reports</a></div>
            <div class="mt-5 flex items-center justify-between gap-3 border-t border-slate-100 pt-4"><p class="text-sm text-slate-500">Account access</p>
                @if(!$user->user_suspended)<button type="button" class="student-action student-action-suspend" onclick="document.getElementById('suspend-student-{{ $user->getKey() }}').showModal()">Suspend Student</button>@else<form method="POST" action="{{ route('mpp.users.unsuspend', $user->getKey()) }}">@csrf<button class="student-action student-action-restore">Unsuspend Student</button></form>@endif
            </div>
        </section>
        <section class="rounded-2xl border border-slate-200 bg-white p-5">
            <div class="mb-3 flex flex-wrap items-center justify-between gap-2"><h2>Recent audit activity</h2><a class="text-sm font-semibold text-indigo-600" href="{{ route('mpp.audit.logs', ['user_id' => $user->getKey()]) }}">View all activity <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a></div>
            @include('mpp.student-activity', ['user' => $user])
        </section>
    </div>
</div>
</main></div>
@include('mpp.student-details', ['users' => collect([$user])])
</body></html>
