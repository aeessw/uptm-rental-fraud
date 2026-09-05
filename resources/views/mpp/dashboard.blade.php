<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>MPP Admin Dashboard - UPTM Rental</title>


    <!-- ================= GOOGLE FONT ================= -->

    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet"
    >


    <!-- ================= TAILWIND ================= -->

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


    <!-- ================= FONT AWESOME ================= -->

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    >

</head>


<body class="bg-slate-50/80 font-sans antialiased text-slate-800">


<!-- ===================================================== -->
<!-- MAIN WRAPPER -->
<!-- ===================================================== -->

<div class="min-h-screen">


    <!-- ===================================================== -->
    <!-- SIDEBAR -->
    <!-- ===================================================== -->

    @include('mpp.sidebar')


    <!-- ===================================================== -->
    <!-- MAIN CONTENT -->
    <!-- ===================================================== -->

    <main class="ml-0 min-w-0 md:ml-64">


        <!-- ===================================================== -->
        <!-- TOP HEADER -->
        <!-- ===================================================== -->

        <header
            class="sticky top-0 z-40 flex items-center justify-between
                   border-b border-slate-200/60
                   bg-white/90 px-8 py-4
                   backdrop-blur-md"
        >

            <!-- Page Title -->

            <div>

                <h2 class="text-base font-bold tracking-tight text-slate-900">

                    MPP Admin Dashboard

                </h2>

            </div>


            <!-- ================================================= -->
            <!-- ADMIN PROFILE -->
            <!-- ================================================= -->

            <div class="flex items-center space-x-3">


                <div class="text-right">

                    <div
                        class="mb-0.5 text-xs font-bold
                               leading-none text-slate-900"
                    >

                        {{ Auth::user()->name }}

                    </div>


                    <div
                        class="text-[11px] font-medium
                               leading-none text-slate-400"
                    >

                        {{ Auth::user()->email }}

                    </div>

                </div>


                <!-- Avatar -->

                <div
                    class="flex h-9 w-9 items-center justify-center
                           rounded-full border border-brand-100
                           bg-brand-50 text-xs font-bold uppercase
                           text-brand-600 ring-2 ring-brand-500/10"
                >

                    {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}

                </div>

            </div>

        </header>



        <!-- ===================================================== -->
        <!-- PAGE CONTENT -->
        <!-- ===================================================== -->

        <div class="space-y-8 p-8">


            <!-- ===================================================== -->
            <!-- WELCOME BANNER -->
            <!-- ===================================================== -->

            <section
                class="flex items-center justify-between
                       rounded-2xl border border-slate-200/60
                       bg-white p-6 shadow-sm"
            >

                <div>

                    <h1 class="text-lg font-bold text-slate-900">

                        Welcome, {{ Auth::user()->name }}

                    </h1>


                    <p class="mt-1 text-xs font-medium text-slate-500">

                        Manage rental listings, reports and student accounts.

                    </p>

                </div>


                <!-- MPP STATUS -->

                <span
                    class="flex items-center space-x-1.5
                           rounded-full border border-emerald-500/20
                           bg-emerald-500/10 px-3 py-1
                           text-[11px] font-bold text-emerald-700"
                >

                    <span
                        class="h-1.5 w-1.5 rounded-full
                               bg-emerald-500"
                    ></span>

                    <span>MPP</span>

                </span>

            </section>



            <!-- ===================================================== -->
            <!-- SUCCESS MESSAGE -->
            <!-- ===================================================== -->

            @if(session('success'))

                <div
                    class="flex items-center space-x-2.5
                           rounded-xl border border-emerald-500/20
                           bg-emerald-500/10 p-4
                           text-xs font-semibold text-emerald-800"
                >

                    <i
                        class="fa-solid fa-circle-check
                               text-emerald-600"
                    ></i>

                    <span>

                        {{ session('success') }}

                    </span>

                </div>

            @endif



            <!-- ===================================================== -->
            <!-- SYSTEM OVERVIEW -->
            <!-- ===================================================== -->

            <section>


                <!-- Section Header -->

                <div class="mb-4">

                    <h2
                        class="text-xs font-bold uppercase
                               tracking-wider text-slate-900"
                    >

                        System Overview

                    </h2>


                    <p class="mt-1 text-xs text-slate-400">

                        Current status of the rental platform.

                    </p>

                </div>



                <!-- ================================================= -->
                <!-- STATISTICS -->
                <!-- ================================================= -->

                <div
                    class="grid grid-cols-1 gap-4
                           sm:grid-cols-2 lg:grid-cols-4"
                >


                    <!-- ================================================= -->
                    <!-- ACTIVE LISTINGS -->
                    <!-- ================================================= -->

                    <a
                        href="{{ route('mpp.listings') }}"
                        class="group rounded-2xl
                               border border-slate-200/60
                               bg-white p-5 shadow-sm
                               transition
                               hover:-translate-y-0.5
                               hover:shadow-md"
                    >

                        <div
                            class="flex items-center
                                   justify-between"
                        >

                            <div
                                class="flex h-10 w-10
                                       items-center justify-center
                                       rounded-xl bg-brand-50
                                       text-brand-600"
                            >

                                <i class="fa-solid fa-house"></i>

                            </div>


                            <i
                                class="fa-solid fa-arrow-right
                                       text-xs text-slate-300
                                       transition
                                       group-hover:text-brand-500"
                            ></i>

                        </div>


                        <div class="mt-5">

                            <p
                                class="text-xs font-semibold
                                       text-slate-400"
                            >

                                Active Listings

                            </p>


                            <p
                                class="mt-1 text-2xl
                                       font-extrabold text-slate-900"
                            >

                                {{ $activeListings->count() }}

                            </p>


                            <p
                                class="mt-1 text-[11px]
                                       font-medium text-brand-600"
                            >

                                View listings

                            </p>

                        </div>

                    </a>



                    <!-- ================================================= -->
                    <!-- REPORTED LISTINGS -->
                    <!-- ================================================= -->

                    <a
                        href="{{ route('mpp.reports') }}"
                        class="group rounded-2xl
                               border border-slate-200/60
                               bg-white p-5 shadow-sm
                               transition
                               hover:-translate-y-0.5
                               hover:shadow-md"
                    >

                        <div
                            class="flex items-center
                                   justify-between"
                        >

                            <div
                                class="flex h-10 w-10
                                       items-center justify-center
                                       rounded-xl bg-rose-50
                                       text-rose-600"
                            >

                                <i
                                    class="fa-solid
                                           fa-triangle-exclamation"
                                ></i>

                            </div>


                            <i
                                class="fa-solid fa-arrow-right
                                       text-xs text-slate-300
                                       transition
                                       group-hover:text-rose-500"
                            ></i>

                        </div>


                        <div class="mt-5">

                            <p
                                class="text-xs font-semibold
                                       text-slate-400"
                            >

                                Reported Listings

                            </p>


                            <p
                                class="mt-1 text-2xl
                                       font-extrabold text-slate-900"
                            >

                                {{ $reportedListings->count() }}

                            </p>


                            <p
                                class="mt-1 text-[11px]
                                       font-medium text-rose-600"
                            >

                                Review reports

                            </p>

                        </div>

                    </a>



                    <!-- ================================================= -->
                    <!-- HIDDEN LISTINGS -->
                    <!-- ================================================= -->

                    <a
                        href="{{ route('mpp.listings') }}"
                        class="group rounded-2xl
                               border border-slate-200/60
                               bg-white p-5 shadow-sm
                               transition
                               hover:-translate-y-0.5
                               hover:shadow-md"
                    >

                        <div
                            class="flex items-center
                                   justify-between"
                        >

                            <div
                                class="flex h-10 w-10
                                       items-center justify-center
                                       rounded-xl bg-amber-50
                                       text-amber-600"
                            >

                                <i class="fa-solid fa-eye-slash"></i>

                            </div>


                            <i
                                class="fa-solid fa-arrow-right
                                       text-xs text-slate-300
                                       transition
                                       group-hover:text-amber-500"
                            ></i>

                        </div>


                        <div class="mt-5">

                            <p
                                class="text-xs font-semibold
                                       text-slate-400"
                            >

                                Hidden Listings

                            </p>


                            <p
                                class="mt-1 text-2xl
                                       font-extrabold text-slate-900"
                            >

                                {{ $hiddenListings->count() }}

                            </p>


                            <p
                                class="mt-1 text-[11px]
                                       font-medium text-amber-600"
                            >

                                Manage listings

                            </p>

                        </div>

                    </a>



                    <!-- ================================================= -->
                    <!-- STUDENT ACCOUNTS -->
                    <!-- ================================================= -->

                    <a
                        href="{{ route('mpp.students') }}"
                        class="group rounded-2xl
                               border border-slate-200/60
                               bg-white p-5 shadow-sm
                               transition
                               hover:-translate-y-0.5
                               hover:shadow-md"
                    >

                        <div
                            class="flex items-center
                                   justify-between"
                        >

                            <div
                                class="flex h-10 w-10
                                       items-center justify-center
                                       rounded-xl bg-sky-50
                                       text-sky-600"
                            >

                                <i class="fa-solid fa-users"></i>

                            </div>


                            <i
                                class="fa-solid fa-arrow-right
                                       text-xs text-slate-300
                                       transition
                                       group-hover:text-sky-500"
                            ></i>

                        </div>


                        <div class="mt-5">

                            <p
                                class="text-xs font-semibold
                                       text-slate-400"
                            >

                                Student Accounts

                            </p>


                            <p
                                class="mt-1 text-2xl
                                       font-extrabold text-slate-900"
                            >

                                {{ $users->count() }}

                            </p>


                            <p
                                class="mt-1 text-[11px]
                                       font-medium text-sky-600"
                            >

                                Manage students

                            </p>

                        </div>

                    </a>

                </div>

            </section>



            <!-- ===================================================== -->
            <!-- RECENT ACTIVITY -->
            <!-- ===================================================== -->

            <section>

                <!-- Header -->
                <div class="mb-4 flex items-center justify-between">
                    <div>
                        <h2 class="text-xs font-bold uppercase tracking-wider text-slate-900">
                            Recent Activity
                        </h2>
                        <p class="mt-1 text-xs text-slate-400">
                            Latest actions recorded in the system.
                        </p>
                    </div>

                    <!-- View All -->
                    <a
                        href="{{ route('mpp.audit.logs') }}"
                        class="text-[11px] font-bold text-brand-600 hover:text-brand-700"
                    >
                        View all
                    </a>
                </div>

                <!-- ================================================= -->
                <!-- ACTIVITY BOX -->
                <!-- ================================================= -->

                <div class="overflow-hidden rounded-2xl border border-slate-200/60 bg-white shadow-sm">
                    @if(isset($auditLogs) && $auditLogs->count() > 0)

                        @foreach($auditLogs->take(5) as $log)

                            <!-- ================================================= -->
                            <!-- ACTIVITY ITEM (PRO UI HORIZONTAL ROW) -->
                            <!-- ================================================= -->

                            <div class="grid grid-cols-1 items-center gap-2 border-b border-slate-100 px-5 py-4 last:border-b-0 md:grid-cols-12 md:gap-4">

                                <!-- 1. ACTION & ICON (Columns 1-4) -->
                                <div class="flex min-w-0 items-center space-x-3 md:col-span-4">
                                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-500">
                                        <i class="fa-solid fa-shield-halved text-xs"></i>
                                    </div>

                                    <p class="truncate text-xs font-bold text-slate-800">
                                        {{ $log->action }}
                                    </p>
                                </div>

                                <!-- 2. USER (Columns 5-7) -->
                                <div class="min-w-0 md:col-span-3">
                                    <p class="truncate text-[11px] text-slate-500">
                                        <span class="font-semibold text-slate-600">By:</span>
                                        @if($log->user)
                                            {{ $log->user->name }}
                                        @else
                                            System
                                        @endif
                                    </p>
                                </div>

                                <!-- 3. DESCRIPTION (Columns 8-10) -->
                                <div class="min-w-0 md:col-span-3">
                                    <p class="truncate text-[10px] text-slate-400">
                                        {{ $log->description ?? 'System activity recorded.' }}
                                    </p>
                                </div>

                                <!-- 4. DATE & TIME (Columns 11-12) -->
                                <div class="shrink-0 text-left md:col-span-2 md:text-right">
                                    <p class="text-[10px] font-semibold text-slate-500">
                                        {{ $log->created_at->format('d M Y') }}
                                    </p>
                                    <p class="mt-0.5 text-[10px] text-slate-400">
                                        {{ $log->created_at->format('h:i A') }}
                                    </p>
                                </div>

                            </div>

                        @endforeach

                    @else

                        <!-- ================================================= -->
                        <!-- NO ACTIVITY -->
                        <!-- ================================================= -->

                        <div class="p-8 text-center">
                            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                                <i class="fa-solid fa-clock-rotate-left"></i>
                            </div>

                            <p class="mt-3 text-xs font-semibold text-slate-500">
                                No recent activity.
                            </p>

                            <p class="mt-1 text-[11px] text-slate-400">
                                System activities will appear here.
                            </p>
                        </div>

                    @endif
                </div>

            </section>

        </div>


    </main>


</div>


</body>

</html>