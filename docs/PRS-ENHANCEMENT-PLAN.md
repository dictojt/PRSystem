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

This document is the single reference for the approved plan. Implementation can follow the order in §5 and the checklist in §6.
