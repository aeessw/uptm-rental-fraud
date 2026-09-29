<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Student Accounts - UPTM Rental</title>

<!-- Google Font -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

<link
    href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
    rel="stylesheet"
>

<!-- Tailwind CSS -->
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


<!-- ================= MAIN CONTENT ================= -->

<main class="ml-0 min-w-0 md:ml-[280px]">


    <!-- ================= HEADER ================= -->





    <!-- ================= PAGE CONTENT ================= -->

    <div class="mpp-page-content space-y-4 p-4 md:p-6">
        @include('mpp.page-heading', ['title' => 'Student Accounts', 'description' => 'Review student accounts, reports received, and account status.'])




        @if($errors->any())
            <div role="alert" class="rounded-xl border border-rose-200 bg-rose-50 p-4 text-rose-700">{{ $errors->first() }} Please reopen the suspension form to correct it.</div>
        @endif
        <!-- SUCCESS MESSAGE -->

        @if(session('success'))
            <script>
                window.alert(@json(session('success')));
            </script>
        @endif



        <div class="grid gap-3 sm:grid-cols-3" aria-label="Student account summary">
            @foreach(['Total Students' => $users->count(), 'Active' => $users->where('user_suspended', false)->count(), 'Suspended' => $users->where('user_suspended', true)->count()] as $label => $count)
                <div class="rounded-xl border border-slate-200 bg-white px-5 py-4"><p class="text-sm text-slate-500">{{ $label }}</p><p class="mt-1 text-lg font-semibold text-slate-900">{{ $count }}</p></div>
            @endforeach
        </div>
        <!-- ================= FILTER ================= -->

        <section
            class="rounded-2xl border border-slate-200/60
                   bg-white p-4 shadow-sm"
        >

            <div
                class="flex flex-col gap-3
                       sm:flex-row sm:items-center sm:justify-between"
            >

                <!-- SEARCH -->

                <div class="relative w-full min-w-0 sm:flex-1">

                    <i
                        class="fa-solid fa-magnifying-glass
                               absolute left-3 top-1/2
                               -translate-y-1/2
                               text-xs text-slate-400"
                    ></i>

                    <input
                        type="text"
                        id="studentSearch"
                        aria-label="Search students by name or email"
                        placeholder="Search name or email..."
                        class="w-full rounded-xl
                               border border-slate-200
                               bg-slate-50
                               py-2.5 pl-9 pr-4
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



                <!-- STATUS FILTER -->

                <select
                    id="statusFilter"
                    aria-label="Filter students by status"
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

                    <option value="suspended">
                        Suspended
                    </option>

                </select>

            </div>

        </section>



        <!-- ================= STUDENT TABLE ================= -->

        <section
            class="overflow-hidden rounded-2xl
                   border border-slate-200/60
                   bg-white shadow-sm"
        >

            @if($users->count() > 0)

                <div class="overflow-x-auto">

                    <table
                        id="studentsTable"
                        class="w-full min-w-[850px] text-left"
                    >

                        <!-- TABLE HEADER -->

                        <thead class="border-b border-slate-200 bg-slate-50">

                            <tr>

                                <th
                                    class="px-6 py-4
                                           text-[10px] font-bold
                                           uppercase tracking-wider
                                           text-slate-500"
                                >
                                    Student
                                </th>

                                <th
                                    class="px-6 py-4
                                           text-[10px] font-bold
                                           uppercase tracking-wider
                                           text-slate-500"
                                >
                                    Email
                                </th>

                                <th
                                    class="px-6 py-4
                                           text-[10px] font-bold
                                           uppercase tracking-wider
                                           text-slate-500"
                                >
                                    Listings
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
                                    class="px-6 py-4
                                           text-[10px] font-bold
                                           uppercase tracking-wider
                                           text-slate-500"
                                >
                                    Reports Received
                                </th>

                                <th
                                    class="px-6 py-4 text-right
                                           text-[10px] font-bold
                                           uppercase tracking-wider
                                           text-slate-500"
                                >
                                    Actions
                                </th>

                            </tr>

                        </thead>


                        <!-- TABLE BODY -->

                        <tbody class="divide-y divide-slate-100">

                            @foreach($users as $user)

                                <tr
                                    class="student-row transition hover:bg-slate-50"
                                    data-name="{{ strtolower($user->user_name) }}"
                                    data-email="{{ strtolower($user->user_email) }}"
                                    data-status="{{ $user->user_suspended ? 'suspended' : 'active' }}"
                                >


                                    <!-- STUDENT -->

                                    <td class="px-6 py-4">

                                        <div class="flex items-center gap-3">

                                            <div
                                                class="flex h-9 w-9 shrink-0
                                                       items-center justify-center
                                                       rounded-full
                                                       bg-brand-50
                                                       text-xs font-bold
                                                       uppercase
                                                       text-brand-600"
                                            >

                                                {{ strtoupper(substr($user->user_name, 0, 2)) }}

                                            </div>


                                            <div class="min-w-0">

                                                <p
                                                    class="truncate
                                                           text-xs font-bold
                                                           text-slate-800"
                                                >

                                                    {{ $user->user_name }}

                                                </p>

                                            </div>

                                        </div>

                                    </td>



                                    <!-- EMAIL -->

                                    <td class="px-6 py-4">

                                        <p
                                            class="text-xs font-medium
                                                   text-slate-600"
                                        >

                                            {{ $user->user_email }}

                                        </p>

                                    </td>



                                    <td class="px-6 py-4"><a class="font-semibold text-indigo-600" href="{{ route('mpp.listings', ['user_id' => $user->getKey()]) }}" aria-label="View listings by {{ $user->user_name }}">{{ $user->listings_count }}</a></td>

                                    <!-- STATUS -->

                                    <td class="px-6 py-4">

                                        @if($user->user_suspended)

                                            <span
                                                class="inline-flex items-center gap-1.5
                                                       rounded-full
                                                       bg-rose-100
                                                       px-2.5 py-1
                                                       text-[10px]
                                                       font-bold
                                                       text-rose-600"
                                            >

                                                <span
                                                    class="h-1.5 w-1.5
                                                           rounded-full
                                                           bg-rose-500"
                                                ></span>

                                                Suspended

                                            </span>

                                        @else

                                            <span
                                                class="inline-flex items-center gap-1.5
                                                       rounded-full
                                                       bg-emerald-100
                                                       px-2.5 py-1
                                                       text-[10px]
                                                       font-bold
                                                       text-emerald-600"
                                            >

                                                <span
                                                    class="h-1.5 w-1.5
                                                           rounded-full
                                                           bg-emerald-500"
                                                ></span>

                                                Active

                                            </span>

                                        @endif

                                    </td>

                                    <!-- REPORT COUNT -->

                                    <td class="px-6 py-4">
                                        <a href="{{ route('mpp.reports', ['user_id' => $user->getKey()]) }}" aria-label="View reports received by {{ $user->user_name }}" class="inline-flex items-center gap-1.5 rounded-full {{ $user->received_reports_count > 3 ? 'bg-rose-100 text-rose-700' : ($user->received_reports_count > 0 ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-500') }} px-2.5 py-1 text-[10px] font-bold">
                                            <i class="fa-solid fa-flag"></i>
                                            {{ $user->received_reports_count }}
                                        </a>
                                    </td>



                                    <!-- Consistent row actions; report history is linked in its own column. -->
                                    <td class="px-6 py-4">
                                        <div class="student-row-actions">
                                            <a class="student-action student-action-view" href="{{ route('mpp.students.show', $user->getKey()) }}" data-student-details="{{ $user->getKey() }}">View Student</a>
                                            @if(!$user->user_suspended)
                                                <button type="button" class="student-action student-action-suspend" onclick="document.getElementById('suspend-student-{{ $user->getKey() }}').showModal()">Suspend</button>
                                            @else
                                                <form action="{{ route('mpp.users.unsuspend', $user->getKey()) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="student-action student-action-restore" onclick="return confirm('Restore this student account?')">Unsuspend</button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>



                <!-- NO SEARCH RESULT -->

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
                        No students found
                    </p>

                    <p class="mt-1 text-[11px] text-slate-400">
                        Try another name, email or status.
                    </p>

                </div>


            @else

                <!-- EMPTY STATE -->

                <div class="p-12 text-center">

                    <div
                        class="mx-auto mb-3 flex h-12 w-12
                               items-center justify-center
                               rounded-full bg-slate-100
                               text-slate-400"
                    >

                        <i class="fa-solid fa-users text-lg"></i>

                    </div>

                    <h3 class="text-sm font-bold text-slate-700">
                        No Students
                    </h3>

                    <p class="mt-1 text-xs text-slate-400">
                        No student accounts are registered.
                    </p>

                </div>

            @endif

            <div class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 px-5 py-4">
                <p id="studentPageSummary" role="status" aria-live="polite" class="text-sm text-slate-500"></p>
                <nav aria-label="Student result pages" class="flex items-center gap-2"><button type="button" id="studentPrevious" class="rounded-lg border border-slate-200 px-3 py-2 text-sm disabled:opacity-40">Previous</button><span id="studentPageNumber" class="text-sm text-slate-500"></span><button type="button" id="studentNext" class="rounded-lg border border-slate-200 px-3 py-2 text-sm disabled:opacity-40">Next</button></nav>
            </div>
        </section>

    </div>

</main>

</div>

@include('mpp.student-details')
<script>
(() => {
    const openStudent = (id) => {
        const dialog = document.getElementById(`student-details-${id}`);
        if (dialog) dialog.showModal();
    };
    document.querySelectorAll('[data-student-details]').forEach((link) => {
        link.addEventListener('click', (event) => {
            if (event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;
            event.preventDefault();
            openStudent(link.dataset.studentDetails);
        });
    });
    const selected = new URLSearchParams(window.location.search).get('student');
    if (selected && /^\d+$/.test(selected)) openStudent(selected);
})();
</script>

<!-- ================= FILTER SCRIPT ================= -->

<script>
(() => {
    const search = document.getElementById('studentSearch');
    const status = document.getElementById('statusFilter');
    const rows = [...document.querySelectorAll('.student-row')];
    const previous = document.getElementById('studentPrevious');
    const next = document.getElementById('studentNext');
    let page = 1;
    const pageSize = 10;
    function render() {
        const words = search.value.toLowerCase().trim().split(/\s+/).filter(Boolean);
        const matching = rows.filter(row => words.every(word => (row.dataset.name + ' ' + row.dataset.email).includes(word)) && (status.value === 'all' || status.value === row.dataset.status));
        const pages = Math.max(1, Math.ceil(matching.length / pageSize));
        page = Math.min(page, pages);
        rows.forEach(row => row.classList.add('hidden'));
        matching.slice((page - 1) * pageSize, page * pageSize).forEach(row => row.classList.remove('hidden'));
        document.getElementById('noResults')?.classList.toggle('hidden', matching.length !== 0);
        document.getElementById('studentPageSummary').textContent = matching.length ? `Showing ${(page - 1) * pageSize + 1}-${Math.min(page * pageSize, matching.length)} of ${matching.length} students` : '0 matching students';
        document.getElementById('studentPageNumber').textContent = `Page ${page} of ${pages}`;
        previous.disabled = page === 1; next.disabled = page === pages;
        previous.parentElement.hidden = matching.length <= pageSize;
    }
    search.addEventListener('input', () => { page = 1; render(); });
    status.addEventListener('change', () => { page = 1; render(); });
    previous.addEventListener('click', () => { page--; render(); });
    next.addEventListener('click', () => { page++; render(); });
    render();
})();
</script>

</body>

</html>
