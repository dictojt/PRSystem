/**
 * Intercepts sidebar (.menu-top) link clicks, fetches the page with X-Partial-Content,
 * replaces only .main content, and updates URL/title/active state (no full reload).
 */
export function initSidebarAjaxNav() {
  const main = document.querySelector('.main');
  const menuTop = document.querySelector('.menu-top');
  if (!main || !menuTop) return;

  const links = menuTop.querySelectorAll('a[href]');
  const PARTIAL_HEADER = 'X-Partial-Content';
  const PARTIAL_VALUE = '1';

  function isSameOrigin(href) {
    try {
      const u = new URL(href, window.location.origin);
      return u.origin === window.location.origin && u.pathname;
    } catch (_) {
      return false;
    }
  }

  function shouldIntercept(link) {
    if (link.hasAttribute('data-full-reload')) return false;
    if (link.classList.contains('logout-link-get') || link.classList.contains('logout-link')) return false;
    if (link.target === '_blank') return false;
    const origin = isSameOrigin(link.href);
    return origin && link.href !== window.location.href;
  }

  function setActiveLink() {
    const current = window.location.pathname + window.location.search;
    links.forEach((a) => {
      try {
        const aPath = new URL(a.href, window.location.origin).pathname + new URL(a.href, window.location.origin).search;
        if (aPath === current) {
          a.classList.add('active');
        } else {
          a.classList.remove('active');
        }
      } catch (_) {
        a.classList.remove('active');
      }
    });
  }

  function runScripts(container) {
    if (!container) return;
    const scripts = Array.from(container.querySelectorAll('script'));
    scripts.forEach((oldScript) => {
      const script = document.createElement('script');
      if (oldScript.src) {
        script.src = oldScript.src;
      } else {
        script.textContent = oldScript.textContent;
      }
      oldScript.getAttributeNames().forEach((name) => {
        if (name !== 'src' && name !== 'textContent') script.setAttribute(name, oldScript.getAttribute(name));
      });
      document.body.appendChild(script);
      oldScript.remove();
    });
  }

  function applyPartialHtml(html) {
    const wrap = document.createElement('div');
    wrap.innerHTML = html;
    const root = wrap.firstElementChild;
    const titleEl = root && (root.getAttribute('data-page-title') ? root : root.querySelector('[data-page-title]'));
    if (titleEl && titleEl.getAttribute('data-page-title')) {
      document.title = titleEl.getAttribute('data-page-title');
    }
    main.innerHTML = root ? root.innerHTML : html;
    runScripts(main);
    setActiveLink();
  }

  function loadPartial(href) {
    return fetch(href, {
      method: 'GET',
      headers: {
        [PARTIAL_HEADER]: PARTIAL_VALUE,
        Accept: 'text/html',
        'X-Requested-With': 'XMLHttpRequest',
      },
    }).then((response) => {
      const ct = response.headers.get('Content-Type') || '';
      if (!response.ok || !ct.includes('text/html')) {
        window.location.href = href;
        return false;
      }
      return response.text().then((text) => {
        applyPartialHtml(text);
        return true;
      });
    }).catch(() => {
      window.location.href = href;
      return false;
    });
  }

  links.forEach((link) => {
    link.addEventListener('click', (e) => {
      if (!shouldIntercept(link)) return;
      e.preventDefault();
      const href = link.href;
      if (!href) return;
      loadPartial(href).then((applied) => {
        if (applied) {
          history.pushState({ sidebarPartial: true }, '', href);
          setActiveLink();
        }
      });
    });
  });

  window.addEventListener('popstate', () => {
    loadPartial(window.location.href);
  });

  setActiveLink();
}
