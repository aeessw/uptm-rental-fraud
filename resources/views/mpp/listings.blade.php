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
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous"
>


    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
</head>

<body class="bg-slate-50/80 font-sans antialiased text-slate-800">

<div class="min-h-screen">

<!-- ================= SIDEBAR ================= -->

@include('mpp.sidebar')


<!-- ================= MAIN ================= -->

<main class="mpp-listings-page ml-0 min-w-0 md:ml-[280px]">


    <!-- ================= HEADER ================= -->


@if($errors->any())<div role="alert" class="mx-8 mt-4 rounded-lg bg-rose-50 p-3 text-rose-700">{{ $errors->first() }} Please reopen the moderation form.</div>@endif

@if(request()->filled('user_id'))<div class="mx-8 mt-5 rounded-xl border border-indigo-100 bg-indigo-50 p-3 text-sm text-indigo-700">Showing records for the selected student. <a class="ml-2 font-semibold underline" href="{{ route('mpp.listings') }}">Show all</a></div>@endif




    <!-- ================= CONTENT ================= -->

    <div class="mpp-page-content space-y-4 p-4 md:p-6">
        @include('mpp.page-heading', ['title' => 'Room Listings', 'description' => 'Review rental listings, availability, and moderation status.'])


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

                <select id="availabilityFilter" aria-label="Filter availability" class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs"><option value="all">All Availability</option><option value="available">Available</option><option value="rented">Rented</option><option value="unavailable">Unavailable</option></select>
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
                <button id="listingClear" type="button" class="shrink-0 rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50">Clear</button>

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



                                <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-wider text-slate-500">Availability</th>

                                <th
                                    class="px-6 py-4
                                           text-center
                                           text-[10px] font-bold
                                           uppercase tracking-wider
                                           text-slate-500"
                                >
                                    Reports
                                </th>

                                <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-wider text-slate-500">Review Status</th>

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
                                        $listing->listing_title . ' ' .
                                        ($listing->user->user_name ?? '') . ' ' .
                                        ($listing->user->user_email ?? '') . ' ' .
                                        $listing->listing_location . ' ' .
                                        $listing->room_type
                                    ) }}"
                                    data-status="{{ $listing->listing_status }}"
                                    data-availability="{{ $listing->listing_availability ?? 'unavailable' }}" data-reports="{{ $listing->report_count > 0 ? 'reported' : 'none' }}"
                                >


                                    <!-- LISTING -->

                                    <td class="px-6 py-4">

                                        <p
                                            title="{{ $listing->listing_title }}"
                                            class="listing-cell-title w-full min-w-0
                                                   truncate text-xs
                                                   font-bold text-slate-800"
                                        >

                                            {{ $listing->listing_title }}

                                        </p>

                                        <p
                                            class="listing-cell-description mt-1 w-full min-w-0
                                                   truncate text-[10px]
                                                   text-slate-400"
                                        >

                                            {{ $listing->listing_description }}

                                        </p>

                                    </td>



                                    <!-- OWNER -->

                                    <td class="px-6 py-4">

                                        @if($listing->user)

                                            <p
                                                title="{{ $listing->user->user_name }}"
                                                class="listing-cell-owner w-full min-w-0
                                                       truncate text-xs
                                                       font-semibold
                                                       text-slate-700"
                                            >

                                                {{ $listing->user->user_name }}

                                            </p>

                                            <p
                                                title="{{ $listing->user->user_email }}"
                                                class="listing-cell-email mt-1 w-full min-w-0
                                                       truncate text-[10px]
                                                       text-slate-400"
                                            >

                                                {{ $listing->user->user_email }}

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







                                    <td class="px-6 py-4">
                                        <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $listing->listing_availability === 'available' ? 'bg-emerald-100 text-emerald-700' : ($listing->listing_availability === 'rented' ? 'bg-rose-100 text-rose-700' : 'bg-slate-100 text-slate-600') }}">{{ ucfirst($listing->listing_availability ?? 'unavailable') }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if($listing->report_count > 0)
                                            <a href="{{ route('mpp.reports', ['listing_id' => $listing->getKey()]) }}" class="inline-flex whitespace-nowrap rounded-full px-3 py-1 text-xs font-semibold {{ $listing->report_count >= 3 ? 'bg-rose-100 text-rose-700' : ($listing->report_count === 2 ? 'bg-orange-100 text-orange-700' : 'bg-amber-100 text-amber-700') }}" aria-label="View reports for {{ $listing->listing_title }}">{{ $listing->report_count }} {{ $listing->report_count == 1 ? 'Report' : 'Reports' }}</a>
                                        @else
                                            <span class="text-xs text-slate-400">0 Reports</span>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4">@include('mpp.review-status', ['listing' => $listing])</td>

                                    <!-- STATUS -->

                                    <td class="px-6 py-4">

                                        @if($listing->listing_status === 'active')

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

                                        @elseif($listing->listing_status === 'hidden')

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

                                                {{ ucfirst($listing->listing_status) }}

                                            </span>

                                        @endif

                                    </td>



                                    <!-- Listing details and moderation actions. -->
                                    <td class="px-6 py-4 text-center">
                                        <div class="listing-row-actions">
                                            <button type="button" onclick="document.getElementById('listing-details-{{ $listing->getKey() }}').showModal()" class="rounded-lg bg-indigo-50 px-4 py-2 text-sm font-semibold text-indigo-700 hover:bg-indigo-100" aria-label="View details for {{ $listing->listing_title }}"><i class="fa-solid fa-eye mr-1" aria-hidden="true"></i>View</button>
                                        </div>
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

<div class="flex items-center justify-end gap-3 p-4"><span id="listingPageSummary" class="text-sm text-slate-500"></span><button id="listingPrevious" type="button" class="rounded-lg border px-3 py-2 disabled:opacity-40">Previous</button><button id="listingNext" type="button" class="rounded-lg border px-3 py-2 disabled:opacity-40">Next</button></div>
</main>
```

</div>

<!-- ================= FILTER SCRIPT ================= -->


<script>
(() => {
 const search = document.getElementById('listingSearch'), status = document.getElementById('statusFilter'), reports = document.getElementById('reportFilter'), availability = document.getElementById('availabilityFilter');
 const rows = [...document.querySelectorAll('.listing-row')]; let page = 1; const pageSize = 6;
 const previous = document.getElementById('listingPrevious'), next = document.getElementById('listingNext');
 function render() {
  const matching = rows.filter(row => row.dataset.search.includes(search.value.toLowerCase().trim()) && (status.value === 'all' || status.value === row.dataset.status) && (reports.value === 'all' || reports.value === row.dataset.reports) && (availability.value === 'all' || availability.value === row.dataset.availability));
  const pages = Math.max(1, Math.ceil(matching.length / pageSize)); page = Math.min(page, pages);
  rows.forEach(row => row.classList.add('hidden')); matching.slice((page - 1)*pageSize, page*pageSize).forEach(row => row.classList.remove('hidden'));
  document.getElementById('noResults')?.classList.toggle('hidden', matching.length !== 0);
  document.getElementById('listingPageSummary').textContent = matching.length ? `Showing ${(page-1)*pageSize+1}-${Math.min(page*pageSize,matching.length)} of ${matching.length} listings | Page ${page} of ${pages}` : 'No matching listings';
  previous.disabled = page === 1; next.disabled = page === pages; previous.hidden = next.hidden = pages === 1;
 }
 document.getElementById('listingClear').addEventListener('click', () => {search.value = '';status.value = reports.value = availability.value = 'all';page = 1;render();search.focus();});
 [search,status,reports,availability].forEach(el => el.addEventListener('input', () => {page = 1; render();}));
 previous.addEventListener('click', () => {page--;render();}); next.addEventListener('click', () => {page++;render();}); render();
})();
</script>

@include('mpp.listing-details-modals')
</body>

</html>
