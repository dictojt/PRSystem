import { initSidebar } from './sidebar.js';
import { initSidebarAjaxNav } from './sidebar-ajax-nav.js';
import './pwa-register.js';

function closeMobileSidebar() {
    document.body.classList.remove('sidebar-open');
}

document.addEventListener('DOMContentLoaded', () => {
    initSidebar('prs_approver_sidebar_collapsed');
    initSidebarAjaxNav();

    const mobileOpen = document.getElementById('sidebarMobileOpen');
    const backdrop = document.getElementById('sidebarBackdrop');
    const sidebarToggle = document.getElementById('sidebarToggle');

    if (mobileOpen) {
        mobileOpen.addEventListener('click', () => {
            document.body.classList.add('sidebar-open');
        });
    }
    if (backdrop) {
        backdrop.addEventListener('click', closeMobileSidebar);
    }
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', () => {
            if (window.innerWidth <= 768) closeMobileSidebar();
        });
    }
});
