import { initSidebar } from './sidebar.js';
import { initSidebarAjaxNav } from './sidebar-ajax-nav.js';

document.addEventListener('DOMContentLoaded', () => {
    initSidebar('prs_superadmin_sidebar_collapsed');
    initSidebarAjaxNav();
});
