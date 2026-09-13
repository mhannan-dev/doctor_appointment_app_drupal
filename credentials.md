# 🔐 Project Credentials & Access Guide
### Doctor Appointment Booking System (Drupal 11 & Docker)

This document contains all default credentials, access endpoints, database connection parameters, and Drush administrative commands for local development and testing.

---

## 🌐 Application URLs

| Service / Endpoint | URL | Description |
| :--- | :--- | :--- |
| **Site Homepage** | [http://localhost:8090](http://localhost:8090) | Public landing page & doctor directory |
| **User Login** | [http://localhost:8090/user/login](http://localhost:8090/user/login) | Standard authentication form |
| **User Logout** | [http://localhost:8090/user/logout](http://localhost:8090/user/logout) | Session termination |
| **Doctor Console** | [http://localhost:8090/doctor/dashboard](http://localhost:8090/doctor/dashboard) | Clinical queue & chamber management |
| **Patient Portal** | [http://localhost:8090/patient/dashboard](http://localhost:8090/patient/dashboard) | Booking history & specialist appointment booking |
| **Clinic Admin Console** | [http://localhost:8090/clinic-admin/dashboard](http://localhost:8090/clinic-admin/dashboard) | Doctor roster & global appointment oversight |
| **Drupal Admin Panel** | [http://localhost:8090/admin](http://localhost:8090/admin) | Drupal core site administration (Admin only) |

---

## 👥 User Accounts & Roles

| Role | Username | Email | Password | Default Redirect Route |
| :--- | :--- | :--- | :--- | :--- |
| 👑 **Administrator** | `admin` | `admin@example.com` | `Test@1234` | Drupal Core Admin `/admin` |
| 🩺 **Doctor** | `test_doctor` | `doctor@example.com` | `Test@1234` | `/doctor/dashboard` |
| 🧑‍⚕️ **Patient** | `test_patient` | `patient@example.com` | `Test@1234` | `/patient/dashboard` |

> **Note on Login Redirection:**  
> The custom `doctor_appointment` module automatically redirects users to their role-specific dashboard upon login via `doctor_appointment_user_login()`.

---

## 🗄️ Database Credentials (MySQL 8.0)

| Parameter | Inside Docker Network | From Windows Host (GUI / DBeaver / Navicat) |
| :--- | :--- | :--- |
| **Host** | `db` | `localhost` or `127.0.0.1` |
| **Port** | `3306` | `3308` |
| **Database Name** | `drupal` | `drupal` |
| **Database User** | `drupal` | `drupal` |
| **User Password** | `drupal` | `drupal` |
| **Root Password** | `root` | `root` |

---

## 🛠️ Handy Administrative Drush Commands

Run these commands from PowerShell inside `e:\DockerProjects\drupal`:

### 1. Generate One-Time Instant Login Link (No password needed)
```powershell
# For Admin:
docker compose exec -T drupal vendor/bin/drush user:login --name=admin --uri=http://localhost:8090

# For Doctor:
docker compose exec -T drupal vendor/bin/drush user:login --name=test_doctor --uri=http://localhost:8090

# For Patient:
docker compose exec -T drupal vendor/bin/drush user:login --name=test_patient --uri=http://localhost:8090
```

### 2. Reset / Change User Password
```powershell
docker compose exec -T drupal vendor/bin/drush user:password <username> "<new_password>"

# Example:
docker compose exec -T drupal vendor/bin/drush user:password test_doctor "MyNewPassword123"
```

### 3. Create a New User and Assign Role
```powershell
# 1. Create User:
docker compose exec -T drupal vendor/bin/drush user:create new_user --mail="user@example.com" --password="password123"

# 2. Assign Role (patient, doctor, or clinic_admin):
docker compose exec -T drupal vendor/bin/drush user:role:add doctor new_user
```

### 4. Clear & Rebuild Drupal Cache
```powershell
docker compose exec -T drupal vendor/bin/drush cr
```
