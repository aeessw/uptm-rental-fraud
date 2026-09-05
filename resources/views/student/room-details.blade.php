<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $listing->title }} - UPTM Rental</title>

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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-slate-50/80 font-sans antialiased text-slate-800">

@php
    /*
     * Build photo list.
     * Main listing photo is added first.
     * Additional photos are added after it.
     */
    $listingPhotos = [];

    if ($listing->photo) {
        $listingPhotos[] = asset('storage/' . $listing->photo);
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

        <!-- Top Header -->
        <header class="sticky top-0 z-10 flex items-center justify-between border-b border-slate-200/60 bg-white/80 px-8 py-4 backdrop-blur-md">
            <div class="flex items-center space-x-3">
                <h2 class="text-base font-bold tracking-tight text-slate-900">
                    Room Details: {{ $listing->title }}
                </h2>
            </div>

            <!-- User Profile Header -->
            <div class="flex items-center space-x-3">
                <div class="text-right">
                    <div class="mb-1 text-xs font-bold leading-none text-slate-900">
                        {{ Auth::user()->name }}
                    </div>
                    <div class="text-[11px] font-medium leading-none text-slate-400">
                        {{ Auth::user()->email }}
                    </div>
                </div>

                <div class="flex h-9 w-9 items-center justify-center rounded-full border border-brand-100 bg-brand-50 text-xs font-bold uppercase text-brand-600 ring-2 ring-brand-500/10">
                    {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                </div>
            </div>
        </header>

        <!-- ================= PAGE CONTENT ================= -->
        <div class="space-y-6 overflow-y-auto p-8">

            <!-- Success Message -->
            @if(session('success'))
                <div class="flex items-center space-x-2.5 rounded-xl border border-emerald-500/20 bg-emerald-500/10 p-4 text-xs font-semibold text-emerald-800">
                    <i class="fa-solid fa-circle-check text-emerald-600"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <!-- Error Message -->
            @if(session('error'))
                <div class="flex items-center space-x-2.5 rounded-xl border border-rose-500/20 bg-rose-500/10 p-4 text-xs font-semibold text-rose-800">
                    <i class="fa-solid fa-circle-exclamation text-rose-600"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <!-- ================= LISTING CARD ================= -->
            <div class="mx-auto max-w-5xl rounded-2xl border border-slate-200/60 bg-white p-6 shadow-xs md:p-8">

                <!-- PHOTO AREA -->
                <div class="relative h-[380px] w-full overflow-hidden rounded-xl bg-slate-100">
                    @if(count($listingPhotos) > 0)
                        <!-- Clickable Main Photo -->
                        <button type="button"
                                onclick="openPhotoViewer(0)"
                                class="group block h-full w-full cursor-pointer">
                            <img src="{{ $listingPhotos[0] }}"
                                 alt="{{ $listing->title }}"
                                 class="h-full w-full object-cover transition duration-300 group-hover:scale-105">

                            <!-- Hover Overlay -->
                            <div class="absolute inset-0 flex items-center justify-center bg-slate-900/0 transition group-hover:bg-slate-900/30">
                                <div class="rounded-full bg-white/90 px-4 py-2 text-xs font-bold text-slate-700 opacity-0 shadow-md transition group-hover:opacity-100">
                                    <i class="fa-solid fa-expand mr-1.5"></i>
                                    View Photos
                                </div>
                            </div>
                        </button>
                    @else
                        <div class="flex h-full w-full items-center justify-center text-slate-300">
                            <i class="fa-solid fa-image text-5xl"></i>
                        </div>
                    @endif

                    <!-- Photo Count Badge -->
                    @if(count($listingPhotos) > 1)
                        <span class="absolute bottom-4 left-4 rounded-full bg-slate-900/80 px-3 py-1.5 text-[10px] font-bold text-white backdrop-blur-xs">
                            <i class="fa-solid fa-images mr-1"></i>
                            {{ count($listingPhotos) }} photos
                        </span>
                    @endif
                </div>

                <!-- LISTING DETAILS -->
                <div class="mt-6 flex flex-col justify-between md:flex-row md:items-start">
                    <div>
                        <h1 class="text-xl font-extrabold tracking-tight text-slate-900">
                            {{ $listing->title }}
                        </h1>

                        <div class="mt-1 flex items-center space-x-1.5 text-xs font-medium text-slate-500">
                            <i class="fa-solid fa-location-dot text-slate-400"></i>
                            <span>{{ $listing->location }}</span>
                        </div>
                    </div>

                    <div class="mt-4 text-left md:mt-0 md:text-right">
                        <div class="text-2xl font-extrabold tracking-tight text-brand-600">
                            RM {{ number_format($listing->rent, 0) }}<span class="text-xs font-medium text-slate-400">/mo</span>
                        </div>

                        <span class="mt-1 inline-block rounded-md border border-brand-100 bg-brand-50 px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider text-brand-600">
                            {{ $listing->room_type ?? 'N/A' }}
                        </span>
                    </div>
                </div>

                <hr class="my-6 border-slate-100">

                <!-- Description -->
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-900">
                        Listing Description
                    </h3>

                    <p class="mt-2.5 whitespace-pre-line text-xs font-medium leading-relaxed text-slate-600">
                        {{ $listing->description }}
                    </p>
                </div>

                <!-- PHOTO THUMBNAILS -->
                @if(count($listingPhotos) > 1)
                    <div class="mt-6 border-t border-slate-100 pt-5">
                        <div class="mb-3 flex items-center justify-between">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-900">Photos</h3>
                            <span class="text-[10px] font-medium text-slate-400">Click a photo to view</span>
                        </div>

                        <div class="grid grid-cols-4 gap-3 sm:grid-cols-5 md:grid-cols-6">
                            @foreach($listingPhotos as $index => $photo)
                                <button type="button"
                                        onclick="openPhotoViewer({{ $index }})"
                                        class="group relative aspect-square overflow-hidden rounded-xl border border-slate-200/80 bg-slate-100">
                                    <img src="{{ $photo }}"
                                         alt="{{ $listing->title }} photo {{ $index + 1 }}"
                                         class="h-full w-full object-cover transition duration-300 group-hover:scale-110">

                                    <div class="absolute inset-0 flex items-center justify-center bg-slate-900/0 transition group-hover:bg-slate-900/30">
                                        <i class="fa-solid fa-expand text-xs text-white opacity-0 transition group-hover:opacity-100"></i>
                                    </div>
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- ================= BOTTOM SECTION: OWNER CARD & ACTIONS ================= -->
                <div class="mt-8 border-t border-slate-100 pt-6">
                    <div class="grid grid-cols-1 items-center gap-4 md:grid-cols-2">

                        <!-- OWNER INFO CARD -->
                        <div class="rounded-xl border border-slate-200/60 bg-slate-50/60 p-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3.5">
                                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-brand-600 text-sm font-bold text-white shadow-xs">
                                        {{ strtoupper(substr($listing->user->name ?? 'User', 0, 2)) }}
                                    </div>
                                    <div>
                                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                                            Posted By
                                        </span>
                                        <h4 class="text-sm font-bold text-slate-900">
                                            {{ $listing->user->name ?? 'Unknown Student' }}
                                        </h4>
                                        @if(isset($listing->user->email))
                                            <p class="text-[11px] font-medium text-slate-400">
                                                {{ $listing->user->email }}
                                            </p>
                                        @endif
                                    </div>
                                </div>

                                @if(Auth::id() !== $listing->user_id)
                                    <a href="{{ route('student.messages', $listing->user_id) }}"
                                       class="inline-flex items-center space-x-2 rounded-xl bg-brand-600 px-4 py-2.5 text-xs font-bold text-white shadow-xs transition hover:bg-brand-700">
                                        <i class="fa-regular fa-comment"></i>
                                        <span>Contact Poster</span>
                                    </a>
                                @else
                                    <span class="rounded-lg bg-slate-200/60 px-3 py-1 text-[11px] font-bold text-slate-500">
                                        Your Listing
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- ACTION BUTTONS (REPORT / EDIT) -->
                        <div class="flex items-center justify-end">
                            @if(Auth::id() === $listing->user_id)
                                <a href="{{ route('student.listings.edit', $listing->id) }}"
                                   class="flex w-full items-center justify-center space-x-2 rounded-xl border border-brand-200/80 bg-brand-50 px-6 py-3 text-xs font-bold text-brand-600 transition hover:bg-brand-100 md:w-auto">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                    <span>Edit Listing</span>
                                </a>
                            @else
                                <button type="button"
                                        onclick="document.getElementById('report-modal').classList.remove('hidden')"
                                        class="flex w-full items-center justify-center space-x-2 rounded-xl border border-rose-200/80 bg-rose-50/50 px-5 py-3 text-xs font-bold text-rose-600 transition hover:bg-rose-100/60 md:w-auto">
                                    <i class="fa-solid fa-triangle-exclamation"></i>
                                    <span>Report Listing</span>
                                </button>
                            @endif
                        </div>

                    </div>
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
                 alt="{{ $listing->title }}"
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
              action="{{ route('student.listings.report', $listing->id) }}"
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