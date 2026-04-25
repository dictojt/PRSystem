import { initSidebar } from './sidebar.js';
import { initSidebarAjaxNav } from './sidebar-ajax-nav.js';
import './pwa-register.js';
import './theme.js';

const APPROVER_SIDEBAR_KEY = 'prs_approver_sidebar_collapsed';

/** Routes where sidebar should auto-collapse (same idea as superadmin) */
function isApproverContentRoute() {
    const path = window.location.pathname.replace(/\/$/, '') || '/';
    return path === '/approver' || path === '/approver/settings';
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
    try { localStorage.setItem(APPROVER_SIDEBAR_KEY, '1'); } catch (e) {}
}

/** Force sidebar to expanded state when not on a content route */
export function expandApproverSidebar() {
    const sidebar = document.getElementById('sidebar');
    const toggle = document.getElementById('sidebarToggle');
    const icon = toggle ? toggle.querySelector('.material-icons') : null;
    if (!sidebar || !document.body.classList.contains('panel-approver')) return;
    sidebar.classList.remove('collapsed');
    document.body.classList.remove('sidebar-collapsed');
    if (icon) icon.textContent = 'chevron_left';
    try { localStorage.removeItem(APPROVER_SIDEBAR_KEY); } catch (e) {}
}

document.addEventListener('DOMContentLoaded', () => {
    initSidebar(APPROVER_SIDEBAR_KEY);
    if (isApproverContentRoute()) {
        collapseSidebarNow();
    } else {
        expandApproverSidebar();
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
        try { localStorage.setItem(APPROVER_SIDEBAR_KEY, '1'); } catch (e) {}
    }

    if (backdrop) {
        backdrop.addEventListener('click', collapseSidebar);
    }

    /* After AJAX nav: collapse on content route, else expand (same as superadmin) */
    window.addEventListener('sidebar-ajax-nav-applied', () => {
        if (isApproverContentRoute()) collapseSidebarNow();
        else expandApproverSidebar();
    });
});
