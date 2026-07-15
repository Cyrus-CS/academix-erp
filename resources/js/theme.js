export function initTheme() {
    const root = document.documentElement;

    // ── Le thème est déjà appliqué par le script inline dans <head>
    // ── On lit juste l'état actuel pour synchroniser les icônes
    const isDark = root.classList.contains('dark');

    const btn      = document.getElementById('theme-toggle');
    const iconSun  = document.getElementById('icon-sun');
    const iconMoon = document.getElementById('icon-moon');

    // Sync icônes avec l'état actuel
    _syncIcons(isDark, iconSun, iconMoon);

    if (!btn) return;

    btn.addEventListener('click', () => {
        const nowDark = root.classList.toggle('dark');
        localStorage.setItem('theme', nowDark ? 'dark' : 'light');
        _syncIcons(nowDark, iconSun, iconMoon);

        window.dispatchEvent(new CustomEvent('theme-changed', {
            detail: { isDark: nowDark }
        }));
    });

    // Écouter les changements système
    window.matchMedia('(prefers-color-scheme: dark)')
        .addEventListener('change', (e) => {
            if (!localStorage.getItem('theme')) {
                root.classList.toggle('dark', e.matches);
                _syncIcons(e.matches, iconSun, iconMoon);
            }
        });
}

function _syncIcons(isDark, iconSun, iconMoon) {
    if (!iconSun || !iconMoon) return;
    iconSun.classList.toggle('hidden', isDark);
    iconMoon.classList.toggle('hidden', !isDark);
}