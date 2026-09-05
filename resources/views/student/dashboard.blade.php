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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-50/80 font-sans antialiased text-slate-800">

    <div class="min-h-screen flex">

        <!-- Sidebar Navigation Included Here -->
        @include('student.sidebar')

        <!-- Main Content Area -->
        <main class="flex-1 flex flex-col min-w-0">

            <!-- Top Header -->
            <header class="bg-white/80 backdrop-blur-md border-b border-slate-200/60 px-8 py-4 flex items-center justify-between sticky top-0 z-10">
                <div class="flex items-center space-x-3">
                    <h2 class="text-base font-bold text-slate-900 tracking-tight">Student Dashboard</h2>
                </div>

                <!-- User Profile Header -->
                <div class="flex items-center space-x-3">
                    <div class="text-right">
                        <div class="text-xs font-bold text-slate-900 leading-none mb-1">{{ Auth::user()->name }}</div>
                        <div class="text-[11px] text-slate-400 font-medium leading-none">{{ Auth::user()->email }}</div>
                    </div>
                    <div class="w-9 h-9 rounded-full bg-brand-50 text-brand-600 flex items-center justify-center font-bold text-xs border border-brand-100 uppercase ring-2 ring-brand-500/10">
                        {{ substr(Auth::user()->name, 0, 2) }}
                    </div>
                </div>
            </header>

            <!-- Dashboard Content -->
            <div class="p-8 space-y-6 overflow-y-auto">

                <!-- Welcome Banner -->
                <div class="bg-white rounded-2xl p-6 border border-slate-200/60 shadow-xs flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900 tracking-tight">Welcome back, {{ Auth::user()->name }}</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Explore verified rooms and stay safe from scams.</p>
                    </div>
                    <span class="bg-emerald-500/10 text-emerald-700 text-[11px] font-bold px-3 py-1 rounded-full border border-emerald-500/20 flex items-center space-x-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        <span class="capitalize">Student</span>
                    </span>
                </div>

                <!-- Recently Verified Listings Section -->
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Recently Verified Listings</h4>
                        <a href="{{ route('student.listings') }}" class="text-xs font-bold text-brand-600 hover:text-brand-700 transition">
                            View all listings <i class="fa-solid fa-arrow-right text-[10px] ml-1"></i>
                        </a>
                    </div>

                    @if($listings->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                            @foreach($listings as $listing)
                                <a href="{{ route('student.listings.show', $listing->id) }}" class="group block bg-white rounded-2xl border border-slate-200/60 shadow-xs overflow-hidden flex flex-col justify-between hover:shadow-md transition-all duration-200 hover:-translate-y-1">
                                    <div>
                                        <!-- Room Photo -->
                                        <div class="aspect-video bg-slate-100 relative overflow-hidden">
                                            @if($listing->photo)
                                                <img src="{{ asset('storage/' . $listing->photo) }}" alt="{{ $listing->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-slate-300">
                                                    <i class="fa-solid fa-image text-3xl"></i>
                                                </div>
                                            @endif
                                            
                                            <span class="absolute top-3 right-3 bg-slate-900/80 backdrop-blur-xs text-white text-[10px] font-bold px-2.5 py-0.5 rounded-full border border-white/20">
                                                Active
                                            </span>
                                        </div>

                                        <!-- Info -->
                                        <div class="p-4">
                                            <h5 class="font-bold text-slate-900 text-sm line-clamp-1 group-hover:text-brand-600 transition">
                                                {{ $listing->title }}
                                            </h5>

                                            <div class="flex items-center text-slate-500 text-[11px] mt-1.5 space-x-1.5">
                                                <i class="fa-solid fa-location-dot text-slate-400"></i>
                                                <span class="line-clamp-1 font-medium">{{ $listing->location }}</span>
                                            </div>

                                            <div class="mt-3 flex items-baseline space-x-1">
                                                <span class="text-brand-600 font-extrabold text-base">RM {{ number_format($listing->rent, 2) }}</span>
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
                                            {{ $listing->created_at ? $listing->created_at->diffForHumans() : 'Recently posted' }}
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

                <!-- MPP Cybersecurity Warning Banner -->
                <div class="bg-amber-500/5 border border-amber-500/20 rounded-2xl p-4 flex items-start space-x-3">
                    <div class="w-7 h-7 rounded-lg bg-amber-500/10 text-amber-600 flex items-center justify-center shrink-0 mt-0.5">
                        <i class="fa-solid fa-shield-halved text-xs"></i>
                    </div>
                    <div>
                        <h5 class="text-xs font-bold text-amber-950">Important Security Notice</h5>
                        <p class="text-xs text-amber-900/80 mt-0.5 leading-relaxed">
                            Always verify the room and listing details before making any payment. Avoid listings with suspicious prices or unclear information. Report anything suspicious using the "Report Listing" button.
                        </p>
                    </div>
                </div>

            </div>
        </main>

    </div>

</body>
</html>