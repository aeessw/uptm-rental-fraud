<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Saved Listings - UPTM Rental</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { fontFamily: { sans: ['"Plus Jakarta Sans"', 'sans-serif'] }, colors: { brand: { 50: '#EEF2FF', 100: '#E0E7FF', 500: '#6366F1', 600: '#4F46E5', 700: '#4338CA' } } } } };
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
</head>
<body class="bg-slate-50/80 font-sans text-slate-800 antialiased">
<div class="flex min-h-screen">
    @include('student.sidebar')
    <main class="flex min-w-0 flex-1 flex-col">
        <div class="space-y-6 overflow-y-auto p-8">
            @if(session('success'))
                <div id="saved-listings-toast" role="status" class="fixed right-5 top-5 z-[9999] flex max-w-sm items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-xs font-semibold text-emerald-800 shadow-lg">
                    <i class="fa-solid fa-circle-check text-emerald-600"></i>
                    <span>{{ session('success') }}</span>
                    <button type="button" onclick="document.getElementById('saved-listings-toast')?.remove()" class="ml-2 text-emerald-600 transition hover:text-emerald-800" aria-label="Close notification" title="Close notification">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            @endif

            <div>
                <div class="flex flex-wrap items-end justify-between gap-3">
                    <div>
                        <h1 class="text-lg font-bold tracking-tight text-slate-900">Saved Listings</h1>
                        <p class="mt-1 text-xs text-slate-500">Keep the rooms you are considering in one place.</p>
                    </div>
                    @if($listings->count() > 0)
                        <form id="bulk-remove-form" action="{{ route('student.saved.bulk-remove') }}" method="POST">
                            @csrf
                            <button type="submit" id="bulk-remove-button" disabled class="inline-flex items-center gap-2 rounded-xl bg-rose-600 px-4 py-2 text-xs font-bold text-white opacity-50 shadow-xs transition hover:bg-rose-700 disabled:cursor-not-allowed">
                                <i class="fa-solid fa-trash-can"></i><span>Remove selected</span>
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            @if($listings->count() > 0)
                <div class="flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-slate-200/60 bg-white px-4 py-3 shadow-xs">
                    <label class="inline-flex items-center gap-2 text-xs font-semibold text-slate-600">
                        <input type="checkbox" id="select-all-listings" class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                        <span>Select all on this page</span>
                    </label>
                    <span id="selected-listings-count" class="text-xs font-semibold text-slate-400">0 selected</span>
                </div>
                <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3">
                    @foreach($listings as $listing)
                        <article class="relative overflow-hidden rounded-2xl border border-slate-200/60 bg-white shadow-xs">
                            <a href="{{ route('student.listings.show', $listing->getKey()) }}" class="group block">
                                <div class="relative aspect-video overflow-hidden bg-slate-100">
                                    @if($listing->listing_photo)
                                        <img src="{{ asset('storage/' . $listing->listing_photo) }}" alt="{{ $listing->listing_title }}" class="h-full w-full object-cover transition duration-300 group-hover:scale-105">
                                    @else
                                        <div class="flex h-full items-center justify-center text-slate-300"><i class="fa-solid fa-image text-3xl"></i></div>
                                    @endif
                                </div>
                                <div class="p-4">
                                    @if((string) $listing->user_id === (string) Auth::id() || $listing->listing_availability !== 'available')
                                    <span class="mb-3 inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[10px] font-bold {{ $listing->listing_availability === 'available' ? 'bg-emerald-100 text-emerald-700' : ($listing->listing_availability === 'rented' ? 'bg-rose-100 text-rose-700' : 'bg-slate-100 text-slate-600') }}">
                                        <span aria-hidden="true" class="h-1.5 w-1.5 rounded-full bg-current"></span>
                                        {{ ucfirst($listing->listing_availability ?? 'unavailable') }}
                                    </span>
                                    @endif
                                    <h2 class="line-clamp-1 text-sm font-bold text-slate-900 group-hover:text-brand-600">{{ $listing->listing_title }}</h2>
                                    <p class="mt-1.5 flex items-center gap-1.5 text-[11px] font-medium text-slate-500"><i class="fa-solid fa-location-dot text-slate-400"></i>{{ $listing->listing_location }}</p>
                                    <p class="mt-3 text-base font-extrabold text-brand-600">RM {{ number_format($listing->listing_rent, 2) }} <span class="text-xs font-medium text-slate-400">/mo</span>@if($listing->room_type)<span class="text-xs font-medium text-slate-500"> &bull; {{ preg_match('/\broom$/i', trim($listing->room_type)) || strcasecmp(trim($listing->room_type), 'Studio') === 0 ? trim($listing->room_type) : trim($listing->room_type) . ' Room' }}</span>@endif</p>
                                    <p class="mt-3 text-[11px] font-medium text-slate-400">Saved <time datetime="{{ $listing->pivot->save_created_at->toIso8601String() }}" title="{{ $listing->pivot->save_created_at->format('d M Y, H:i') }}">{{ $listing->pivot->save_created_at->diffForHumans() }}</time></p>
                                    <span class="mt-4 flex items-center justify-end gap-2 text-xs font-bold text-brand-600">View Details <i aria-hidden="true" class="fa-solid fa-arrow-right"></i></span>
                                </div>
                            </a>
                            <label class="absolute left-3 top-3 z-10 flex h-9 w-9 cursor-pointer items-center justify-center rounded-full bg-white/95 shadow-sm" title="Select listing">
                                <input type="checkbox" name="listing_ids[]" value="{{ $listing->getKey() }}" form="bulk-remove-form" class="listing-checkbox h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500" aria-label="Select {{ $listing->listing_title }}">
                            </label>
                            <form action="{{ route('student.listings.save', $listing->getKey()) }}" method="POST" class="absolute right-3 top-3 z-10">
                                @csrf
                                <button type="submit" class="flex h-9 w-9 items-center justify-center rounded-full bg-white/95 text-slate-500 shadow-sm transition hover:bg-rose-50 hover:text-rose-600" aria-label="Remove {{ $listing->listing_title }} from saved listings" title="Remove from saved listings">
                                    <i class="fa-solid fa-trash-can text-xs"></i>
                                </button>
                            </form>
                        </article>
                    @endforeach
                </div>
                @if($listings->hasPages())
                    <div class="mt-6 flex justify-center">{{ $listings->links() }}</div>
                @endif
            @else
                <div class="rounded-2xl border border-dashed border-slate-200 bg-white p-12 text-center">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-brand-50 text-brand-600"><i aria-hidden="true" class="fa-regular fa-heart text-lg"></i></div>
                    <h2 class="mt-4 text-sm font-bold text-slate-900">No saved listings yet</h2>
                    <p class="mt-1 text-xs font-medium text-slate-400">Save rooms you're interested in and they'll appear here.</p>
                    <a href="{{ route('student.listings') }}" class="mt-4 inline-flex items-center gap-2 rounded-xl bg-brand-600 px-4 py-2 text-xs font-bold text-white transition hover:bg-brand-700">Browse Room Listings</a>
                </div>
            @endif
        </div>
    </main>
</div>
<script>
    const savedListingsToast = document.getElementById('saved-listings-toast');
    const selectAll = document.getElementById('select-all-listings');
    const listingCheckboxes = Array.from(document.querySelectorAll('.listing-checkbox'));
    const bulkRemoveButton = document.getElementById('bulk-remove-button');
    const selectedCount = document.getElementById('selected-listings-count');

    if (savedListingsToast) {
        window.setTimeout(() => savedListingsToast.remove(), 4000);
    }

    function updateSelectionState() {
        const selected = listingCheckboxes.filter((checkbox) => checkbox.checked).length;

        if (bulkRemoveButton) {
            bulkRemoveButton.disabled = selected === 0;
            bulkRemoveButton.classList.toggle('opacity-50', selected === 0);
        }

        if (selectedCount) {
            selectedCount.textContent = `${selected} selected`;
        }

        if (selectAll) {
            selectAll.checked = listingCheckboxes.length > 0 && selected === listingCheckboxes.length;
            selectAll.indeterminate = selected > 0 && selected < listingCheckboxes.length;
        }
    }

    selectAll?.addEventListener('change', () => {
        listingCheckboxes.forEach((checkbox) => {
            checkbox.checked = selectAll.checked;
        });
        updateSelectionState();
    });

    listingCheckboxes.forEach((checkbox) => {
        checkbox.addEventListener('change', updateSelectionState);
    });
</script>
</body>
</html>
