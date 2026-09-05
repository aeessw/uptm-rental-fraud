<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Listing - UPTM Rental</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome -->
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    >
</head>

<body class="bg-[#F8FAFC] min-h-screen font-sans text-slate-800 antialiased">

<div class="flex min-h-screen w-full">

    <!-- ========================================================= -->
    <!-- SIDEBAR -->
    <!-- ========================================================= -->

    @include('student.sidebar')


    <!-- ========================================================= -->
    <!-- MAIN -->
    <!-- ========================================================= -->

    <main class="min-w-0 flex-1 overflow-y-auto">

        <div class="mx-auto w-full max-w-6xl px-6 py-8">

            <!-- ================================================= -->
            <!-- PAGE HEADING -->
            <!-- ================================================= -->

            <div class="mb-6">

                <h2 class="text-2xl font-bold text-slate-900">
                    Edit Your Room
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Update your listing information and manage your photos.
                </p>

            </div>


            <!-- ================================================= -->
            <!-- ERROR MESSAGE -->
            <!-- ================================================= -->

            @if($errors->any())

                <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-4">

                    <div class="flex items-start gap-3">

                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-red-100 text-red-600">
                            <i class="fa-solid fa-triangle-exclamation text-xs"></i>
                        </div>

                        <div>

                            <p class="text-xs font-bold text-red-700">
                                Unable to update listing
                            </p>

                            <ul class="mt-2 list-disc space-y-1 pl-4 text-xs text-red-600">

                                @foreach($errors->all() as $error)

                                    <li>
                                        {{ $error }}
                                    </li>

                                @endforeach

                            </ul>

                        </div>

                    </div>

                </div>

            @endif


            <!-- ================================================= -->
            <!-- SUCCESS MESSAGE -->
            <!-- ================================================= -->

            @if(session('success'))

                <div class="mb-6 flex items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 p-4">

                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-100 text-emerald-600">
                        <i class="fa-solid fa-check text-xs"></i>
                    </div>

                    <p class="text-xs font-medium text-emerald-700">
                        {{ session('success') }}
                    </p>

                </div>

            @endif


            <!-- ================================================= -->
            <!-- FORM -->
            <!-- ================================================= -->

            <form
                id="listing-form"
                action="{{ route('student.listings.update', $listing->id) }}"
                method="POST"
                enctype="multipart/form-data"
            >

                @csrf

                @method('PUT')


                <!-- ================================================= -->
                <!-- LISTING INFORMATION CARD -->
                <!-- ================================================= -->

                <div class="mb-6 overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-sm">

                    <!-- HEADER -->

                    <div class="border-b border-slate-100 px-6 py-5">

                        <h3 class="text-sm font-bold text-slate-900">
                            Listing Information
                        </h3>

                        <p class="mt-1 text-xs text-slate-400">
                            Make changes to your room details.
                        </p>

                    </div>


                    <!-- FIELDS -->

                    <div class="grid grid-cols-1 gap-5 p-6">


                        <!-- ================================================= -->
                        <!-- ROOM TITLE -->
                        <!-- ================================================= -->

                        <div>

                            <label
                                for="title"
                                class="mb-1.5 block text-xs font-semibold text-slate-700"
                            >
                                Room Title
                            </label>

                            <input
                                id="title"
                                type="text"
                                name="title"
                                value="{{ old('title', $listing->title) }}"
                                required
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-xs text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:bg-white focus:ring-1 focus:ring-blue-500"
                                placeholder="Example: Kelana D' Putera"
                            >

                        </div>


                        <!-- ================================================= -->
                        <!-- DESCRIPTION -->
                        <!-- ================================================= -->

                        <div>

                            <label
                                for="description"
                                class="mb-1.5 block text-xs font-semibold text-slate-700"
                            >
                                Description
                            </label>

                            <textarea
                                id="description"
                                name="description"
                                rows="5"
                                required
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-xs leading-5 text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:bg-white focus:ring-1 focus:ring-blue-500"
                                placeholder="Describe your room..."
                            >{{ old('description', $listing->description) }}</textarea>

                        </div>


                        <!-- ================================================= -->
                        <!-- LOCATION + RENT -->
                        <!-- ================================================= -->

                        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

                            <!-- LOCATION -->

                            <div>

                                <label
                                    for="location"
                                    class="mb-1.5 block text-xs font-semibold text-slate-700"
                                >
                                    Location
                                </label>

                                <div class="relative">

                                    <i class="fa-solid fa-location-dot absolute left-4 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>

                                    <input
                                        id="location"
                                        type="text"
                                        name="location"
                                        value="{{ old('location', $listing->location) }}"
                                        required
                                        class="w-full rounded-xl border border-slate-200 bg-slate-50 py-3 pl-10 pr-4 text-xs text-slate-700 outline-none transition focus:border-blue-500 focus:bg-white focus:ring-1 focus:ring-blue-500"
                                        placeholder="Example: Cheras"
                                    >

                                </div>

                            </div>


                            <!-- RENT -->

                            <div>

                                <label
                                    for="rent"
                                    class="mb-1.5 block text-xs font-semibold text-slate-700"
                                >
                                    Monthly Rent
                                </label>

                                <div class="relative">

                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">
                                        RM
                                    </span>

                                    <input
                                        id="rent"
                                        type="number"
                                        name="rent"
                                        step="0.01"
                                        min="0"
                                        value="{{ old('rent', $listing->rent) }}"
                                        required
                                        class="w-full rounded-xl border border-slate-200 bg-slate-50 py-3 pl-12 pr-4 text-xs text-slate-700 outline-none transition focus:border-blue-500 focus:bg-white focus:ring-1 focus:ring-blue-500"
                                    >

                                </div>

                            </div>

                        </div>


                        <!-- ================================================= -->
                        <!-- ROOM TYPE + STATUS -->
                        <!-- ================================================= -->

                        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">


                            <!-- ROOM TYPE -->

                            <div>

                                <label
                                    for="room_type"
                                    class="mb-1.5 block text-xs font-semibold text-slate-700"
                                >
                                    Room Type
                                </label>

                                <select
                                    id="room_type"
                                    name="room_type"
                                    required
                                    class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-xs text-slate-700 outline-none transition focus:border-blue-500 focus:bg-white focus:ring-1 focus:ring-blue-500"
                                >

                                    <option
                                        value="Single"
                                        {{ old('room_type', $listing->room_type) == 'Single' ? 'selected' : '' }}
                                    >
                                        Single
                                    </option>

                                    <option
                                        value="Shared"
                                        {{ old('room_type', $listing->room_type) == 'Shared' ? 'selected' : '' }}
                                    >
                                        Shared
                                    </option>

                                    <option
                                        value="Master"
                                        {{ old('room_type', $listing->room_type) == 'Master' ? 'selected' : '' }}
                                    >
                                        Master
                                    </option>

                                    <option
                                        value="Studio"
                                        {{ old('room_type', $listing->room_type) == 'Studio' ? 'selected' : '' }}
                                    >
                                        Studio
                                    </option>

                                </select>

                            </div>


                            <!-- STATUS -->

                            <div>

                                <label class="mb-1.5 block text-xs font-semibold text-slate-700">
                                    Listing Status
                                </label>

                                <div class="flex items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">

                                    @if($listing->status === 'active')

                                        <span class="h-2 w-2 rounded-full bg-emerald-500"></span>

                                        <span class="text-xs font-semibold text-emerald-700">
                                            Active
                                        </span>

                                    @else

                                        <span class="h-2 w-2 rounded-full bg-slate-400"></span>

                                        <span class="text-xs font-semibold text-slate-600">
                                            {{ ucfirst($listing->status) }}
                                        </span>

                                    @endif

                                </div>

                            </div>

                        </div>

                    </div>

                </div>


                <!-- ================================================= -->
                <!-- PHOTO MANAGEMENT CARD -->
                <!-- ================================================= -->

                <div class="mb-6 overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-sm">


                    <!-- HEADER -->

                    <div class="border-b border-slate-100 px-6 py-5">

                        <h3 class="text-sm font-bold text-slate-900">
                            Manage Photos
                        </h3>

                        <p class="mt-1 text-xs text-slate-400">
                            Click any photo to view it in full size.
                        </p>

                    </div>


                    <div class="p-6">


                        <!-- ================================================= -->
                        <!-- PREPARE ALL PHOTOS -->
                        <!-- ================================================= -->

                        @php

                            $allPhotos = [];

                            /*
                             * Main photo
                             */

                            if ($listing->photo) {

                                $allPhotos[] = [
                                    'id' => 'main',
                                    'path' => $listing->photo,
                                    'type' => 'main'
                                ];

                            }


                            /*
                             * Additional photos
                             *
                             * IMPORTANT:
                             * Database column is photo_path
                             */

                            if ($listing->photos && $listing->photos->count() > 0) {

                                foreach ($listing->photos as $additionalPhoto) {

                                    if ($additionalPhoto->photo_path) {

                                        $allPhotos[] = [
                                            'id' => $additionalPhoto->id,
                                            'path' => $additionalPhoto->photo_path,
                                            'type' => 'additional'
                                        ];

                                    }

                                }

                            }

                        @endphp


                        <!-- ================================================= -->
                        <!-- CURRENT PHOTOS -->
                        <!-- ================================================= -->

                        <div class="mb-8">

                            <div class="mb-3 flex items-center justify-between">

                                <div>

                                    <h4 class="text-xs font-bold text-slate-800">
                                        Current Photos
                                    </h4>

                                    <p class="mt-1 text-[10px] text-slate-400">
                                        {{ count($allPhotos) }} photo(s)
                                    </p>

                                </div>

                                <span class="hidden text-[10px] text-slate-400 sm:block">

                                    <i class="fa-solid fa-hand-pointer mr-1"></i>

                                    Click photo to enlarge

                                </span>

                            </div>


                            @if(count($allPhotos) > 0)

                                <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">

                                    @foreach($allPhotos as $index => $photo)

                                        <div class="group relative overflow-hidden rounded-xl border border-slate-200 bg-slate-100">


                                            <!-- PHOTO -->

                                            <button
                                                type="button"
                                                onclick="openPhotoViewer({{ $index }})"
                                                class="relative block h-44 w-full overflow-hidden"
                                            >

                                                <img
                                                    src="{{ asset('storage/' . $photo['path']) }}"
                                                    alt="Listing photo"
                                                    class="h-full w-full object-cover transition duration-300 group-hover:scale-105"
                                                >


                                                <!-- Hover -->

                                                <div class="absolute inset-0 flex items-center justify-center bg-slate-900/0 transition duration-200 group-hover:bg-slate-900/40">

                                                    <div class="flex h-10 w-10 scale-75 items-center justify-center rounded-full bg-white text-slate-700 opacity-0 shadow-lg transition duration-200 group-hover:scale-100 group-hover:opacity-100">

                                                        <i class="fa-solid fa-expand text-xs"></i>

                                                    </div>

                                                </div>

                                            </button>


                                            <!-- MAIN BADGE -->

                                            @if($photo['type'] === 'main')

                                                <span class="absolute left-2 top-2 rounded-full bg-blue-600 px-2.5 py-1 text-[9px] font-bold text-white shadow">

                                                    <i class="fa-solid fa-star mr-1"></i>

                                                    Main Photo

                                                </span>

                                            @endif


                                            <!-- DELETE -->

                                            @if($photo['type'] === 'additional')

                                                <label class="absolute bottom-2 left-2 flex cursor-pointer items-center gap-1.5 rounded-lg bg-white/95 px-2.5 py-1.5 text-[10px] font-semibold text-red-600 shadow backdrop-blur-sm">

                                                    <input
                                                        type="checkbox"
                                                        name="delete_photos[]"
                                                        value="{{ $photo['id'] }}"
                                                        class="h-3.5 w-3.5 rounded border-slate-300 text-red-600 focus:ring-red-500"
                                                    >

                                                    Delete

                                                </label>

                                            @endif

                                        </div>

                                    @endforeach

                                </div>

                            @else

                                <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 p-10 text-center">

                                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-100">

                                        <i class="fa-solid fa-image text-slate-400"></i>

                                    </div>

                                    <p class="mt-3 text-xs font-semibold text-slate-600">
                                        No photos available
                                    </p>

                                    <p class="mt-1 text-[10px] text-slate-400">
                                        Add photos below.
                                    </p>

                                </div>

                            @endif

                        </div>


                        <!-- ================================================= -->
                        <!-- PHOTO ACTIONS -->
                        <!-- ================================================= -->

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">


                            <!-- ================================================= -->
                            <!-- REPLACE MAIN PHOTO -->
                            <!-- ================================================= -->

                            <div class="rounded-xl border border-slate-200 bg-slate-50 p-5">

                                <div class="flex items-start gap-3">

                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-100 text-blue-600">

                                        <i class="fa-solid fa-image text-sm"></i>

                                    </div>

                                    <div>

                                        <h4 class="text-xs font-bold text-slate-800">
                                            Replace Main Photo
                                        </h4>

                                        <p class="mt-1 text-[10px] leading-4 text-slate-400">
                                            Replace the current main photo with a new one.
                                        </p>

                                    </div>

                                </div>


                                <label class="mt-4 flex cursor-pointer flex-col items-center justify-center rounded-xl border border-dashed border-slate-300 bg-white px-4 py-6 text-center transition hover:border-blue-400 hover:bg-blue-50">

                                    <i class="fa-solid fa-cloud-arrow-up text-xl text-slate-400"></i>

                                    <span class="mt-2 text-xs font-semibold text-slate-600">
                                        Choose Main Photo
                                    </span>

                                    <span class="mt-1 text-[10px] text-slate-400">
                                        JPG, PNG or WEBP
                                    </span>

                                    <input
                                        type="file"
                                        name="photo"
                                        accept="image/jpeg,image/png,image/webp"
                                        class="hidden"
                                        onchange="showSelectedMainPhoto(this)"
                                    >

                                </label>


                                <!-- Selected filename -->

                                <p
                                    id="mainPhotoName"
                                    class="mt-3 hidden truncate rounded-lg bg-blue-50 px-3 py-2 text-[10px] font-medium text-blue-600"
                                ></p>

                            </div>


                            <!-- ================================================= -->
                            <!-- ADD MORE PHOTOS -->
                            <!-- ================================================= -->

                            <div class="rounded-xl border border-slate-200 bg-slate-50 p-5">

                                <div class="flex items-start gap-3">

                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600">

                                        <i class="fa-solid fa-images text-sm"></i>

                                    </div>

                                    <div>

                                        <h4 class="text-xs font-bold text-slate-800">
                                            Add More Photos
                                        </h4>

                                        <p class="mt-1 text-[10px] leading-4 text-slate-400">
                                            Select multiple photos to add to this listing.
                                        </p>

                                    </div>

                                </div>


                                <label class="mt-4 flex cursor-pointer flex-col items-center justify-center rounded-xl border border-dashed border-slate-300 bg-white px-4 py-6 text-center transition hover:border-emerald-400 hover:bg-emerald-50">

                                    <i class="fa-solid fa-cloud-arrow-up text-xl text-slate-400"></i>

                                    <span class="mt-2 text-xs font-semibold text-slate-600">
                                        Choose More Photos
                                    </span>

                                    <span class="mt-1 text-[10px] text-slate-400">
                                        You can select multiple images
                                    </span>

                                    <input
                                        id="photos"
                                        type="file"
                                        name="photos[]"
                                        accept="image/jpeg,image/png,image/webp"
                                        multiple
                                        class="hidden"
                                        onchange="showSelectedPhotos(this)"
                                    >

                                </label>


                                <!-- Selected files -->

                                <div
                                    id="selectedPhotos"
                                    class="mt-3 hidden rounded-lg bg-emerald-50 px-3 py-2"
                                >

                                    <p class="text-[10px] font-semibold text-emerald-700">

                                        <i class="fa-solid fa-check mr-1"></i>

                                        <span id="selectedPhotosText"></span>

                                    </p>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>


                <!-- ================================================= -->
                <!-- SAVE BUTTONS -->
                <!-- ================================================= -->

                <div class="flex items-center justify-end gap-3 pb-8">


                    <!-- CANCEL -->

                    <a
                        href="{{ url()->previous() }}"
                        class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-5 py-3 text-xs font-semibold text-slate-600 shadow-sm transition hover:bg-slate-50"
                    >

                        <i class="fa-solid fa-xmark"></i>

                        Cancel

                    </a>


                    <!-- SAVE CHANGES -->

                    <button
                        type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-6 py-3 text-xs font-semibold text-white shadow-sm transition hover:bg-blue-700"
                    >

                        <i class="fa-solid fa-floppy-disk"></i>

                        Save Changes

                    </button>

                </div>

            </form>

        </div>

    </main>

</div>


<!-- ============================================================= -->
<!-- PHOTO VIEWER -->
<!-- ============================================================= -->

<div
    id="photoViewer"
    class="fixed inset-0 z-[999] hidden items-center justify-center bg-slate-950/90 p-4 backdrop-blur-sm"
>


    <!-- CLOSE -->

    <button
        type="button"
        onclick="closePhotoViewer()"
        class="absolute right-5 top-5 z-[1000] flex h-11 w-11 items-center justify-center rounded-full bg-white/10 text-white transition hover:bg-white/20"
    >

        <i class="fa-solid fa-xmark"></i>

    </button>


    <!-- PREVIOUS -->

    <button
        id="previousButton"
        type="button"
        onclick="previousPhoto()"
        class="absolute left-4 top-1/2 z-[1000] flex h-12 w-12 -translate-y-1/2 items-center justify-center rounded-full bg-white/10 text-white transition hover:bg-white/20"
    >

        <i class="fa-solid fa-chevron-left"></i>

    </button>


    <!-- IMAGE -->

    <div class="relative flex max-h-[90vh] max-w-[90vw] items-center justify-center">

        <img
            id="viewerImage"
            src=""
            alt="Photo preview"
            class="max-h-[85vh] max-w-[85vw] rounded-xl object-contain shadow-2xl"
        >


        <!-- COUNTER -->

        <div
            id="photoCounter"
            class="absolute bottom-4 left-1/2 -translate-x-1/2 rounded-full bg-black/70 px-3 py-1.5 text-[10px] font-medium text-white"
        ></div>

    </div>


    <!-- NEXT -->

    <button
        id="nextButton"
        type="button"
        onclick="nextPhoto()"
        class="absolute right-4 top-1/2 z-[1000] flex h-12 w-12 -translate-y-1/2 items-center justify-center rounded-full bg-white/10 text-white transition hover:bg-white/20"
    >

        <i class="fa-solid fa-chevron-right"></i>

    </button>

</div>


<!-- ============================================================= -->
<!-- JAVASCRIPT -->
<!-- ============================================================= -->

<script>

    /* =========================================================
       ALL PHOTO URLS
    ========================================================= */

    const photos = [

        @foreach($allPhotos as $photo)

            @json(asset('storage/' . $photo['path'])),

        @endforeach

    ];


    /* =========================================================
       CURRENT PHOTO
    ========================================================= */

    let currentPhotoIndex = 0;


    /* =========================================================
       OPEN PHOTO VIEWER
    ========================================================= */

    function openPhotoViewer(index) {

        if (photos.length === 0) {
            return;
        }

        currentPhotoIndex = index;

        updatePhotoViewer();

        const viewer =
            document.getElementById('photoViewer');

        viewer.classList.remove('hidden');

        viewer.classList.add('flex');

        document.body.classList.add('overflow-hidden');

    }


    /* =========================================================
       CLOSE PHOTO VIEWER
    ========================================================= */

    function closePhotoViewer() {

        const viewer =
            document.getElementById('photoViewer');

        viewer.classList.add('hidden');

        viewer.classList.remove('flex');

        document.body.classList.remove('overflow-hidden');

    }


    /* =========================================================
       UPDATE PHOTO VIEWER
    ========================================================= */

    function updatePhotoViewer() {

        const image =
            document.getElementById('viewerImage');

        const counter =
            document.getElementById('photoCounter');

        const previousButton =
            document.getElementById('previousButton');

        const nextButton =
            document.getElementById('nextButton');


        image.src =
            photos[currentPhotoIndex];


        counter.textContent =
            (currentPhotoIndex + 1) +
            ' / ' +
            photos.length;


        if (photos.length <= 1) {

            previousButton.classList.add('hidden');

            nextButton.classList.add('hidden');

        } else {

            previousButton.classList.remove('hidden');

            nextButton.classList.remove('hidden');

        }

    }


    /* =========================================================
       NEXT PHOTO
    ========================================================= */

    function nextPhoto() {

        if (photos.length <= 1) {
            return;
        }

        currentPhotoIndex++;

        if (currentPhotoIndex >= photos.length) {
            currentPhotoIndex = 0;
        }

        updatePhotoViewer();

    }


    /* =========================================================
       PREVIOUS PHOTO
    ========================================================= */

    function previousPhoto() {

        if (photos.length <= 1) {
            return;
        }

        currentPhotoIndex--;

        if (currentPhotoIndex < 0) {

            currentPhotoIndex =
                photos.length - 1;

        }

        updatePhotoViewer();

    }


    /* =========================================================
       KEYBOARD CONTROLS
    ========================================================= */

    document.addEventListener(
        'keydown',
        function(event) {

            const viewer =
                document.getElementById('photoViewer');

            if (viewer.classList.contains('hidden')) {
                return;
            }

            if (event.key === 'Escape') {
                closePhotoViewer();
            }

            if (event.key === 'ArrowRight') {
                nextPhoto();
            }

            if (event.key === 'ArrowLeft') {
                previousPhoto();
            }

        }
    );


    /* =========================================================
       CLICK OUTSIDE IMAGE
    ========================================================= */

    document
        .getElementById('photoViewer')
        .addEventListener(
            'click',
            function(event) {

                if (event.target === this) {
                    closePhotoViewer();
                }

            }
        );


    /* =========================================================
       MAIN PHOTO SELECTED
    ========================================================= */

    function showSelectedMainPhoto(input) {

        const nameBox =
            document.getElementById('mainPhotoName');


        if (
            input.files &&
            input.files.length > 0
        ) {

            nameBox.textContent =
                'Selected: ' +
                input.files[0].name;

            nameBox.classList.remove('hidden');

        } else {

            nameBox.classList.add('hidden');

        }

    }


    /* =========================================================
       ADDITIONAL PHOTOS SELECTED
    ========================================================= */

    function showSelectedPhotos(input) {

        const box =
            document.getElementById('selectedPhotos');

        const text =
            document.getElementById('selectedPhotosText');


        if (
            input.files &&
            input.files.length > 0
        ) {

            text.textContent =
                input.files.length +
                ' photo(s) selected';

            box.classList.remove('hidden');

        } else {

            box.classList.add('hidden');

        }

    }

</script>


</body>
</html>