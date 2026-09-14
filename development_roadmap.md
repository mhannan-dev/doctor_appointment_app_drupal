# 🏥 Development Roadmap — Doctor Appointment Booking System (Drupal 11 & Docker)
## 🇧🇩 (বাংলা + English Step-by-Step Implementation Guide)

A comprehensive, production-ready roadmap for building the Doctor Appointment Booking application from scratch with custom module architecture and custom responsive theme (`medicare_theme`).

AI অ্যাসিস্ট্যান্ট বা ডেভেলপারদের জন্য স্ক্র্যাচ থেকে সম্পূর্ণ সিস্টেমটি (কাস্টম মডিউল + কাস্টম থিম + স্লট ইঞ্জিন + রোল ড্যাশবোর্ড) ডেভেলপ করার বিস্তারিত গাইড।

---

## 📊 প্রজেক্ট প্রগ্রেস ওভারভিউ (Progress Summary)

| Phase | Description | Status |
| :--- | :--- | :---: |
| **Phase 0** | Planning, Data Modeling & Feature Spec | `[x] Completed` |
| **Phase 1** | Docker Environment, Database & Drupal 11 Setup | `[x] Completed` |
| **Phase 2** | Custom Module Scaffold (`doctor_appointment`) & Entity Types | `[~] In Progress` |
| **Phase 3** | Custom Theme Scaffold (`medicare_theme`) & Global Layout | `[ ] Ready for Dev` |
| **Phase 4** | Scheduling & Dynamic Slot Engine (`SlotGeneratorService`) | `[ ] Ready for Dev` |
| **Phase 5** | Public Doctor Directory & Live Filters (`DirectoryController`) | `[ ] Ready for Dev` |
| **Phase 6** | Interactive Booking Wizard & Concurrency Engine (`BookingController`) | `[ ] Ready for Dev` |
| **Phase 7** | Role Dashboards (Patient, Doctor & Clinic Admin Portals) | `[ ] Ready for Dev` |
| **Phase 8** | Role Authentication & Event-driven Login Redirection | `[ ] Ready for Dev` |
| **Phase 9** | Security Hardening, Automated Testing & Config Export | `[ ] Pending` |

---

## 🚀 Phase 0 — Planning & Data Architecture (পরিকল্পনা ও ডেটা মডেলিং)

- [x] **Requirement Gathering & Feature Spec:** Defined in `FEATURES.md` (Patient booking, Doctor roster, Slot engine, Role consoles).
      *কী কী ফিচার লাগবে তা `FEATURES.md`-এ স্পেসিফাই করা হয়েছে।*
- [x] **Data Model Design:**
  - **Taxonomies:**
    - `specialties` (Cardiology, Dermatology, Orthopedics, Pediatrics, General Medicine, Neurology, Gynecology, ENT).
    - `chamber_locations` (Hospital/Clinic name, address, consultation room).
  - **Content Types:**
    - `doctor_profile`: Doctor name, photo, degrees, experience, fee, slot duration (15/20/30m), working days, shift start/end, linked user account.
    - `appointment`: Reference ID (`APT-YYYYMM-XXXX`), Doctor ref, Patient UID, Date, Time Slot, Patient details (name, phone, age, gender, complaint), Status (`pending`, `confirmed`, `completed`, `cancelled`, `no_show`).
- [x] **User Role Matrix:**
  - `Anonymous`: Public directory, view doctor profiles, book appointments, track by reference code.
  - `patient`: Patient dashboard, upcoming visits, booking history, cancel request.
  - `doctor`: Doctor clinical console, today's queue, confirm/complete appointments, daily schedule.
  - `clinic_admin`: Clinic management, all appointment records, system metrics, override actions.
  - `administrator`: Full Drupal backend access.

---

## 🐳 Phase 1 — Environment & Infrastructure Setup (ডকার ও ড্রুপাল ইনস্টলেশন)

- [x] **Drupal 11 Recommended Project:** Scaffolded using Composer (`drupal/recommended-project:^11.4`, Drush 13.8).
- [x] **Docker Stack:**
  - PHP 8.3 Apache with OPcache, APCu, GD, PDO MySQL, Zip, Intl (`Dockerfile`).
  - MySQL 8.0 on host port `3309` with persistent volume (`docker-compose.yml`).
  - Drupal web interface mapped to `http://localhost:8091`.
- [x] **Database & Site Installation:**
  - MySQL healthcheck configured.
  - Standard site installation completed via Drush (`vendor/bin/drush site:install standard`).
  - Environment verified and responding with HTTP 200 OK.

---

## 🧩 Phase 2 — Custom Module Architecture (`doctor_appointment`)

**Location:** `web/modules/custom/doctor_appointment/`

### ১. মডিউল ফাইল স্ট্রাকচার (File Structure):
```
web/modules/custom/doctor_appointment/
├── doctor_appointment.info.yml          # Module info, core: ^10 || ^11
├── doctor_appointment.module            # hook_theme(), preprocess hooks
├── doctor_appointment.install           # hook_install() -> create roles, fields, sample data
├── doctor_appointment.routing.yml       # All route definitions
├── doctor_appointment.permissions.yml   # Custom permission definitions
├── doctor_appointment.services.yml      # Dependency injection services
├── doctor_appointment.libraries.yml     # Module asset libraries
├── src/
│   ├── Controller/
│   │   ├── DirectoryController.php      # Doctor search & directory
│   │   ├── BookingController.php        # Interactive booking & slot reservation
│   │   ├── DashboardController.php      # Patient, Doctor & Admin portals
│   │   ├── AppointmentActionController.php # Status actions (Confirm, Cancel, Complete)
│   │   └── TrackController.php          # Public reference tracking
│   ├── Service/
│   │   └── SlotGeneratorService.php     # Dynamic slot calculations & concurrency locking
│   └── EventSubscriber/
│       └── LoginRedirectSubscriber.php  # Role-based dashboard redirect on login
└── templates/
    ├── doctor-directory.html.twig
    ├── doctor-profile-detail.html.twig
    ├── booking-wizard.html.twig
    ├── patient-dashboard.html.twig
    ├── doctor-dashboard.html.twig
    ├── clinic-admin-dashboard.html.twig
    └── appointment-track.html.twig
```

### ২. কিভাবে ডেভেলপ করবেন (How to Develop — Step-by-step):
- [ ] Create `doctor_appointment.info.yml` and declare dependencies on `node`, `taxonomy`, `user`, `datetime`, `options`.
      *`.info.yml`-এ মডিউলের নাম, টাইপ এবং কোর ভার্সন রিকোয়ারমেন্ট দিয়ে শুরু করো, প্রয়োজনীয় কোর মডিউল (node, taxonomy, user, datetime, options) dependency হিসেবে যোগ করো।*
- [ ] Write `doctor_appointment.permissions.yml` declaring `access patient dashboard`, `access doctor dashboard`, `access clinic admin dashboard`, and `administer doctor appointment`.
      *প্রতিটা role-specific dashboard-এর জন্য আলাদা permission ডিফাইন করো, যাতে পরে routing.yml-এ `_permission` requirement হিসেবে ব্যবহার করা যায়।*
- [ ] Write `doctor_appointment.routing.yml` mapping clean URLs (`/doctors`, `/doctor/{node}`, `/book-appointment/{node}`, `/patient/dashboard`, `/doctor/dashboard`, `/clinic-admin/dashboard`, `/appointment/track`).
      *প্রতিটা controller method-কে একটা clean URL path-এ ম্যাপ করো এবং `_permission`/`_role` requirement সেট করো যাতে ভুল role অন্যের dashboard-এ ঢুকতে না পারে।*
- [ ] Implement `doctor_appointment.install` to automatically create taxonomy terms, content types, and realistic seed data (sample doctors with schedules and fees).
      *`hook_install()`-এ প্রোগ্রামেটিকভাবে taxonomy term, content type field, এবং কিছু sample doctor node তৈরি করো — যাতে মডিউল enable করলেই ডেমো ডেটা রেডি থাকে।*
- [ ] Build each Controller one at a time (`DirectoryController` → `BookingController` → `DashboardController` → `AppointmentActionController` → `TrackController`), testing each route in the browser before moving to the next.
      *একবারে সব controller না বানিয়ে একটা একটা করে বানাও ও ব্রাউজারে টেস্ট করো — এতে বাগ ধরা সহজ হয়।*
- [ ] Enable the module via Drush:
  ```powershell
  docker compose exec -T drupal vendor/bin/drush en doctor_appointment -y
  ```

---

## 🎨 Phase 3 — Custom Theme Development (`medicare_theme`)

*একটি আধুনিক, ক্লিনিক্যাল এবং সম্পূর্ণ রেসপন্সিভ কাস্টম ড্রুপাল থিম তৈরি করা যা Tailwind CSS, আধুনিক টাইপোগ্রাফি এবং ড্যাশবোর্ড কম্পোনেন্ট সরবরাহ করে।*

**Location:** `web/themes/custom/medicare_theme/`

### ১. থিম ফাইল স্ট্রাকচার (Theme Structure):
```
web/themes/custom/medicare_theme/
├── medicare_theme.info.yml          # Theme metadata, base theme: false or claro/olivero
├── medicare_theme.libraries.yml     # Theme CSS/JS (Tailwind CDN, Google Fonts Inter, theme.css)
├── medicare_theme.theme             # Preprocess functions (hook_preprocess_page, hook_preprocess_html)
├── css/
│   └── theme.css                    # Custom styles, clinical color palette, badges, card animations
├── js/
│   └── theme.js                     # Mobile navbar toggle, modal dialogs, copy reference code
└── templates/
    ├── layout/
    │   ├── html.html.twig           # Master HTML wrapper with meta viewport and fonts
    │   └── page.html.twig           # Master layout: Clinical Navbar, main content, Footer
    └── navigation/
        └── header-nav.html.twig     # Role-aware navigation (Doctor Console / Patient Portal link)
```

### ২. থিম ডেভেলপমেন্টের বিস্তারিত পদক্ষেপ (Step-by-Step Instructions):

#### Step 3.1: Create `medicare_theme.info.yml`
```yaml
name: 'Medicare Doctor Portal Theme'
type: theme
description: 'A modern, clinical responsive theme for Doctor Appointment Booking.'
core_version_requirement: ^10 || ^11
base theme: false
libraries:
  - medicare_theme/global-styling
regions:
  header: 'Header'
  highlighted: 'Highlighted'
  content: 'Content'
  sidebar: 'Sidebar'
  footer: 'Footer'
```

#### Step 3.2: Create `medicare_theme.libraries.yml`
```yaml
global-styling:
  version: 1.0
  css:
    theme:
      https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap: { type: external }
      https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css: { type: external }
      css/theme.css: {}
  js:
    js/theme.js: {}
  dependencies:
    - core/drupal
    - core/drupalSettings
```

#### Step 3.3: Global Clinical Navbar & Layout (`templates/layout/page.html.twig`)
- **Brand Logo:** "MediCare Appointment System" with medical cross icon.
- **Navigation Links:**
  - `Find Doctors` (`/doctors`)
  - `Track Appointment` (`/appointment/track`)
- **Dynamic Role Badge & Links:**
  - If user has `patient` role ➔ Shows "My Appointments" (`/patient/dashboard`)
  - If user has `doctor` role ➔ Shows "Doctor Console" (`/doctor/dashboard`)
  - If user has `clinic_admin` or `administrator` ➔ Shows "Admin Dashboard" (`/clinic-admin/dashboard`)
  - If anonymous ➔ Quick login link or "Book Appointment" CTA.
- **Footer:** Emergency hotline, clinic working hours, copyright notice.

#### Step 3.4: Set as Default Theme:
```powershell
docker compose exec -T drupal vendor/bin/drush theme:enable medicare_theme -y
docker compose exec -T drupal vendor/bin/drush config:set system.theme default medicare_theme -y
docker compose exec -T drupal vendor/bin/drush cr
```

---

## ⏱️ Phase 4 — Scheduling & Dynamic Slot Engine (`SlotGeneratorService`)

*ডাক্তারের সাপ্তাহিক কর্মদিবস এবং শিফটের উপর ভিত্তি করে রিয়েল-টাইম বুকিং স্লট জেনারেশন।*

### ১. লজিক ও অ্যালগরিদম (Algorithm Details):
1. **Available Dates Calculation:**
   - ডক্টরের নোডে `field_working_days` থেকে কর্মদিবস রিড করা (যেমন: Sunday, Tuesday, Thursday)।
   - আগামী ১৪ দিনের মধ্যে যে দিনগুলো ডক্টরের কর্মদিবসের সাথে ম্যাচ করে সেগুলো রিটার্ন করা।
2. **Time Slot Generation:**
   - ডক্টরের শিফট স্টার্ট (`field_shift_start`, e.g., `17:00`) এবং শিফট এন্ড (`field_shift_end`, e.g., `21:00`) রিড করা।
   - স্লট ডিউরেশন (`field_slot_duration`, e.g., `20` মিনিট) অনুযায়ী পর্যায়ক্রমে সময় ভাগ করা:
     - `05:00 PM - 05:20 PM`
     - `05:20 PM - 05:40 PM`
     - ...
3. **Availability & Concurrency Check:**
   - ডাটাবেজে ওই ডক্টরের ওই তারিখের বুকিং কোয়েরি করা (`type = appointment`, `status != cancelled`).
   - কোনো স্লট বুক হয়ে থাকলে সেটিকে `booked = true` মার্ক করা।
   - সাবমিট করার সময় ট্রানজ্যাকশন লক দিয়ে নিশ্চিত করা যেন ডাবল বুকিং না হয়।

---

## 🔎 Phase 5 — Public Doctor Directory & Live Filters (`DirectoryController`)

*পাবলিক রোগীদের জন্য ডাক্তার খোঁজা, স্পেশালিটি ও লোকেশন অনুযায়ী ফিল্টার করার ব্যবস্থা।*

### ১. ফিচার উপাদানসমূহ (Components):
- **সার্চ বার:** ডাক্তারের নাম বা ডিগ্রি দিয়ে লাইভ কীওয়ার্ড সার্চ।
- **স্পেশালিটি ফিল্টার:** Cardiology, Medicine, Pediatrics ইত্যাদি ড্রপডাউন বা ব্যাজ ফিল্টার।
- **চেম্বার লোকেশন ফিল্টার:** বিভিন্ন হাসপাতাল বা শাখা ক্লিনিক নির্বাচন।
- **ডক্টর কার্ড ইউআই (Doctor Card Component):**
  - ডাক্তারের ছবি / অ্যাভাটার
  - ডিগ্রি এবং স্পেশালিটি ব্যাজ (Clinical Teal)
  - চেম্বারের রুম ও ঠিকানা
  - ভিজিট ফি (Consultation Fee, e.g., ৳800)
  - স্লট ডিউরেশন (যেমন: ২০ মিনিট)
  - অ্যাকশন বাটন: `Book Appointment` (লিংক: `/book-appointment/{id}`) এবং `View Profile`.

---

## 📅 Phase 6 — Interactive Booking Wizard & Concurrency Engine (`BookingController`)

*৩-ধাপের সহজ ও আকর্ষণীয় অ্যাপয়েন্টমেন্ট বুকিং উইজার্ড।*

### ১. বুকিং ধাপসমূহ (Wizard Steps):
- **Step 1: তারিখ নির্বাচন (Select Date):**
  - আগামী দিনগুলোর মধ্যে ডক্টরের অ্যাভেইলেবল তারিখের বাটন/চিপস। তারিখ সিলেক্ট করলে তাৎক্ষণিক ওই দিনের স্লট লোড হবে।
- **Step 2: স্লট নির্বাচন (Pick Time Slot):**
  - সময় স্লটগুলো গ্রিড আকারে প্রদর্শিত হবে (সবুজ = Available, ধূসর/স্ট্রাইকথ্রু = Already Booked)।
  - রোগী খালি স্লটে ক্লিক করলে তা অ্যাক্টিভ/হাইলাইট হবে।
- **Step 3: রোগীর তথ্য ইনপুট (Patient Details):**
  - নাম, মোবাইল নম্বর, বয়স, জেন্ডার, এবং রোগের প্রধান লক্ষণ (Chief Complaint)।
- **Step 4: বুকিং নিশ্চিতকরণ ও রেফারেন্স তৈরি:**
  - সাবমিশনের পর অটোমেটিক ইউনিক রেফারেন্স তৈরি (`APT-202609-XXXXX`)।
  - সফল বুকিংয়ের পর ট্র্যাকিং পেজে রিডাইরেক্ট এবং রেফারেন্স নম্বর প্রদর্শন।

---

## 📊 Phase 7 — Role-Based Dashboards & Portals

### ১. পেশেন্ট পোর্টাল (`/patient/dashboard`):
- **হেডার স্ট্যাটস:** মোট অ্যাপয়েন্টমেন্ট সংখ্যা, আসন্ন বুকিং, সম্পন্ন কনসালটেশন।
- **আপকামিং অ্যাপয়েন্টমেন্টস:** ডাক্তারের নাম, তারিখ, সময় স্লট, চেম্বারের ঠিকানা এবং স্ট্যাটাস ব্যাজ (Confirmed / Pending)।
- **কুইক অ্যাকশন:** সরাসরি `Cancel Appointment` করার সুবিধা।
- **হিস্ট্রি ট্যাব:** পূর্ববর্তী সকল ভিজিটের রেকর্ড।

### ২. ডক্টর ক্লিনিক্যাল কনসোল (`/doctor/dashboard`):
- **আজকের রোগী কিউ (Today's Live Queue):** আজকের সিরিয়াল নম্বর, রোগীর নাম, মোবাইল, বয়স, সমস্যার বিবরণ।
- **ওয়ান-ক্লিক স্ট্যাটাস কন্ট্রোল:**
  - `Mark Completed` বাটন (পরামর্শ সম্পন্ন হলে)।
  - `Confirm` বাটন (পেন্ডিং বুকিং কনফার্ম করতে)।
  - `Cancel` বাটন।
- **শিডিউল সামারি:** আজকের মোট রোগী এবং বাকি থাকা রোগীর কাউন্টার।

### ৩. ক্লিনিক অ্যাডমিন কনসোল (`/clinic-admin/dashboard`):
- **সিস্টেম মেট্রিক্স:** মোট ডাক্তার সংখ্যা, আজকের বুকিং, মোট সফল অ্যাপয়েন্টমেন্ট, বাতিল হওয়া বুকিং।
- **মাস্টার অ্যাপয়েন্টমেন্ট টেবিল:** তারিখ, ডাক্তার, রোগী, ফোন এবং স্ট্যাটাস ফিল্টার সহ সার্চ।
- **ম্যানেজমেন্ট অ্যাকশন:** যেকোনো বুকিংয়ের স্ট্যাটাস ওভাররাইড করার ক্ষমতা।

### ৪. পাবলিক ট্র্যাকিং পেজ (`/appointment/track`):
- যেকোনো রোগী লগইন ছাড়া শুধু রেফারেন্স নম্বর (`APT-...`) দিয়ে তার বুকিংয়ের বর্তমান অবস্থা যাচাই করতে পারবেন।

---

## 🔐 Phase 8 — Authentication & Role Redirection (`LoginRedirectSubscriber`)

- [ ] ড্রুপাল ইভেন্ট সাবস্ক্রাইবার `LoginRedirectSubscriber` তৈরি করা।
- [ ] ইউজার লগইন করার সাথে সাথে তার রোল চেক করা:
  - `doctor` ➔ `/doctor/dashboard`-এ রিডাইরেক্ট।
  - `patient` ➔ `/patient/dashboard`-এ রিডাইরেক্ট।
  - `clinic_admin` ➔ `/clinic-admin/dashboard`-এ রিডাইরেক্ট।
  - `administrator` ➔ ড্রুপাল স্ট্যান্ডার্ড অ্যাডমিনে প্রবেশ।

---

## 🧪 Phase 9 — Testing, Security & MVP Launch

### ১. কনকারেন্সি ও ভ্যালিডেশন টেস্ট:
- একই স্লটে দুইজন রোগী এক সাথে সাবমিট করলে দ্বিতীয় জন "Slot already taken" সতর্কবার্তা পাবে কিনা তা যাচাই।
- তারিখ বা ফোন নম্বর ছাড়া সাবমিট করলে প্রপার ফর্ম ভ্যালিডেশন প্রদর্শন।

### ২. সিকিউরিটি অডিট:
- কোনো পেশেন্ট যেন অন্য পেশেন্টের ব্যক্তিগত ডেটা দেখতে বা এডিট করতে না পারে (Access Control via Node Grants / Controller checks).
- XSS ও CSRF প্রতিরোধে Form API ও Drupal sanitization ব্যবহার।

### ৩. কনফিগারেশন এক্সপোর্ট:
```powershell
docker compose exec -T drupal vendor/bin/drush config:export -y
```

---

## 💡 AI-Assisted Prompting Tips (কার্যকরভাবে কাজ করার টিপস)

1. **Feature-by-Feature Implementation:** পুরো অ্যাপ একসাথে তৈরি না করে প্রথমে Module Setup ➔ Custom Theme ➔ Directory ➔ Booking Wizard ➔ Dashboards ধারাবাহিকভাবে সম্পন্ন করুন।
2. **Clear Cache Regularly:** রাউটিং, টেমপ্লেট বা সার্ভিস পরিবর্তনের পর `vendor/bin/drush cr` চালাতে ভুলবেন না।
3. **Commit Cleanly:** প্রতি ফেজের কাজ শেষে Git-এ মিনিংফুল মেসেজ সহ কমিট করুন (`git commit -m "feat: implement slot generator engine"`).
