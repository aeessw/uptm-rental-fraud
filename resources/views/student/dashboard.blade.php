<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Workspace - UPTM Rental Fraud Detection</title>

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
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-slate-50/80 font-sans antialiased text-slate-800">

    <div class="min-h-screen flex">

        <!-- Sidebar Navigation Included Here -->
        @include('student.sidebar')

        <!-- Main Content Area -->
        <main class="flex-1 flex flex-col min-w-0">


            <!-- Dashboard Content -->
            <div class="p-8 space-y-6 overflow-y-auto">

                <!-- Welcome -->
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h1 class="text-lg font-bold text-slate-900 tracking-tight">Welcome back, {{ Auth::user()->user_name }}</h1>
                        <p class="text-xs text-slate-500 mt-1">Explore room listings and stay safe from scams.</p>
                    </div>


                </div>

                <!-- Recent Listings Section -->
                <div class="rounded-2xl border border-slate-200/60 bg-white p-5 sm:p-6 shadow-sm {{ $listings->count() === 1 ? 'min-h-[530px]' : '' }}">
                    <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
                        <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Recent Listings</h4>
                        <a href="{{ route('student.listings') }}" class="text-xs font-bold text-[#4F46E5] hover:text-[#1C457D] transition">
                            View all listings <i class="fa-solid fa-arrow-right text-[10px] ml-1"></i>
                        </a>
                    </div>

                    @if($listings->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                            @foreach($listings as $listing)
                                <a href="{{ route('student.listings.show', $listing->getKey()) }}" class="group block bg-white rounded-2xl border border-slate-200/60 shadow-xs overflow-hidden flex flex-col justify-between hover:shadow-md transition-all duration-200 hover:-translate-y-1">
                                    <div>
                                        <!-- Room Photo -->
                                        <div class="aspect-video bg-slate-100 relative overflow-hidden">
                                            @if($listing->listing_photo)
                                                <img src="{{ asset('storage/' . $listing->listing_photo) }}" alt="{{ $listing->listing_title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300 {{ $listing->listing_availability === 'rented' ? 'opacity-75 saturate-50' : '' }}">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-slate-300">
                                                    <i class="fa-solid fa-image text-3xl"></i>
                                                </div>
                                            @endif



                                        </div>

                                        <!-- Info -->
                                        <div class="p-4">
                                            <h5 class="font-bold text-slate-900 text-sm line-clamp-1 group-hover:text-[#1C457D] transition">
                                                {{ $listing->listing_title }}
                                            </h5>

                                            <div class="flex items-center text-slate-500 text-[11px] mt-1.5 space-x-1.5">
                                                <i class="fa-solid fa-location-dot text-slate-400"></i>
                                                <span class="line-clamp-1 font-medium">{{ $listing->listing_location }}</span>
                                            </div>

                                            <div class="mt-3 flex items-baseline space-x-1">
                                                <span class="text-[#4F46E5] font-extrabold text-base">RM {{ number_format($listing->listing_rent, 2) }}</span>
                                                <span class="text-slate-400 text-xs font-medium">/mo</span>
                                                @if($listing->room_type)
                                                    <span class="text-slate-300 text-xs mx-1">•</span>
                                                    <span class="text-xs text-slate-500 font-semibold">{{ $listing->room_type }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Footer -->
                                    <div class="px-4 py-2.5 bg-slate-50/60 border-t border-slate-100 flex items-center justify-between text-[11px]">
                                        <span class="text-slate-400 font-medium">
                                            {{ $listing->listing_created_at ? $listing->listing_created_at->diffForHumans() : 'Recently posted' }}
                                        </span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="bg-white rounded-2xl border border-dashed border-slate-200 p-12 text-center">
                            <p class="text-xs text-slate-400 font-medium">No active listings available right now.</p>
                        </div>
                    @endif
                </div>

                <!-- Rental Safety Tips -->
                <section class="rounded-2xl border border-emerald-200/70 bg-emerald-50/70 p-6 shadow-sm sm:p-7" aria-labelledby="safety-tips-heading">
                    <div class="flex flex-wrap items-center justify-between gap-6">
                        <div>
                            <h2 id="safety-tips-heading" class="text-sm font-bold text-slate-900">Stay Safe When Renting</h2>
                            <ul class="mt-3 grid gap-x-8 gap-y-2 text-xs font-medium text-slate-600 sm:grid-cols-2">
                                <li class="flex items-center gap-2"><i class="fa-solid fa-check text-[10px] text-emerald-600"></i>Communicate through the platform</li>
                                <li class="flex items-center gap-2"><i class="fa-solid fa-check text-[10px] text-emerald-600"></i>Check listing information carefully</li>
                                <li class="flex items-center gap-2"><i class="fa-solid fa-check text-[10px] text-emerald-600"></i>Protect your personal information</li>
                                <li class="flex items-center gap-2"><i class="fa-solid fa-check text-[10px] text-emerald-600"></i>Report suspicious listings</li>
                            </ul>
                        </div>
                        <button type="button" id="open-safety-tips" class="inline-flex shrink-0 items-center gap-1 text-xs font-bold text-emerald-700 transition hover:text-emerald-900">
                            View all tips <i class="fa-solid fa-arrow-right text-[10px]"></i>
                        </button>
                    </div>
                </section>


            </div>
        </main>

    </div>

    <div id="safety-tips-modal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-slate-950/50 p-4 backdrop-blur-sm" role="dialog" aria-modal="true" aria-labelledby="safety-tips-modal-title">
        <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 text-emerald-700">
                        <i class="fa-solid fa-shield-halved"></i>
                        <h2 id="safety-tips-modal-title" class="text-base font-bold text-slate-900">Stay Safe When Renting</h2>
                    </div>
                    <p class="mt-1 text-xs text-slate-500">Simple steps to help avoid rental fraud.</p>
                </div>
                <button type="button" id="close-safety-tips" class="text-slate-400 transition hover:text-slate-700" aria-label="Close safety tips" title="Close safety tips">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <ul class="mt-5 space-y-3 text-xs font-medium text-slate-700">
                <li class="flex gap-3"><i class="fa-solid fa-check mt-0.5 text-emerald-600"></i><span>Keep conversations and payment discussions inside the platform.</span></li>
                <li class="flex gap-3"><i class="fa-solid fa-check mt-0.5 text-emerald-600"></i><span>Confirm the room details, location, price, and owner information.</span></li>
                <li class="flex gap-3"><i class="fa-solid fa-check mt-0.5 text-emerald-600"></i><span>Never share passwords, identity documents, or banking details unnecessarily.</span></li>
                <li class="flex gap-3"><i class="fa-solid fa-check mt-0.5 text-emerald-600"></i><span>Report suspicious listings or users so they can be reviewed.</span></li>
                <li class="flex gap-3"><i class="fa-solid fa-check mt-0.5 text-emerald-600"></i><span>Do not send money before verifying the room and rental agreement.</span></li>
            </ul>
        </div>
    </div>

<script>
(() => {
    const modal = document.getElementById('safety-tips-modal');
    const openButton = document.getElementById('open-safety-tips');
    const closeButton = document.getElementById('close-safety-tips');

    const closeModal = () => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.classList.remove('overflow-hidden');
    };

    openButton?.addEventListener('click', () => {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.classList.add('overflow-hidden');
    });
    closeButton?.addEventListener('click', closeModal);
    modal?.addEventListener('click', (event) => {
        if (event.target === modal) closeModal();
    });
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') closeModal();
    });
})();


</script>

</body>
</html>