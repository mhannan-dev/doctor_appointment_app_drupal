# 🏥 Doctor Appointment Booking System: Development Roadmap
### Drupal 11 & Docker Architecture

> **Note:** The full development roadmap is maintained in [Features.md](file:///e:/DockerProjects/drupal/Features.md) and summarized below.

---

## 📑 Table of Contents
1. [Architecture & Tech Stack](#-architecture--tech-stack)
2. [Development Phases](#-development-phases)
   - [Phase 0: Infrastructure & Base Setup](#phase-0-infrastructure--base-setup)
   - [Phase 1: User Authentication, Roles & Permissions](#phase-1-user-authentication-roles--permissions)
   - [Phase 2: Doctor Profile & Specialty Management](#phase-2-doctor-profile--specialty-management)
   - [Phase 3: Scheduling & Dynamic Slot Engine](#phase-3-scheduling--dynamic-slot-engine)
   - [Phase 4: Appointment Booking & Status Tracking](#phase-4-appointment-booking--status-tracking)
   - [Phase 5: User & Doctor Dashboards](#phase-5-user--doctor-dashboards)
   - [Phase 6: Notifications & Communication](#phase-6-notifications--communication)
   - [Phase 7: Frontend Theming & UX](#phase-7-frontend-theming--ux)
   - [Phase 8: Security, QA & MVP Launch](#phase-8-security-qa--mvp-launch)
3. [Future Enhancements (v1.1+)](#-future-enhancements-v11)
4. [Milestone Checklist](#-milestone-checklist)

---

## 🛠 Architecture & Tech Stack

| Component | Technology | Purpose |
| :--- | :--- | :--- |
| **Core CMS** | Drupal 11.4+ | Content modeling, user entities, enterprise routing |
| **Database** | MySQL 8.0 | Relational data persistence & transactional consistency |
| **Containerization**| Docker & Docker Compose | Isolated PHP 8.3 & MySQL environment |
| **CLI & Tools** | Drush 13+, Composer 2.x | Automation, deployment, config sync |
| **Custom Module** | `doctor_appointment` | Booking logic, slot generator, state machine |
| **Core Systems** | Views, Form API, Workflows | Querying, dynamic UI, status transitions |
| **Frontend** | Tailwind CSS (CDN), Twig | Responsive layouts, modern clinical UI |

---

## 📐 Drupal Naming Conventions & Architecture Standards

All components, machine names, files, and schemas strictly adhere to [Drupal Coding Standards](https://www.drupal.org/docs/develop/standards) and naming conventions:

| Element | Drupal Convention | Implementation in this Project |
| :--- | :--- | :--- |
| **Custom Module** | `snake_case`, lowercase, no abbreviations | `doctor_appointment` (under `web/modules/custom/doctor_appointment`) |
| **Package** | `Custom` (for custom modules) | `package: Custom` in `.info.yml` |
| **User Roles** | `snake_case`, singular/role title | `patient`, `doctor`, `clinic_admin`, `administrator` |
| **Permissions** | `[verb] [subject]` or `administer [module]` | `access patient dashboard`, `access doctor dashboard`, `administer doctor appointment` |
| **Routes** | `<module_name>.<route_name>` | `doctor_appointment.patient_dashboard`, `doctor_appointment.doctor_dashboard` |
| **Controllers** | PascalCase in `src/Controller/` | `DashboardController.php` under `Drupal\doctor_appointment\Controller` |
| **Theme Hooks** | `snake_case`, matching Twig kebab-case | `patient_dashboard` ➔ `templates/patient-dashboard.html.twig` |
| **Asset Libraries**| `<module>/<library_name>` | `doctor_appointment/dashboard` defined in `doctor_appointment.libraries.yml` |
| **Content Types** | `snake_case`, singular, max 32 chars | `doctor_profile`, `appointment` |
| **Taxonomies** | `snake_case`, plural or descriptive | `specialties` (Medical Specialties), `chamber_locations` |
| **Entity Fields** | Prefix with `field_`, max 32 chars | `field_doctor_specialty`, `field_doctor_degrees`, `field_consultation_fee`, `field_slot_duration` |

---

## 🚀 Development Phases

### Phase 0: Infrastructure & Base Setup
- [x] Docker environment verification (`docker-compose up -d`) with healthy MySQL service.
- [x] Drupal 11 standard site installation via Drush (`drush site:install`).
- [ ] Git version control initialization with standard `.gitignore`.
- [x] Drupal configuration management sync setup (`config:export` / `config:import`).

### Phase 1: User Authentication, Roles & Permissions
- [x] Roles: `Patient`, `Doctor`, `Clinic Admin`, `Administrator`.
- [ ] Role-specific registration and onboarding workflows.
- [x] Role-based login redirection (Patients to `/patient/dashboard`, Doctors to `/doctor/dashboard`).
- [x] Access control & permission matrices for sensitive medical dashboards.

### Phase 2: Doctor Profile & Specialty Management
- [ ] Taxonomies: `Medical Specialties` (Cardiology, Medicine, etc.) & `Chamber Locations`.
- [ ] `doctor_profile` Content Type: Degrees, experience, chamber address, fee, slot duration.
- [ ] Public searchable doctor directory using Drupal Views with live filters.

### Phase 3: Scheduling & Dynamic Slot Engine
- [ ] Doctor weekly schedule data model (Working days, shift hours, max capacity, breaks).
- [ ] Slot Generator Service: Automated generation of 15m/30m slots based on doctor config.
- [ ] Real-time slot availability engine with concurrency lock to prevent double-booking.

### Phase 4: Appointment Booking & Status Tracking
- [ ] `appointment` entity schema: Doctor ref, Patient ref, Date, Slot, Chief complaint, Ref ID.
- [ ] Multi-step patient booking wizard (Doctor -> Date/Slot -> Patient Info -> Confirmation).
- [ ] State Machine Workflows: `Pending` ➔ `Confirmed` / `Cancelled` ➔ `Completed` / `No-Show`.
- [ ] Live status tracking with unique booking reference code.

### Phase 5: User & Doctor Dashboards
- [ ] **Patient Dashboard:** Upcoming appointments, consultation history, cancellation requests.
- [ ] **Doctor Console:** Daily patient queue, real-time status toggling (Confirm, Complete, Cancel).
- [ ] **Admin Console:** Global booking stats, doctor verification, dispute override.

### Phase 6: Notifications & Communication
- [ ] Automated transactional emails via Symfony Mailer on booking, confirmation, cancellation.
- [ ] 24-hour and 2-hour appointment reminder triggers.
- [ ] Visual dashboard badges and status updates.

### Phase 7: Frontend Theming & UX
- [ ] Mobile-first, responsive medical UI layout.
- [ ] Interactive datepicker & slot selection component with smooth micro-interactions.
- [ ] Accessible WCAG 2.1 compliant color palette and typography.

### Phase 8: Security, QA & MVP Launch
- [ ] Concurrency and stress testing on simultaneous slot reservations.
- [ ] Security audit: CSRF verification, cross-patient data isolation, sanitization.
- [ ] Drupal caching optimization (Dynamic page cache & BigPipe).
- [ ] MVP production deployment runbook.

---

## 🔮 Future Enhancements (v1.1+)
1. **Payment Gateway Integration:** bKash, Nagad, SSLCommerz, Stripe for consultation fees.
2. **Telemedicine / Video Consultation:** Embedded WebRTC / Jitsi / Zoom consultation.
3. **Digital Prescriptions & Diagnostic Reports:** In-portal prescription creation & report uploads.
4. **SMS Gateway:** Automated mobile SMS alerts for appointment confirmation.
5. **Multi-Clinic & Receptionist Role:** Chamber assistants managing physical queues.

---

## 📊 Milestone Checklist

| Milestone | Deliverables | Status |
| :--- | :--- | :---: |
| **M1: Core Setup & Roles** | Docker, Drupal 11 install, Patient & Doctor roles | `[x]` |
| **M2: Doctor Profile & Directory** | `doctor_profile` entity, specialties taxonomy, searchable view | `[ ]` |
| **M3: Scheduling & Slots** | Weekly schedule schema & slot generator service | `[ ]` |
| **M4: Booking Flow & Tracking** | Booking wizard, concurrency lock, state machine | `[ ]` |
| **M5: Portals & Dashboards** | Patient portal, doctor queue console, admin view | `[ ]` |
| **M6: Notifications & Theme** | Email triggers & responsive clinical UI theme | `[ ]` |
| **M7: QA & MVP Release** | Concurrency verification, security audit, deployment | `[ ]` |
