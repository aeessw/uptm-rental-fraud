(() => {
    const status = document.getElementById('pagination-status');
    let controller;
    let revision = 0;
    let activeUrl = location.href;

    async function navigate(url, push = true) {
        controller?.abort();
        controller = new AbortController();
        const token = ++revision;
        const section = document.getElementById('profile-listings');
        section.setAttribute('aria-busy', 'true');
        status.hidden = true;
        try {
            const response = await fetch(url, {
                headers: {Accept: 'text/html'},
                cache: 'no-store',
                signal: controller.signal,
            });
            if (!response.ok || response.redirected) throw new Error('Unable to load this page. Please try again or refresh to sign in.');
            const doc = new DOMParser().parseFromString(await response.text(), 'text/html');
            if (token !== revision) return;
            const next = doc.getElementById('profile-listings');
            if (!next) throw new Error('Unable to load listings. Please try again.');
            section.replaceWith(next);
            if (push) history.pushState(null, '', url);
            activeUrl = url;
            const heading = next.querySelector('#my-listings-heading');
            heading.focus({preventScroll: true});
            const reduceMotion = matchMedia('(prefers-reduced-motion: reduce)').matches;
            next.scrollIntoView({behavior: reduceMotion ? 'instant' : 'smooth', block: 'start'});
            if (!reduceMotion) next.animate([{opacity: 0.65}, {opacity: 1}], {duration: 180});
        } catch (error) {
            if (token !== revision || error.name === 'AbortError') return;
            status.textContent = error.message || 'Connection interrupted. Please try again.';
            status.hidden = false;
            if (!push) history.replaceState(null, '', activeUrl);
        } finally {
            if (token === revision) document.getElementById('profile-listings').removeAttribute('aria-busy');
        }
    }

    document.addEventListener('click', event => {
        const link = event.target.closest('[data-profile-pagination] a');
        if (!link || event.button !== 0 || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;
        event.preventDefault();
        navigate(link.href);
    });

    window.addEventListener('popstate', () => navigate(location.href, false));
})();
