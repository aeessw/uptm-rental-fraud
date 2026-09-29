<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $listing->listing_title }} - UPTM Rental</title>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN & Custom Config -->
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

<body class="bg-[#f4f7fb] font-sans antialiased text-slate-800">

@php
    /*
     * Build photo list.
     * Main listing photo is added first.
     * Additional photos are added after it.
     */
    $listingPhotos = [];

    if ($listing->listing_photo) {
        $listingPhotos[] = asset('storage/' . $listing->listing_photo);
    }

    if ($listing->photos && $listing->photos->count() > 0) {
        foreach ($listing->photos as $additionalPhoto) {
            if (!empty($additionalPhoto->photo_path)) {
                $listingPhotos[] = asset('storage/' . $additionalPhoto->photo_path);
            } elseif (!empty($additionalPhoto->path)) {
                $listingPhotos[] = asset('storage/' . $additionalPhoto->path);
            } elseif (!empty($additionalPhoto->filename)) {
                $listingPhotos[] = asset('storage/' . $additionalPhoto->filename);
            }
        }
    }

    $listingPhotos = array_values(array_unique($listingPhotos));
@endphp

<div class="flex min-h-screen">

    <!-- Sidebar Navigation Included Here -->
    @include('student.sidebar')

    <!-- ================= MAIN CONTENT ================= -->
    <main class="flex min-w-0 flex-1 flex-col">


        <!-- ================= PAGE CONTENT ================= -->
        <div class="overflow-y-auto px-4 py-5 sm:px-6 lg:px-10 lg:py-8">
            <div class="mx-auto w-full max-w-6xl">
                <div class="mb-5 flex items-center justify-between">
                    <a href="{{ route('student.listings') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-500 transition hover:text-brand-600">
                        <i class="fa-solid fa-arrow-left text-xs"></i>
                        <span>Back to listings</span>
                    </a>
                    <button type="button"
                            onclick="copyListingLink()"
                            class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-600 shadow-sm transition hover:border-brand-200 hover:text-brand-600"
                            aria-label="Copy listing link">
                        <i class="fa-solid fa-share-nodes text-xs"></i>
                        <span id="copy-link-label">Share</span>
                    </button>
                </div>

                @if(session('error'))
                    <div class="mb-5 flex items-center gap-2.5 rounded-xl border border-rose-500/20 bg-rose-500/10 p-4 text-sm font-semibold text-rose-800">
                        <i class="fa-solid fa-circle-exclamation text-rose-600"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                <div class="grid grid-cols-1 gap-8 lg:grid-cols-[minmax(0,1fr)_280px]">
                    <div class="min-w-0">
                        <section id="photos" aria-label="Room photos">
                            @if(count($listingPhotos) > 0)
                                <div class="grid h-[280px] grid-cols-2 gap-2 overflow-hidden rounded-2xl sm:h-[390px] lg:h-[430px] {{ count($listingPhotos) > 1 ? 'lg:grid-cols-[minmax(0,1.65fr)_minmax(180px,0.8fr)]' : '' }}">
                                    <button type="button" onclick="openPhotoViewer(0)" class="group relative min-h-0 overflow-hidden rounded-xl bg-slate-100 {{ count($listingPhotos) === 1 ? 'col-span-2' : '' }}">
                                        <img src="{{ $listingPhotos[0] }}" alt="{{ $listing->listing_title }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                                        <span class="absolute bottom-3 left-3 rounded-full bg-slate-950/75 px-3 py-1.5 text-xs font-semibold text-white backdrop-blur-sm">
                                            <i class="fa-solid fa-images mr-1"></i>{{ count($listingPhotos) }} {{ count($listingPhotos) === 1 ? 'photo' : 'photos' }}
                                        </span>
                                    </button>

                                    @if(count($listingPhotos) > 1)
                                        <div class="grid min-h-0 grid-cols-2 gap-2 lg:grid-cols-1">
                                            @foreach(array_slice($listingPhotos, 1, 2) as $index => $photo)
                                                <button type="button" onclick="openPhotoViewer({{ $index + 1 }})" class="group relative min-h-0 overflow-hidden rounded-xl bg-slate-100">
                                                    <img src="{{ $photo }}" alt="{{ $listing->listing_title }} photo {{ $index + 2 }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                                                    @if($index === 1 && count($listingPhotos) > 2)
                                                        <span class="absolute bottom-3 right-3 rounded-full bg-white/95 px-3 py-1.5 text-xs font-semibold text-slate-800 shadow-md">View all photos</span>
                                                    @endif
                                                </button>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @else
                                <div class="flex h-[280px] items-center justify-center rounded-2xl bg-slate-100 text-slate-300 sm:h-[390px]">
                                    <i class="fa-regular fa-image text-5xl"></i>
                                </div>
                            @endif
                        </section>

                        <section class="mt-6" aria-labelledby="listing-title">
                            <div class="flex flex-wrap items-start justify-between gap-4">
                                <div>
                                    <h1 id="listing-title" class="text-2xl font-bold tracking-tight text-slate-950 sm:text-3xl">{{ $listing->listing_title }}</h1>
                                    @if((string) $listing->user_id === (string) Auth::id() || $listing->listing_availability !== 'available')
                                    <span class="mt-2 inline-flex items-center rounded-full px-2.5 py-1 text-[10px] font-bold {{ $listing->listing_availability === 'available' ? 'bg-emerald-100 text-emerald-700' : ($listing->listing_availability === 'rented' ? 'bg-rose-100 text-rose-700' : 'bg-slate-100 text-slate-600') }}">
                                        <span class="mr-1.5 h-1.5 w-1.5 rounded-full {{ $listing->listing_availability === 'available' ? 'bg-emerald-500' : ($listing->listing_availability === 'rented' ? 'bg-rose-500' : 'bg-slate-500') }}"></span>
                                        {{ ucfirst($listing->listing_availability ?? 'available') }}
                                    </span>
                                    @endif
                                    <p class="mt-2 flex items-center gap-2 text-sm font-medium text-slate-500">
                                        <i class="fa-solid fa-location-dot text-brand-500"></i>{{ $listing->listing_location }}
                                    </p>
                                </div>
                            </div>
                        </section>

                        <nav class="mt-7 flex gap-6 overflow-x-auto border-b border-slate-200" aria-label="Listing sections">
                            <a href="#description" class="border-b-2 border-brand-600 pb-3 text-sm font-semibold text-slate-900">Room details</a>
                        </nav>

                        <section class="pt-7" aria-labelledby="rental-details-heading">
                            <h2 id="rental-details-heading" class="text-base font-bold text-slate-900">Rental Details</h2>
                            <dl class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-3 text-sm">
                                <div><dt class="text-slate-500">Available From</dt><dd class="mt-1 font-semibold text-slate-800">{{ $listing->available_from?->format('d M Y') ?? 'Not specified' }}</dd></div>
                                <div><dt class="text-slate-500">Rental Period</dt><dd class="mt-1 font-semibold text-slate-800">{{ \App\Models\Listing::RENTAL_PERIODS[$listing->rental_period] ?? 'Not specified' }}</dd></div>
                                <div><dt class="text-slate-500">Preferred Tenant</dt><dd class="mt-1 font-semibold text-slate-800">{{ \App\Models\Listing::TENANT_PREFERENCES[$listing->preferred_tenant] ?? 'Not specified' }}</dd></div>
                            </dl>
                        </section>
                        <section class="pt-7" aria-labelledby="facilities-heading">
                            <h2 id="facilities-heading" class="text-base font-bold text-slate-900">Facilities</h2>
                            <ul class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2 text-sm text-slate-600">
                                @forelse($listing->facilities ?? [] as $facility)
                                    <li><i class="fa-solid fa-check mr-2 text-emerald-600" aria-hidden="true"></i>{{ \App\Models\Listing::FACILITIES[$facility] ?? $facility }}</li>
                                @empty
                                    <li>Facilities not specified.</li>
                                @endforelse
                            </ul>
                        </section>

                        <section id="description" class="pt-7" aria-labelledby="description-heading">
                            <h2 id="description-heading" class="text-base font-bold text-slate-900">Room Description</h2>
                            <p class="mt-4 whitespace-pre-line text-sm font-medium leading-7 text-slate-600">{{ $listing->listing_description }}</p>
                        </section>

                    </div>

                    <aside>
                        <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm lg:h-[430px]" aria-labelledby="details-heading">
                            <h2 id="details-heading" class="border-b border-slate-100 pb-3 text-base font-semibold text-slate-900">Details</h2>

                            <div class="border-b border-slate-100 py-3" aria-labelledby="rent-heading">
                                <h3 id="rent-heading" class="text-xs font-semibold text-slate-500">Monthly rent</h3>
                                <p class="mt-1 text-2xl font-bold tracking-tight text-slate-950">RM {{ number_format($listing->listing_rent, 2) }}<span class="text-sm font-medium text-slate-400"> / month</span></p>
                            </div>

                            <div class="border-b border-slate-100 py-3">
                                <div>
                                    <p class="text-xs font-medium text-slate-400">Room type</p>
                                    <p class="mt-1 text-sm font-semibold text-slate-800">{{ $listing->room_type }} <span class="font-normal text-slate-500">&middot; @include('student.listings.pax-label')</span></p>
                                </div>
                            </div>

                            <div class="py-3" aria-labelledby="poster-heading">
                                <h3 id="poster-heading" class="text-sm font-bold text-slate-900">Posted by</h3>
                                <div class="mt-3 flex items-center gap-3">
                                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-[#E0E7FF] text-xs font-semibold text-[#123568]">{{ strtoupper(substr($listing->user->user_name ?? 'S', 0, 1)) }}</div>
                                    <div class="min-w-0">
                                        <p class="break-words text-sm font-semibold leading-5 text-slate-900">{{ $listing->user->user_name ?? 'Unknown Student' }}</p>
                                    </div>
                                </div>
                                @if(Auth::id() !== $listing->user_id)
                                    <a href="{{ route('student.messages', ['userId' => $listing->user_id, 'listing_id' => $listing->getKey()]) }}" class="mt-3 inline-flex w-full items-center justify-center gap-2 rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-700">
                                        <i class="fa-regular fa-comment"></i>Send Message
                                    </a>
                                @else
                                    <span class="mt-3 block rounded-xl bg-slate-100 px-4 py-2.5 text-center text-sm font-semibold text-slate-500">Your Listing</span>
                                @endif
                            </div>

                            @if(Auth::id() === $listing->user_id)
                                <a href="{{ route('student.listings.edit', $listing->getKey()) }}" class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-brand-200 bg-brand-50 px-4 py-2.5 text-sm font-semibold text-brand-600 transition hover:bg-brand-100">
                                    <i class="fa-solid fa-pen-to-square"></i>Edit Listing
                                </a>
                            @else
                                <div class="mt-3 grid grid-cols-2 gap-2">
                                    @if($listing->listing_availability === 'available')
                                        <form id="save-listing-form" action="{{ route('student.listings.save', $listing->getKey()) }}" method="POST">
                                            @csrf
                                            @php $isSaved = Auth::user()->savedListings()->where('listings.listing_id', $listing->getKey())->exists(); @endphp
                                            <button type="submit" id="save-listing-button" class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-brand-200 bg-brand-50 px-3 py-2.5 text-sm font-semibold text-brand-600 transition hover:bg-brand-100">
                                                <i id="save-listing-icon" class="{{ $isSaved ? 'fa-solid' : 'fa-regular' }} fa-bookmark"></i><span id="save-listing-label">{{ $isSaved ? 'Saved' : 'Save' }}</span>
                                            </button>
                                        </form>
                                    @else
                                        <span class="inline-flex w-full cursor-not-allowed items-center justify-center gap-2 rounded-xl border border-slate-200 bg-slate-100 px-3 py-2.5 text-sm font-semibold text-slate-500" title="This listing is rented and unavailable">
                                            <i class="fa-solid fa-ban"></i><span>Unavailable</span>
                                        </span>
                                    @endif
                                    <button type="button" onclick="document.getElementById('report-modal').classList.remove('hidden')" class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-rose-200 bg-rose-50 px-3 py-2.5 text-sm font-semibold text-rose-600 transition hover:bg-rose-100">
                                        <i class="fa-solid fa-triangle-exclamation"></i><span>Report</span>
                                    </button>
                                </div>
                            @endif
                        </section>
                    </aside>
                </div>
            </div>

        </div>

    </main>

</div>

<!-- PHOTO VIEWER MODAL -->
<div id="photo-viewer"
     class="fixed inset-0 z-[100] hidden items-center justify-center bg-slate-950/90 p-4 backdrop-blur-sm">
    <div class="relative flex h-full w-full items-center justify-center">

        <!-- Close Button -->
        <button type="button"
                onclick="closePhotoViewer()"
                class="absolute right-2 top-2 z-30 flex h-11 w-11 items-center justify-center rounded-full bg-white/10 text-xl text-white transition hover:bg-white/20 md:right-5 md:top-5">
            <i class="fa-solid fa-xmark"></i>
        </button>

        @if(count($listingPhotos) > 1)
            <button type="button"
                    onclick="changePhoto(-1)"
                    class="absolute left-2 z-30 flex h-12 w-12 items-center justify-center rounded-full bg-white/10 text-white transition hover:bg-white/20 md:left-8">
                <i class="fa-solid fa-chevron-left"></i>
            </button>
        @endif

        <!-- Expanded Image Container -->
        <div class="flex w-full max-w-4xl flex-col items-center justify-center px-4 md:px-12">
            <img id="photo-viewer-image"
                 src="{{ count($listingPhotos) > 0 ? $listingPhotos[0] : '' }}"
                 alt="{{ $listing->listing_title }}"
                 class="max-h-[80vh] w-full rounded-2xl object-contain shadow-2xl">

            @if(count($listingPhotos) > 1)
                <div id="photo-counter"
                     class="mt-4 rounded-full bg-white/10 px-4 py-1.5 text-xs font-semibold text-white backdrop-blur-sm">
                    1 / {{ count($listingPhotos) }}
                </div>
            @endif
        </div>

        @if(count($listingPhotos) > 1)
            <button type="button"
                    onclick="changePhoto(1)"
                    class="absolute right-2 z-30 flex h-12 w-12 items-center justify-center rounded-full bg-white/10 text-white transition hover:bg-white/20 md:right-8">
                <i class="fa-solid fa-chevron-right"></i>
            </button>
        @endif

    </div>
</div>

<!-- REPORT MODAL -->
<div id="report-modal"
     class="fixed inset-0 z-50 flex hidden items-center justify-center bg-slate-900/40 backdrop-blur-sm">
    <div class="w-full max-w-md rounded-2xl border border-slate-200/60 bg-white p-6 shadow-xl">

        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <div class="flex items-center space-x-2 text-rose-600">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <h3 class="text-sm font-bold text-slate-900">Report Listing</h3>
            </div>

            <button type="button"
                    onclick="document.getElementById('report-modal').classList.add('hidden')"
                    class="text-slate-400 transition hover:text-slate-600">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form id="report-form"
              action="{{ route('student.listings.report', $listing->getKey()) }}"
              method="POST"
              class="mt-4 space-y-4"
              onsubmit="appendReasonDetails()">
            @csrf

            <input type="hidden" name="reason" id="final_reason">

            <div>
                <label class="mb-2 block text-xs font-bold text-slate-700">Reason for Reporting</label>
                <div class="space-y-2.5 text-xs">
                    <label class="flex cursor-pointer items-center space-x-2.5 font-medium text-slate-600 hover:text-slate-900">
                        <input type="radio" name="reason_type" value="Suspected Fraud / Scam" class="text-brand-600 focus:ring-brand-500" required>
                        <span>Suspected Fraud / Scam</span>
                    </label>

                    <label class="flex cursor-pointer items-center space-x-2.5 font-medium text-slate-600 hover:text-slate-900">
                        <input type="radio" name="reason_type" value="Inaccurate / Misleading Information" class="text-brand-600 focus:ring-brand-500">
                        <span>Inaccurate / Misleading Information</span>
                    </label>

                    <label class="flex cursor-pointer items-center space-x-2.5 font-medium text-slate-600 hover:text-slate-900">
                        <input type="radio" name="reason_type" value="Duplicate Listing" class="text-brand-600 focus:ring-brand-500">
                        <span>Duplicate Listing</span>
                    </label>

                    <label class="flex cursor-pointer items-center space-x-2.5 font-medium text-slate-600 hover:text-slate-900">
                        <input type="radio" name="reason_type" value="Other" class="text-brand-600 focus:ring-brand-500">
                        <span>Other</span>
                    </label>
                </div>
            </div>

            <div>
                <label for="details" class="mb-1 block text-xs font-bold text-slate-700">
                    Additional Details (Optional)
                </label>
                <textarea id="details"
                          rows="3"
                          class="w-full rounded-xl border border-slate-200 p-3 text-xs focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500"
                          placeholder="Provide extra details to help admins verify this report..."></textarea>
            </div>

            <div class="flex justify-end space-x-2 pt-2">
                <button type="button"
                        onclick="document.getElementById('report-modal').classList.add('hidden')"
                        class="rounded-xl border border-slate-200 px-4 py-2 text-xs font-bold text-slate-600 transition hover:bg-slate-50">
                    Cancel
                </button>

                <button type="submit"
                        class="rounded-xl bg-rose-600 px-4 py-2 text-xs font-bold text-white transition hover:bg-rose-700 shadow-xs">
                    Submit Report
                </button>
            </div>
        </form>

    </div>
</div>

<!-- JAVASCRIPT -->
<script>
    const listingPhotos = @json($listingPhotos);
    let currentPhotoIndex = 0;

    const saveListingForm = document.getElementById('save-listing-form');
    const saveListingButton = document.getElementById('save-listing-button');
    const saveListingIcon = document.getElementById('save-listing-icon');
    const saveListingLabel = document.getElementById('save-listing-label');

    saveListingForm?.addEventListener('submit', async function (event) {
        event.preventDefault();
        saveListingButton.disabled = true;

        try {
            const response = await fetch(saveListingForm.action, {
                method: 'POST',
                body: new FormData(saveListingForm),
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            if (!response.ok) throw new Error('Unable to update saved listing.');

            const result = await response.json();
            saveListingIcon.classList.toggle('fa-solid', result.saved);
            saveListingIcon.classList.toggle('fa-regular', !result.saved);
            saveListingLabel.textContent = result.saved ? 'Saved' : 'Save';
        } catch (error) {
            saveListingForm.submit();
            return;
        } finally {
            saveListingButton.disabled = false;
        }
    });

    function openPhotoViewer(index = 0) {
        if (!listingPhotos.length) return;
        currentPhotoIndex = index;
        updatePhotoViewer();

        const modal = document.getElementById('photo-viewer');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.classList.add('overflow-hidden');
    }

    function closePhotoViewer() {
        const modal = document.getElementById('photo-viewer');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.classList.remove('overflow-hidden');
    }

    function changePhoto(direction) {
        if (!listingPhotos.length) return;
        currentPhotoIndex += direction;

        if (currentPhotoIndex < 0) {
            currentPhotoIndex = listingPhotos.length - 1;
        }

        if (currentPhotoIndex >= listingPhotos.length) {
            currentPhotoIndex = 0;
        }

        updatePhotoViewer();
    }

    function updatePhotoViewer() {
        const image = document.getElementById('photo-viewer-image');
        const counter = document.getElementById('photo-counter');

        if (!image || !listingPhotos[currentPhotoIndex]) return;

        image.src = listingPhotos[currentPhotoIndex];

        if (counter) {
            counter.textContent = `${currentPhotoIndex + 1} / ${listingPhotos.length}`;
        }
    }

    function copyListingLink() {
        const label = document.getElementById('copy-link-label');
        const listingUrl = window.location.href;

        const showCopiedState = function () {
            label.textContent = 'Copied';
            window.setTimeout(function () {
                label.textContent = 'Share';
            }, 1800);
        };

        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(listingUrl).then(showCopiedState);
            return;
        }

        const temporaryInput = document.createElement('textarea');
        temporaryInput.value = listingUrl;
        temporaryInput.setAttribute('readonly', '');
        temporaryInput.style.position = 'fixed';
        temporaryInput.style.opacity = '0';
        document.body.appendChild(temporaryInput);
        temporaryInput.select();
        document.execCommand('copy');
        temporaryInput.remove();
        showCopiedState();
    }

    document.getElementById('photo-viewer').addEventListener('click', function(event) {
        if (event.target === this) {
            closePhotoViewer();
        }
    });

    document.addEventListener('keydown', function(event) {
        const modal = document.getElementById('photo-viewer');
        if (modal.classList.contains('hidden')) return;

        if (event.key === 'ArrowLeft') changePhoto(-1);
        if (event.key === 'ArrowRight') changePhoto(1);
        if (event.key === 'Escape') closePhotoViewer();
    });

    function appendReasonDetails() {
        const selectedReason = document.querySelector('input[name="reason_type"]:checked')?.value || 'Unspecified';
        const detailsText = document.getElementById('details').value.trim();

        if (detailsText) {
            document.getElementById('final_reason').value = `${selectedReason}: ${detailsText}`;
        } else {
            document.getElementById('final_reason').value = selectedReason;
        }
    }
</script>

</body>
</html>