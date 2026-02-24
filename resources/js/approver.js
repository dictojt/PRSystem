import { initSidebar } from './sidebar.js';
import { initSidebarAjaxNav } from './sidebar-ajax-nav.js';
import './pwa-register.js';

const APPROVER_SIDEBAR_KEY = 'prs_approver_sidebar_collapsed';

/** Force sidebar to expanded state so labels show (same design as user panel) */
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
    /* Approver panel: always start with sidebar expanded so labels show */
    expandApproverSidebar();
    initSidebarAjaxNav();

    /* Click outside (backdrop): collapse sidebar when expanded on mobile */
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

    /* After AJAX nav, expand sidebar so labels show */
    window.addEventListener('sidebar-ajax-nav-applied', expandApproverSidebar);
});
