# 🏥 MediCareHub — Doctor Appointment Booking System
### Enterprise Medical Portal built on Drupal 11 & Docker Architecture

[![Drupal Version](https://img.shields.io/badge/Drupal-11.4.6-0678BE?logo=drupal&logoColor=white)](https://www.drupal.org/)
[![PHP Version](https://img.shields.io/badge/PHP-8.3-777BB4?logo=php&logoColor=white)](https://www.php.net/)
[![Database](https://img.shields.io/badge/MySQL-8.0-4479A1?logo=mysql&logoColor=white)](https://www.mysql.com/)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-3.4-38B2AC?logo=tailwind-css&logoColor=white)](https://tailwindcss.com/)
[![Docker](https://img.shields.io/badge/Docker_Compose-Supported-2496ED?logo=docker&logoColor=white)](https://www.docker.com/)

**MediCareHub** is a modern, high-performance doctor appointment booking and clinical queue management platform built on Drupal 11. It provides dedicated role-based portals for Patients, Doctors, Clinic Administrators, and System Admins, powered by a custom module (`doctor_appointment`) and a zero-conflict Tailwind CSS design system.

---

## 📑 Table of Contents

- [Key Highlights](#-key-highlights)
- [Architecture & Tech Stack](#-architecture--tech-stack)
- [Live Portals & Access Endpoints](#-live-portals--access-endpoints)
- [Demo Credentials](#-demo-credentials)
- [Getting Started](#-getting-started)
- [Custom Module Structure](#-custom-module-structure)
- [Tailwind CSS Build Workflow](#-tailwind-css-build-workflow)
- [Performance Optimization Highlights](#-performance-optimization-highlights)
- [Handy Administrative Commands](#-handy-administrative-commands)
- [Project Documentation](#-project-documentation)

---

## ✨ Key Highlights

- 👨‍⚕️ **Doctor Workstation (`/doctor/dashboard`)**: Live sequential patient queue management (`#01`, `#02`...), "In Chamber" active tracking, "Call Next" & "Mark Completed" workflows, consultation schedules, and emergency slots.
- 🩺 **Patient Portal (`/patient/dashboard`)**: Appointment history, upcoming consultations, quick doctor booking, and medical assistance contacts.
- 🏢 **Clinic Admin Console (`/clinic-admin/dashboard`)**: Specialist roster management, chamber assignment, consultation fees, and appointment oversight.
- 👤 **Clinical Enterprise User Profile (`/user/1`)**: 3-column workspace for direct portal access, account & security protocols, and administrative operations.
- 🔐 **One-Click Authentication (`/user/login`)**: Streamlined login card with preconfigured quick-fill demo buttons for `test_doctor`, `test_patient`, and `admin`.
- ⚡ **Zero-Conflict Tailwind CSS**: Full-page responsive layouts that integrate seamlessly with Drupal 11 without disrupting Drupal Core admin toolbars or off-canvas dialogs.
- 🚀 **Blazing Fast Performance**: Sub-30ms load times achieved via OPcache (512M), APCu in-memory caching, tmpfs Twig/container RAM storage, and optimized Composer classmaps.

---

## 🛠 Architecture & Tech Stack

| Layer | Technology | Details |
| :--- | :--- | :--- |
| **Core CMS** | Drupal 11.4.6 | Standard profile, custom entities, role-based access control |
| **Runtime** | PHP 8.3 (Apache) | OPcache 512M, APCu 128M, memory limit 512M |
| **Database** | MySQL 8.0 | InnoDB buffer pool 256M, tuned transaction flush |
| **Container** | Docker & Docker Compose | Multi-container setup with named volumes for core & vendor |
| **CLI Tools** | Drush 13.8.x | Command-line automation and cache management |
| **Custom Module** | `doctor_appointment` | Namespaced controllers, routes, hooks, and Twig templates |
| **Styling** | Tailwind CSS v3.4 | Static minified stylesheet with zero runtime CDN overhead |

---

## 🌐 Live Portals & Access Endpoints

| Portal / View | Local URL | Target Audience |
| :--- | :--- | :--- |
| 🏠 **Public Homepage** | [http://localhost:8090](http://localhost:8090) | Patients & General Visitors |
| 🔐 **Portal Login** | [http://localhost:8090/user/login](http://localhost:8090/user/login) | All Users (with Quick-Fill) |
| 👨‍⚕️ **Doctor Workstation** | [http://localhost:8090/doctor/dashboard](http://localhost:8090/doctor/dashboard) | Doctors |
| 🩺 **Patient Portal** | [http://localhost:8090/patient/dashboard](http://localhost:8090/patient/dashboard) | Registered Patients |
| 🏢 **Clinic Admin Console** | [http://localhost:8090/clinic-admin/dashboard](http://localhost:8090/clinic-admin/dashboard) | Clinic Managers |
| 👤 **User Profile Workspace** | [http://localhost:8090/user/1](http://localhost:8090/user/1) | Account Profile & Operations |
| ⚙️ **Drupal Core Admin** | [http://localhost:8090/admin](http://localhost:8090/admin) | Site Administrators |

---

## 👥 Demo Credentials

All test accounts use the standardized password: **`Test@1234`**

| Role | Username | Email | Default Dashboard |
| :--- | :--- | :--- | :--- |
| 👑 **Administrator** | `admin` | `admin@example.com` | Drupal Admin `/admin` |
| 👨‍⚕️ **Doctor** | `test_doctor` | `doctor@example.com` | `/doctor/dashboard` |
| 🩺 **Patient** | `test_patient` | `patient@example.com` | `/patient/dashboard` |

> **Tip:** You can click the **Doctor**, **Patient**, or **Admin** quick-fill buttons directly on the [Login Page](http://localhost:8090/user/login) to log in instantly.

### Database Credentials (MySQL 8.0)

| Setting | Inside Docker (`db` container) | From Host (DBeaver / Navicat / CLI) |
| :--- | :--- | :--- |
| **Host** | `db` | `localhost` or `127.0.0.1` |
| **Port** | `3306` | `3308` |
| **Database** | `drupal` | `drupal` |
| **Username** | `drupal` | `drupal` |
| **Password** | `drupal` | `drupal` |
| **Root Password** | `root` | `root` |

---

## 🚀 Getting Started

### Prerequisites

- [Docker Desktop](https://www.docker.com/products/docker-desktop/) (Windows with WSL2 or macOS / Linux)
- [Git](https://git-scm.com/)

### 1. Clone Repository & Start Containers

```bash
# Clone the project
git clone <repository-url> drupal
cd drupal

# Launch Drupal & MySQL containers in background
docker compose up -d
```

### 2. Verify Container Health

```bash
docker compose ps
```
Ensure both `drupal-drupal-1` (port `8090`) and `drupal-db-1` (port `3308`) report `Up` / `healthy`.

### 3. Clear Cache & Access the Site

```bash
# Rebuild Drupal cache via Drush
docker compose exec -T drupal vendor/bin/drush cr
```

Open **[http://localhost:8090](http://localhost:8090)** in your browser!

---

## 📦 Custom Module Structure

The core business logic, custom templates, and styling reside in `web/modules/custom/doctor_appointment/`:

```
web/modules/custom/doctor_appointment/
├── build/
│   └── input.css                 # Source CSS (Tailwind directives + Drupal protection)
├── css/
│   ├── tailwind.css              # Minified production bundle (~40KB)
│   └── password-toggle.css       # Password field eye toggle styling
├── js/
│   └── password-toggle.js        # Password reveal behavior
├── src/
│   └── Controller/
│       └── DashboardController.php # Dashboard routing controller
├── templates/
│   ├── page--front.html.twig               # Public landing page shell
│   ├── home-page.html.twig                 # Landing page hero & specialties
│   ├── page--doctor-appointment-dashboard.html.twig # Dashboard application shell
│   ├── doctor-dashboard.html.twig          # Doctor clinical queue workstation
│   ├── patient-dashboard.html.twig         # Patient consultation portal
│   ├── clinic-admin-dashboard.html.twig    # Clinic administration console
│   ├── page--user--login.html.twig         # Clean authentication card & quick-fills
│   ├── page--user--profile.html.twig       # User profile 3-column workspace
│   └── region--content.html.twig           # Unwrapped content region
├── doctor_appointment.info.yml   # Module declaration & Drupal 11 compatibility
├── doctor_appointment.libraries.yml # Asset library definitions
├── doctor_appointment.module     # Hooks (theme, suggestions, preprocess, form_alter)
├── doctor_appointment.permissions.yml # Role permission definitions
├── doctor_appointment.routing.yml# Application routes definition
├── package.json                  # Frontend dependencies (Tailwind CLI)
└── tailwind.config.js            # Tailwind scanning content globs
```

---

## 🎨 Tailwind CSS Build Workflow

All styles are managed through a unified, zero-conflict Tailwind pipeline:

```bash
# Navigate to the module directory
cd web/modules/custom/doctor_appointment

# Install dependencies (first time only)
npm install

# Rebuild minified production CSS
npm run build:css
```

### Zero-Conflict Architecture Principles

1. **Drupal Admin Protection (`@layer base`)**: Prevents Tailwind Preflight resets from distorting the Drupal Administration Toolbar (`#toolbar-administration`), admin dialogs (`.ui-dialog`), and off-canvas trays.
2. **Path-Scoped Breakouts (`@layer utilities`)**: Olivero container overrides (`max-width: 100%`) are explicitly scoped to application paths (`body.path-frontpage`, `body.path-user`, etc.) without touching administrative backend routes (`/admin/*`).
3. **Form Enhancement with `@apply`**: Drupal Core form elements (`.form-text`, `.form-submit`, `.form-item`) are styled cleanly via Tailwind utilities while maintaining native Drupal AJAX states and validation hooks.

---

## ⚡ Performance Optimization Highlights

The development stack was tuned to eliminate Windows filesystem bottlenecks:

- **PHP OPcache & APCu**: `opcache.memory_consumption=512`, `revalidate_freq=60`, and `apc.shm_size=128M` enabled in container PHP configuration.
- **RAM-Backed Twig & Container Cache**: Twig templates and Symfony container definitions execute from `/tmp/drupal_php` (Linux RAM/tmpfs) rather than the slower mounted host filesystem.
- **Optimized Composer Autoloader**: Static classmap (`vendor/composer/autoload_classmap.php`) with 6,300+ classes pre-indexed for O(1) resolution.
- **MySQL 8.0 InnoDB Tuning**: `innodb_buffer_pool_size=256M` and `innodb_flush_log_at_trx_commit=2` for frictionless transactional writes.
- **Static CSS Compilation**: Replaced dynamic 3.5MB client-side Tailwind CDN script with a minified 38KB static production bundle.

---

## 🛠 Handy Administrative Commands

Run these commands from PowerShell in your project root:

### Clear Drupal Cache
```powershell
docker compose exec -T drupal vendor/bin/drush cr
```

### Generate Instant One-Time Login Links
```powershell
# Admin
docker compose exec -T drupal vendor/bin/drush user:login --name=admin --uri=http://localhost:8090

# Doctor
docker compose exec -T drupal vendor/bin/drush user:login --name=test_doctor --uri=http://localhost:8090

# Patient
docker compose exec -T drupal vendor/bin/drush user:login --name=test_patient --uri=http://localhost:8090
```

### Reset Any User Password
```powershell
docker compose exec -T drupal vendor/bin/drush user:password <username> "<new_password>"
```

### Run Database Backup & Restore
```powershell
# Export database
docker compose exec -T db mysqldump -uroot -proot drupal > backup.sql

# Import database
docker compose exec -i db mysql -uroot -proot drupal < backup.sql
```

---

## 📚 Project Documentation

For deeper details regarding development phases, naming standards, and feature matrices:

- 📋 **[FEATURES.md](file:///e:/DockerProjects/drupal/FEATURES.md)** — Comprehensive MVP feature list and module requirements.
- 🗺️ **[ROADMAP.md](file:///e:/DockerProjects/drupal/ROADMAP.md)** — 9-phase software engineering roadmap, milestone tracker, and Drupal architecture standards.
- 📖 **[LEARNING-PLAN.md](file:///e:/DockerProjects/drupal/LEARNING-PLAN.md)** — Step-by-step Drupal 11 learning roadmap & vibe coding guide.
- 🔐 **[credentials.md](file:///e:/DockerProjects/drupal/credentials.md)** — Full access credentials, ports, and Drush command reference.

---

## 📄 License

This project is licensed under the [General Public License (GPL-2.0-or-later)](LICENSE.txt) matching Drupal Core.
