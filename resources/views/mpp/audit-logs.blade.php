<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Audit Logs - UPTM Rental</title>

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

<body class="bg-slate-50 font-sans antialiased text-slate-800">

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
    <!-- HEADER -->
    <!-- ===================================================== -->

    <header
        class="sticky top-0 z-40 flex h-[68px]
               items-center justify-between
               border-b border-slate-200
               bg-white/95 px-5 sm:px-6 lg:px-8
               backdrop-blur-md"
    >

        <!-- PAGE TITLE -->

        <div class="min-w-0">

            <h2
                class="truncate text-sm font-bold
                       tracking-tight text-slate-900
                       sm:text-base"
            >

                Audit Logs

            </h2>

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


            <!-- AVATAR -->

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
    <!-- PAGE CONTENT -->
    <!-- ===================================================== -->

    <div class="p-5 sm:p-6 lg:p-7">


        <!-- ===================================================== -->
        <!-- PAGE TITLE + AUTO UPDATE -->
        <!-- ===================================================== -->

        <div
            class="mb-5 flex flex-col gap-4
                   lg:flex-row lg:items-start
                   lg:justify-between"
        >

            <div>

                <h1
                    class="text-base font-bold
                           text-slate-900
                           sm:text-lg"
                >

                    System Activity

                </h1>

                <p
                    class="mt-1 text-[11px]
                           font-medium text-slate-500
                           sm:text-xs"
                >

                    Review recent actions recorded by the system.

                </p>

            </div>


            <!-- ================================================= -->
            <!-- AUTO UPDATE -->
            <!-- ================================================= -->

            <div
                class="flex w-fit items-center gap-3
                       rounded-xl border border-slate-200
                       bg-white px-4 py-2.5 shadow-sm"
            >

                <div
                    class="flex h-7 w-7 shrink-0
                           items-center justify-center
                           rounded-lg bg-brand-50
                           text-brand-600"
                >

                    <i
                        class="fa-solid fa-rotate
                               text-[11px]"
                    ></i>

                </div>


                <div>

                    <p
                        class="text-[10px]
                               font-bold text-slate-700"
                    >

                        Automatic updates

                    </p>

                    <p
                        class="mt-0.5 text-[9px]
                               font-medium text-slate-400"
                    >

                        Checks for new activity every 5 seconds.

                    </p>

                    <p
                        class="mt-0.5 text-[9px]
                               font-medium text-slate-400"
                    >

                        Last checked:

                        <span id="lastChecked">
                            {{ now()->format('h:i:s A') }}
                        </span>

                    </p>

                </div>

            </div>

        </div>



        <!-- ===================================================== -->
        <!-- FILTERS -->
        <!-- ===================================================== -->

        <section
            class="mb-5 rounded-xl
                   border border-slate-200
                   bg-white p-4 shadow-sm"
        >

            <div
                class="mb-3 flex items-center
                       justify-between"
            >

                


                <!-- RESULT COUNT -->

                <span
                    id="resultCount"
                    class="text-[10px]
                           font-semibold
                           text-slate-400"
                >

                    {{ $auditLogs->count() }} records

                </span>

            </div>


            <!-- FILTER ROW -->

            <div
                class="grid grid-cols-1 gap-3
                       md:grid-cols-3"
            >


                <!-- SEARCH -->

                <div class="relative">

                    <i
                        class="fa-solid fa-magnifying-glass
                               absolute left-3 top-1/2
                               -translate-y-1/2
                               text-[11px] text-slate-400"
                    ></i>

                    <input
                        type="text"
                        id="searchInput"
                        placeholder="Search user or action..."
                        class="w-full rounded-lg
                               border border-slate-200
                               bg-slate-50 py-2.5 pl-9 pr-3
                               text-[11px] font-medium
                               text-slate-700
                               outline-none
                               transition
                               focus:border-brand-500
                               focus:bg-white
                               focus:ring-2
                               focus:ring-brand-500/10"
                    >

                </div>


                <!-- ACTION FILTER -->

                <div class="relative">

                    <select
                        id="actionFilter"
                        class="w-full appearance-none
                               rounded-lg
                               border border-slate-200
                               bg-slate-50 px-3 py-2.5
                               text-[11px] font-medium
                               text-slate-600
                               outline-none
                               transition
                               focus:border-brand-500
                               focus:bg-white
                               focus:ring-2
                               focus:ring-brand-500/10"
                    >

                        <option value="">All Actions</option>

                        @foreach($auditLogs->pluck('action')->unique() as $action)

                            <option value="{{ strtolower($action) }}">
                                {{ $action }}
                            </option>

                        @endforeach

                    </select>

                </div>


                <!-- DATE FILTER -->

                <div>

                    <select
                        id="dateFilter"
                        class="w-full rounded-lg
                               border border-slate-200
                               bg-slate-50 px-3 py-2.5
                               text-[11px] font-medium
                               text-slate-600
                               outline-none
                               transition
                               focus:border-brand-500
                               focus:bg-white
                               focus:ring-2
                               focus:ring-brand-500/10"
                    >

                        <option value="">All Dates</option>

                        <option value="today">
                            Today
                        </option>

                        <option value="yesterday">
                            Yesterday
                        </option>

                        <option value="week">
                            Last 7 Days
                        </option>

                    </select>

                </div>

            </div>

        </section>



        <!-- ===================================================== -->
        <!-- AUDIT TABLE -->
        <!-- ===================================================== -->

        <section
            class="overflow-hidden rounded-xl
                   border border-slate-200
                   bg-white shadow-sm"
        >

            @if($auditLogs->count() > 0)

                <div class="w-full overflow-x-auto">

                    <table
                        class="w-full min-w-[950px]
                               table-fixed text-left"
                    >

                        <!-- ================================================= -->
                        <!-- TABLE HEADER -->
                        <!-- ================================================= -->

                        <thead
                            class="border-b border-slate-200
                                   bg-slate-50"
                        >

                            <tr>

                                <!-- DATE -->

                                <th
                                    class="w-[170px]
                                           whitespace-nowrap
                                           px-4 py-3
                                           text-[9px] font-bold
                                           uppercase tracking-wider
                                           text-slate-500
                                           sm:px-5"
                                >

                                    Date & Time

                                </th>


                                <!-- USER WIDE -->

                                <th
                                    class="w-[420px]
                                           px-4 py-3
                                           text-[9px] font-bold
                                           uppercase tracking-wider
                                           text-slate-500
                                           sm:px-5"
                                >

                                    User

                                </th>


                                <!-- ACTION -->

                                <th
                                    class="w-[170px]
                                           whitespace-nowrap
                                           px-4 py-3
                                           text-[9px] font-bold
                                           uppercase tracking-wider
                                           text-slate-500
                                           sm:px-5"
                                >

                                    Action

                                </th>


                                <!-- DESCRIPTION -->

                                <th
                                    class="px-4 py-3
                                           text-[9px] font-bold
                                           uppercase tracking-wider
                                           text-slate-500
                                           sm:px-5"
                                >

                                    Description

                                </th>

                            </tr>

                        </thead>



                        <!-- ================================================= -->
                        <!-- TABLE BODY -->
                        <!-- ================================================= -->

                        <tbody
                            id="auditTableBody"
                            class="divide-y divide-slate-100"
                        >

                            @foreach($auditLogs as $log)

                                <tr
                                    class="audit-row transition
                                           hover:bg-slate-50"
                                    data-action="{{ strtolower($log->action) }}"
                                    data-user="{{ strtolower($log->user->name ?? '') }}"
                                    data-email="{{ strtolower($log->user->email ?? '') }}"
                                    data-date="{{ $log->created_at->format('Y-m-d') }}"
                                >


                                    <!-- DATE & TIME -->

                                    <td
                                        class="whitespace-nowrap
                                               px-4 py-3.5
                                               sm:px-5"
                                    >

                                        <div
                                            class="text-[11px]
                                                   font-semibold
                                                   text-slate-700"
                                        >

                                            {{ $log->created_at->format('d M Y') }}

                                        </div>

                                        <div
                                            class="mt-0.5 text-[9px]
                                                   font-medium
                                                   text-slate-400"
                                        >

                                            {{ $log->created_at->format('h:i A') }}

                                        </div>

                                    </td>



                                    <!-- USER -->

                                    <td
                                        class="px-4 py-3.5
                                               sm:px-5"
                                    >

                                        @if($log->user)

                                            <div
                                                class="text-[11px]
                                                       font-bold
                                                       leading-5
                                                       text-slate-800"
                                            >

                                                {{ $log->user->name }}

                                            </div>

                                            <div
                                                class="mt-0.5
                                                       break-all
                                                       text-[9px]
                                                       font-medium
                                                       leading-4
                                                       text-slate-400"
                                            >

                                                {{ $log->user->email }}

                                            </div>

                                        @else

                                            <span
                                                class="text-[10px]
                                                       font-medium
                                                       text-slate-400"
                                            >

                                                Unknown User

                                            </span>

                                        @endif

                                    </td>



                                    <!-- ACTION -->

                                    <td
                                        class="px-4 py-3.5
                                               sm:px-5"
                                    >

                                        <span
                                            class="inline-flex
                                                   rounded-full
                                                   bg-brand-50
                                                   px-2.5 py-1
                                                   text-[9px]
                                                   font-bold
                                                   text-brand-600"
                                        >

                                            {{ $log->action }}

                                        </span>

                                    </td>



                                    <!-- DESCRIPTION -->

                                    <td
                                        class="px-4 py-3.5
                                               sm:px-5"
                                    >

                                        <p
                                            class="text-[10px]
                                                   font-medium
                                                   leading-relaxed
                                                   text-slate-600
                                                   sm:text-[11px]"
                                        >

                                            {{ $log->description ?? 'System activity recorded.' }}

                                        </p>

                                    </td>


                                </tr>

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
                        class="mx-auto mb-3
                               flex h-11 w-11
                               items-center justify-center
                               rounded-full
                               bg-slate-100
                               text-slate-400"
                    >

                        <i
                            class="fa-solid
                                   fa-magnifying-glass
                                   text-sm"
                        ></i>

                    </div>

                    <h3
                        class="text-xs font-bold
                               text-slate-700
                               sm:text-sm"
                    >

                        No matching activity

                    </h3>

                    <p
                        class="mt-1 text-[10px]
                               font-medium
                               text-slate-400
                               sm:text-xs"
                    >

                        Try another search or filter.

                    </p>

                </div>


            @else


                <!-- EMPTY STATE -->

                <div
                    class="p-10 text-center sm:p-12"
                >

                    <div
                        class="mx-auto mb-3
                               flex h-11 w-11
                               items-center justify-center
                               rounded-full bg-slate-100
                               text-slate-400"
                    >

                        <i
                            class="fa-solid
                                   fa-clock-rotate-left
                                   text-sm"
                        ></i>

                    </div>


                    <h3
                        class="text-xs font-bold
                               text-slate-700
                               sm:text-sm"
                    >

                        No Activity

                    </h3>


                    <p
                        class="mt-1 text-[10px]
                               font-medium
                               text-slate-400
                               sm:text-xs"
                    >

                        No system activity has been recorded yet.

                    </p>

                </div>

            @endif

        </section>


    </div>

</main>
```

</div>

<!-- ===================================================== -->

<!-- FILTER SCRIPT -->

<!-- ===================================================== -->

<script>

    const searchInput =
        document.getElementById('searchInput');

    const actionFilter =
        document.getElementById('actionFilter');

    const dateFilter =
        document.getElementById('dateFilter');

    const resultCount =
        document.getElementById('resultCount');

    const noResults =
        document.getElementById('noResults');

    const rows =
        document.querySelectorAll('.audit-row');


    function filterLogs() {

        const search =
            searchInput.value.toLowerCase().trim();

        const action =
            actionFilter.value.toLowerCase();

        const date =
            dateFilter.value;


        let visibleCount = 0;


        const now = new Date();

        const today =
            new Date(
                now.getFullYear(),
                now.getMonth(),
                now.getDate()
            );


        rows.forEach(function(row) {

            const rowUser =
                row.dataset.user || '';

            const rowEmail =
                row.dataset.email || '';

            const rowAction =
                row.dataset.action || '';

            const rowDate =
                row.dataset.date || '';


            let matchesSearch =
                rowUser.includes(search) ||
                rowEmail.includes(search) ||
                rowAction.includes(search);


            let matchesAction =
                !action ||
                rowAction === action;


            let matchesDate = true;


            if (date) {

                const activityDate =
                    new Date(rowDate + 'T00:00:00');


                if (date === 'today') {

                    matchesDate =
                        activityDate.getTime() === today.getTime();

                }


                else if (date === 'yesterday') {

                    const yesterday =
                        new Date(today);

                    yesterday.setDate(
                        today.getDate() - 1
                    );

                    matchesDate =
                        activityDate.getTime() === yesterday.getTime();

                }


                else if (date === 'week') {

                    const sevenDaysAgo =
                        new Date(today);

                    sevenDaysAgo.setDate(
                        today.getDate() - 6
                    );

                    matchesDate =
                        activityDate >= sevenDaysAgo &&
                        activityDate <= today;

                }

            }


            if (
                matchesSearch &&
                matchesAction &&
                matchesDate
            ) {

                row.style.display = '';

                visibleCount++;

            }

            else {

                row.style.display = 'none';

            }

        });


        resultCount.textContent =
            visibleCount +
            (visibleCount === 1
                ? ' record'
                : ' records');


        if (noResults) {

            if (visibleCount === 0) {

                noResults.classList.remove('hidden');

            }

            else {

                noResults.classList.add('hidden');

            }

        }

    }


    searchInput.addEventListener(
        'input',
        filterLogs
    );

    actionFilter.addEventListener(
        'change',
        filterLogs
    );

    dateFilter.addEventListener(
        'change',
        filterLogs
    );



    // =====================================================
    // LAST CHECKED TIME
    // =====================================================

    function updateLastChecked() {

        const now = new Date();

        const hours =
            now.getHours();

        const minutes =
            String(now.getMinutes())
                .padStart(2, '0');

        const seconds =
            String(now.getSeconds())
                .padStart(2, '0');

        const ampm =
            hours >= 12 ? 'PM' : 'AM';

        const displayHours =
            String(hours % 12 || 12)
                .padStart(2, '0');

        const time =
            `${displayHours}:${minutes}:${seconds} ${ampm}`;


        const element =
            document.getElementById('lastChecked');


        if (element) {

            element.textContent = time;

        }

    }


    setInterval(
        updateLastChecked,
        1000
    );



    // =====================================================
    // AUTO REFRESH EVERY 5 SECONDS
    // =====================================================

    setInterval(function() {

        window.location.reload();

    }, 5000);

</script>

</body>

</html>
