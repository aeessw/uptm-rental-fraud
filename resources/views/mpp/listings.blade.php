<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Listings - UPTM Rental</title>

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

<body class="bg-slate-50/80 font-sans antialiased text-slate-800">

<div class="min-h-screen">

<!-- ================= SIDEBAR ================= -->

@include('mpp.sidebar')


<!-- ================= MAIN ================= -->

<main class="ml-0 min-w-0 md:ml-64">


    <!-- ================= HEADER ================= -->

    <header
        class="sticky top-0 z-40 flex items-center
               justify-between border-b border-slate-200/60
               bg-white/90 px-8 py-4 backdrop-blur-md"
    >

        <div>

            <h2 class="text-base font-bold tracking-tight text-slate-900">
                Listings
            </h2>

        </div>


        <!-- PROFILE -->

        <div class="flex items-center space-x-3">

            <div class="text-right">

                <div class="mb-0.5 text-xs font-bold leading-none text-slate-900">
                    {{ Auth::user()->name }}
                </div>

                <div class="text-[11px] font-medium leading-none text-slate-400">
                    {{ Auth::user()->email }}
                </div>

            </div>


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



    <!-- ================= CONTENT ================= -->

    <div class="space-y-5 p-8">


        <!-- SUCCESS -->

        @if(session('success'))

            <div
                class="flex items-center gap-2.5 rounded-xl
                       border border-emerald-500/20
                       bg-emerald-500/10 p-4
                       text-xs font-semibold text-emerald-800"
            >

                <i class="fa-solid fa-circle-check text-emerald-600"></i>

                <span>
                    {{ session('success') }}
                </span>

            </div>

        @endif



        <!-- ================= FILTER ================= -->

        <section
            class="rounded-2xl border border-slate-200/60
                   bg-white p-4 shadow-sm"
        >

            <div
                class="flex flex-col gap-3
                       lg:flex-row lg:items-center"
            >

                <!-- SEARCH -->

                <div class="relative flex-1">

                    <i
                        class="fa-solid fa-magnifying-glass
                               absolute left-3 top-1/2
                               -translate-y-1/2
                               text-xs text-slate-400"
                    ></i>

                    <input
                        type="text"
                        id="listingSearch"
                        placeholder="Search listings..."
                        class="w-full rounded-xl
                               border border-slate-200
                               bg-slate-50
                               py-2.5 pl-9 pr-4
                               text-xs font-medium
                               text-slate-700
                               outline-none
                               focus:border-brand-500
                               focus:bg-white
                               focus:ring-2
                               focus:ring-brand-500/10"
                    >

                </div>



                <!-- STATUS -->

                <select
                    id="statusFilter"
                    class="rounded-xl
                           border border-slate-200
                           bg-slate-50
                           px-4 py-2.5
                           text-xs font-semibold
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



                <!-- REPORTS -->

                <select
                    id="reportFilter"
                    class="rounded-xl
                           border border-slate-200
                           bg-slate-50
                           px-4 py-2.5
                           text-xs font-semibold
                           text-slate-600
                           outline-none
                           focus:border-brand-500
                           focus:ring-2
                           focus:ring-brand-500/10"
                >

                    <option value="all">
                        All Reports
                    </option>

                    <option value="reported">
                        Reported
                    </option>

                    <option value="none">
                        No Reports
                    </option>

                </select>

            </div>

        </section>



        <!-- ================= TABLE ================= -->

        <section
            class="overflow-hidden rounded-2xl
                   border border-slate-200/60
                   bg-white shadow-sm"
        >

            @if($listings->count() > 0)

                <div class="overflow-x-auto">

                    <table
                        id="listingsTable"
                        class="w-full min-w-[950px] text-left"
                    >

                        <thead
                            class="border-b border-slate-200
                                   bg-slate-50"
                        >

                            <tr>

                                <th
                                    class="px-6 py-4
                                           text-[10px] font-bold
                                           uppercase tracking-wider
                                           text-slate-500"
                                >
                                    Listing
                                </th>

                                <th
                                    class="px-6 py-4
                                           text-[10px] font-bold
                                           uppercase tracking-wider
                                           text-slate-500"
                                >
                                    Owner
                                </th>

                                <th
                                    class="px-6 py-4
                                           text-[10px] font-bold
                                           uppercase tracking-wider
                                           text-slate-500"
                                >
                                    Location
                                </th>

                                <th
                                    class="px-6 py-4
                                           text-[10px] font-bold
                                           uppercase tracking-wider
                                           text-slate-500"
                                >
                                    Rent
                                </th>

                                <th
                                    class="px-6 py-4
                                           text-[10px] font-bold
                                           uppercase tracking-wider
                                           text-slate-500"
                                >
                                    Room
                                </th>

                                <th
                                    class="px-6 py-4
                                           text-center
                                           text-[10px] font-bold
                                           uppercase tracking-wider
                                           text-slate-500"
                                >
                                    Reports
                                </th>

                                <th
                                    class="px-6 py-4
                                           text-[10px] font-bold
                                           uppercase tracking-wider
                                           text-slate-500"
                                >
                                    Status
                                </th>

                                <th
                                    class="px-6 py-4 text-right
                                           text-[10px] font-bold
                                           uppercase tracking-wider
                                           text-slate-500"
                                >
                                    Action
                                </th>

                            </tr>

                        </thead>



                        <tbody class="divide-y divide-slate-100">

                            @foreach($listings as $listing)

                                <tr
                                    class="listing-row transition hover:bg-slate-50"
                                    data-search="{{ strtolower(
                                        $listing->title . ' ' .
                                        ($listing->user->name ?? '') . ' ' .
                                        ($listing->user->email ?? '') . ' ' .
                                        $listing->location . ' ' .
                                        $listing->room_type
                                    ) }}"
                                    data-status="{{ $listing->status }}"
                                    data-reports="{{ $listing->report_count > 0 ? 'reported' : 'none' }}"
                                >


                                    <!-- LISTING -->

                                    <td class="px-6 py-4">

                                        <p
                                            class="max-w-[200px]
                                                   truncate text-xs
                                                   font-bold text-slate-800"
                                        >

                                            {{ $listing->title }}

                                        </p>

                                        <p
                                            class="mt-1 max-w-[200px]
                                                   truncate text-[10px]
                                                   text-slate-400"
                                        >

                                            {{ $listing->description }}

                                        </p>

                                    </td>



                                    <!-- OWNER -->

                                    <td class="px-6 py-4">

                                        @if($listing->user)

                                            <p
                                                class="max-w-[190px]
                                                       truncate text-xs
                                                       font-semibold
                                                       text-slate-700"
                                            >

                                                {{ $listing->user->name }}

                                            </p>

                                            <p
                                                class="mt-1 max-w-[190px]
                                                       truncate text-[10px]
                                                       text-slate-400"
                                            >

                                                {{ $listing->user->email }}

                                            </p>

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

                                    <td class="px-6 py-4">

                                        <p
                                            class="max-w-[180px]
                                                   text-xs
                                                   text-slate-600"
                                        >

                                            {{ $listing->location }}

                                        </p>

                                    </td>



                                    <!-- RENT -->

                                    <td class="whitespace-nowrap px-6 py-4">

                                        <span
                                            class="text-xs font-bold
                                                   text-brand-600"
                                        >

                                            RM {{ number_format($listing->rent, 2) }}

                                        </span>

                                    </td>



                                    <!-- ROOM -->

                                    <td class="px-6 py-4">

                                        <span
                                            class="rounded-full
                                                   bg-slate-100
                                                   px-2.5 py-1
                                                   text-[10px]
                                                   font-bold
                                                   text-slate-600"
                                        >

                                            {{ $listing->room_type }}

                                        </span>

                                    </td>



                                    <!-- REPORTS -->

                                    <td class="px-6 py-4 text-center">

                                        @if($listing->report_count > 0)

                                            <span
                                                class="inline-flex min-w-[28px]
                                                       items-center justify-center
                                                       rounded-full
                                                       bg-rose-100
                                                       px-2 py-1
                                                       text-[10px]
                                                       font-bold
                                                       text-rose-600"
                                            >

                                                {{ $listing->report_count }}

                                            </span>

                                        @else

                                            <span
                                                class="text-xs
                                                       text-slate-400"
                                            >
                                                0
                                            </span>

                                        @endif

                                    </td>



                                    <!-- STATUS -->

                                    <td class="px-6 py-4">

                                        @if($listing->status === 'active')

                                            <span
                                                class="inline-flex items-center gap-1.5
                                                       rounded-full
                                                       bg-emerald-100
                                                       px-2.5 py-1
                                                       text-[10px]
                                                       font-bold
                                                       text-emerald-700"
                                            >

                                                <span
                                                    class="h-1.5 w-1.5
                                                           rounded-full
                                                           bg-emerald-500"
                                                ></span>

                                                Active

                                            </span>

                                        @elseif($listing->status === 'hidden')

                                            <span
                                                class="inline-flex items-center gap-1.5
                                                       rounded-full
                                                       bg-amber-100
                                                       px-2.5 py-1
                                                       text-[10px]
                                                       font-bold
                                                       text-amber-700"
                                            >

                                                <span
                                                    class="h-1.5 w-1.5
                                                           rounded-full
                                                           bg-amber-500"
                                                ></span>

                                                Hidden

                                            </span>

                                        @else

                                            <span
                                                class="rounded-full
                                                       bg-slate-100
                                                       px-2.5 py-1
                                                       text-[10px]
                                                       font-bold
                                                       text-slate-600"
                                            >

                                                {{ ucfirst($listing->status) }}

                                            </span>

                                        @endif

                                    </td>



                                    <!-- ACTION -->

                                    <td class="px-6 py-4 text-right">

                                        @if($listing->status === 'active')

                                            <form
                                                action="{{ route('mpp.listings.remove', $listing->id) }}"
                                                method="POST"
                                                class="inline"
                                            >

                                                @csrf

                                                <button
                                                    type="submit"
                                                    class="rounded-lg
                                                           bg-rose-50
                                                           px-3 py-2
                                                           text-[10px]
                                                           font-bold
                                                           text-rose-600
                                                           transition
                                                           hover:bg-rose-100"
                                                    onclick="return confirm('Remove this listing?')"
                                                >

                                                    <i
                                                        class="fa-solid
                                                               fa-eye-slash
                                                               mr-1"
                                                    ></i>

                                                    Remove

                                                </button>

                                            </form>

                                        @elseif($listing->status === 'hidden')

                                            <form
                                                action="{{ route('mpp.listings.restore', $listing->id) }}"
                                                method="POST"
                                                class="inline"
                                            >

                                                @csrf

                                                <button
                                                    type="submit"
                                                    class="rounded-lg
                                                           bg-amber-50
                                                           px-3 py-2
                                                           text-[10px]
                                                           font-bold
                                                           text-amber-700
                                                           transition
                                                           hover:bg-amber-100"
                                                    onclick="return confirm('Restore this listing?')"
                                                >

                                                    <i
                                                        class="fa-solid
                                                               fa-rotate-left
                                                               mr-1"
                                                    ></i>

                                                    Restore

                                                </button>

                                            </form>

                                        @endif

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>



                <!-- NO RESULTS -->

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

                        <i class="fa-solid fa-magnifying-glass"></i>

                    </div>

                    <p class="text-xs font-semibold text-slate-600">
                        No listings found
                    </p>

                    <p class="mt-1 text-[11px] text-slate-400">
                        Try changing your search or filters.
                    </p>

                </div>


            @else

                <!-- EMPTY -->

                <div class="p-12 text-center">

                    <div
                        class="mx-auto mb-3 flex h-12 w-12
                               items-center justify-center
                               rounded-full bg-slate-100
                               text-slate-400"
                    >

                        <i class="fa-solid fa-house text-lg"></i>

                    </div>

                    <h3 class="text-sm font-bold text-slate-700">
                        No listings
                    </h3>

                    <p class="mt-1 text-xs text-slate-400">
                        No student listings are available.
                    </p>

                </div>

            @endif

        </section>

    </div>

</main>
```

</div>

<!-- ================= FILTER SCRIPT ================= -->

<script>

    const listingSearch = document.getElementById('listingSearch');
    const statusFilter = document.getElementById('statusFilter');
    const reportFilter = document.getElementById('reportFilter');

    const listingRows = document.querySelectorAll('.listing-row');
    const noResults = document.getElementById('noResults');


    function filterListings() {

        const search =
            listingSearch.value.toLowerCase().trim();

        const status =
            statusFilter.value;

        const reports =
            reportFilter.value;

        let visible = 0;


        listingRows.forEach(row => {

            const rowSearch =
                row.dataset.search;

            const rowStatus =
                row.dataset.status;

            const rowReports =
                row.dataset.reports;


            const matchesSearch =
                rowSearch.includes(search);

            const matchesStatus =
                status === 'all' ||
                rowStatus === status;

            const matchesReports =
                reports === 'all' ||
                rowReports === reports;


            if (
                matchesSearch &&
                matchesStatus &&
                matchesReports
            ) {

                row.classList.remove('hidden');

                visible++;

            } else {

                row.classList.add('hidden');

            }

        });


        if (visible === 0 && listingRows.length > 0) {

            noResults.classList.remove('hidden');

        } else {

            noResults.classList.add('hidden');

        }

    }


    listingSearch.addEventListener(
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

</script>

</body>

</html>
