import { initSidebar } from './sidebar.js';
import { initSidebarAjaxNav } from './sidebar-ajax-nav.js';

document.addEventListener('DOMContentLoaded', () => {
    initSidebar('prs_approver_sidebar_collapsed');
    initSidebarAjaxNav();
});
