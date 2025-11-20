/* project/js/theme.js */

(function(){
  const THEME_KEY = 'phishplay_theme';
  const root = document.documentElement;
  const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)');

  function applyTheme(theme){
    if (!theme) {
      const isDark = prefersDark.matches;
      root.setAttribute('data-theme', isDark ? 'dark' : 'light');
    } else {
      root.setAttribute('data-theme', theme);
    }
    updateToggleUI(); // update icon if toggle exists
  }

  function updateToggleUI(){
    const toggle = document.getElementById('themeToggle');
    if (!toggle) return;
    const current = root.getAttribute('data-theme');
    if (current === 'light') {
      toggle.textContent = '🌒';
      toggle.setAttribute('aria-pressed','false');
      toggle.title = 'Switch to dark mode';
    } else {
      toggle.textContent = '☀️';
      toggle.setAttribute('aria-pressed','true');
      toggle.title = 'Switch to light mode';
    }
  }

  function loadTheme(){
    const saved = localStorage.getItem(THEME_KEY);
    if (saved === 'light' || saved === 'dark') applyTheme(saved);
    else applyTheme(null);
  }

  function toggleTheme(){
    const current = root.getAttribute('data-theme');
    const next = (current === 'light') ? 'dark' : 'light';
    root.setAttribute('data-theme', next);
    localStorage.setItem(THEME_KEY, next);
    updateToggleUI();
  }

  // init
  document.addEventListener('DOMContentLoaded', ()=> {
    loadTheme();
    const toggle = document.getElementById('themeToggle');
    if (toggle) toggle.addEventListener('click', toggleTheme);
    // listen to OS changes only if user didn't save a theme
    if (prefersDark && prefersDark.addEventListener) {
      prefersDark.addEventListener('change', () => {
        if (!localStorage.getItem(THEME_KEY)) applyTheme(null);
      });
    }
  });
})();
