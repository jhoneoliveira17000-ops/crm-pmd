// js/theme-loader.js
(function () {
    const cachedColor = localStorage.getItem('theme_color') || '#00BF24';
    // Set CSS variable immediately on the root element
    document.documentElement.style.setProperty('--theme-color', cachedColor);
})();
