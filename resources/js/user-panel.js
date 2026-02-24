import { initSidebar } from './sidebar.js';
import { initSidebarAjaxNav } from './sidebar-ajax-nav.js';
import './pwa-register.js';

const USER_SIDEBAR_KEY = 'prs_user_sidebar_collapsed';

/** Force sidebar to expanded state so labels show (same design as dashboard) */
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
    /* User panel: always start with sidebar expanded so labels show (like dashboard) */
    expandUserSidebar();
    initSidebarAjaxNav();

    /* Click outside (backdrop): close sidebar when expanded on mobile */
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

    /* After AJAX nav to another page, expand sidebar so labels show (same design as dashboard) */
    window.addEventListener('sidebar-ajax-nav-applied', expandUserSidebar);
});
