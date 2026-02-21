// js/theme-loader.js
(function () {
    // 1. Load Theme Color
    const cachedColor = localStorage.getItem('theme_color') || '#00BF24';
    document.documentElement.style.setProperty('--theme-color', cachedColor);

    // 2. Load Dark/Light Mode instantly to prevent white flash
    if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }
})();
