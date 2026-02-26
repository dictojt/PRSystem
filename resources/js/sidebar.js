/**
 * Reusable sidebar collapse toggle.
 * @param {string} storageKey - localStorage key to persist collapsed state
 */
export function initSidebar(storageKey) {
    const sidebar = document.getElementById('sidebar');
    const toggle = document.getElementById('sidebarToggle');
    const icon = toggle ? toggle.querySelector('.material-icons') : null;

    function setCollapsed(collapsed) {
        if (collapsed) {
            sidebar.classList.add('collapsed');
            document.body.classList.add('sidebar-collapsed');
            if (icon) icon.textContent = 'chevron_right';
            try { localStorage.setItem(storageKey, '1'); } catch (e) {}
        } else {
            sidebar.classList.remove('collapsed');
            document.body.classList.remove('sidebar-collapsed');
            if (icon) icon.textContent = 'chevron_left';
            try { localStorage.removeItem(storageKey); } catch (e) {}
        }
    }

    if (toggle) {
        toggle.addEventListener('click', () => setCollapsed(!sidebar.classList.contains('collapsed')));
    }

    /* If sidebar is already collapsed (e.g. server-set for superadmin content pages), keep it and persist */
    if (sidebar && sidebar.classList.contains('collapsed')) {
        try { localStorage.setItem(storageKey, '1'); } catch (e) {}
        return;
    }
    /* Otherwise start expanded; clear any saved collapsed state */
    try {
        localStorage.removeItem(storageKey);
    } catch (e) {}
    if (sidebar) {
        setCollapsed(false);
    }
}
