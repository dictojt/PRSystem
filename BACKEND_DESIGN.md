# PRS Backend Design Document

## 1. UI Analysis

### Pages & Components
| Page | URL | Key Components | User Actions |
|------|-----|----------------|--------------|
| **Login** | `/` | Google OAuth button | Sign in with Gmail |
| **User Dashboard** | `/user`, `/user/guest` | Active requests, Pending actions, Completed, Quick actions | View requests, Create request, Track, Reports, Support |
| **Approver Dashboard** | `/approver`, `/approver/guest` | Pending count, Approved today, Recent requests table | Approve, Reject requests |
| **Super Admin Dashboard** | `/superadmin`, `/superadmin/guest` | Total admins, Pending approvals, Approved requests, Products | View metrics, Admin Management, Approvers, All Requests, Product Control, Reports, Settings |
| **Create Request** | `/user/requests/create` | Form (item_name, description) | Submit request |
| **Track Item** | `/user/track` | Search by request ID | Track request status |
| **Reports** | `/user/reports` | Summary stats | View reports |
| **Support** | `/user/support` | Support info | Contact support |

### Required Features
- **Auth:** Google OAuth, session-based login, role-based redirect (user/approver/superadmin)
- **Requests:** Create, list, track, approve, reject
- **User Panel:** Active requests, pending actions, completed items
- **Approver Panel:** Pending count, approved today, approve/reject actions
- **Super Admin:** Total admins, pending approvals, approved this month, product count

---

## 2. Backend Requirements

### APIs / Actions
- **Auth:** `GET /auth/google`, `GET /auth/google/callback`, `POST /logout`
- **Requests:** Create (POST), List (via dashboards), Approve (POST), Reject (POST)
- **Track:** Search by request_id

### Validation Rules
- **Create Request:** item_name required, max 255, letters/spaces/dots/commas; description optional, max 500
- **Approve/Reject:** Valid request ID, request must be Pending

### Error Handling
- 404 for invalid request ID
- 422 for validation errors
- Session flash messages for success/error

---

## 3. Database Design

### Tables

#### `users` (existing, extended)
| Column | Type | Notes |
|--------|------|-------|
| id | bigint PK | |
| google_id | varchar nullable unique | |
| name | varchar | |
| email | varchar unique | |
| password | varchar nullable | For Google OAuth users |
| avatar | varchar nullable | |
| role | varchar default 'user' | user, approver, superadmin |
| created_at, updated_at | timestamps | |

#### `requests`
| Column | Type | Notes |
|--------|------|-------|
| id | bigint PK | |
| user_id | bigint FK → users | Requestor |
| request_id | varchar unique | Display ID: REQ-YYYY-MM-NNNNN |
| item_name | varchar(255) | |
| description | text nullable | |
| status | varchar(20) | Pending, Processing, In Review, Approved, Rejected |
| approved_by_id | bigint FK nullable → users | |
| approved_at | timestamp nullable | |
| rejected_by_id | bigint FK nullable → users | |
| rejected_at | timestamp nullable | |
| rejection_reason | text nullable | |
| created_at, updated_at | timestamps | |

#### `request_actions` (pending actions for user)
| Column | Type | Notes |
|--------|------|-------|
| id | bigint PK | |
| request_id | bigint FK → requests | |
| description | varchar(255) | e.g. "Verify budget approval" |
| due_date | date nullable | |
| status | varchar(20) default 'pending' | pending, completed |
| created_at, updated_at | timestamps | |

#### `products`
| Column | Type | Notes |
|--------|------|-------|
| id | bigint PK | |
| name | varchar(255) | |
| description | text nullable | |
| created_at, updated_at | timestamps | |

### Relationships
- User → hasMany Requests (as requestor)
- Request → belongsTo User (requestor)
- Request → belongsTo User (approved_by, rejected_by)
- Request → hasMany RequestActions

---

## 4. API / Route Design

| Method | URL | Purpose |
|--------|-----|---------|
| GET | / | Home / Login |
| GET | /auth/google | Redirect to Google OAuth |
| GET | /auth/google/callback | Handle OAuth callback |
| POST | /logout | Logout |
| GET | /user, /user/guest | User dashboard |
| GET | /approver, /approver/guest | Approver dashboard |
| GET | /superadmin, /superadmin/guest | Super Admin dashboard |
| GET | /user/requests/create | Create request form |
| POST | /user/requests | Store new request |
| GET | /user/track | Track by request_id |
| GET | /user/reports | User reports |
| GET | /user/support | Support page |
| POST | /approver/approve/{id} | Approve request (id = requests.id) |
| POST | /approver/reject/{id} | Reject request |
| POST | /approver/guest/approve/{id} | Approve (guest mode) |
| POST | /approver/guest/reject/{id} | Reject (guest mode) |

---

## 5. Implementation Notes

- **Request ID format:** REQ-YYYY-MM-NNNNN (e.g. REQ-2025-02-00001)
- **Approver routes:** Use `requests.id` (database PK) in URL, not display request_id
- **Blade views:** Pass `id` (PK) for approve/reject forms; display `request_id` for user-facing ID
