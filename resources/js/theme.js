/**
 * Client-side theme (light/dark). Stored in localStorage only; no server save.
 */
(function () {
  var STORAGE_KEY = 'prs-theme';

  function get() {
    return localStorage.getItem(STORAGE_KEY) || 'light';
  }

  function set(theme) {
    theme = theme === 'dark' ? 'dark' : 'light';
    localStorage.setItem(STORAGE_KEY, theme);
    document.body.classList.remove('theme-light', 'theme-dark');
    document.body.classList.add('theme-' + theme);
  }

  function init() {
    document.body.classList.remove('theme-light', 'theme-dark');
    document.body.classList.add('theme-' + get());
  }

  function bindToggles() {
    document.querySelectorAll('select[data-theme-toggle]').forEach(function (el) {
      el.value = get();
      el.addEventListener('change', function () {
        set(this.value);
      });
    });
  }

  window.prsTheme = { get: get, set: set, init: init, bindToggles: bindToggles };

  if (document.body) {
    init();
  } else {
    document.addEventListener('DOMContentLoaded', init);
  }
  document.addEventListener('DOMContentLoaded', bindToggles);

  /* Re-apply theme when page is shown (e.g. restored from bfcache) so UI matches dropdown */
  window.addEventListener('pageshow', function (e) {
    init();
    if (e.persisted && document.querySelectorAll('select[data-theme-toggle]').length) {
      bindToggles();
    }
  });

  /* After AJAX partial nav (e.g. to System Settings), bind theme toggles in new content and re-sync body */
  window.addEventListener('sidebar-ajax-nav-applied', function () {
    init();
    bindToggles();
  });
})();
