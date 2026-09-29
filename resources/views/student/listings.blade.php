<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Listings - UPTM Rental Fraud Detection</title>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
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

    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
</head>
<body class="bg-slate-50/80 font-sans text-slate-800 antialiased">

<div class="flex min-h-screen">

    <!-- Sidebar Navigation Included Here -->
    @include('student.sidebar')

    <!-- Main Content Area -->
    <main class="flex min-w-0 flex-1 flex-col">


        <!-- Page Body Content -->
        <div class="space-y-6 overflow-y-auto p-8">

            <!-- Error Alert -->
            @if(session('error'))
                <div class="flex items-center space-x-2.5 rounded-2xl border border-rose-500/20 bg-rose-500/10 px-4 py-3 text-xs font-semibold text-rose-800">
                    <i class="fa-solid fa-triangle-exclamation text-rose-600"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <!-- Filter Bar -->
            <form action="{{ route('student.listings') }}"
                  method="GET"
                  class="flex flex-wrap items-center gap-3 rounded-2xl border border-slate-200/60 bg-white p-3 shadow-xs">

                <!-- Search Input -->
                <div class="relative min-w-[240px] flex-1">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Search title or location..."
                           class="w-full rounded-xl border-none bg-slate-50/80 py-2 pl-9 pr-4 text-xs font-medium text-slate-800 placeholder-slate-400 transition focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20">
                </div>

                <!-- Location Select
                <select name="location"
                        class="rounded-xl border border-slate-200/80 bg-white px-3 py-2 text-xs font-semibold text-slate-600 transition focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20">
                    <option value="">All Locations</option>
                    <option value="Cheras" {{ request('location') == 'Cheras' ? 'selected' : '' }}>Cheras</option>
                    <option value="Maluri" {{ request('location') == 'Maluri' ? 'selected' : '' }}>Maluri</option>
                    <option value="Pandan Indah" {{ request('location') == 'Pandan Indah' ? 'selected' : '' }}>Pandan Indah</option>
                    <option value="Petaling Jaya" {{ request('location') == 'Petaling Jaya' ? 'selected' : '' }}>Petaling Jaya</option>
                </select>-->

                <!-- Rent Select -->
                <select name="rent"
                        class="rounded-xl border border-slate-200/80 bg-white px-3 py-2 text-xs font-semibold text-slate-600 transition focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20">
                    <option value="">All Prices</option>
                    <option value="below500" {{ request('rent') == 'below500' ? 'selected' : '' }}>Below RM 500</option>
                    <option value="500-800" {{ request('rent') == '500-800' ? 'selected' : '' }}>RM 500 - RM 800</option>
                    <option value="800-1000" {{ request('rent') == '800-1000' ? 'selected' : '' }}>RM 800 - RM 1,000</option>
                    <option value="above1000" {{ request('rent') == 'above1000' ? 'selected' : '' }}>Above RM 1,000</option>
                </select>

                <!-- Room Type Select -->
                <select name="room_type"
                        class="rounded-xl border border-slate-200/80 bg-white px-3 py-2 text-xs font-semibold text-slate-600 transition focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20">
                    <option value="">All Room Types</option>
                    <option value="Single" {{ request('room_type') == 'Single' ? 'selected' : '' }}>Single</option>
                    <option value="Shared" {{ request('room_type') == 'Shared' ? 'selected' : '' }}>Shared</option>
                </select>

                <!-- Sort Select -->
                <select name="sort"
                        class="rounded-xl border border-slate-200/80 bg-white px-3 py-2 text-xs font-semibold text-slate-600 transition focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20">
                    <option value="newest" {{ request('sort', 'newest') == 'newest' ? 'selected' : '' }}>Newest</option>
                    <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                    <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                </select>

                <label for="pax" class="text-xs font-semibold text-slate-600">Pax</label>
                <select id="pax" name="pax"
                        class="rounded-xl border border-slate-200/80 bg-white px-3 py-2 text-xs font-semibold text-slate-600 transition focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20">
                    <option value="">All</option>
                    @foreach(range(1, 4) as $pax)
                        <option value="{{ $pax }}" @selected(request('pax') == $pax)>{{ $pax }} pax</option>
                    @endforeach
                    <option value="5+" @selected(request('pax') === '5+')>5+ pax</option>
                </select>

                <!-- Apply Filters Button -->
                <button type="submit"
                        class="rounded-xl bg-brand-600 px-4 py-2 text-xs font-bold text-white shadow-xs transition hover:bg-brand-700 active:scale-95">
                    <i class="fa-solid fa-filter mr-1.5"></i>
                    Apply Filters
                </button>

                <!-- Clear Filters Button -->
                @if(request()->hasAny(['search', 'location', 'rent', 'room_type', 'sort', 'pax']))
                    <a href="{{ route('student.listings') }}"
                       class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-bold text-slate-600 transition hover:bg-slate-50">
                        <i class="fa-solid fa-xmark mr-1.5"></i>
                        Clear
                    </a>
                @endif
            </form>

            <!-- Active Filters Notification Bar -->
            @if(request()->hasAny(['search', 'location', 'rent', 'room_type', 'sort', 'pax']))
                <div class="flex items-center justify-between rounded-2xl border border-brand-100 bg-brand-50/50 px-4 py-3">
                    <div class="flex items-center space-x-2 text-xs font-semibold text-brand-900">
                        <i class="fa-solid fa-filter text-brand-600"></i>
                        <span>Showing filtered results</span>
                    </div>
                    <span class="text-xs font-bold text-brand-600">
                        {{ $listings->total() }} {{ $listings->total() == 1 ? 'listing' : 'listings' }}
                    </span>
                </div>
            @endif

            <!-- Listings Grid -->
            @if($listings->count() > 0)
                <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3">
                    @foreach($listings as $listing)
                        @php $isSaved = Auth::user()->savedListings()->whereKey($listing->getKey())->exists(); @endphp
                        <article class="group relative flex cursor-pointer flex-col justify-between overflow-hidden rounded-2xl border border-slate-200/60 bg-white shadow-xs transition-all duration-200 hover:-translate-y-1 hover:shadow-md">
                            <a href="{{ route('student.listings.show', $listing->getKey()) }}" class="block">
                                <!-- Image Container -->
                                <div class="relative aspect-video overflow-hidden bg-slate-100">
                                    @if($listing->listing_photo)
                                        <img src="{{ asset('storage/' . $listing->listing_photo) }}"
                                             alt="{{ $listing->listing_title }}"
                                             class="h-full w-full object-cover transition duration-300 group-hover:scale-105 {{ $listing->listing_availability === 'rented' ? 'opacity-75 saturate-50' : '' }}">
                                    @else
                                        <div class="flex h-full w-full items-center justify-center text-slate-300">
                                            <i class="fa-solid fa-image text-3xl"></i>
                                        </div>
                                    @endif

                                    @if((string) $listing->user_id === (string) Auth::id() || $listing->listing_availability !== 'available')
                                    <span class="absolute left-3 top-3 z-10 inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[10px] font-bold shadow-sm {{ $listing->listing_availability === 'available' ? 'bg-emerald-100 text-emerald-700' : ($listing->listing_availability === 'rented' ? 'bg-rose-100 text-rose-700' : 'bg-slate-100 text-slate-600') }}">
                                        <span class="h-1.5 w-1.5 rounded-full {{ $listing->listing_availability === 'available' ? 'bg-emerald-500' : ($listing->listing_availability === 'rented' ? 'bg-rose-500' : 'bg-slate-500') }}"></span>
                                        {{ ucfirst($listing->listing_availability ?? 'available') }}
                                    </span>
                                    @endif


                                    <!-- Hover Overlay Indicator -->
                                    <div class="absolute inset-0 flex items-center justify-center bg-slate-900/0 transition duration-300 group-hover:bg-slate-900/20">
                                        <div class="flex items-center space-x-2 rounded-full bg-white/95 px-4 py-2 text-xs font-bold text-slate-900 opacity-0 shadow-sm transition duration-300 group-hover:opacity-100">
                                            <i class="fa-solid fa-eye text-brand-600"></i>
                                            <span>View Listing</span>
                                        </div>
                                    </div>

                                </div>

                                <!-- Card Details -->
                                <div class="p-4">
                                    <h5 class="line-clamp-1 text-sm font-bold text-slate-900 transition group-hover:text-brand-600">
                                        {{ $listing->listing_title }}
                                    </h5>

                                    <div class="mt-1.5 flex items-center space-x-1.5 text-[11px] text-slate-500">
                                        <i class="fa-solid fa-location-dot text-slate-400"></i>
                                        <span class="line-clamp-1 font-medium">{{ $listing->listing_location }}</span>
                                    </div>

                                    <div class="mt-3 flex flex-wrap items-baseline gap-x-1 gap-y-1">
                                        <span class="text-base font-extrabold text-brand-600">
                                            RM {{ number_format($listing->listing_rent, 2) }}
                                        </span>
                                        <span class="text-xs font-medium text-slate-400">/mo</span>

                                        @if($listing->room_type)
                                            <span class="mx-1 text-xs text-slate-300">•</span>
                                            <span class="text-xs font-semibold text-slate-500">
                                                {{ $listing->room_type }}
                                            </span>
                                        @endif
                                        <span class="mx-1 text-xs text-slate-300">&bull;</span>
                                        <span class="text-xs font-semibold text-slate-500">
                                            @include('student.listings.pax-label')
                                        </span>
                                    </div>
                                </div>
                            </a>

                            @if($listing->listing_availability === 'available')
                            <form action="{{ route('student.listings.save', $listing->getKey()) }}" method="POST" class="save-listing-form absolute right-3 top-3 z-20" data-title="{{ $listing->listing_title }}" data-saved="{{ $isSaved ? 'true' : 'false' }}">
                                @csrf
                                <button type="submit" class="save-listing-button flex h-9 w-9 items-center justify-center rounded-full bg-white/95 text-brand-600 shadow-sm transition hover:bg-brand-50" aria-label="{{ $isSaved ? 'Remove ' : 'Save ' }}{{ $listing->listing_title }} {{ $isSaved ? 'from saved listings' : 'to saved listings' }}" title="{{ $isSaved ? 'Remove from saved listings' : 'Save listing' }}">
                                    <i class="save-listing-icon {{ $isSaved ? 'fa-solid' : 'fa-regular' }} fa-bookmark"></i>
                                </button>
                            </form>
                            @endif

                            <!-- Card Footer -->
                            <div class="border-t border-slate-100 bg-slate-50/60 px-4 py-2.5">
                                <div class="flex items-center justify-between text-[11px]">
                                    <span class="font-medium text-slate-400">
                                        {{ $listing->listing_created_at ? $listing->listing_created_at->diffForHumans() : 'Posted recently' }}
                                    </span>

                                    <span class="font-bold text-brand-600 opacity-0 transition group-hover:opacity-100">
                                        View details
                                        <i class="fa-solid fa-arrow-right text-[10px] ml-1"></i>
                                    </span>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($listings->hasPages())
                    <div class="mt-6 flex justify-center">
                        {{ $listings->links() }}
                    </div>
                @endif

            @else
                <!-- No Results State -->
                <div class="rounded-2xl border border-dashed border-slate-200 bg-white p-12 text-center">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                        <i class="fa-solid fa-house-circle-xmark text-lg"></i>
                    </div>

                    <h4 class="mt-4 text-xs font-bold uppercase tracking-wider text-slate-900">No Listings Found</h4>
                    <p class="mt-1 text-xs text-slate-400">No room listings match your current search filters.</p>

                    <a href="{{ route('student.listings') }}"
                       class="mt-4 inline-flex items-center space-x-2 rounded-xl bg-brand-600 px-4 py-2 text-xs font-bold text-white transition hover:bg-brand-700">
                        <i class="fa-solid fa-rotate-left"></i>
                        <span>Clear Filters</span>
                    </a>
                </div>
            @endif

        </div>
    </main>

</div>

<script>
    document.querySelectorAll('.save-listing-form').forEach((form) => {
        form.addEventListener('submit', async (event) => {
            event.preventDefault();

            const button = form.querySelector('.save-listing-button');
            const icon = form.querySelector('.save-listing-icon');
            button.disabled = true;

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: new FormData(form),
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });

                if (!response.ok) throw new Error('Unable to update saved listing.');

                const result = await response.json();
                const title = form.dataset.title;
                form.dataset.saved = String(result.saved);
                icon.classList.toggle('fa-solid', result.saved);
                icon.classList.toggle('fa-regular', !result.saved);
                button.setAttribute('aria-label', `${result.saved ? 'Remove ' : 'Save '}${title} ${result.saved ? 'from saved listings' : 'to saved listings'}`);
                button.title = result.saved ? 'Remove from saved listings' : 'Save listing';
            } catch (error) {
                form.submit();
            } finally {
                button.disabled = false;
            }
        });
    });
</script>

</body>
</html>