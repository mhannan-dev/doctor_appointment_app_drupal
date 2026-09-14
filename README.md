# 🏥 Doc Appointment Drupal (Fresh Drupal 11 Core Project)

A clean, fresh Drupal 11 installation ready for custom theme and module development.

---

## 🛠 Tech Stack & Environment

- **Core CMS:** Drupal 11 (Standard Recommended Project template)
- **CLI:** Drush 13.8.x
- **PHP:** 8.3 (Apache) with OPcache, APCu, GD, PDO MySQL, Zip, Intl
- **Database:** MySQL 8.0
- **Containerization:** Docker & Docker Compose

---

## 🚀 How to Run the App

### 1. Start Docker Containers

From this directory (E:\DockerProjects\doc_appointment_drupal):

`powershell
docker compose up -d
`

- **Drupal Web Interface:** [http://localhost:8091](http://localhost:8091)
- **MySQL Port (Host):** 3309 (Database: drupal, User: drupal, Password: drupal, Root Password: oot)

### 2. Install Drupal via Drush (or via Web Installer)

`powershell
docker compose exec -T drupal vendor/bin/drush site:install standard --db-url=mysql://drupal:drupal@db:3306/drupal --site-name=Doctor Appointment --account-name=admin --account-pass=admin -y
`

### 3. Clear Cache

`powershell
docker compose exec -T drupal vendor/bin/drush cr
`
