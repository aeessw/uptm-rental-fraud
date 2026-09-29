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
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous"
    >

    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
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

    <main class="ml-0 min-w-0 md:ml-[280px]">






        <!-- ===================================================== -->
        <!-- PAGE CONTENT -->
        <!-- ===================================================== -->

        <div class="mpp-page-content space-y-4 p-4 md:p-6">


            <!-- Welcome -->
@include('mpp.page-heading', ['title' => 'Dashboard', 'description' => 'Monitor rental listings, student accounts, and recent moderation activity.'])



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
                        <h2 class="text-sm font-semibold text-slate-900">
                            Recent Activity
                        </h2>
                        <p class="mt-1 text-xs text-slate-400">
                            Latest account and moderation events.
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

                            @php
                                $activityLabel = match($log->audit_action) {
                                    'login', 'google_login' => 'Logged in',
                                    'removed_listing', 'hidden_listing' => 'Hidden listing',
                                    'listing_auto_hidden' => 'Automatically hidden listing',
                                    'suspended_user', 'suspended_student' => 'Suspended student',
                                    'unsuspended_user' => 'Unsuspended student',
                                    'auto_suspended_user' => 'Automatically suspended student',
                                    default => ucfirst(str_replace('_', ' ', $log->audit_action)),
                                };
                                $activityIcon = match($log->audit_action) {
                                    'restored_listing' => 'fa-rotate-left',
                                    'reported_listing' => 'fa-flag',
                                    'created_listing' => 'fa-house',
                                    'suspended_user', 'suspended_student', 'auto_suspended_user' => 'fa-user-slash',
                                    'unsuspended_user' => 'fa-user-check',
                                    default => 'fa-shield-halved',
                                };
                                $targetLabel = in_array($log->audit_action, ['login', 'google_login']) ? 'Account login' : ($log->audit_target ?: 'Account activity');
                                if ($log->listingId()) {
                                    $targetLabel = ($log->auditListing?->listing_title ? $log->auditListing->listing_title.' '.mb_chr(183).' ' : '').'Listing #'.$log->listingId();
                                } elseif (preg_match('/User ID: ?([0-9]+)/', (string) $log->audit_target, $targetUser)) {
                                    $targetLabel = ($log->affectedUser?->user_name ? $log->affectedUser->user_name.' '.mb_chr(183).' ' : '').'Student #'.$targetUser[1];
                                }
                            @endphp
                            <div class="grid items-center gap-3 border-b border-slate-100 px-5 py-4 last:border-b-0 md:grid-cols-[minmax(0,1.4fr)_minmax(0,1fr)_auto]">
                                <div class="flex min-w-0 items-center gap-3">
                                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-500"><i class="fa-solid {{ $activityIcon }}" aria-hidden="true"></i></span>
                                    <div class="min-w-0"><p class="text-sm font-semibold text-slate-800">{{ $activityLabel }}</p><p class="mt-1 truncate text-xs text-slate-500" title="{{ $targetLabel }}">{{ $targetLabel }}</p></div>
                                </div>
                                <div class="min-w-0"><p class="text-xs text-slate-400">Performed by</p><p class="mt-1 truncate text-sm font-semibold text-slate-700" title="{{ $log->user?->user_name ?? 'System' }}">{{ $log->user?->user_name ?? 'System' }}</p></div>
                                <time class="whitespace-nowrap text-xs text-slate-500 md:text-right">{{ $log->audit_created_at->format('d M Y') }} &middot; {{ $log->audit_created_at->format('h:i A') }}</time>
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