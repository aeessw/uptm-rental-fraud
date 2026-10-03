<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - UPTM Rental</title>
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

        <main class="flex-1 min-w-0">

            <div class="max-w-5xl mx-auto p-6 md:p-10">
                <a href="{{ route('student.dashboard') }}" class="inline-flex items-center gap-2 text-sm text-slate-600 hover:text-blue-600 mb-6">
                    <i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Back to dashboard
                </a>

                <section class="bg-white rounded-2xl border border-slate-200 p-6 md:p-8" aria-labelledby="profile-name">
                    <div class="flex items-center gap-4 mb-8">
                        <div class="w-16 h-16 shrink-0 rounded-full bg-slate-200 flex items-center justify-center text-xl font-bold" aria-hidden="true">
                            {{ strtoupper(substr(Auth::user()->user_name ?? 'S', 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <h2 id="profile-name" class="text-xl font-bold break-words">{{ Auth::user()->user_name }}</h2>
                            <p class="text-sm text-slate-500 mt-1">UPTM Student</p>
                        </div>
                    </div>

                    <dl class="space-y-6">
                        <div>
                            <dt class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Full name</dt>
                            <dd class="mt-2 text-sm break-words">{{ Auth::user()->user_name }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Email address</dt>
                            <dd class="mt-2 text-sm break-words">{{ Auth::user()->user_email }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Member since</dt>
                            <dd class="mt-2 text-sm">{{ Auth::user()->user_created_at?->format('d F Y') ?? 'Not available' }}</dd>
                        </div>
                    </dl>
                </section>

                <section id="settings" class="mt-6 rounded-2xl border border-slate-200 bg-white p-6 md:p-8" aria-labelledby="settings-heading">
                    <h2 id="settings-heading">Settings</h2>
                    <p class="mt-2 text-sm text-slate-500">Manage who can message you. Blocking prevents messages and hides your listings from each other. Unblocking removes your block only.</p>
                    @if(session('success'))
                        <p role="status" class="mt-4 rounded-xl bg-emerald-50 p-3 text-sm text-emerald-700">{{ session('success') }}</p>
                    @endif
                    <h3 class="mt-6">Blocked Users ({{ $blockedUsers->count() }})</h3>
                    <div class="mt-3 space-y-3">
                        @forelse($blockedUsers as $contact)
                            <div class="flex items-center justify-between gap-4 rounded-xl border border-slate-200 p-4">
                                <span class="min-w-0 break-words text-sm font-semibold">{{ $contact->user_name }}</span>
                                <form method="POST" action="{{ route('student.users.unblock', $contact) }}">
                                    @csrf
                                    <button class="min-h-11 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700" aria-label="Unblock {{ $contact->user_name }}">Unblock</button>
                                </form>
                            </div>
                        @empty
                            <p class="text-sm text-slate-500">You have no blocked users.</p>
                        @endforelse
                    </div>
                </section>

                <p id="pagination-status" role="status" class="mt-4 text-sm text-slate-600" hidden></p>
                <section id="profile-listings" class="mt-6 bg-white rounded-2xl border border-slate-200 p-6 md:p-8" aria-labelledby="my-listings-heading">
                    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
                        <div>
                            <h2 id="my-listings-heading" tabindex="-1">My Listings <span class="text-sm font-medium text-slate-500">({{ $listings->total() }})</span></h2>
                            <p class="text-sm text-slate-500 mt-1">You can have up to two posts. Posts stay hidden until MPP approves them.</p>
                        </div>
                        <a href="{{ route('student.listings.create') }}" class="inline-flex items-center justify-center min-h-11 rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Post a Room</a>
                    </div>

                    <div class="space-y-4">
                        @forelse($listings as $listing)
                            <article class="flex flex-col sm:flex-row gap-4 rounded-xl border border-slate-200 p-4">
                                @if($listing->listing_photo)
                                    <img src="{{ asset('storage/' . $listing->listing_photo) }}" alt="{{ $listing->listing_title }}" loading="lazy" class="w-full h-40 sm:w-28 sm:h-24 rounded-lg object-cover shrink-0">
                                @else
                                    <div class="w-full h-40 sm:w-28 sm:h-24 rounded-lg bg-slate-100 text-slate-400 flex items-center justify-center shrink-0" aria-label="No room photo">
                                        <i class="fa-regular fa-image text-2xl" aria-hidden="true"></i>
                                    </div>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h3 class="break-words">{{ $listing->listing_title }}</h3>
                                        <span class="rounded-full bg-indigo-50 px-2.5 py-1 text-xs text-indigo-700">{{ ucfirst($listing->listing_status) }}</span>
                                        <span class="rounded-full px-2.5 py-1 text-xs font-medium {{ ($listing->listing_availability ?? 'available') === 'available' ? 'bg-emerald-50 text-emerald-700' : ($listing->listing_availability === 'rented' ? 'bg-rose-50 text-rose-700' : 'bg-slate-100 text-slate-600') }}">{{ ucfirst($listing->listing_availability ?? 'available') }}</span>
                                    </div>
                                    <p class="mt-1 text-sm text-slate-500 break-words">{{ $listing->listing_location }}</p>
                                    <p class="mt-2 text-sm"><span class="font-semibold text-indigo-600">RM {{ number_format($listing->listing_rent, 2) }}</span><span class="text-slate-500"> / month &middot; {{ $listing->room_type }}</span></p>
                                </div>
                                <div class="flex sm:flex-col gap-2 shrink-0 sm:justify-center">
                                    <a href="{{ route('student.listings.show', $listing) }}" aria-label="View {{ $listing->listing_title }}" class="inline-flex items-center justify-center min-h-11 px-4 py-2 rounded-lg border border-slate-200 text-sm font-medium hover:bg-slate-50">View</a>
                                    <a href="{{ route('student.listings.edit', $listing) }}" aria-label="Edit {{ $listing->listing_title }}" class="inline-flex items-center justify-center min-h-11 px-4 py-2 rounded-lg bg-indigo-50 text-indigo-700 text-sm font-semibold hover:bg-indigo-100">Edit</a>
                                </div>
                            </article>
                        @empty
                            <div class="rounded-xl border border-dashed border-slate-200 p-8 text-center">
                                <h3>No listings yet</h3>
                                <p class="mt-2 text-sm text-slate-500">Your posted rooms will appear here. Select Post a Room to create your first listing.</p>
                            </div>
                        @endforelse
                    </div>

                    @if($listings->hasPages())
                        <div data-profile-pagination class="mt-6">{{ $listings->links() }}</div>
                    @endif
                </section>
            </div>
        </main>
    </div>
    <script src="{{ asset('js/profile-pagination.js') }}" defer></script>
</body>
</html>
