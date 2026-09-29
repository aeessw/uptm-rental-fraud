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
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous"
>

    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
</head>

<body class="bg-slate-50 font-sans antialiased text-slate-800">
<div class="min-h-screen">@include('mpp.sidebar')
<main class="audit-logs-page min-w-0"><div class="mpp-page-content space-y-4 p-4 md:p-6">
    <div class="flex flex-wrap items-center justify-between gap-3">@include('mpp.page-heading', ['title' => 'Audit Logs', 'description' => 'Review recorded actions and signature integrity.'])<label class="flex items-center gap-2 text-sm"><input id="autoRefresh" type="checkbox" checked> Auto-refresh every 30 seconds</label></div>
    @if(request()->filled('user_id'))<p class="rounded-lg bg-indigo-50 p-3 text-sm text-indigo-700">Filtered to the selected student's activity. <a href="{{ route('mpp.audit.logs') }}" class="font-semibold underline">Show all</a></p>@endif
    @if(request()->filled('listing_id'))<p class="rounded-lg bg-indigo-50 p-3 text-sm text-indigo-700">Showing activity for Listing #{{ request('listing_id') }}. <a class="font-semibold underline" href="{{ route('mpp.audit.logs') }}">Show all</a></p>@endif
    <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
        @foreach(['Total logs' => $auditLogs->count(), "Today's activity" => $auditLogs->filter(fn ($log) => $log->audit_created_at->isToday())->count(), 'Security actions' => $auditLogs->filter(fn ($log) => $log->isSecurityEvent())->count()] as $label => $count)
        <div class="rounded-xl border border-slate-200 bg-white px-4 py-3"><p class="text-xs text-slate-500">{{ $label }}</p><p class="text-lg font-semibold">{{ $count }}</p></div>
        @endforeach
    </div>
    <section class="rounded-xl border border-slate-200 bg-white p-4"><p id="resultCount" role="status" aria-live="polite" class="mb-3 text-xs text-slate-500"></p><div class="grid gap-3 md:grid-cols-2 xl:grid-cols-4">
        <input id="auditSearch" aria-label="Search audit logs" placeholder="Search user, action or details" class="rounded-lg border border-slate-200 bg-slate-50 p-3 text-sm">
        <select id="auditAction" aria-label="Filter actions" class="rounded-lg border border-slate-200 bg-slate-50 p-3 text-sm"><option value="">All Actions</option>
        @foreach(['Authentication', 'Listing Activity', 'Reports', 'Messages', 'Account Activity', 'MPP Actions'] as $category)
            <optgroup label="{{ $category }}"><option value="category:{{ $category }}">All {{ $category }}</option>@foreach(collect(\App\Models\AuditLog::actionGroups()[$category] ?? [])->merge($auditLogs->filter(fn ($log) => $log->category() === $category)->pluck('audit_action'))->unique()->sort() as $action)<option value="action:{{ $action }}">{{ ucfirst(str_replace('_', ' ', $action)) }}</option>@endforeach</optgroup>
        @endforeach</select>
        <select id="auditRole" aria-label="Filter role" class="rounded-lg border border-slate-200 bg-slate-50 p-3 text-sm"><option value="">All Roles</option><option value="student">Student</option><option value="mpp">MPP Admin</option></select>
        <select id="auditDate" aria-label="Filter dates" class="rounded-lg border border-slate-200 bg-slate-50 p-3 text-sm"><option value="">All Dates</option>@foreach($auditLogs->map(fn ($log) => $log->audit_created_at->format('Y-m-d'))->unique() as $date)<option value="{{ $date }}">{{ $date }}</option>@endforeach</select>
    </div></section>
    <section class="overflow-x-auto rounded-xl border border-slate-200 bg-white"><table class="w-full min-w-[1000px] text-left text-sm"><thead class="bg-slate-50 text-xs uppercase text-slate-500"><tr>@foreach(['Date & time', 'User', 'Role', 'Action', 'Target / details', 'Integrity', ''] as $heading)<th class="px-4 py-3">{{ $heading }}</th>@endforeach</tr></thead><tbody class="divide-y divide-slate-100">
        @foreach($auditLogs as $log)
        @php($integrity = $log->integrityStatus())
        <tr class="audit-row {{ $integrity === 'Invalid' ? 'bg-rose-50' : '' }}" data-role="{{ $log->user?->user_role }}" data-search="{{ strtolower(($log->user?->user_name ?? 'System').' '.($log->user?->user_email ?? '').' '.($log->user?->user_role ?? '').' '.$log->audit_action.' '.$log->category().' '.$log->displayDetails().' '.$log->audit_created_at->format('d M Y Y-m-d').' '.$integrity) }}" data-action="{{ $log->audit_action }}" data-category="{{ $log->category() }}" data-date="{{ $log->audit_created_at->format('Y-m-d') }}">
            <td class="whitespace-nowrap px-4 py-4">{{ $log->audit_created_at->format('d M Y') }}<p class="text-xs text-slate-500">{{ $log->audit_created_at->format('g:i A') }}</p></td>
            <td class="px-4 py-4"><span>{{ $log->user?->user_name ?? 'System / deleted user' }}</span>@if($log->user?->user_email)<p class="mt-1 break-words text-xs text-slate-500">{{ $log->user->user_email }}</p>@endif</td><td class="px-4 py-4">{{ ($log->user?->user_role === 'mpp' ? 'MPP' : ucfirst($log->user?->user_role ?? 'Unknown')) }}</td>
            <td class="px-4 py-4"><p>{{ ucfirst(str_replace('_', ' ', $log->audit_action)) }}</p><span class="mt-1 inline-block rounded-full px-2 py-1 text-xs {{ match($log->category()) { 'Authentication' => 'bg-indigo-50 text-indigo-700', 'Reports' => 'bg-rose-50 text-rose-700', 'MPP Actions' => 'bg-amber-50 text-amber-700', 'Messages' => 'bg-emerald-50 text-emerald-700', default => 'bg-slate-100 text-slate-600' } }}">{{ $log->category() }}</span></td><td class="max-w-sm break-words px-4 py-4">{{ $log->displayDetails() }}</td>
            <td class="px-4 py-4"><span class="whitespace-nowrap rounded-full px-2 py-1 text-xs font-semibold {{ $integrity === 'Valid' ? 'bg-emerald-50 text-emerald-700' : ($integrity === 'Invalid' ? 'bg-rose-50 text-rose-700' : 'bg-slate-100 text-slate-500') }}">{{ $integrity }}</span>@if($integrity === 'Invalid')<p class="mt-2 text-xs text-rose-700">Integrity verification failed. This record may have been modified.</p>@endif</td>
            <td class="px-4 py-4"><button type="button" class="whitespace-nowrap rounded-lg bg-indigo-50 px-3 py-2 text-xs font-semibold text-indigo-700" onclick="document.getElementById('audit-detail-{{ $log->getKey() }}').showModal()">View Details</button></td>
        </tr>
        @endforeach
    </tbody></table><p id="auditEmpty" hidden class="p-8 text-center text-sm text-slate-500">No matching audit records.</p></section>
    <p class="text-xs text-slate-500">Security actions count reports, moderation events, failed logins, and invalid signatures, counting each record once. Integrity verifies stored signed fields. Listing titles are current names, not signed historical snapshots. User names and roles reflect current accounts. Invalid signatures may indicate changed data or a changed signing key.</p>
</div></main></div>
@foreach($auditLogs as $log)
<dialog id="audit-detail-{{ $log->getKey() }}" aria-labelledby="audit-title-{{ $log->getKey() }}" class="audit-log-dialog m-auto w-full max-w-lg rounded-2xl bg-white p-6 text-slate-800 shadow-xl backdrop:bg-slate-900/50" style="max-height:85dvh;overflow-y:auto"><h2 id="audit-title-{{ $log->getKey() }}" class="mb-4 text-lg font-semibold">Audit log details</h2><dl class="space-y-3 text-sm">
@foreach(['Action' => ucfirst(str_replace('_', ' ', $log->audit_action)), 'Performed by' => $log->user?->user_name ?? 'System / deleted user', 'Role' => ($log->user?->user_role === 'mpp' ? 'MPP' : ucfirst($log->user?->user_role ?? 'Unknown')), 'Target / description' => $log->displayDetails(), 'Date & time' => $log->audit_created_at->format('d M Y, g:i:s A'), 'Integrity check' => $log->integrityStatus()] as $label => $value)<div><dt class="text-xs text-slate-500">{{ $label }}</dt><dd class="break-words">{{ $value }}</dd></div>@endforeach
</dl><div class="mt-4 rounded-lg border border-slate-200 p-3 text-sm"><p class="font-semibold">HMAC-SHA256</p><code class="block break-all text-xs" aria-label="Shortened HMAC signature">{{ $log->audit_hmac ? substr($log->audit_hmac, 0, 6).'...'.substr($log->audit_hmac, -6) : 'Unavailable' }}</code><p>{{ match($log->integrityStatus()) { 'Valid' => 'Signature successfully verified.', 'Invalid' => 'Integrity verification failed. This record may have been modified, or the signing key changed.', default => 'Verification unavailable: signing key or timestamp is missing.' } }}</p></div><form method="dialog" class="mt-5 text-right"><button class="rounded-lg bg-indigo-50 px-4 py-2 text-sm text-indigo-700">Close</button></form></dialog>
@endforeach
<script>
(() => {
 const search = document.getElementById('auditSearch'), action = document.getElementById('auditAction'), date = document.getElementById('auditDate'), role = document.getElementById('auditRole'), auto = document.getElementById('autoRefresh');
 const key = 'mpp-audit-filters:' + window.location.search;
 try { const saved = JSON.parse(sessionStorage.getItem(key) || '{}'); search.value = saved.search || ''; action.value = saved.action || ''; date.value = saved.date || ''; role.value = saved.role || ''; auto.checked = saved.auto !== false; } catch (_) {}
 const normalize = value => value.toLowerCase().replace(/[_\s]+/g, ' ').trim();
 const rows = [...document.querySelectorAll('.audit-row')].map(row => ({row, text: normalize(row.dataset.search)}));
 function filter() {
   let count = 0;
   const words = normalize(search.value).split(' ').filter(Boolean);
   rows.forEach(({row, text}) => {
     const match = words.every(word => text.includes(word)) && (!role.value || row.dataset.role === role.value) && (!date.value || row.dataset.date === date.value) && (!action.value || action.value === 'action:' + row.dataset.action || action.value === 'category:' + row.dataset.category);
     row.hidden = !match; if (match) count++;
   });
   document.getElementById('resultCount').textContent = count + ' matching records'; document.getElementById('auditEmpty').hidden = count !== 0;
   try { sessionStorage.setItem(key, JSON.stringify({search: search.value, action: action.value, date: date.value, role: role.value, auto: auto.checked})); } catch (_) {}
 }
 search.addEventListener('input', () => {
   if (!search.value.trim()) { action.value = ''; date.value = ''; role.value = ''; }
   filter();
 });
 [action, date, role, auto].forEach(el => el.addEventListener('change', filter)); filter();
 setInterval(() => { if (auto.checked && !document.hidden && !document.querySelector('dialog[open]') && !['INPUT','SELECT'].includes(document.activeElement.tagName)) window.location.reload(); }, 30000);
})();
</script></body></html>
