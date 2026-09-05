<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Reports - UPTM Rental</title>

<!-- Google Font -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

<link
    href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
    rel="stylesheet"
>

<!-- Tailwind -->
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

<!-- Font Awesome -->
<link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
>

</head>

<body class="bg-slate-50 font-sans antialiased text-slate-800">

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
    <!-- HEADER -->
    <!-- ===================================================== -->

    <header
        class="sticky top-0 z-40 flex h-[68px]
               items-center justify-between
               border-b border-slate-200
               bg-white/95 px-5 sm:px-6 lg:px-8
               backdrop-blur-md"
    >

        <div class="min-w-0">

            <h2
                class="truncate text-sm font-bold
                       tracking-tight text-slate-900
                       sm:text-base"
            >
                Reports
            </h2>

            <p
                class="mt-0.5 hidden text-[10px]
                       font-medium text-slate-400 sm:block"
            >
                Review reported listings
            </p>

        </div>


        <!-- MPP PROFILE -->

        <div class="ml-4 flex shrink-0 items-center gap-2.5">

            <div class="hidden text-right sm:block">

                <div
                    class="text-[11px] font-bold
                           leading-tight text-slate-900"
                >
                    {{ Auth::user()->name }}
                </div>

                <div
                    class="mt-0.5 max-w-[180px]
                           truncate text-[10px]
                           font-medium text-slate-400"
                >
                    {{ Auth::user()->email }}
                </div>

            </div>


            <div
                class="flex h-8 w-8 shrink-0
                       items-center justify-center
                       rounded-full border border-brand-100
                       bg-brand-50 text-[10px] font-bold
                       uppercase text-brand-600
                       ring-2 ring-brand-500/10
                       sm:h-9 sm:w-9"
            >
                {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
            </div>

        </div>

    </header>



    <!-- ===================================================== -->
    <!-- PAGE -->
    <!-- ===================================================== -->

    <div class="p-5 sm:p-6 lg:p-7">


        <!-- ===================================================== -->
        <!-- TITLE -->
        <!-- ===================================================== -->

        <div class="mb-5">

            <h1
                class="text-base font-bold
                       text-slate-900 sm:text-lg"
            >
                Reported Listings
            </h1>

            <p
                class="mt-1 text-[11px]
                       font-medium text-slate-500 sm:text-xs"
            >
                Review listings flagged by students.
            </p>

        </div>



        <!-- ===================================================== -->
        <!-- SUCCESS -->
        <!-- ===================================================== -->

        @if(session('success'))

            <div
                class="mb-5 flex items-center gap-2.5
                       rounded-xl
                       border border-emerald-500/20
                       bg-emerald-500/10
                       p-3.5 text-xs font-semibold
                       text-emerald-800"
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
        <!-- FILTER BAR -->
        <!-- ===================================================== -->

        <section
            class="mb-5 rounded-xl
                   border border-slate-200
                   bg-white p-4 shadow-sm"
        >

            <div
                class="flex flex-col gap-3
                       lg:flex-row lg:items-center"
            >

                <!-- SEARCH -->

                <div class="relative min-w-0 flex-1">

                    <i
                        class="fa-solid fa-magnifying-glass
                               absolute left-3 top-1/2
                               -translate-y-1/2
                               text-[11px] text-slate-400"
                    ></i>

                    <input
                        type="text"
                        id="searchInput"
                        placeholder="Search listings..."
                        class="w-full rounded-lg
                               border border-slate-200
                               bg-slate-50
                               py-2.5 pl-9 pr-3
                               text-xs font-medium
                               text-slate-700
                               outline-none
                               transition
                               focus:border-brand-500
                               focus:bg-white
                               focus:ring-2
                               focus:ring-brand-500/10"
                    >

                </div>


                <!-- STATUS -->

                <select
                    id="statusFilter"
                    class="rounded-lg
                           border border-slate-200
                           bg-slate-50
                           px-3 py-2.5
                           text-xs font-medium
                           text-slate-600
                           outline-none
                           focus:border-brand-500
                           focus:ring-2
                           focus:ring-brand-500/10"
                >

                    <option value="all">
                        All Status
                    </option>

                    <option value="active">
                        Active
                    </option>

                    <option value="hidden">
                        Hidden
                    </option>

                </select>


                <!-- REPORT COUNT -->

                <select
                    id="reportFilter"
                    class="rounded-lg
                           border border-slate-200
                           bg-slate-50
                           px-3 py-2.5
                           text-xs font-medium
                           text-slate-600
                           outline-none
                           focus:border-brand-500
                           focus:ring-2
                           focus:ring-brand-500/10"
                >

                    <option value="all">
                        All Reports
                    </option>

                    <option value="1">
                        1 Report
                    </option>

                    <option value="2">
                        2 Reports
                    </option>

                    <option value="3">
                        3+ Reports
                    </option>

                </select>


                <!-- CLEAR -->

                <button
                    type="button"
                    id="clearFilters"
                    class="rounded-lg
                           border border-slate-200
                           bg-white px-4 py-2.5
                           text-xs font-bold
                           text-slate-500
                           transition
                           hover:bg-slate-50
                           hover:text-slate-700"
                >
                    <i class="fa-solid fa-xmark mr-1"></i>
                    Clear
                </button>

            </div>

        </section>



        <!-- ===================================================== -->
        <!-- LISTING TABLE -->
        <!-- ===================================================== -->

        <section
            class="overflow-hidden rounded-xl
                   border border-slate-200
                   bg-white shadow-sm"
        >


            @if($reportedListings->count() > 0)


                <div class="w-full overflow-x-auto">

                    <table
                        class="w-full min-w-[950px]
                               table-fixed text-left"
                    >

                        <!-- TABLE HEADER -->

                        <thead
                            class="border-b border-slate-200
                                   bg-slate-50"
                        >

                            <tr>

                                <th
                                    class="w-[250px] px-5 py-3
                                           text-[9px] font-bold
                                           uppercase tracking-wider
                                           text-slate-500"
                                >
                                    Listing
                                </th>

                                <th
                                    class="w-[230px] px-5 py-3
                                           text-[9px] font-bold
                                           uppercase tracking-wider
                                           text-slate-500"
                                >
                                    Owner
                                </th>

                                <th
                                    class="w-[180px] px-5 py-3
                                           text-[9px] font-bold
                                           uppercase tracking-wider
                                           text-slate-500"
                                >
                                    Location
                                </th>

                                <th
                                    class="w-[100px] px-5 py-3
                                           text-[9px] font-bold
                                           uppercase tracking-wider
                                           text-slate-500"
                                >
                                    Rent
                                </th>

                                <th
                                    class="w-[110px] px-5 py-3
                                           text-[9px] font-bold
                                           uppercase tracking-wider
                                           text-slate-500"
                                >
                                    Reports
                                </th>

                                <th
                                    class="w-[110px] px-5 py-3
                                           text-[9px] font-bold
                                           uppercase tracking-wider
                                           text-slate-500"
                                >
                                    Status
                                </th>

                                <th
                                    class="w-[130px] px-5 py-3
                                           text-[9px] font-bold
                                           uppercase tracking-wider
                                           text-slate-500"
                                >
                                    Action
                                </th>

                            </tr>

                        </thead>



                        <!-- TABLE BODY -->

                        <tbody
                            id="listingTable"
                            class="divide-y divide-slate-100"
                        >

                            @foreach($reportedListings as $listing)

                                @php

                                    $reportCount = (int) $listing->report_count;

                                    if ($reportCount >= 3) {
                                        $reportClass = 'bg-rose-100 text-rose-700';
                                    } elseif ($reportCount == 2) {
                                        $reportClass = 'bg-orange-100 text-orange-700';
                                    } else {
                                        $reportClass = 'bg-amber-100 text-amber-700';
                                    }

                                @endphp


                                <tr
                                    class="listing-row transition
                                           hover:bg-slate-50"
                                    data-title="{{ strtolower($listing->title) }}"
                                    data-owner="{{ strtolower($listing->user->name ?? '') }}"
                                    data-location="{{ strtolower($listing->location) }}"
                                    data-status="{{ strtolower($listing->status) }}"
                                    data-reports="{{ $reportCount }}"
                                >


                                    <!-- LISTING -->

                                    <td class="px-5 py-4">

                                        <div
                                            class="max-w-[220px]
                                                   truncate text-xs
                                                   font-bold text-slate-800"
                                        >
                                            {{ $listing->title }}
                                        </div>

                                        @if($listing->room_type)

                                            <div
                                                class="mt-1 text-[10px]
                                                       font-medium
                                                       text-slate-400"
                                            >
                                                {{ $listing->room_type }}
                                            </div>

                                        @endif

                                    </td>



                                    <!-- OWNER -->

                                    <td class="px-5 py-4">

                                        @if($listing->user)

                                            <div
                                                class="max-w-[210px]
                                                       truncate text-xs
                                                       font-semibold
                                                       text-slate-700"
                                            >
                                                {{ $listing->user->name }}
                                            </div>

                                            <div
                                                class="mt-1 max-w-[210px]
                                                       truncate text-[10px]
                                                       font-medium
                                                       text-slate-400"
                                            >
                                                {{ $listing->user->email }}
                                            </div>

                                        @else

                                            <span
                                                class="text-xs
                                                       text-slate-400"
                                            >
                                                Unknown
                                            </span>

                                        @endif

                                    </td>



                                    <!-- LOCATION -->

                                    <td class="px-5 py-4">

                                        <div
                                            class="max-w-[160px]
                                                   truncate text-xs
                                                   font-medium
                                                   text-slate-600"
                                        >
                                            {{ $listing->location }}
                                        </div>

                                    </td>



                                    <!-- RENT -->

                                    <td class="px-5 py-4">

                                        <span
                                            class="text-xs font-bold
                                                   text-brand-600"
                                        >
                                            RM {{ number_format($listing->rent, 2) }}
                                        </span>

                                    </td>



                                    <!-- REPORTS -->

                                    <td class="px-5 py-4">

                                        <span
                                            class="inline-flex
                                                   min-w-[34px]
                                                   items-center
                                                   justify-center
                                                   rounded-full
                                                   px-2.5 py-1
                                                   text-[10px]
                                                   font-extrabold
                                                   {{ $reportClass }}"
                                        >
                                            {{ $reportCount }}
                                        </span>

                                    </td>



                                    <!-- STATUS -->

                                    <td class="px-5 py-4">

                                        @if($listing->status === 'hidden')

                                            <span
                                                class="inline-flex
                                                       rounded-full
                                                       bg-slate-100
                                                       px-2.5 py-1
                                                       text-[10px]
                                                       font-bold
                                                       text-slate-600"
                                            >
                                                Hidden
                                            </span>

                                        @else

                                            <span
                                                class="inline-flex
                                                       rounded-full
                                                       bg-emerald-100
                                                       px-2.5 py-1
                                                       text-[10px]
                                                       font-bold
                                                       text-emerald-700"
                                            >
                                                Active
                                            </span>

                                        @endif

                                    </td>



                                    <!-- ACTION -->

                                    <td class="px-5 py-4">

                                        <button
                                            type="button"
                                            class="view-details
                                                   rounded-lg
                                                   bg-brand-50
                                                   px-3 py-2
                                                   text-[10px]
                                                   font-bold
                                                   text-brand-600
                                                   transition
                                                   hover:bg-brand-100"
                                            data-id="{{ $listing->id }}"
                                        >
                                            <i
                                                class="fa-solid fa-eye
                                                       mr-1"
                                            ></i>
                                            View Details
                                        </button>

                                    </td>


                                </tr>


                                <!-- ================================================= -->
                                <!-- HIDDEN DATA FOR POPUP -->
                                <!-- ================================================= -->

                                <div
                                    id="listing-data-{{ $listing->id }}"
                                    class="hidden"
                                >

                                    <div class="listing-title">
                                        {{ $listing->title }}
                                    </div>

                                 
                                    <div class="listing-location">
                                        {{ $listing->location }}
                                    </div>

                                    <div class="listing-rent">
                                        RM {{ number_format($listing->rent, 2) }}
                                    </div>

                                    <div class="listing-room">
                                        {{ $listing->room_type }}
                                    </div>

                                    <div class="listing-status">
                                        {{ ucfirst($listing->status) }}
                                    </div>

                                    <div class="listing-report-count">
                                        {{ $reportCount }}
                                    </div>

                                    <div class="listing-owner">
                                        {{ $listing->user->name ?? 'Unknown User' }}
                                    </div>

                                    <div class="listing-email">
                                        {{ $listing->user->email ?? '' }}
                                    </div>


                                    <!-- REPORT REASONS -->

                                    <div class="report-reasons">

                                        @if($listing->reports && $listing->reports->count() > 0)

                                            @foreach($listing->reports as $report)

                                                <div class="report-item">

                                                    <div class="report-reason">
                                                        {{ $report->reason }}
                                                    </div>

                                                    <div class="report-date">

                                                        {{ $report->created_at
                                                            ? $report->created_at->format('d M Y, h:i A')
                                                            : ''
                                                        }}

                                                    </div>

                                                </div>

                                            @endforeach

                                        @else

                                            <div class="report-item">

                                                <div class="report-reason">
                                                    No report reason available.
                                                </div>

                                            </div>

                                        @endif

                                    </div>

                                </div>

                            @endforeach

                        </tbody>

                    </table>

                </div>



                <!-- NO FILTER RESULTS -->

                <div
                    id="noResults"
                    class="hidden p-10 text-center"
                >

                    <div
                        class="mx-auto mb-3 flex h-10 w-10
                               items-center justify-center
                               rounded-full bg-slate-100
                               text-slate-400"
                    >

                        <i
                            class="fa-solid fa-magnifying-glass
                                   text-sm"
                        ></i>

                    </div>

                    <h3
                        class="text-sm font-bold
                               text-slate-700"
                    >
                        No listings found
                    </h3>

                    <p
                        class="mt-1 text-[11px]
                               font-medium text-slate-400"
                    >
                        Try changing your search or filters.
                    </p>

                </div>


            @else


                <!-- ================================================= -->
                <!-- EMPTY -->
                <!-- ================================================= -->

                <div class="p-10 text-center sm:p-12">

                    <div
                        class="mx-auto mb-3 flex h-11 w-11
                               items-center justify-center
                               rounded-full bg-emerald-50
                               text-emerald-500"
                    >

                        <i
                            class="fa-solid fa-shield-halved
                                   text-sm"
                        ></i>

                    </div>

                    <h3
                        class="text-sm font-bold
                               text-slate-700"
                    >
                        No Reports
                    </h3>

                    <p
                        class="mt-1 text-[10px]
                               font-medium text-slate-400
                               sm:text-xs"
                    >
                        No listings have been reported.
                    </p>

                </div>


            @endif

        </section>


    </div>

</main>
```

</div>

<!-- ===================================================== -->

<!-- DETAILS MODAL -->

<!-- ===================================================== -->

<div
    id="detailsModal"
    class="fixed inset-0 z-[100] hidden
           items-center justify-center
           bg-slate-900/50 px-4 py-6
           backdrop-blur-sm"
>

```
<div
    class="w-full max-w-2xl
           max-h-[90vh]
           overflow-y-auto
           rounded-2xl
           border border-slate-200
           bg-white shadow-2xl"
>


    <!-- MODAL HEADER -->

    <div
        class="sticky top-0 z-10
               flex items-center justify-between
               border-b border-slate-200
               bg-white px-5 py-4"
    >

        <div>

            <h2
                id="modalTitle"
                class="text-sm font-bold
                       text-slate-900"
            >
                Listing Details
            </h2>

            <p
                class="mt-0.5 text-[10px]
                       text-slate-400"
            >
                Review listing and reports
            </p>

        </div>


        <button
            type="button"
            id="closeModal"
            class="flex h-8 w-8 items-center
                   justify-center rounded-lg
                   text-slate-400 transition
                   hover:bg-slate-100
                   hover:text-slate-700"
        >

            <i class="fa-solid fa-xmark"></i>

        </button>

    </div>



    <!-- MODAL CONTENT -->

    <div class="space-y-5 p-5">


        <!-- LISTING INFO -->

        <div>

            <div
                class="mb-3 flex items-center
                       justify-between"
            >

                <h3
                    class="text-[10px]
                           font-bold uppercase
                           tracking-wider
                           text-slate-400"
                >
                    Listing
                </h3>

                <span
                    id="modalReportCount"
                    class="rounded-full px-2.5 py-1
                           text-[10px] font-extrabold"
                >
                </span>

            </div>


            <div
                class="rounded-xl
                       border border-slate-200
                       bg-slate-50 p-4"
            >

                <h3
                    id="modalListingTitle"
                    class="text-sm font-bold
                           text-slate-900"
                >
                </h3>


                <p
                    id="modalDescription"
                    class="mt-2 text-xs
                           leading-5 text-slate-600"
                >
                </p>


                <div
                    class="mt-4 grid grid-cols-1
                           gap-3 sm:grid-cols-2"
                >

                    <div>

                        <p
                            class="text-[9px] font-bold
                                   uppercase tracking-wider
                                   text-slate-400"
                        >
                            Owner
                        </p>

                        <p
                            id="modalOwner"
                            class="mt-1 text-xs
                                   font-semibold
                                   text-slate-700"
                        >
                        </p>

                        <p
                            id="modalEmail"
                            class="mt-0.5 break-all
                                   text-[10px]
                                   text-slate-400"
                        >
                        </p>

                    </div>


                    <div>

                        <p
                            class="text-[9px] font-bold
                                   uppercase tracking-wider
                                   text-slate-400"
                        >
                            Location
                        </p>

                        <p
                            id="modalLocation"
                            class="mt-1 text-xs
                                   font-semibold
                                   text-slate-700"
                        >
                        </p>

                    </div>


                    <div>

                        <p
                            class="text-[9px] font-bold
                                   uppercase tracking-wider
                                   text-slate-400"
                        >
                            Rent
                        </p>

                        <p
                            id="modalRent"
                            class="mt-1 text-xs
                                   font-bold
                                   text-brand-600"
                        >
                        </p>

                    </div>


                    <div>

                        <p
                            class="text-[9px] font-bold
                                   uppercase tracking-wider
                                   text-slate-400"
                        >
                            Room Type
                        </p>

                        <p
                            id="modalRoom"
                            class="mt-1 text-xs
                                   font-semibold
                                   text-slate-700"
                        >
                        </p>

                    </div>

                </div>

            </div>

        </div>



        <!-- REPORT REASONS -->

        <div>

            <div class="mb-3 flex items-center justify-between">

                <h3
                    class="text-[10px]
                           font-bold uppercase
                           tracking-wider
                           text-slate-400"
                >
                    Report Reasons
                </h3>

                <span
                    id="modalReportLabel"
                    class="text-[10px]
                           font-semibold
                           text-slate-400"
                >
                </span>

            </div>


            <div
                id="modalReasons"
                class="space-y-2"
            >
            </div>

        </div>



        <!-- ACTIONS -->

        <div
            class="border-t border-slate-100
                   pt-4"
        >

            <div
                id="modalActions"
                class="flex flex-col gap-2
                       sm:flex-row sm:justify-end"
            >
            </div>

        </div>

    </div>

</div>
```

</div>

<!-- ===================================================== -->

<!-- JAVASCRIPT -->

<!-- ===================================================== -->

<script>

document.addEventListener('DOMContentLoaded', function () {


    /* ===================================================== */
    /* FILTERS */
    /* ===================================================== */

    const searchInput =
        document.getElementById('searchInput');

    const statusFilter =
        document.getElementById('statusFilter');

    const reportFilter =
        document.getElementById('reportFilter');

    const clearFilters =
        document.getElementById('clearFilters');

    const rows =
        document.querySelectorAll('.listing-row');

    const noResults =
        document.getElementById('noResults');


    function filterListings() {

        const search =
            searchInput.value
                .toLowerCase()
                .trim();

        const status =
            statusFilter.value;

        const report =
            reportFilter.value;

        let visibleCount = 0;


        rows.forEach(function (row) {

            const title =
                row.dataset.title || '';

            const owner =
                row.dataset.owner || '';

            const location =
                row.dataset.location || '';

            const rowStatus =
                row.dataset.status || '';

            const reportCount =
                parseInt(row.dataset.reports || '0');


            const matchesSearch =
                title.includes(search) ||
                owner.includes(search) ||
                location.includes(search);


            const matchesStatus =
                status === 'all' ||
                rowStatus === status;


            let matchesReports = true;


            if (report === '1') {

                matchesReports =
                    reportCount === 1;

            }

            else if (report === '2') {

                matchesReports =
                    reportCount === 2;

            }

            else if (report === '3') {

                matchesReports =
                    reportCount >= 3;

            }


            if (
                matchesSearch &&
                matchesStatus &&
                matchesReports
            ) {

                row.classList.remove('hidden');

                visibleCount++;

            }

            else {

                row.classList.add('hidden');

            }

        });


        if (visibleCount === 0) {

            noResults.classList.remove('hidden');

        }

        else {

            noResults.classList.add('hidden');

        }

    }


    searchInput.addEventListener(
        'input',
        filterListings
    );

    statusFilter.addEventListener(
        'change',
        filterListings
    );

    reportFilter.addEventListener(
        'change',
        filterListings
    );


    clearFilters.addEventListener(
        'click',
        function () {

            searchInput.value = '';

            statusFilter.value = 'all';

            reportFilter.value = 'all';

            filterListings();

        }
    );



    /* ===================================================== */
    /* MODAL */
    /* ===================================================== */

    const modal =
        document.getElementById('detailsModal');

    const closeModal =
        document.getElementById('closeModal');


    const modalTitle =
        document.getElementById('modalTitle');

    const modalListingTitle =
        document.getElementById('modalListingTitle');

    const modalDescription =
        document.getElementById('modalDescription');

    const modalOwner =
        document.getElementById('modalOwner');

    const modalEmail =
        document.getElementById('modalEmail');

    const modalLocation =
        document.getElementById('modalLocation');

    const modalRent =
        document.getElementById('modalRent');

    const modalRoom =
        document.getElementById('modalRoom');

    const modalReportCount =
        document.getElementById('modalReportCount');

    const modalReportLabel =
        document.getElementById('modalReportLabel');

    const modalReasons =
        document.getElementById('modalReasons');

    const modalActions =
        document.getElementById('modalActions');



    /* ===================================================== */
    /* ESCAPE HTML */
    /* ===================================================== */

    function escapeHtml(value) {

        const div =
            document.createElement('div');

        div.textContent =
            value ?? '';

        return div.innerHTML;

    }



    /* ===================================================== */
    /* OPEN DETAILS */
    /* ===================================================== */

    document.querySelectorAll('.view-details')
        .forEach(function (button) {

            button.addEventListener(
                'click',
                function () {

                    const id =
                        button.dataset.id;

                    const data =
                        document.getElementById(
                            'listing-data-' + id
                        );


                    if (!data) {
                        return;
                    }


                    const title =
                        data.querySelector(
                            '.listing-title'
                        )?.textContent.trim() || '';


                    const description =
                        data.querySelector(
                            '.listing-description'
                        )?.textContent.trim() || '';


                    const owner =
                        data.querySelector(
                            '.listing-owner'
                        )?.textContent.trim() || '';


                    const email =
                        data.querySelector(
                            '.listing-email'
                        )?.textContent.trim() || '';


                    const location =
                        data.querySelector(
                            '.listing-location'
                        )?.textContent.trim() || '';


                    const rent =
                        data.querySelector(
                            '.listing-rent'
                        )?.textContent.trim() || '';


                    const room =
                        data.querySelector(
                            '.listing-room'
                        )?.textContent.trim() || '';


                    const status =
                        data.querySelector(
                            '.listing-status'
                        )?.textContent.trim() || '';


                    const count =
                        parseInt(
                            data.querySelector(
                                '.listing-report-count'
                            )?.textContent.trim() || '0'
                        );


                    /* TITLE */

                    modalTitle.textContent =
                        title;

                    modalListingTitle.textContent =
                        title;


                   
                    /* DETAILS */

                    modalOwner.textContent =
                        owner || 'Unknown';

                    modalEmail.textContent =
                        email;

                    modalLocation.textContent =
                        location || '-';

                    modalRent.textContent =
                        rent || '-';

                    modalRoom.textContent =
                        room || '-';


                    /* REPORT COUNT COLOR */

                    modalReportCount.textContent =
                        count + (
                            count === 1
                                ? ' Report'
                                : ' Reports'
                        );


                    modalReportCount.className =
                        'rounded-full px-2.5 py-1 text-[10px] font-extrabold';


                    if (count >= 3) {

                        modalReportCount.classList.add(
                            'bg-rose-100',
                            'text-rose-700'
                        );

                    }

                    else if (count === 2) {

                        modalReportCount.classList.add(
                            'bg-orange-100',
                            'text-orange-700'
                        );

                    }

                    else {

                        modalReportCount.classList.add(
                            'bg-amber-100',
                            'text-amber-700'
                        );

                    }


                    modalReportLabel.textContent =
                        count + (
                            count === 1
                                ? ' report'
                                : ' reports'
                        );


                    /* ================================================= */
                    /* REASONS */
                    /* ================================================= */

                    modalReasons.innerHTML = '';


                    const reportItems =
                        data.querySelectorAll(
                            '.report-item'
                        );


                    reportItems.forEach(function (item) {

                        const reason =
                            item.querySelector(
                                '.report-reason'
                            )?.textContent.trim() || '';


                        const date =
                            item.querySelector(
                                '.report-date'
                            )?.textContent.trim() || '';


                        const reasonBox =
                            document.createElement('div');


                        reasonBox.className =
                            'rounded-xl border border-slate-200 bg-white p-3';


                        reasonBox.innerHTML = `

                            <div class="flex items-start gap-3">

                                <div
                                    class="flex h-7 w-7 shrink-0
                                           items-center justify-center
                                           rounded-lg bg-rose-50
                                           text-rose-500"
                                >
                                    <i class="fa-solid fa-flag text-[10px]"></i>
                                </div>

                                <div class="min-w-0">

                                    <p
                                        class="text-xs font-semibold
                                               leading-5 text-slate-700"
                                    >
                                        ${escapeHtml(reason)}
                                    </p>

                                    ${
                                        date
                                        ? `
                                            <p
                                                class="mt-1 text-[9px]
                                                       font-medium
                                                       text-slate-400"
                                            >
                                                ${escapeHtml(date)}
                                            </p>
                                        `
                                        : ''
                                    }

                                </div>

                            </div>

                        `;


                        modalReasons.appendChild(
                            reasonBox
                        );

                    });


                    /* ================================================= */
                    /* ACTION BUTTONS */
                    /* ================================================= */

                    modalActions.innerHTML = '';


                    if (
                        status.toLowerCase() === 'active'
                    ) {

                        modalActions.innerHTML = `

                            <form
                                action="/mpp/listings/${id}/remove"
                                method="POST"
                                class="w-full sm:w-auto"
                            >

                                <input
                                    type="hidden"
                                    name="_token"
                                    value="{{ csrf_token() }}"
                                >

                                <button
                                    type="submit"
                                    class="w-full rounded-lg
                                           bg-rose-500
                                           px-4 py-2.5
                                           text-xs font-bold
                                           text-white transition
                                           hover:bg-rose-600"
                                    onclick="
                                        return confirm(
                                            'Are you sure you want to hide this listing?'
                                        )
                                    "
                                >
                                    <i
                                        class="fa-solid fa-eye-slash mr-1"
                                    ></i>

                                    Hide Listing

                                </button>

                            </form>

                        `;

                    }

                    else {

                        modalActions.innerHTML = `

                            <form
                                action="/mpp/listings/${id}/restore"
                                method="POST"
                                class="w-full sm:w-auto"
                            >

                                <input
                                    type="hidden"
                                    name="_token"
                                    value="{{ csrf_token() }}"
                                >

                                <button
                                    type="submit"
                                    class="w-full rounded-lg
                                           bg-emerald-500
                                           px-4 py-2.5
                                           text-xs font-bold
                                           text-white transition
                                           hover:bg-emerald-600"
                                    onclick="
                                        return confirm(
                                            'Restore this listing?'
                                        )
                                    "
                                >
                                    <i
                                        class="fa-solid fa-rotate-left mr-1"
                                    ></i>

                                    Restore Listing

                                </button>

                            </form>

                        `;

                    }


                    /* SHOW MODAL */

                    modal.classList.remove('hidden');

                    modal.classList.add('flex');

                    document.body.classList.add(
                        'overflow-hidden'
                    );

                }
            );

        });



    /* ===================================================== */
    /* CLOSE MODAL */
    /* ===================================================== */

    function closeDetailsModal() {

        modal.classList.add('hidden');

        modal.classList.remove('flex');

        document.body.classList.remove(
            'overflow-hidden'
        );

    }


    closeModal.addEventListener(
        'click',
        closeDetailsModal
    );


    modal.addEventListener(
        'click',
        function (event) {

            if (event.target === modal) {

                closeDetailsModal();

            }

        }
    );


    document.addEventListener(
        'keydown',
        function (event) {

            if (event.key === 'Escape') {

                closeDetailsModal();

            }

        }
    );


});

</script>

</body>

</html>
