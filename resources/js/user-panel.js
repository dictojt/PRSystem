import { initSidebar } from './sidebar.js';
import { initSidebarAjaxNav } from './sidebar-ajax-nav.js';
import './pwa-register.js';
import './theme.js';

const USER_SIDEBAR_KEY = 'prs_user_sidebar_collapsed';

/** Routes where sidebar should auto-collapse (same idea as superadmin) */
function isUserContentRoute() {
    const path = window.location.pathname.replace(/\/$/, '') || '/';
    return path === '/user' || path === '/user/requests/create' || path === '/user/requests' ||
        path === '/user/reports' || path === '/user/support' || path === '/user/settings';
}

/** Collapse sidebar and persist (like superadmin) */
function collapseSidebarNow() {
    const sidebar = document.getElementById('sidebar');
    const toggle = document.getElementById('sidebarToggle');
    const icon = toggle ? toggle.querySelector('.material-icons') : null;
    if (!sidebar || sidebar.classList.contains('collapsed')) return;
    sidebar.classList.add('collapsed');
    document.body.classList.add('sidebar-collapsed');
    if (icon) icon.textContent = 'chevron_right';
    try { localStorage.setItem(USER_SIDEBAR_KEY, '1'); } catch (e) {}
}

/** Force sidebar to expanded state (e.g. when not on a content route) */
export function expandUserSidebar() {
    const sidebar = document.getElementById('sidebar');
    const toggle = document.getElementById('sidebarToggle');
    const icon = toggle ? toggle.querySelector('.material-icons') : null;
    if (!sidebar || !document.body.classList.contains('panel-user')) return;
    sidebar.classList.remove('collapsed');
    document.body.classList.remove('sidebar-collapsed');
    if (icon) icon.textContent = 'chevron_left';
    try { localStorage.removeItem(USER_SIDEBAR_KEY); } catch (e) {}
}

document.addEventListener('DOMContentLoaded', () => {
    initSidebar(USER_SIDEBAR_KEY);
    if (isUserContentRoute()) {
        collapseSidebarNow();
    } else {
        expandUserSidebar();
    }
    initSidebarAjaxNav();

    /* Sidebar collapse happens after AJAX nav (sidebar-ajax-nav-applied) so one click loads content; no double-click. */

    const backdrop = document.getElementById('sidebarBackdrop');
    const sidebar = document.getElementById('sidebar');
    const toggle = document.getElementById('sidebarToggle');
    const icon = toggle ? toggle.querySelector('.material-icons') : null;

    function collapseSidebar() {
        if (!sidebar || sidebar.classList.contains('collapsed')) return;
        if (window.innerWidth > 992) return;
        sidebar.classList.add('collapsed');
        document.body.classList.add('sidebar-collapsed');
        if (icon) icon.textContent = 'chevron_right';
        try { localStorage.setItem(USER_SIDEBAR_KEY, '1'); } catch (e) {}
    }

    if (backdrop) {
        backdrop.addEventListener('click', collapseSidebar);
    }

    /* After AJAX nav: collapse on content route, else expand (same as superadmin) */
    window.addEventListener('sidebar-ajax-nav-applied', () => {
        if (isUserContentRoute()) collapseSidebarNow();
        else expandUserSidebar();
    });
});
