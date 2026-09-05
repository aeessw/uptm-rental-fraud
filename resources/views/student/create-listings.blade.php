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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-50/80 font-sans text-slate-800 antialiased">

<div class="flex h-screen overflow-hidden">

    <!-- Sidebar Navigation Included Here -->
    @include('student.sidebar')

    <!-- Main Content Area -->
    <main class="flex h-full min-w-0 flex-1 flex-col overflow-y-auto">

        <!-- Top Header -->
        <header class="sticky top-0 z-30 flex items-center justify-between border-b border-slate-200/60 bg-white/80 px-8 py-4 backdrop-blur-md">
            <div>
                <h2 class="text-base font-bold tracking-tight text-slate-900">Post a Room</h2>
            </div>

            <!-- User Profile Header -->
            <div class="flex items-center space-x-3">
                <div class="text-right">
                    <div class="mb-1 text-xs font-bold leading-none text-slate-900">{{ Auth::user()->name ?? 'Ahmad Farhan' }}</div>
                    <div class="text-[11px] font-medium leading-none text-slate-400">{{ Auth::user()->email ?? 'farhan@student.uptm.edu.my' }}</div>
                </div>
                <div class="flex h-9 w-9 items-center justify-center rounded-full border border-brand-100 bg-brand-50 text-xs font-bold uppercase text-brand-600 ring-2 ring-brand-500/10">
                    {{ strtoupper(substr(Auth::user()->name ?? 'AF', 0, 2)) }}
                </div>
            </div>
        </header>

        <!-- Page Body Content -->
        <div class="p-8">
            <div class="mx-auto max-w-4xl space-y-6">

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
                    <div class="border-b border-slate-100 px-8 py-6">
                        <h3 class="text-base font-bold text-slate-900">Room Details</h3>
                        <p class="mt-1 text-xs font-medium text-slate-400">
                            Fill in the details below to post your room. Your student identity will be attached to verify this listing.
                        </p>
                    </div>

                    <!-- Room Form -->
                    <form action="{{ route('student.listings.store') }}"
                          method="POST"
                          enctype="multipart/form-data"
                          id="listing-form">
                        @csrf

                        <div class="space-y-6 p-8">

                            <!-- Room Title & Type -->
                            <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                                
                                <!-- Room Name -->
                                <div class="space-y-1.5 md:col-span-2">
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
                                            <option value="Single" {{ old('room_type') == 'Single' ? 'selected' : '' }}>Single Room</option>
                                            <option value="Shared" {{ old('room_type') == 'Shared' ? 'selected' : '' }}>Shared Room</option>
                                        </select>
                                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-400">
                                            <i class="fa-solid fa-chevron-down text-[10px]"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Location & Rent -->
                            <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                                
                                <!-- Location -->
                                <div class="space-y-1.5 md:col-span-2">
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

                            <!-- Photo Upload Section -->
                            <div class="space-y-2 pt-2">
                                <div class="flex items-center justify-between">
                                    <label class="block text-xs font-bold text-slate-700">
                                        Room Photos <span class="text-rose-500">*</span>
                                    </label>
                                    <span id="photo-count" class="text-[11px] font-semibold text-slate-400">
                                        0 / 10 photos
                                    </span>
                                </div>

                                <!-- Drag & Drop Box -->
                                <div id="upload-area"
                                     class="group relative overflow-hidden rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50/50 p-8 text-center transition hover:border-brand-500/50 hover:bg-brand-50/30">
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
                                            Click here to select photos. You can add more photos later.
                                        </p>
                                        <div class="mt-3 inline-flex items-center rounded-full border border-slate-200/60 bg-white px-3 py-1 text-[10px] font-medium text-slate-500 shadow-2xs">
                                            <i class="fa-solid fa-shield-halved mr-1.5 text-emerald-500"></i>
                                            JPG, PNG or WEBP · Max 5MB per photo · Up to 10 photos
                                        </div>
                                    </div>
                                </div>

                                <!-- Previews -->
                                <div id="photo-preview" class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4"></div>

                                <p class="text-[11px] font-medium text-slate-400">
                                    <i class="fa-solid fa-circle-info mr-1 text-slate-400"></i>
                                    You can click the upload box again to add more photos.
                                </p>
                            </div>

                            <!-- Buttons -->
                            <div class="flex items-center justify-end space-x-3 border-t border-slate-100 pt-6">
                                <a href="{{ route('student.listings') }}"
                                   class="rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-xs font-bold text-slate-600 transition hover:bg-slate-50">
                                    Cancel
                                </a>
                                <button type="submit"
                                        class="flex items-center space-x-2 rounded-xl bg-brand-600 px-5 py-2.5 text-xs font-bold text-white shadow-xs transition hover:bg-brand-700 active:scale-95">
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
    const maxPhotos = 10;
    const maxFileSize = 5 * 1024 * 1024;

    const input = document.getElementById('photos');
    const preview = document.getElementById('photo-preview');
    const photoCount = document.getElementById('photo-count');
    const form = document.getElementById('listing-form');
    const uploadArea = document.getElementById('upload-area');

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
        if (selectedFiles.length === 0) {
            alert('Please select at least 1 photo.');
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
        photoCount.textContent = selectedFiles.length + ' / ' + maxPhotos + ' photos';

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