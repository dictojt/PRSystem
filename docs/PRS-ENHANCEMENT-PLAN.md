# PRS Enhancement Plan

Consolidated plan for: **Approved ID**, **Status filter**, **Edit modal**, and **Archive tab with Restore**.

---

## 1. Approved ID

### 1.1 Behavior

| When | Stored in DB | Shown in list table (ID column) |
|------|----------------|----------------------------------|
| **Approved** | `request_id` + `approved_id` (6-digit) | **Approved ID only** — Request ID hidden in table |
| **Pending / Rejected** | `request_id` only; `approved_id` = null | **Request ID** |

- Request ID always remains in the database (never removed).
- Approved ID is generated **only when** status becomes **Approved**.

### 1.2 Data & Logic

- **Migration**: Add to `requests` table:
  - `approved_id` — nullable, unique (e.g. `string`, 6 chars, or `unsignedInteger` 100000–999999).
- **Generation**: On approve action (DashboardController or equivalent), generate unique 6-digit value and set `approved_id` on the request. Strategy: sequential (with uniqueness check) or random with retry on collision.
- **Model**: Add `approved_id` to `PrsRequest` `$fillable` (and ensure it is not mass-assigned on create, only set on approve).

### 1.3 Display Rule (all request lists)

- **ID column**:
  - If `status === 'Approved'` → show `approved_id` (e.g. padded to 6 digits).
  - Else → show `request_id`.
- Same rule for: User (View Request), Approver (dashboard list), Superadmin (All Requests).

---

## 2. Status Filter (All Roles)

- **No new sidebar links** for Pending / Approved / Rejected.
- **One list page per role** with a **filter** (tabs or dropdown): **All | Pending | Approved | Rejected**.

| Role | List page | Change |
|------|-----------|--------|
| **User** | View Request | Filter options: All / Pending / Approved / Rejected (replace or extend current All/Pending/Completed). Apply filter **server-side** (query param e.g. `?status=approved`). |
| **Approver** | Dashboard | Keep existing tabs/links (Pending | Approved & Rejected) or unify into one filter: All / Pending / Approved / Rejected. Ensure list and ID column follow §1.3. |
| **Superadmin** | All Requests | Add filter: All / Pending / Approved / Rejected (tabs or dropdown), server-side. |

---

## 3. Edit (Admin)

- **Who**: Superadmin (and optionally Approvers), as decided.
- **Where**: Same request list(s) where admin sees requests (e.g. All Requests, Approver list).
- **UX**: Row action **Edit** → open **modal** with current data:
  - Item name, description, quantity (and any other editable fields).
  - Optional: admin-only fields (e.g. internal notes) if needed later.
- **Actions**: Save (PATCH/PUT to update request) | Cancel (close modal).
- **Backend**: New route(s) and controller method(s), e.g.:
  - `GET .../requests/{id}` (or inline data) for modal content.
  - `PUT` or `PATCH .../requests/{id}` for update. Validate and update only allowed fields.

---

## 4. Archive & Archived Tab + Restore

### 4.1 Archive

- **Who**: Superadmin (and optionally Approvers).
- **Action**: Row action **Archive** → confirmation dialog (“Archive this request? It will be hidden from the main list.”) → on confirm, set request as archived.
- **Data**: Add to `requests` table:
  - `archived_at` (nullable timestamp), **or**
  - `is_archived` (boolean, default false).
- **Scoping**: Default lists (All Requests, Approver list, User View Request) **exclude** archived items (`archived_at` is null or `is_archived` is false), unless viewing the “Archived” tab.

### 4.2 Archived tab (same list)

- **Where**: Same list page(s) where archive is available (e.g. Superadmin All Requests, and Approver dashboard if they can archive).
- **UX**: Add an **Archived** tab (or filter option “Archived”) in the same list. When selected:
  - Show only requests where `archived_at` is set (or `is_archived` is true).
  - Show **Restore** action per row (and optionally Edit, depending on policy).

### 4.3 Restore

- **Action**: In the Archived tab, row action **Restore** → optional confirm (“Restore this request?”) → clear `archived_at` / set `is_archived` to false.
- **Effect**: Request reappears in the default (non-archived) list according to its status.

---

## 5. Implementation Order (Suggested)

1. **Database**
   - Migration: add `approved_id` (nullable, unique).
   - Migration: add `archived_at` (nullable timestamp) or `is_archived` (boolean).

2. **Approved ID**
   - Generate and set `approved_id` in approve action.
   - Update all request list views (User, Approver, Superadmin) to show Approved ID vs Request ID per §1.3.

3. **Status filter**
   - User: server-side filter All / Pending / Approved / Rejected on View Request.
   - Superadmin: add filter on All Requests.
   - Approver: align with existing or new tab/filter.

4. **Archive**
   - Archive action (route + controller), confirmation dialog, set `archived_at` / `is_archived`.
   - Default list queries exclude archived unless “Archived” tab/filter is selected.

5. **Archived tab + Restore**
   - Add “Archived” tab (or filter) to the same list; query for archived only when selected.
   - Restore action (route + controller), clear archive flag.

6. **Edit**
   - Edit modal (markup + JS) on the relevant list view(s).
   - GET (if needed) and PUT/PATCH route + controller for updating request; validation.

---

## 6. Files to Touch (Checklist)

- **Migrations**: New migration for `approved_id` and `archived_at` (or `is_archived`).
- **Model**: `App\Models\PrsRequest` — `approved_id`, archive field, `generateApprovedId()` (or similar), scopes e.g. `scopeNotArchived`, `scopeArchived`.
- **Controllers**: 
  - Approve action: set `approved_id` on approve.
  - SuperAdminController (or dedicated RequestController): update (edit), archive, restore; list with status and archived filters.
  - DashboardController (approver): list with status/archived if approver can archive/restore.
  - UserController: View Request with status filter and ID column rule.
- **Routes**: `web.php` — GET/PATCH or PUT for edit; POST (or PUT) for archive and restore.
- **Views**:
  - User: `view-requests.blade.php` — filter options, ID column logic.
  - Superadmin: `all-requests.blade.php` (or equivalent) — filter (All/Pending/Approved/Rejected + Archived tab), ID column, Edit button, Archive button, Edit modal; Archived tab with Restore button.
  - Approver: dashboard partials — ID column, optional Edit/Archive/Restore and Archived tab if approver has those permissions.

---

## 7. Summary

| Feature | Summary |
|--------|---------|
| **Approved ID** | 6-digit unique ID generated on approval; shown in table when Approved; Request ID hidden in column but kept in DB. |
| **Status filter** | One list per role with filter: All / Pending / Approved / Rejected (no extra sidebar links). |
| **Edit** | Admin row action → modal with item name, description, quantity → Save/Cancel. |
| **Archive** | Row action Archive + confirm; requests hidden from default list. |
| **Archived tab** | Same list; tab (or filter) “Archived” shows only archived items. |
| **Restore** | In Archived tab, Restore action clears archive so item returns to main list. |
| **Push notifications** | Web push for browser + PWA; push settings in Settings for all users; see §8. |
| **Dark mode** | Settings for **all users**; per-user theme (light/dark); see §9.1. |
| **System settings** | Settings page for **all users** (User, Approver, Superadmin); some options superadmin-only (e.g. export); see §9. |
| **Export data** | **Superadmin only**; last 30 or 60 days; CSV with ID, Item name, Description, Quantity, Date; see §11. |
| **Phased implementation** | Phase 1: Settings + Dark mode. Phase 2: Push notification settings. Phase 3: Export (superadmin). See §12. |

This document is the single reference for the approved plan. Implementation can follow the order in §5 and the checklist in §6.

---

## 8. Push Notifications (Browser + PWA) — Planned

### 8.1 Goal

- Send **web push notifications** to users in both contexts:
  - **Browser**: when PRS is open in a normal tab (or in the background).
  - **PWA**: when PRS is installed and running as an installed app (same service worker, same push channel).

Examples: “New request submitted”, “Request #123 was approved”, “Request #456 was rejected”, optional digest (e.g. daily pending count).

### 8.2 Current State

- The service worker (`resources/js/sw.js` → built as `public/sw.js`) is used for:
  - Caching and offline (Workbox).
  - PWA install (handled in `pwa-register.js`).
- **Push is not implemented**: no `push` event, no `showNotification`, no PushManager subscription, no backend sending.

### 8.3 What Needs to Be Done (High Level)

1. **Service worker**
   - Add a `push` event listener: parse payload and call `self.registration.showNotification(...)`.
   - Add `notificationclick` (and optionally `notificationclose`) to open the app or a specific URL when the user clicks the notification.

2. **Client (browser / PWA)**
   - Request permission: `Notification.requestPermission()` (e.g. after login or via an “Enable notifications” control).
   - Subscribe: use `registration.pushManager.subscribe()` with a VAPID public key.
   - Send the push subscription (endpoint + keys) to the backend and store it per user/device.

3. **Backend**
   - Store push subscriptions (e.g. table `push_subscriptions` linked to user).
   - When events occur (new request, approval, rejection, etc.), use a web-push library (e.g. `web-push` with VAPID) to send a payload to each relevant subscription’s endpoint.
   - Optional: System Settings toggles (e.g. “Notify on new request”, “Notify requester on decision”) so notifications are sent only when the user has opted in.

4. **Environment**
   - Push requires a **secure context** (HTTPS in production; localhost is fine for development).

### 8.4 Scope

- Same implementation serves **both** browser and PWA: once the service worker is registered (which already happens for both), push works the same. No separate “PWA-only” path needed for notifications.

### 8.5 Push Notification Settings (All Users)

- **Push notification settings** live in **Settings** and are available to **all users** (User, Approver, Superadmin).
- Each user can enable/disable push and choose what to receive (e.g. “When my request is approved/rejected”, “When a new request is submitted” for approvers).
- Stored per user (e.g. `user_preferences` or `push_preferences` table). Backend uses these when sending push (§8.3); if user has disabled push or a specific type, do not send.

---

## 9. System Settings for All Users — Scope

- **System Settings** (or a **Settings** area) is available to **all users**, not only superadmin.
- Each role has access to a Settings page with **role-appropriate options**:
  - **User (requester), Approver, Superadmin**: Dark mode (§9.1), Push notification preferences (§8, §8.5).
  - **Superadmin only**: Export data (§11), and any global/system-wide options (e.g. default theme, defaults for requests) if desired later.
- So: **Settings applies to all users**; some options (e.g. export) are **superadmin-only** within that.

---

## 9.1 Dark Mode (System Settings, All Users) — Planned

### Goal

- **Dark mode** is a setting in **Settings** that **all users** can use (User, Approver, Superadmin).
- Each user chooses their own theme (Light / Dark); the choice applies only to that user’s session.

### Where It Lives

- **Settings** page available to **every role** (each layout has a “Settings” or “System Settings” entry that goes to a role-specific settings view).
- Section **Theme / Appearance**: option **Light** (default) or **Dark**.
- Stored **per user** (e.g. `user_preferences` or column on `users` table: `theme` = `light` | `dark`).

### What Needs to Be Done (High Level)

1. **Storage**
   - Persist per user (e.g. `users.theme` or `user_preferences.theme`): `light` | `dark`.

2. **Settings UI (all roles)**
   - User panel, Approver, and Superadmin each have a Settings page (or section) with a Theme control (dropdown or toggle). Save via a shared or per-role settings endpoint.

3. **Applying the theme**
   - On each request, backend passes the current user’s theme to the layout. Apply a class or attribute on the root (e.g. `<body class="theme-dark">` or `data-theme="dark"`) so CSS variables or dark-mode styles apply.
   - All relevant pages (dashboards, lists, modals, sidebar) have dark-mode styles or use CSS variables that switch with the theme class.

### Summary

| Item | Description |
|------|-------------|
| **Where** | Settings page for **all users** (User, Approver, Superadmin). |
| **What** | Dark mode (Light / Dark) per user. |
| **Who** | Every role can set their own theme. |
| **Persistence** | Per-user (e.g. `users.theme` or preferences table). |
| **Application** | Root class/attribute + CSS so the chosen theme applies for that user. |

---

## 10. Other System Settings Suggestions (Excluding Email, Password, Dark Mode)

Additional content that could live in System Settings (superadmin). Not auth/credential-related; dark mode is covered in §9.

### 10.1 Request & Workflow

| Setting | Description |
|--------|-------------|
| **Default quantity** | Default value for “quantity” when creating a request (e.g. 1). |
| **Max quantity per request** | Upper limit (e.g. 1–9999) to prevent typos or abuse. |
| **Auto-archive after (days)** | Option to auto-archive approved/rejected requests after X days (or “Never”). |
| **Require rejection reason** | Toggle: when rejecting, require a reason (yes/no). |
| **Request ID format / prefix** | Optional prefix or pattern for request IDs (e.g. `PRS-` or `PRS-2025-`). |

### 10.2 Notifications & Alerts

| Setting | Description |
|--------|-------------|
| **Email on new request** | Notify approvers (or a list) when a new request is submitted (on/off). |
| **Email on decision** | Notify requester when their request is approved or rejected (on/off). |
| **Daily or weekly digest** | Optional summary of pending or recent requests (on/off, frequency). |
| *(Push notifications)* | See §8; toggles (e.g. “Notify on new request”) can live here when implemented. |

### 10.3 Session & Security (Non-Credential)

| Setting | Description |
|--------|-------------|
| **Session timeout (minutes)** | Auto-logout after X minutes of inactivity (e.g. 30, 60, 120). |
| **Require re-auth for sensitive actions** | When to ask for OTP/re-auth (e.g. before deactivate user, change role). |

### 10.4 Branding & Display

| Setting | Description |
|--------|-------------|
| **System name** | Display name (e.g. “Product Request System” or “DICT PRS”) used in headers/titles. |
| **Logo / favicon** | Upload or URL for logo and favicon (optional). |
| **Primary / accent color** | Theme color for buttons and headers (e.g. hex); can coexist with dark mode. |
| **Date format** | How dates are shown (e.g. “M d, Y” vs “d/m/Y”) system-wide. |
| **Timezone** | Default timezone for server-generated dates (if needed). |

### 10.5 Lists & UX Defaults

| Setting | Description |
|--------|-------------|
| **Requests per page** | Default pagination size (e.g. 10, 25, 50) for All Requests and similar lists. |
| **Default dashboard tab** | Default tab or filter when opening a dashboard (e.g. Pending vs All). |

### 10.6 Data & Maintenance

| Setting | Description |
|--------|-------------|
| **Export format** | Default format for exports (e.g. CSV, Excel) if export is added. |
| **Data retention notice** | Optional short text or link (e.g. “Request data is retained for X months”) for transparency. |

### 10.7 PWA & Offline (Optional)

| Setting | Description |
|--------|-------------|
| **Show install prompt** | Allow or suppress the “Install app” prompt (on/off). |
| **Offline message** | Custom message shown on `offline.html` (optional). |

---

These are suggestions only; implementation order and scope can be decided later. Email and password changes are intentionally excluded for security.

---

## 11. Export Data (Superadmin Only) — Planned

### 11.1 Goal

- Allow **superadmin** to **export request data** from System Settings (or a dedicated Export area in superadmin).
- **Not** available to User or Approver — superadmin only.

### 11.2 Options

- **Date range** (choose one for the export):
  - **Last 30 days**
  - **Last 60 days**
  - (Optional later: custom date range.)
- **Format**: CSV (or Excel if added later). CSV for Phase 1.

### 11.3 Data Exported (Columns)

| Column   | Source / meaning                |
|----------|---------------------------------|
| **ID**   | Request ID (or Approved ID when applicable; can be defined in plan). |
| **Item name** | Request item name.          |
| **Description** | Request description.     |
| **Quantity** | Request quantity.          |
| **Date** | Request creation date (e.g. formatted). |

- Only requests whose **created_at** (or equivalent) falls within the selected range are included.
- Optionally include status, requestor, etc. in a later phase; for the planned phase, the five columns above are the scope.

### 11.4 Where It Lives

- **Superadmin only**: e.g. System Settings page with an “Export data” section, or a separate “Export” link in the superadmin sidebar. One route (e.g. GET with query params `range=30` or `range=60`) that returns a CSV download.

### 11.5 Summary

| Item   | Description |
|--------|-------------|
| **Who** | Superadmin only. |
| **Options** | Last 30 days, Last 60 days (and optionally custom range later). |
| **Columns** | ID, Item name, Description, Quantity, Date. |
| **Format** | CSV (Phase 1). |
| **Persistence** | No storage of exports; generate on demand and stream download. |

---

## 12. Phased Implementation: System Settings (Dark Mode, Push Settings, Export)

Implementation order for the three features: **phase by phase**.

### Phase 1: Settings for All Users + Dark Mode

1. **Settings entry for all roles**
   - Add a **Settings** (or “System settings”) link/page for **User**, **Approver**, and **Superadmin** (each role’s layout and route).
   - One shared or per-role Settings view; superadmin can have additional sections later.

2. **Dark mode**
   - **Storage**: Add per-user theme (e.g. `users.theme` or `user_preferences`: `light` | `dark`). Migration if needed.
   - **Backend**: Endpoint(s) to get/update current user’s theme (e.g. GET/PATCH `settings/theme` or part of user profile).
   - **UI**: On each role’s Settings page, add Theme section (Light / Dark). Save and reload or apply without full reload.
   - **Apply theme**: In every layout (user panel, approver, superadmin), pass current user’s theme to the view and set root class or `data-theme`. Add CSS (variables or dark-mode rules) for all main surfaces (sidebar, main, cards, tables, modals).

**Deliverable**: All users can open Settings and set Light/Dark; theme applies across the app for that user.

---

### Phase 2: Push Notification Settings (All Users)

1. **Settings UI**
   - On the same Settings page (all roles), add a **Notifications** or **Push notifications** section.
   - Toggles (e.g. “Enable push notifications”, “Notify when my request is approved/rejected”, “Notify me of new requests” for approvers). Stored per user.

2. **Backend**
   - Store preferences (e.g. `user_preferences` or `push_subscription`-related table: enabled, types of events). Migration if needed.
   - When implementing actual push (§8), backend checks these preferences before sending.

3. **Client (optional in this phase)**
   - If push is not yet implemented: only save preferences. When §8 is done, subscribe and send subscription to backend only if user enabled push.

**Deliverable**: All users can turn push on/off and choose notification types in Settings; preferences are stored and ready for use when push is implemented.

---

### Phase 3: Export Data (Superadmin Only)

1. **Backend**
   - New route (e.g. `GET /superadmin/export/requests`) with query param `range=30` or `range=60` (days). Superadmin-only middleware.
   - Query requests where `created_at` is within the last 30 or 60 days. Select columns: ID (request_id or approved_id as decided), item name, description, quantity, date. Stream CSV response (filename e.g. `prs-requests-30d.csv`).

2. **UI**
   - In superadmin System Settings (or dedicated Export page), add “Export data” section: two buttons or links (e.g. “Last 30 days”, “Last 60 days”) that point to the export URL with the right `range` param. Trigger file download.

**Deliverable**: Superadmin can export request data (ID, Item name, Description, Quantity, Date) for the last 30 or 60 days as CSV; User and Approver do not see or access export.

---

### Summary Table

| Phase | Scope | Deliverable |
|-------|--------|-------------|
| **Phase 1** | All users | Settings page for every role; Dark mode (per-user theme, applied everywhere). |
| **Phase 2** | All users | Push notification settings (on/off + event types) in Settings; stored per user; ready for §8. |
| **Phase 3** | Superadmin only | Export requests as CSV: Last 30 days or Last 60 days; columns: ID, Item name, Description, Quantity, Date. |
