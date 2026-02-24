# Mobile responsive rollout – phase by phase

Responsive work is done **by user type** so you can test one role at a time before moving on.

## Standardized breakpoints (use everywhere)

| Name   | Value  | Target devices                          |
|--------|--------|-----------------------------------------|
| `--bp-xs`  | 320px  | Extra small phones (e.g. iPhone SE)     |
| `--bp-sm`  | 360px  | Small phones                            |
| `--bp-md`  | 480px  | Large phones                            |
| `--bp-lg`  | 576px  | Large phones / phablets                 |
| `--bp-xl`  | 768px  | Tablets portrait                        |
| `--bp-2xl` | 992px  | Tablets landscape / small laptop        |
| `--bp-3xl` | 1200px | Desktop                                 |

In CSS use the **exact pixel values** in `@media (max-width: 320px)` etc. Variables are in `resources/css/partials/variables.css`.

---

## Phases (check off after testing)

### Phase 1: User panel
- [ ] Layout: sidebar collapse, main content, safe areas (320 → 768px)
- [ ] View Requests: filter, table → cards, touch targets
- [ ] Create Request: form stacking, buttons, add/remove item
- [ ] Reports, Support, Track: readable and no horizontal scroll
- [ ] User dashboard (if any): stat cards and lists

**Files:** `user-panel.css`, `panel-layout.css`, `view-requests.blade.php`, `create-request.blade.php`, other user views.

---

### Phase 2: Approver panel
- [ ] Layout: sidebar, main content, all breakpoints
- [ ] Tables → cards or scroll; filters and actions
- [ ] Approve/Reject modals on small screens
- [ ] Approver dashboard / overview

**Files:** `approver.css`, approver views, dashboard partials.

---

### Phase 3: Superadmin panel
- [ ] Layout: sidebar, main content, all breakpoints
- [ ] Admins, All requests, Approvers: tables and forms
- [ ] Superadmin dashboard

**Files:** `super-admin.css`, superadmin views.

---

### Phase 4: Home & auth (guest-facing)
- [ ] Home: hero, CTA, layout 320px and up
- [ ] Login / Sign in with Google
- [ ] Forgot password, offline page

**Files:** `home.css`, `base.css`, auth views, `offline.html`.

---

## Testing widths (recommended)

- **320px** – iPhone SE, very small Android
- **360px** – Small phones
- **375px** – iPhone 12/13/14 mini
- **414px** – iPhone Plus / Pro Max
- **768px** – Tablet portrait
- **992px** – Tablet landscape

Use Chrome DevTools device toolbar or resize the browser to each width and confirm no horizontal scroll and readable tap targets.
