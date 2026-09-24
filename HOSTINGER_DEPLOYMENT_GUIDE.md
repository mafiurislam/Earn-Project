# Hostinger SQLite Deployment Guide for Rajdoot Nivedan Media

This application is built with **PHP 8.2+ / 8.3**, **Laravel Framework**, and a high-performance **SQLite Database**. 
**Zero MySQL setup is required.** The database is fully embedded, pre-migrated, and pre-seeded inside `database/database.sqlite`.

---

## Why SQLite on Hostinger?
- **Zero Database Server Configuration**: No need to create MySQL databases, usernames, passwords, or deal with connection drops.
- **Portability**: All your data, users, verification documents, songs, and tables live inside the `database/database.sqlite` file.
- **Speed & Security**: Direct file I/O protected by Apache `.htaccess` deny rules.

---

## 3-Step Hostinger Deployment Instructions

### Step 1: Upload Project Files to Hostinger
1. In your **Hostinger hPanel**, navigate to **Websites** &rarr; select your domain &rarr; open **File Manager**.
2. Go to your domain's root web folder:
   - Typically `public_html/`.
3. Upload your project ZIP file and extract it directly into `public_html/`.
4. **No folder restructuring needed**: The project includes a pre-configured root [`.htaccess`](file:///c:/Users/SIMRAN/OneDrive/Desktop/Earn-Project/.htaccess) and [`index.php`](file:///c:/Users/SIMRAN/OneDrive/Desktop/Earn-Project/index.php) that automatically direct traffic to the `public/` directory while keeping all sensitive files (`.env`, `database/`, `storage/`) completely safe and inaccessible to outside visitors.

---

### Step 2: Configure `.env` on Hostinger
1. In Hostinger File Manager, you will find [`.env.hostinger`](file:///c:/Users/SIMRAN/OneDrive/Desktop/Earn-Project/.env.hostinger) already included with production settings and your application encryption key.
2. Rename [`.env.hostinger`](file:///c:/Users/SIMRAN/OneDrive/Desktop/Earn-Project/.env.hostinger) to `.env` (or copy its contents into your `.env` file).
3. Update `APP_URL` to match your actual domain:
   ```env
   APP_NAME="Rajdoot Nivedan Media"
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://yourdomain.com

   # SQLite Database (Zero MySQL setup needed!)
   DB_CONNECTION=sqlite
   DB_FOREIGN_KEYS=true
   DB_BUSY_TIMEOUT=5000

   # Optimized storage & sessions for Hostinger
   SESSION_DRIVER=file
   FILESYSTEM_DISK=public
   QUEUE_CONNECTION=sync
   CACHE_STORE=file
   ```

---

### Step 3: Set Write Permissions (Crucial for SQLite)
SQLite needs write permissions not just on the database file, but also on the **`database/` directory** (to create temporary write-ahead log & journal files during transactions).

In Hostinger File Manager:
1. Right-click on the **`database/`** folder &rarr; click **Permissions**:
   - Set to **`775`** (or check `Read` and `Write` for Owner and Group).
2. Right-click on **`database/database.sqlite`** &rarr; click **Permissions**:
   - Set to **`775`** (or `664`).
3. Right-click on the **`storage/`** folder &rarr; click **Permissions**:
   - Set to **`775`** (recursive / all subfolders: `framework/`, `logs/`, `app/public/`).
4. Right-click on the **`bootstrap/cache/`** folder &rarr; click **Permissions**:
   - Set to **`775`**.

> [!TIP]
> **No SSH Required for Storage Links!**
> This application includes an automatic route fallback for media files (`/storage/{path}`). If Hostinger does not extract the symlink, song playback, cover art, and verification documents continue to display without errors.

---

## Default Access Credentials

### Admin Console (`/admin/login`):
| Role | Email | Password | Access Level |
| :--- | :--- | :--- | :--- |
| **Owner Admin** | `admin12345@gmail.com` | `admin12345` | Full Admin & Management |
| **Webmaster Admin** | `admin@rajdootnivedan.com` | `admin123` | Full Admin & Management |

### Demo Customer Accounts (`/login`):
| Customer Name | Email | Password | Status | Earnings |
| :--- | :--- | :--- | :--- | :--- |
| **simrantaufik** | `simrantaufik@gmail.com` | `password123` | Verified KYC | ₹600.00 (₹500 Paid Out) |
| **anowora** | `mafiurislam366@gmail.com` | `password123` | Pending KYC | ₹0.00 |
| **Ta** | `rajurm063@gmail.com` | `password123` | Verified KYC | ₹0.00 |
| **Mafiur Islam** | `mafiurislam365@gmail.com` | `password123` | Unverified | ₹0.00 |

---

## Deployment Validation Command
If you have SSH access enabled in Hostinger hPanel, you can run the built-in health check command at any time:
```bash
php artisan hostinger:check
```
This tool validates SQLite extensions, file permissions, tables, data records, and security locks.

---

## Security Protection
- **SQLite Database Security**: Direct web downloads of `database.sqlite` are blocked by:
  - Apache deny rules in `database/.htaccess` (`Require all denied`).
  - Strict pattern matching in root `.htaccess`.
- **Environment & Git Security**: Direct access to `.env`, `.git`, and private storage paths are blocked with HTTP `403 Forbidden`.
