# PRS – Step-by-Step Database Setup

Sundin ang mga hakbang sa ibaba para maayos ang database ng Product Request System.

---

## Option A: SQLite (mas madali, walang MySQL)

Best kung ayaw mo munang mag-configure ng MySQL/XAMPP. Gumagamit ng isang file lang (`database.sqlite`).

### 1. Gumawa ng `.env` file (kung wala pa)

- Sa project folder `c:\xampp\htdocs\PRS`, kopyahin ang `.env.example` at i-rename sa **`.env`**.
- Kung may `.env` na, buksan mo lang.

### 2. I-set ang database sa SQLite sa `.env`

Buksan ang `.env` at hanapin ang mga linyang may `DB_`. Palitan o idagdag para maging ganito:

```env
DB_CONNECTION=sqlite
# I-comment o tanggalin ang mga MySQL lines kapag SQLite:
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=prs
# DB_USERNAME=root
# DB_PASSWORD=
```

**Mahalaga:** Dapat **`DB_CONNECTION=sqlite`** at **walang** `DB_DATABASE=prs` (o kung may `DB_DATABASE`, gawing path sa SQLite file, tingnan Step 3).

### 3. Gumawa ng SQLite database file

Sa **Command Prompt** o **PowerShell**, pumasok sa project folder at i-run:

```bash
cd c:\xampp\htdocs\PRS
```

Pagkatapos, gumawa ng empty SQLite file (Laravel default name):

```bash
type nul > database\database.sqlite
```

*(Kung sabi "cannot find path", subukan: `echo. > database\database.sqlite`)*

Sa `.env` puwede mong iwanan na walang `DB_DATABASE`; Laravel ay gagamit ng `database/database.sqlite` by default.

### 4. I-generate ang Application Key (kung bago ang `.env`)

```bash
php artisan key:generate
```

### 5. I-clear ang config cache

```bash
php artisan config:clear
```

### 6. I-run ang migrations (gawa ng mga table)

```bash
php artisan migrate
```

Kung may tanong na “Do you want to run this migration?”, type **yes** at Enter.

### 7. (Optional) I-run ang seeder (sample users at data)

```bash
php artisan db:seed
```

Pagkatapos nito, may sample users na (hal. juan@dict.gov.ph, admin@dict.gov.ph) at sample requests. Default password sa seeder: **`password`**.

---

## Option B: MySQL (XAMPP)

Kung gusto mong gamitin ang MySQL ng XAMPP.

### 1. I-start ang MySQL sa XAMPP

- Buksan ang **XAMPP Control Panel**.
- I-click **Start** sa **MySQL**.

### 2. Gumawa ng database sa MySQL

1. Buksan ang browser, pumunta sa: **http://localhost/phpmyadmin**
2. I-click **New** (o “New database”).
3. Database name: **`prs`**
4. Collation: **utf8mb4_unicode_ci**
5. I-click **Create**.

### 3. I-configure ang `.env` para sa MySQL

Buksan ang `.env` sa `c:\xampp\htdocs\PRS` at siguraduhing ganito ang DB part:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=prs
DB_USERNAME=root
DB_PASSWORD=
```

Kung may password ang MySQL root user, ilagay sa `DB_PASSWORD=`.

### 4. I-generate ang Application Key (kung bago ang `.env`)

```bash
cd c:\xampp\htdocs\PRS
php artisan key:generate
```

### 5. I-clear ang config

```bash
php artisan config:clear
```

### 6. I-run ang migrations

```bash
php artisan migrate
```

Sagutin **yes** kung may prompt.

### 7. (Optional) I-run ang seeder

```bash
php artisan db:seed
```

---

## Kapag may error o gusto mong ulitin ang database

### I-rollback lahat ng migrations (bura lahat ng table)

```bash
php artisan migrate:rollback
```

### O i-fresh (drop lahat + migrate ulit)

```bash
php artisan migrate:fresh
```

### Fresh + seed ulit (malinis na table + sample data)

```bash
php artisan migrate:fresh --seed
```

**Babala:** `migrate:fresh` at `migrate:fresh --seed` ay **tatanggal lahat ng data** sa database. Gamitin lang kung okay lang mawala ang laman (e.g. development lang).

---

## Checklist

- [ ] `.env` na naka-set sa `DB_CONNECTION=sqlite` O `mysql` (at tama ang DB name/user/pass)
- [ ] Kung SQLite: may file na `database/database.sqlite`
- [ ] Kung MySQL: naka-Start ang MySQL sa XAMPP at may database na `prs`
- [ ] `php artisan key:generate` (kung bagong `.env`)
- [ ] `php artisan config:clear`
- [ ] `php artisan migrate` — walang error
- [ ] (Optional) `php artisan db:seed` — para may sample users at requests

Pagkatapos nito, puwede mo nang i-test ang app (e.g. http://localhost:8000) at mag-log in gamit ang Google OAuth o ang seeded users kung na-configure na ang login.
