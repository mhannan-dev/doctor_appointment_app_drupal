# Drupal শেখার রোডম্যাপ

Doctor Appointment Booking System প্রজেক্ট বানাতে বানাতে, vibe coding ও fundamentals মিশিয়ে Drupal শেখার ধাপে ধাপে পরিকল্পনা।

(ইন্টারেক্টিভ, চেকবক্স-সহ ভার্সন: https://claude.ai/code/artifact/6d59109a-6418-42e3-ad62-d900cffb5d70)

## নিয়ম একটাই

কোনো টপিক নিজে ৫ মিনিটে অন্য কাউকে বুঝিয়ে দিতে না পারলে, সেটা AI দিয়ে জেনারেট করার আগে একবার ম্যানুয়ালি করে দেখুন।
**Boilerplate আর রিপিটিটিভ কাজে** vibe coding দুর্দান্ত — সময় বাঁচায়। কিন্তু **architecture, security, আর business logic**
(যেমন ডাবল-বুকিং আটকানো) — এগুলো নিজে হাতে লিখুন, নাহলে যেদিন এটা ভাঙবে সেদিন কেন ভাঙল বুঝতেও পারবেন না।

## Phase 00 — পরিবেশ প্রস্তুত (✅ সম্পন্ন)

* Composer দিয়ে `drupal/recommended-project` — Drupal 11.4.6, standard profile ইনস্টল করা
* Drush 13.8 কমান্ড-লাইন টুল যোগ করা হয়েছে
* দুই পরিবেশে চলছে: Laragon (Apache + MySQL, root/no-password) এবং Docker Compose (`drupal` + `db` সার্ভিস,
  পোর্ট 8090/3308) — একই `settings.php` environment variable দিয়ে দুই জায়গাতেই কাজ করে
* Admin লগইন: `admin / admin` — এখনই পাসওয়ার্ড বদলে নিন

## Phase 01 — সাইট বিল্ডিং বেসিকস (No-code, ~১ সপ্তাহ)

**ভিত্তি রুট**
* Content type কী, Entity ও Bundle কনসেপ্ট বুঝুন (Structure > Content types)
* Field UI দিয়ে ম্যানুয়ালি Text, Entity Reference, Number, Date — চারটে ভিন্ন ফিল্ড টাইপ যোগ করে দেখুন
* Views UI দিয়ে হাতে-কলমে একটা লিস্টিং পেজ বানান — প্রতিটা filter/sort/pager অপশন কী করে বোঝার চেষ্টা করুন
* Block ও Menu — থিম রিজিয়নে ম্যানুয়ালি ব্লক বসিয়ে রিজিয়ন কনসেপ্ট বুঝুন

**ভাইব কোডিং রুট**
* AI-কে বলুন: "Doctor Profile নামে content type বানাও — specialty, degree, experience, chamber address,
  consultation fee ফিল্ডসহ"
* AI যা বানালো তা Structure > Content types-এ গিয়ে যাচাই করুন — কোন ফিল্ড টাইপ বেছেছে, কেন
* একটা ফিল্ড ম্যানুয়ালি এডিট/ডিলিট করে দেখুন, যেন বুঝতে পারেন AI আসলে কী তৈরি করেছিল

**মাইলফলক:** "ডক্টর প্রোফাইল ও স্পেশালিটি ম্যানেজমেন্ট" — Features.md থেকে সরাসরি: নাম, স্পেশালিটি, ডিগ্রি, অভিজ্ঞতা,
চেম্বার ঠিকানা ও consultation fee সহ একটা কাজ করা content type।

## Phase 02 — ইউজার, রোল ও পারমিশন (No-code, ~৪-৫ দিন)

**ভিত্তি রুট**
* People > Roles-এ ম্যানুয়ালি Patient, Doctor, Admin রোল বানান, permission ম্যাট্রিক্স নিজে টিক দিয়ে সেট করুন
* Drupal-এর User entity ও "permission string" কনসেপ্ট পড়ুন (api.drupal.org: Access checking)
* একটা View বানান যেখানে "content authored by == current user" কন্টেক্সচুয়াল ফিল্টার দিয়ে ইউজার-স্পেসিফিক
  ড্যাশবোর্ড বুঝুন

**ভাইব কোডিং রুট**
* AI-কে বলুন তিনটে রোলের জন্য আলাদা ড্যাশবোর্ড ব্লক/ভিউ স্ক্যাফোল্ড করে দিতে
* জেনারেট হওয়া View-এর Access সেটিং (Permission access / Role access) ম্যানুয়ালি খুলে পড়ুন — এটা বাদ দিলে
  সিকিউরিটি হোল থেকে যেতে পারে

**মাইলফলক:** "রোগীর প্যানেলে upcoming/past বুকিং, ডাক্তারের প্যানেলে দৈনিক রোগীর তালিকা" — Features.md-এর
ড্যাশবোর্ড রিকোয়ারমেন্ট।

## Phase 03 — শিডিউল, স্লট ও Views গভীরে (হালকা কোড, ~১ সপ্তাহ)

**ভিত্তি রুট**
* Views-এর Relationship ও Contextual filter দিয়ে "এই ডাক্তারের খালি স্লট" লিস্ট বানান
* Date/Time field + Views date filter (range, recurring) কনসেপ্ট পড়ুন
* Config export/import (`drush cex` / `drush cim`) দিয়ে একটা View-এর YAML ফাইল হাতে খুলে পড়ুন —
  `config_sync_directory`-তে ঠিক কোথায় সেভ হয় দেখুন

**ভাইব কোডিং রুট**
* AI দিয়ে ড্রাফট করান: "১৫ মিনিট স্লটে সকাল ১০টা–দুপুর ২টা পর্যন্ত ডাক্তারের শিডিউল দেখানোর Views লজিক"
* জেনারেট হওয়া YAML config লাইন ধরে ধরে পড়ে বুঝুন — না বুঝে `drush cim` চালাবেন না

**মাইলফলক:** "শিডিউল ও স্লট ম্যানেজমেন্ট" + "অ্যাপয়েন্টমেন্ট বুকিং সিস্টেম" — দুটো ফিচারের ভিত্তিই এখানে তৈরি হবে।

## Phase 04 — থিমিং (Twig) (হালকা কোড, ~৪-৫ দিন)

**ভিত্তি রুট**
* `services.yml`-এ Twig debug মোড অন করে কোন টেমপ্লেট রেন্ডার হচ্ছে খুঁজে বার করুন
* একটা `node--doctor-profile.html.twig` ওভাররাইড হাতে লিখুন
* `hook_preprocess_node()` দিয়ে Twig-এ নতুন ভ্যারিয়েবল পাঠানো শিখুন

**ভাইব কোডিং রুট**
* AI-কে বুকিং কনফার্মেশন পেজের জন্য একটা Twig টেমপ্লেট ড্রাফট করতে বলুন
* তারপর একটা ভ্যারিয়েবল বদলে বা যোগ করে ব্রাউজারে রেজাল্ট নিজে চেক করুন

**মাইলফলক:** Confirmed / Pending / Cancelled স্ট্যাটাসের জন্য আলাদা ভিজুয়াল ব্যাজ — "স্ট্যাটাস ট্র্যাকিং" ফিচারের
UI অংশ।

## Phase 05 — কাস্টম মডিউল ডেভেলপমেন্ট (আসল কোড শুরু, ~২-৩ সপ্তাহ)

**ভিত্তি রুট**
* `.info.yml` + `.routing.yml` + একটা Controller — সম্পূর্ণ AI ছাড়া, নিজে হাতে "Hello Appointment" মডিউল বানান
* Drupal 11-এর নতুন OOP hook (`#[Hook]` attribute) বনাম পুরনো `hook_*()` প্যাটার্ন — `web/core`-এ থাকা আসল
  মডিউল কোড পড়ুন
* Form API দিয়ে ম্যানুয়ালি একটা বুকিং ফর্ম লিখুন (`buildForm` / `validateForm` / `submitForm`)
* Dependency Injection ও `services.yml` — constructor-এ Database service কেন ইনজেক্ট করা হয় বুঝুন
* ফর্ম সাবমিশনের সময় `$form_state->getValues()` ও raw request payload প্রিন্ট করে ডিবাগ করা শিখুন — নিচে
  "ফর্ম ডিবাগিং টুলকিট" দেখুন

**ভাইব কোডিং রুট**
* বয়লারপ্লেট (plugin attribute, `.yml` স্ট্রাকচার) জেনারেট করতে AI ব্যবহার করুন — সময় বাঁচবে
* কিন্তু ডাবল-বুকিং আটকানোর মূল লজিকটা নিজে লিখুন ও নিজে টেস্ট করুন — এটাই অ্যাপের সবচেয়ে গুরুত্বপূর্ণ business logic,
  ভুল হলে সরাসরি রোগীকে প্রভাবিত করবে

**মাইলফলক:** কাস্টম "Appointment Booking" মডিউল — Content Entity + স্ট্যাটাস workflow
(Pending → Confirmed → Cancelled)।

## Phase 06 — টেস্টিং, সিকিউরিটি ও ডিপ্লয়মেন্ট (প্রোডাকশন রেডি, ~১ সপ্তাহ)

**ভিত্তি রুট**
* একটা Kernel/Functional test নিজে হাতে লিখুন — ডাবল-বুকিং লজিক `assertEquals` দিয়ে ভেরিফাই করুন
* drupal.org/security অ্যাডভাইজরি সাবস্ক্রাইব করুন
* `config_sync_directory` দিয়ে dev → prod কনফিগ ডিপ্লয়মেন্ট ওয়ার্কফ্লো প্র্যাকটিস করুন (আপনার `settings.php`-এ
  এটা আগে থেকেই সেট আছে)

**ভাইব কোডিং রুট**
* AI দিয়ে টেস্ট স্কেলিটন (`setUp`, টেস্ট মেথডের নাম) জেনারেট করান
* কিন্তু assertion-এর ভ্যালু ও এজ-কেস (একই স্লটে দুইজন পেশেন্ট বুক করলে কী হবে?) নিজে ভেবে লিখুন

**মাইলফলক:** Docker Compose স্ট্যাক (`drupal` + `db`) দিয়ে CI-তে অটোমেটেড টেস্ট রান করানো।

## ফর্ম ডিবাগিং টুলকিট

ফর্ম সাবমিট করার পর কী ডেটা গেল তা দেখার কয়েকটা ধাপ — সহজ থেকে গভীরে।

* **Devel + Kint** — `composer require drupal/devel` তারপর `drush en devel devel_kint_extras -y`। এরপর
  `submitForm()`-এর ভেতর `ksm($form_state->getValues());` বসালে পুরো ফর্ম-ভ্যালু পেজেই সুন্দর করে প্রিন্ট হয়ে যাবে।
* **Raw request payload** — শুধু প্রসেসড ফর্ম ভ্যালু না, পুরো POST বডি দেখতে চাইলে:
  `dump(\Drupal::request()->request->all());` — Symfony VarDumper, কোরেই আছে, আলাদা মডিউল লাগে না।
* **AJAX / REST রিকোয়েস্ট** — রেসপন্স JSON হলে সার্ভার-সাইড `dump()` কাজে নাও লাগতে পারে। ব্রাউজারের
  DevTools > Network ট্যাবে রিকোয়েস্ট ক্লিক করে Payload/Request অংশে সরাসরি পাঠানো ডেটা দেখুন।
* **Persistent log** — রিডাইরেক্টের পর ডেটা হারিয়ে গেলে:
  `` \Drupal::logger('appointment_booking')->notice('Payload: @d', ['@d' => print_r($form_state->getValues(), TRUE)]); ``
  — পরে `/admin/reports/dblog` বা `drush watchdog:show` (`drush ws`, লাইভ tail করতে `drush ws --tail`) দিয়ে দেখুন।
* **Docker কন্টেইনারের log সরাসরি** — `docker compose logs -f drupal` অথবা কন্টেইনারের ভেতরে
  `docker compose exec drupal tail -f /var/log/apache2/error.log`।
* **Xdebug (সবচেয়ে নির্ভরযোগ্য)** — PHPStorm/VS Code দিয়ে `submitForm()`-এ breakpoint বসিয়ে `$form_state`
  পুরো অবজেক্ট হিসেবে ইন্সপেক্ট করুন — print statement ছাড়াই।

## রিসোর্স

* **drupal.org/docs/user_guide** — অফিসিয়াল ইউজার গাইড, সাইট বিল্ডিং থেকে শুরু করা ভালো জায়গা
* **api.drupal.org** — hook, Plugin, Entity API-এর রেফারেন্স ডকুমেন্টেশন
* **github.com/drupal/examples** — প্রতিটা কোর কনসেপ্টের ছোট, চলমান কোড উদাহরণ, লোকালি ইনস্টল করে পড়া যায়
* **web/core/modules** (এই প্রজেক্টেই আছে) — রিয়েল-ওয়ার্ল্ড প্যাটার্নের সেরা উদাহরণ, বিশেষ করে `node` ও `views` মডিউল
* **Drupalize.me** — স্ট্রাকচার্ড ভিডিও কোর্স (সাবস্ক্রিপশন), থিমিং ও মডিউল ডেভেলপমেন্টের জন্য ভালো
* **Drupal Slack — #drupal** — লাইভ কমিউনিটিতে প্রশ্ন করার জায়গা
