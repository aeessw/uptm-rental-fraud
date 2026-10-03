<script>
(() => {
    const account = document.getElementById('dashboard-account');
    const trigger = document.getElementById('dashboard-account-toggle');
    const menu = document.getElementById('dashboard-account-panel');
    const closePanels = () => {
        menu.hidden = true;
        trigger.setAttribute('aria-expanded', 'false');
    };
    const positionPanels = () => {
        const rect = trigger.getBoundingClientRect();
        [menu].forEach((panel) => {
            panel.style.left = `${Math.max(8, Math.min(rect.left, window.innerWidth - 296))}px`;
            panel.style.bottom = `${Math.max(8, window.innerHeight - rect.top + 8)}px`;
            panel.style.maxHeight = `${Math.max(120, rect.top - 16)}px`;
            panel.style.overflowY = 'auto';
        });
    };
    window.addEventListener('resize', () => { closePanels(); positionPanels(); });
    document.addEventListener('scroll', (event) => { if (!account.contains(event.target)) closePanels(); }, true);
    // Scrolling within the popup should keep it open.
    [menu].forEach((panel) => panel.addEventListener('scroll', (event) => event.stopPropagation()));
    document.getElementById('student-sidebar-toggle').addEventListener('click', closePanels);
    trigger.addEventListener('click', () => {
        positionPanels();
        const opening = menu.hidden;
        closePanels();
        menu.hidden = !opening;
        trigger.setAttribute('aria-expanded', String(opening));
    });
    document.addEventListener('click', (event) => {
        if (!account.contains(event.target)) closePanels();
    });
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && !menu.hidden) {
            closePanels();
            trigger.focus();
        }
    });
    const choices = account.querySelectorAll('[data-theme-choice]');
    const updateChoices = () => choices.forEach((button) => {
        const selected = button.dataset.themeChoice === window.studentTheme.preference;
        button.setAttribute('aria-pressed', String(selected));
        button.querySelector('.theme-check').hidden = !selected;
    });
    choices.forEach((button) => button.addEventListener('click', () => {
        window.studentTheme.set(button.dataset.themeChoice);
        updateChoices();
    }));
    updateChoices();
})();

</script>
