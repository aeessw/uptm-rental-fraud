@if(session('success'))
<dialog id="submission-success-dialog" aria-labelledby="submission-success-title" aria-describedby="submission-success-message" class="w-[calc(100%-2rem)] max-w-sm rounded-2xl border-0 bg-white p-6 text-center text-slate-800 shadow-xl backdrop:bg-slate-900/50">
    <span class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-emerald-50 text-emerald-600"><i class="fa-solid fa-check text-xl" aria-hidden="true"></i></span>
    <h2 id="submission-success-title" class="text-base font-semibold">Success</h2>
    <p id="submission-success-message" class="mt-2 text-sm text-slate-600">{{ session('success') }}</p>
    <form method="dialog" class="mt-5"><button autofocus class="rounded-xl bg-indigo-600 px-6 py-2 text-sm font-semibold text-white hover:bg-indigo-700">OK</button></form>
</dialog>
<script>
(() => {
    const showSuccess = () => {
        const dialog = document.getElementById('submission-success-dialog');
        document.body.appendChild(dialog);
        dialog.showModal();
    };
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', showSuccess, {once: true});
    else showSuccess();
})();
</script>
@endif
