(() => {
    const form = document.getElementById('listing-filters');
    const search = form.elements.search;
    const clear = document.getElementById('clear-listing-filters');
    const error = document.getElementById('listing-filter-error');
    const status = document.getElementById('listing-filter-status');
    let timer, controller;
    let revision = 0;
    let activeUrl = location.href;

    function cancel() {
        clearTimeout(timer);
        controller?.abort();
        revision++;
        document.getElementById('listing-results').removeAttribute('aria-busy');
    }

    function filterUrl() {
        const url = new URL(form.action);
        for (const [key, value] of new FormData(form)) {
            if (value && !(key === 'sort' && value === 'newest')) url.searchParams.set(key, value);
        }
        return url.href;
    }

    function syncFields(url) {
        const params = new URL(url).searchParams;
        for (const field of form.querySelectorAll('input[name], select[name]')) {
            field.value = params.get(field.name) || (field.name === 'sort' ? 'newest' : '');
        }
    }

    async function update(url = filterUrl(), push = true) {
        cancel();
        const token = revision;
        controller = new AbortController();
        const results = document.getElementById('listing-results');
        results.setAttribute('aria-busy', 'true');
        error.hidden = true;
        try {
            const response = await fetch(url, {
                headers: {Accept: 'text/html'}, cache: 'no-store', signal: controller.signal,
            });
            if (!response.ok || response.redirected) throw new Error('Unable to update results. Please try again or refresh to sign in.');
            const doc = new DOMParser().parseFromString(await response.text(), 'text/html');
            if (token !== revision) return;
            const next = doc.getElementById('listing-results');
            if (!next) throw new Error('Unable to update results. Please try again.');
            results.replaceWith(next);
            clear.hidden = !doc.getElementById('clear-listing-filters') || doc.getElementById('clear-listing-filters').hidden;
            if (push && location.href !== url) history.pushState(null, '', url);
            activeUrl = url;
            status.textContent = next.dataset.total + ' listings found.';
            if (!matchMedia('(prefers-reduced-motion: reduce)').matches) {
                next.animate([{opacity: 0.7}, {opacity: 1}], {duration: 140, easing: 'ease-out'});
            }
        } catch (failure) {
            if (token !== revision || failure.name === 'AbortError') return;
            error.textContent = failure.message || 'Connection interrupted. Please try again.';
            error.hidden = false;
            if (!push) { history.replaceState(null, '', activeUrl); syncFields(activeUrl); }
        } finally {
            if (token === revision) document.getElementById('listing-results').removeAttribute('aria-busy');
        }
    }

    form.addEventListener('submit', event => { event.preventDefault(); update(); });
    form.querySelectorAll('select').forEach(select => select.addEventListener('change', () => update()));
    search.addEventListener('input', event => {
        cancel();
        if (!event.isComposing) timer = setTimeout(() => update(), 300);
    });
    search.addEventListener('compositionstart', cancel);
    search.addEventListener('compositionend', () => { cancel(); timer = setTimeout(() => update(), 300); });
    document.addEventListener('click', event => {
        const link = event.target.closest('a[data-clear-filters], [data-listing-pagination] a');
        if (!link || event.defaultPrevented || event.button !== 0 || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;
        event.preventDefault();
        if (link.hasAttribute('data-clear-filters')) syncFields(form.action);
        update(link.href);
    });
    window.addEventListener('popstate', () => { syncFields(location.href); update(location.href, false); });
    window.addEventListener('pagehide', cancel);
})();
