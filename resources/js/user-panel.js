import { initSidebar } from './sidebar.js';
import { initSidebarAjaxNav } from './sidebar-ajax-nav.js';
import './pwa-register.js';

document.addEventListener('DOMContentLoaded', () => {
    initSidebar('prs_user_sidebar_collapsed');
    initSidebarAjaxNav();
});
