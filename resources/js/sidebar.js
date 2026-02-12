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

    try {
        if (localStorage.getItem(storageKey) === '1') setCollapsed(true);
    } catch (e) {}
}
