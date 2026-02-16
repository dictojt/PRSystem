# Bug Report: Approver UI Not Updating (Stat Cards & All Request Table)

## What you’re seeing
- Stat cards on the Approver Dashboard look unchanged (no gap, old layout).
- “All request” table doesn’t match the user “View Request” table (no full borders, grey header, alternating rows).

## Root cause (most likely)

### 1. **Stale or cached CSS (most likely)**
- The approver page loads CSS via **Vite**: `@vite(['resources/css/approver.css', 'resources/js/approver.js'])`.
- The browser uses either:
  - **Dev:** CSS from the Vite dev server (`npm run dev`), or  
  - **Prod:** A built file from `public/build/` (from `npm run build`).
- If you **never ran `npm run build`** after the CSS changes, the file in `public/build/` is old and doesn’t contain the new stat-card or table styles.
- Or the **browser** (or a CDN/proxy) is **caching** the old CSS file, so you still see the old UI even though the source changed.

**How to confirm:** Run `npm run build` and then hard-refresh the approver page (Ctrl+F5). If the UI updates, the bug was stale/cached CSS.

### 2. **Vite dev server not running**
- In local dev, if you’re not running `npm run dev`, Laravel may still be using an old built manifest and old assets.
- **Check:** Start `npm run dev`, reload the approver page, and see if the UI updates.

### 3. **Wrong or missing `body` class (less likely)**
- Our new rules use `body.panel-approver` so they only apply when `<body>` has `class="panel-approver"`.
- The full approver layout does set `<body class="panel-approver">`, so this is only an issue if you use a different layout or inject the approver content into another page without that body class.

---

## Summary
The **logic in `resources/css/approver.css` is correct** (stat cards and All request table use the right selectors and styles). The problem is almost certainly that the **CSS actually loaded in the browser is old** (stale build or cache). The fix is either to **rebuild and avoid cache**, or to **add critical inline CSS** in the approver view so the desired UI always shows even if the built CSS is outdated or cached.
