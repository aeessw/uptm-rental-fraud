<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post a Room - UPTM Rental Fraud Detection</title>

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

<div class="flex h-screen overflow-hidden">

    <!-- Sidebar Navigation Included Here -->
    @include('student.sidebar')

    <!-- Main Content Area -->
    <main class="flex h-full min-w-0 flex-1 flex-col overflow-y-auto">


        <!-- Page Body Content -->
        <div class="p-4 sm:p-6">
            <div class="w-full space-y-5">

                <div class="flex items-start gap-2 rounded-xl bg-indigo-50 px-4 py-3 text-sm text-indigo-700">
                    <i class="fa-solid fa-circle-info mt-0.5 shrink-0" aria-hidden="true"></i>
                    <p><strong class="font-semibold">Listing Guidelines:</strong> You can have up to 2 listings at a time, including pending and hidden listings. New listings will only be visible after MPP approval.</p>
                </div>
                <!-- Validation Errors Alert -->
                @if ($errors->any())
                    <div class="flex items-start space-x-3 rounded-2xl border border-rose-500/20 bg-rose-500/10 p-4 text-xs font-semibold text-rose-800">
                        <i class="fa-solid fa-triangle-exclamation mt-0.5 text-rose-600"></i>
                        <div>
                            <div class="font-bold">Please fix the following errors:</div>
                            <ul class="mt-1.5 list-disc space-y-1 pl-4 font-medium">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                <!-- Form Card -->
                <div class="overflow-hidden rounded-2xl border border-slate-200/60 bg-white shadow-xs">
                    
                    <!-- Card Header -->
                    <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                        <h3 class="text-base font-bold text-slate-900">Room Details</h3>
                        <p class="mt-1 text-xs font-medium text-slate-400">
                            Fill in the details below to post your room. Your UPTM account will be linked to this listing for accountability and safety.
                        </p>
                    </div>

                    <!-- Room Form -->
                    <form action="{{ route('student.listings.store') }}"
                          method="POST"
                          enctype="multipart/form-data"
                          id="listing-form">
                        @csrf

                        <div class="space-y-5 p-5 sm:p-6">

                            <!-- Room Title & Type -->
                            <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
                                
                                <!-- Room Name -->
                                <div class="space-y-1.5 lg:col-span-2">
                                    <label class="block text-xs font-bold text-slate-700">
                                        Room Name <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="text"
                                           name="title"
                                           value="{{ old('title') }}"
                                           placeholder="Enter a name for your room"
                                           class="w-full rounded-xl border border-slate-200/80 bg-slate-50/80 px-4 py-2.5 text-xs font-medium text-slate-800 placeholder-slate-400 transition focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20"
                                           required>
                                </div>

                                <!-- Room Type -->
                                <div class="space-y-1.5">
                                    <label class="block text-xs font-bold text-slate-700">
                                        Room Type <span class="text-rose-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <select name="room_type"
                                                class="w-full cursor-pointer appearance-none rounded-xl border border-slate-200/80 bg-slate-50/80 px-4 py-2.5 pr-9 text-xs font-semibold text-slate-600 transition focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20"
                                                required>
                                            <option value="" disabled {{ old('room_type') ? '' : 'selected' }}>Select Room Type</option>
                                            <option value="Master" {{ old('room_type') == 'Master' ? 'selected' : '' }}>Master</option>
                                            <option value="Middle" {{ old('room_type') == 'Middle' ? 'selected' : '' }}>Middle</option>
                                            <option value="Single" {{ old('room_type') == 'Single' ? 'selected' : '' }}>Single</option>
                                        </select>
                                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-400">
                                            <i class="fa-solid fa-chevron-down text-[10px]"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Location & Rent -->
                            <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
                                
                                <!-- Location -->
                                <div class="space-y-1.5 lg:col-span-2">
                                    <label class="block text-xs font-bold text-slate-700">
                                        Location <span class="text-rose-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <i class="fa-solid fa-location-dot absolute left-3.5 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
                                        <input type="text"
                                               name="location"
                                               value="{{ old('location') }}"
                                               placeholder="Enter room location"
                                               class="w-full rounded-xl border border-slate-200/80 bg-slate-50/80 py-2.5 pl-9 pr-4 text-xs font-medium text-slate-800 placeholder-slate-400 transition focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20"
                                               required>
                                    </div>
                                </div>

                                <!-- Monthly Rent -->
                                <div class="space-y-1.5">
                                    <label class="block text-xs font-bold text-slate-700">
                                        Monthly Rent (RM) <span class="text-rose-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-xs font-semibold text-slate-400">RM</span>
                                        <input type="number"
                                               step="0.01"
                                               name="rent"
                                               value="{{ old('rent') }}"
                                               placeholder="0.00"
                                               class="w-full rounded-xl border border-slate-200/80 bg-slate-50/80 py-2.5 pl-10 pr-4 text-xs font-medium text-slate-800 placeholder-slate-400 transition focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20"
                                               required>
                                    </div>
                                </div>
                            </div>

                            @include('student.listings.preferences-fields')

                            <!-- Description -->
                            <div class="space-y-1.5">
                                <label class="block text-xs font-bold text-slate-700">
                                    Description <span class="text-rose-500">*</span>
                                </label>
                                <textarea name="description"
                                          rows="5"
                                          placeholder="Describe the room, facilities, nearby amenities, house rules, and other important details..."
                                          class="w-full resize-none rounded-xl border border-slate-200/80 bg-slate-50/80 p-4 text-xs font-medium text-slate-800 placeholder-slate-400 transition focus:border-brand-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20"
                                          required>{{ old('description') }}</textarea>
                            </div>

                            @include('student.listings.facilities-fields')

                            <!-- Photo Upload Section -->
                            <div class="space-y-2 pt-2">
                                <div class="flex items-center justify-between">
                                    <label class="block text-xs font-bold text-slate-700">
                                        Room Photos <span class="text-rose-500">*</span>
                                    </label>
                                    <span id="photo-count" class="text-[11px] font-semibold text-slate-400">
                                        0 / 10 photos (minimum 3)
                                    </span>
                                </div>

                                <!-- Drag & Drop Box -->
                                <div id="upload-area"
                                     class="group relative overflow-hidden rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50/50 p-5 text-center transition hover:border-brand-500/50 hover:bg-brand-50/30">
                                    <input type="file"
                                           name="photos[]"
                                           id="photos"
                                           class="absolute inset-0 z-10 h-full w-full cursor-pointer opacity-0"
                                           accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                                           multiple>

                                    <div class="pointer-events-none">
                                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-brand-50 text-brand-600 transition group-hover:scale-110">
                                            <i class="fa-solid fa-images text-xl"></i>
                                        </div>
                                        <p class="mt-3 text-xs font-bold text-slate-900">Choose room photos</p>
                                        <p class="mt-1 text-[11px] font-medium text-slate-400">
                                            Select at least 3 photos. You can add more photos later.
                                        </p>
                                        <div class="mt-3 inline-flex items-center rounded-full border border-slate-200/60 bg-white px-3 py-1 text-[10px] font-medium text-slate-500 shadow-2xs">
                                            <i class="fa-solid fa-shield-halved mr-1.5 text-emerald-500"></i>
                                            JPG, PNG or WEBP · Max 5MB per photo · 3 to 10 photos
                                        </div>
                                    </div>
                                </div>

                                <!-- Previews -->
                                <div id="photo-preview" class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4"></div>

                                <p class="text-[11px] font-medium text-slate-400">
                                    <i class="fa-solid fa-circle-info mr-1 text-slate-400"></i>
                                    Select at least 3 photos before posting. You can click the upload box again to add more photos.
                                </p>
                            </div>

                            <section class="rounded-xl border border-blue-200 bg-blue-50 p-4 text-xs text-slate-700" aria-labelledby="listing-safety-heading">
                                <h4 id="listing-safety-heading" class="font-semibold text-blue-600"><i class="fa-solid fa-shield-halved mr-2" aria-hidden="true"></i>Listing Safety</h4>
                                <p class="mt-2">Your UPTM account is linked to this listing. Providing false or misleading information may result in the listing being reported and reviewed by MPP.</p>
                            </section>
                            <label class="flex cursor-pointer items-start gap-3 text-xs text-slate-700">
                                <input id="accuracy-confirmed" type="checkbox" name="accuracy_confirmed" value="1" required @checked(old('accuracy_confirmed')) class="mt-0.5 h-4 w-4 shrink-0 rounded border-slate-300">
                                <span>I confirm that the information and photos provided are accurate and belong to this room listing.</span>
                            </label>

                            <!-- Buttons -->
                            <div class="flex items-center justify-end space-x-3 border-t border-slate-100 pt-4">
                                <a href="{{ route('student.listings') }}"
                                   class="rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-xs font-bold text-slate-600 transition hover:bg-slate-50">
                                    Cancel
                                </a>
                                <button type="submit" id="post-room-button" disabled
                                        class="flex items-center space-x-2 rounded-xl bg-brand-600 px-5 py-2.5 text-xs font-bold text-white shadow-xs transition hover:bg-brand-700 active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed">
                                    <i class="fa-solid fa-paper-plane text-[11px]"></i>
                                    <span>Post Room</span>
                                </button>
                            </div>

                        </div>
                    </form>
                </div>

            </div>
        </div>
    </main>

</div>

<!-- Multiple Photo Upload Script -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    let selectedFiles = [];
    const minPhotos = 3;
    const maxPhotos = 10;
    const maxFileSize = 5 * 1024 * 1024;

    const input = document.getElementById('photos');
    const preview = document.getElementById('photo-preview');
    const photoCount = document.getElementById('photo-count');
    const form = document.getElementById('listing-form');
    const uploadArea = document.getElementById('upload-area');
    const confirmation = document.getElementById('accuracy-confirmed');
    const postButton = document.getElementById('post-room-button');
    const syncConfirmation = () => { postButton.disabled = !confirmation.checked; };
    confirmation.addEventListener('change', syncConfirmation);
    window.addEventListener('pageshow', syncConfirmation);
    syncConfirmation();

    input.addEventListener('change', function () {
        const files = Array.from(this.files);

        files.forEach(function (file) {
            if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type)) {
                alert(file.name + ' is not a valid image.');
                return;
            }

            if (file.size > maxFileSize) {
                alert(file.name + ' is larger than 5MB.');
                return;
            }

            if (selectedFiles.length >= maxPhotos) {
                alert('Maximum 10 photos allowed.');
                return;
            }

            const duplicate = selectedFiles.some(function (existingFile) {
                return existingFile.name === file.name &&
                       existingFile.size === file.size &&
                       existingFile.lastModified === file.lastModified;
            });

            if (!duplicate) {
                selectedFiles.push(file);
            }
        });

        updatePreview();
        input.value = '';
    });

    form.addEventListener('submit', function (event) {
        if (selectedFiles.length < minPhotos) {
            alert('Please select at least ' + minPhotos + ' photos.');
            event.preventDefault();
            return;
        }

        const dataTransfer = new DataTransfer();
        selectedFiles.forEach(function (file) {
            dataTransfer.items.add(file);
        });

        input.files = dataTransfer.files;
    });

    function updatePreview() {
        preview.innerHTML = '';
        photoCount.textContent = selectedFiles.length + ' / ' + maxPhotos + ' photos (minimum ' + minPhotos + ')';

        selectedFiles.forEach(function (file, index) {
            const photoBox = document.createElement('div');
            photoBox.className = 'group relative overflow-hidden rounded-xl border border-slate-200/80 bg-white shadow-2xs';

            const image = document.createElement('img');
            image.className = 'h-32 w-full object-cover transition duration-200 group-hover:scale-105';
            image.alt = 'Room photo';

            const removeButton = document.createElement('button');
            removeButton.type = 'button';
            removeButton.className = 'absolute right-2 top-2 z-20 flex h-6 w-6 items-center justify-center rounded-full bg-rose-600 text-white shadow-md transition hover:bg-rose-700 active:scale-95';
            removeButton.innerHTML = '<i class="fa-solid fa-xmark text-[10px]"></i>';

            removeButton.addEventListener('click', function () {
                selectedFiles.splice(index, 1);
                updatePreview();
            });

            const fileName = document.createElement('div');
            fileName.className = 'absolute bottom-0 left-0 right-0 bg-slate-900/70 px-2 py-1 backdrop-blur-xs';

            const fileText = document.createElement('p');
            fileText.className = 'truncate text-[10px] font-medium text-white';
            fileText.textContent = file.name;

            fileName.appendChild(fileText);
            photoBox.appendChild(image);
            photoBox.appendChild(removeButton);
            photoBox.appendChild(fileName);
            preview.appendChild(photoBox);

            const reader = new FileReader();
            reader.onload = function (event) {
                image.src = event.target.result;
            };
            reader.readAsDataURL(file);
        });
    }

    uploadArea.addEventListener('dragover', function (event) {
        event.preventDefault();
        uploadArea.classList.add('border-brand-500', 'bg-brand-50/50');
    });

    uploadArea.addEventListener('dragleave', function () {
        uploadArea.classList.remove('border-brand-500', 'bg-brand-50/50');
    });

    uploadArea.addEventListener('drop', function (event) {
        event.preventDefault();
        uploadArea.classList.remove('border-brand-500', 'bg-brand-50/50');

        const files = Array.from(event.dataTransfer.files);
        files.forEach(function (file) {
            if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type)) {
                alert(file.name + ' is not a valid image.');
                return;
            }

            if (file.size > maxFileSize) {
                alert(file.name + ' is larger than 5MB.');
                return;
            }

            if (selectedFiles.length >= maxPhotos) {
                alert('Maximum 10 photos allowed.');
                return;
            }

            const duplicate = selectedFiles.some(function (existingFile) {
                return existingFile.name === file.name &&
                       existingFile.size === file.size &&
                       existingFile.lastModified === file.lastModified;
            });

            if (!duplicate) {
                selectedFiles.push(file);
            }
        });

        updatePreview();
    });
});
</script>

</body>
</html>
