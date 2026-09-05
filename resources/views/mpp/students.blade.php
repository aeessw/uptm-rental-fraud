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
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
>

</head>

<body class="bg-slate-50/80 font-sans antialiased text-slate-800">

<div class="min-h-screen">

<!-- ================= SIDEBAR ================= -->

@include('mpp.sidebar')


<!-- ================= MAIN CONTENT ================= -->

<main class="ml-0 min-w-0 md:ml-64">


    <!-- ================= HEADER ================= -->

    <header
        class="sticky top-0 z-40 flex items-center justify-between
               border-b border-slate-200/60
               bg-white/90 px-8 py-4
               backdrop-blur-md"
    >

        <div>

            <h2 class="text-base font-bold tracking-tight text-slate-900">
                Student Accounts
            </h2>


        </div>


        <!-- MPP PROFILE -->

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



    <!-- ================= PAGE CONTENT ================= -->

    <div class="space-y-6 p-8">




        <!-- SUCCESS MESSAGE -->

        @if(session('success'))

            <div
                class="flex items-center space-x-2.5
                       rounded-xl border border-emerald-500/20
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
                       sm:flex-row sm:items-center sm:justify-between"
            >

                <!-- SEARCH -->

                <div class="relative w-full sm:max-w-sm">

                    <i
                        class="fa-solid fa-magnifying-glass
                               absolute left-3 top-1/2
                               -translate-y-1/2
                               text-xs text-slate-400"
                    ></i>

                    <input
                        type="text"
                        id="studentSearch"
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
                        All Students
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
                                    Role
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


                        <!-- TABLE BODY -->

                        <tbody class="divide-y divide-slate-100">

                            @foreach($users as $user)

                                <tr
                                    class="student-row transition hover:bg-slate-50"
                                    data-name="{{ strtolower($user->name) }}"
                                    data-email="{{ strtolower($user->email) }}"
                                    data-status="{{ $user->suspended ? 'suspended' : 'active' }}"
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

                                                {{ strtoupper(substr($user->name, 0, 2)) }}

                                            </div>


                                            <div class="min-w-0">

                                                <p
                                                    class="truncate
                                                           text-xs font-bold
                                                           text-slate-800"
                                                >

                                                    {{ $user->name }}

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

                                            {{ $user->email }}

                                        </p>

                                    </td>



                                    <!-- ROLE -->

                                    <td class="px-6 py-4">

                                        <span
                                            class="inline-flex
                                                   rounded-full
                                                   bg-slate-100
                                                   px-2.5 py-1
                                                   text-[10px]
                                                   font-bold
                                                   uppercase
                                                   text-slate-600"
                                        >

                                            {{ $user->role }}

                                        </span>

                                    </td>



                                    <!-- STATUS -->

                                    <td class="px-6 py-4">

                                        @if($user->suspended)

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



                                    <!-- ACTION -->

                                    <td class="px-6 py-4 text-right">

                                        @if(!$user->suspended)

                                            <form
                                                action="{{ route('mpp.users.suspend', $user->id) }}"
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
                                                    onclick="return confirm('Are you sure you want to suspend this student account?')"
                                                >

                                                    <i
                                                        class="fa-solid
                                                               fa-user-slash
                                                               mr-1"
                                                    ></i>

                                                    Suspend

                                                </button>

                                            </form>

                                        @else

                                            <form
                                                action="{{ route('mpp.users.unsuspend', $user->id) }}"
                                                method="POST"
                                                class="inline"
                                            >

                                                @csrf

                                                <button
                                                    type="submit"
                                                    class="rounded-lg
                                                           bg-emerald-50
                                                           px-3 py-2
                                                           text-[10px]
                                                           font-bold
                                                           text-emerald-600
                                                           transition
                                                           hover:bg-emerald-100"
                                                    onclick="return confirm('Restore this student account?')"
                                                >

                                                    <i
                                                        class="fa-solid
                                                               fa-user-check
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

        </section>

    </div>

</main>
```

</div>

<!-- ================= FILTER SCRIPT ================= -->

<script>

    const searchInput = document.getElementById('studentSearch');
    const statusFilter = document.getElementById('statusFilter');
    const rows = document.querySelectorAll('.student-row');
    const noResults = document.getElementById('noResults');

    function filterStudents() {

        const searchValue = searchInput.value.toLowerCase().trim();
        const statusValue = statusFilter.value;

        let visibleRows = 0;

        rows.forEach(row => {

            const name = row.dataset.name;
            const email = row.dataset.email;
            const status = row.dataset.status;

            const matchesSearch =
                name.includes(searchValue) ||
                email.includes(searchValue);

            const matchesStatus =
                statusValue === 'all' ||
                status === statusValue;

            if (matchesSearch && matchesStatus) {

                row.classList.remove('hidden');

                visibleRows++;

            } else {

                row.classList.add('hidden');

            }

        });


        if (visibleRows === 0 && rows.length > 0) {

            noResults.classList.remove('hidden');

        } else {

            noResults.classList.add('hidden');

        }

    }


    searchInput.addEventListener('input', filterStudents);

    statusFilter.addEventListener('change', filterStudents);

</script>

</body>

</html>
