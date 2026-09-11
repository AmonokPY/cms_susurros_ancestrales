export function applyStoredTheme() {
    const stored = localStorage.getItem('colombia-theme');
    const theme = stored || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
    document.documentElement.setAttribute('data-theme', theme);
    return theme;
}

export function initThemeToggle() {
    applyStoredTheme();

    document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
        button.addEventListener('click', () => {
            const next = document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', next);
            localStorage.setItem('colombia-theme', next);
            button.setAttribute('aria-pressed', next === 'dark' ? 'true' : 'false');
        });
    });
}
