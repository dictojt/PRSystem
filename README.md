# Product Request System (PRS)

**Internal Administrative Portal** for the Department of Information and Communications Technology (DICT), Republic of the Philippines.

PRS is a Laravel-based web application that enables employees to submit product/equipment requests, track their status, and receive approvals from designated approvers. Super admins manage users, approve or reject requests, and access system-wide reports.

---

## Features

### For Users
- **Sign in with Google** — No separate sign-up; accounts are created on first sign-in
- **Create requests** — Submit one or more product/equipment items (name, description, quantity)
- **Track requests** — View and filter requests by status (pending, approved, rejected)
- **Dashboard** — Active requests, pending actions, and recently completed items
- **Reports** — Summary of requests this month, completed, and pending counts
- **Support** — Help and contact page

### For Approvers
- **Pending queue** — View and approve/reject pending requests
- **Approved history** — Review past decisions with timestamps
- **Dashboard** — Pending count, approved today, and completed stats

### For Super Admins
- **Overview** — Total admins, pending approvals, approved this month
- **Admin Management** — Add superadmin/approver users, update roles, deactivate/reactivate (OTP re-auth for sensitive actions)
- **Approvers** — List of approver users
- **All Requests** — View, approve, reject, edit, archive, or restore requests
- **System Reports** — Pending, approved/rejected this month, counts by status
- **System Settings** — Configuration placeholder
- **Password resets** — Process forgot-password requests and send new default passwords

### Request Lifecycle
1. User submits request → **Pending**
2. Approver or Super Admin reviews → **Approved** or **Rejected**
3. Approved requests receive a unique 6-digit Approved ID
4. Super Admin can archive old requests (hidden from main list, viewable in Archived tab)

---

## Tech Stack

- **PHP 8.2+** with **Laravel 12**
- **Laravel Socialite** — Google OAuth
- **MySQL** (default) — Database
- **Vite** — Frontend build (CSS/JS)
- **Blade** — Server-side templates
- **Material Icons** & **Inter** font

---

## Requirements

- PHP 8.2 or higher
- Composer
- Node.js & npm
- MySQL 5.7+ (or compatible)
- Google Cloud Console account (for OAuth credentials)

---

## Installation

### 1. Clone and install dependencies

```bash
git clone <repository-url>
cd PRS
composer install
npm install
```

### 2. Environment setup

```bash
cp .env.example .env
php artisan key:generate
```

### 3. Configure `.env`

**Database** (update if needed):
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=prs
DB_USERNAME=root
DB_PASSWORD=
```

**Application URL** (required for OAuth):
```
APP_URL=http://localhost:8000
```
When using a tunnel (ngrok, devtunnels, etc.), set `APP_URL` to your tunnel HTTPS URL.

**Google OAuth** (required for sign-in):

1. Go to [Google Cloud Console](https://console.cloud.google.com/) → APIs & Services → Credentials
2. Create an OAuth 2.0 Client ID (Web application)
3. Add redirect URI: `http://localhost:8000/auth/google/callback` (or your tunnel URL + `/auth/google/callback`)
4. Add to `.env`:

```
GOOGLE_CLIENT_ID=your-client-id
GOOGLE_CLIENT_SECRET=your-client-secret
```

**Optional — restrict sign-in by domain:**
```
ALLOWED_EMAIL_DOMAINS=dict.gov.ph,gmail.com
```

### 4. Run migrations

```bash
php artisan migrate
```

### 5. Build assets

```bash
npm run build
```

### 6. Start the server

```bash
php artisan serve
```

Visit [http://localhost:8000](http://localhost:8000).

---

## Development

Run the development server with hot reload (Vite) and queue worker:

```bash
composer dev
```

This starts:
- PHP server
- Queue listener
- Vite dev server

---

## User Roles

| Role       | Description                          | Assigned by           |
|-----------|--------------------------------------|------------------------|
| `user`    | Default for employees                | Auto on first sign-in  |
| `approver`| Can approve/reject requests          | Super Admin            |
| `superadmin` | Full system administration       | Database or seed       |

Roles are stored in the `users.role` column. New users default to `user`. Promote users to `approver` or `superadmin` via Admin Management (superadmin only) or directly in the database.

---

## Guest Mode (Demo / Testing)

Some pages work without login for demo purposes:

- `/user/guest` — User dashboard
- `/approver/guest` — Approver dashboard (can approve/reject)
- `/superadmin/guest` — Super Admin overview

---

## Request IDs

- **Request ID** — Format: `REQ-YYYY-MM-NNNNN` (e.g. `REQ-2025-02-00001`)
- **Approved ID** — 6-digit unique code assigned when a request is approved

---

## License

[MIT](https://opensource.org/licenses/MIT)
