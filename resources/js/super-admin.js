import { initSidebar } from './sidebar.js';
import { initSidebarAjaxNav } from './sidebar-ajax-nav.js';
import './pwa-register.js';
import './theme.js';

const SUPERADMIN_SIDEBAR_KEY = 'prs_superadmin_sidebar_collapsed';

/** Routes where sidebar should auto-minimize (Overview, Admin Management, Approvers, All Requests, System Settings) */
function isSuperadminContentRoute() {
    const path = window.location.pathname.replace(/\/$/, '') || '/';
    return path === '/superadmin' ||
        path === '/superadmin/admins' ||
        path === '/superadmin/approvers' ||
        path === '/superadmin/requests' ||
        path === '/superadmin/settings';
}

/** Collapse sidebar (any screen size) and persist */
function collapseSidebarNow() {
    const sidebar = document.getElementById('sidebar');
    const toggle = document.getElementById('sidebarToggle');
    const icon = toggle ? toggle.querySelector('.material-icons') : null;
    if (!sidebar || sidebar.classList.contains('collapsed')) return;
    sidebar.classList.add('collapsed');
    document.body.classList.add('sidebar-collapsed');
    if (icon) icon.textContent = 'chevron_right';
    const submenuItem = document.querySelector('.panel-superadmin .menu-item.has-submenu.submenu-open');
    if (submenuItem) {
        submenuItem.classList.remove('submenu-open');
        const subLink = submenuItem.querySelector(':scope > a[href]');
        const sub = document.getElementById('sidebar-submenu-all-requests');
        if (subLink) subLink.setAttribute('aria-expanded', 'false');
        if (sub) sub.setAttribute('aria-hidden', 'true');
    }
    try { localStorage.setItem(SUPERADMIN_SIDEBAR_KEY, '1'); } catch (e) {}
}

document.addEventListener('DOMContentLoaded', () => {
    initSidebar(SUPERADMIN_SIDEBAR_KEY);
    initSidebarAjaxNav();

    /* All Requests: collapsed = show flyout next to icon; expanded = toggle submenu. Flyout in body so not clipped by sidebar overflow. */
    const sidebarEl = document.getElementById('sidebar');
    const FLYOUT_ID = 'all-requests-flyout';

    function getAllRequestsRow() {
        const link = document.querySelector('#sidebar .menu-top .menu-item.has-submenu > a[href*="requests"]');
        return link ? link.closest('.menu-item') : null;
    }

    function getAllRequestsSubmenu() {
        return document.getElementById('sidebar-submenu-all-requests');
    }

    /* Capture clicks inside sidebar first so we run before any other handler */
    if (sidebarEl) {
        sidebarEl.addEventListener('click', (e) => {
            const allRequestsItem = getAllRequestsRow();
            const allRequestsLink = allRequestsItem && allRequestsItem.querySelector(':scope > a[href]');
            const collapsed = sidebarEl.classList.contains('collapsed');

            if (!collapsed || !allRequestsItem || !allRequestsItem.contains(e.target)) return;

            /* Collapsed + click on All Requests row (icon or link): open or close flyout */
            e.preventDefault();
            e.stopPropagation();
            const flyout = document.getElementById(FLYOUT_ID);
            if (flyout) {
                flyout.remove();
                if (allRequestsLink) allRequestsLink.setAttribute('aria-expanded', 'false');
                return;
            }
            const submenu = getAllRequestsSubmenu();
            const rect = allRequestsLink.getBoundingClientRect();
            const flyoutEl = document.createElement('div');
            flyoutEl.id = FLYOUT_ID;
            flyoutEl.className = 'all-requests-flyout';
            flyoutEl.style.top = rect.top + 'px';
            if (submenu) {
                submenu.querySelectorAll('a[href]').forEach((a) => {
                    const link = document.createElement('a');
                    link.href = a.href;
                    link.textContent = a.textContent.trim();
                    if (a.classList.contains('active')) link.classList.add('active');
                    flyoutEl.appendChild(link);
                });
            }
            document.body.appendChild(flyoutEl);
            if (allRequestsLink) allRequestsLink.setAttribute('aria-expanded', 'true');
        }, true);
    }

    /* Close flyout when clicking outside it and outside All Requests row */
    document.addEventListener('click', (e) => {
        const flyout = document.getElementById(FLYOUT_ID);
        if (!flyout) return;
        const allRequestsItem = getAllRequestsRow();
        if (allRequestsItem && allRequestsItem.contains(e.target)) return;
        if (flyout.contains(e.target)) return;
        flyout.remove();
        const link = document.querySelector('#sidebar .menu-top .menu-item.has-submenu > a[href*="requests"]');
        if (link) link.setAttribute('aria-expanded', 'false');
    }, true);

    /* Expanded sidebar: toggle submenu on All Requests link click */
    document.addEventListener('click', (e) => {
        if (sidebarEl && sidebarEl.classList.contains('collapsed')) return;
        if (document.getElementById(FLYOUT_ID)) return;
        const allRequestsItem = getAllRequestsRow();
        const allRequestsLink = allRequestsItem && allRequestsItem.querySelector(':scope > a[href]');
        if (!allRequestsLink || !allRequestsLink.contains(e.target)) return;
        e.preventDefault();
        e.stopPropagation();
        const allRequestsSubmenu = getAllRequestsSubmenu();
        const isOpen = allRequestsItem.classList.toggle('submenu-open');
        allRequestsLink.setAttribute('aria-expanded', isOpen);
        if (allRequestsSubmenu) allRequestsSubmenu.setAttribute('aria-hidden', !isOpen);
    }, true);

    /* Close flyout when a flyout link is clicked (navigate) */
    document.addEventListener('click', (e) => {
        const flyout = document.getElementById(FLYOUT_ID);
        if (flyout && flyout.contains(e.target) && e.target.tagName === 'A') {
            const link = document.querySelector('#sidebar .menu-top .menu-item.has-submenu > a[href*="requests"]');
            if (link) link.setAttribute('aria-expanded', 'false');
            flyout.remove();
        }
    }, true);

    const backdrop = document.getElementById('sidebarBackdrop');
    const sidebar = document.getElementById('sidebar');
    const toggle = document.getElementById('sidebarToggle');
    const icon = toggle ? toggle.querySelector('.material-icons') : null;

    /* On load: if we're on a content page, minimize sidebar like the second pic */
    if (isSuperadminContentRoute()) {
        collapseSidebarNow();
    }

    /* Sidebar collapse happens after AJAX nav (sidebar-ajax-nav-applied) so one click loads content; no double-click. */

    function collapseSidebar() {
        if (!sidebar || sidebar.classList.contains('collapsed')) return;
        if (window.innerWidth > 992) return;
        sidebar.classList.add('collapsed');
        document.body.classList.add('sidebar-collapsed');
        if (icon) icon.textContent = 'chevron_right';
        try { localStorage.setItem(SUPERADMIN_SIDEBAR_KEY, '1'); } catch (e) {}
    }

    if (backdrop) {
        backdrop.addEventListener('click', collapseSidebar);
    }

    /* After AJAX nav: collapse on content route so sidebar state matches the page */
    window.addEventListener('sidebar-ajax-nav-applied', () => {
        if (isSuperadminContentRoute()) collapseSidebarNow();
    });
});
