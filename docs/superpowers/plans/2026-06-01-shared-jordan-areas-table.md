# Shared Jordan Areas Table Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Create and seed a shared landlord `areas` table with the provided 59 Jordan area records.

**Architecture:** A Laravel migration creates the landlord table and its unique key. A dedicated Laravel seeder writes the shared lookup rows through the landlord connection with `updateOrInsert`, so reruns are safe.

**Tech Stack:** Laravel 8, PHP 8.2, MySQL/MariaDB, PHPUnit

---

### Task 1: Add the migration contract test

**Files:**
- Create: `tests/Feature/JordanAreasMigrationTest.php`

- [ ] **Step 1: Write a failing test**

Add a PHPUnit test that checks the migration and seeder files exist, the migration uses the landlord connection and expected columns, the seeder uses the landlord connection and `updateOrInsert`, and the seed dataset has 59 rows.

- [ ] **Step 2: Run the test to verify it fails**

Run: `php artisan test --filter=JordanAreasMigrationTest`

Expected: FAIL because the migration and seeder files do not exist.

### Task 2: Add the landlord migration and seeder

**Files:**
- Create: `database/migrations/2026_06_01_000001_create_areas_table.php`
- Create: `database/seeders/JordanAreasSeeder.php`

- [ ] **Step 1: Create the migration**

Use `Schema::connection(config('tenancy.landlord_connection', 'landlord'))` to create `areas` with `id`, `name`, `city`, `latitude`, `longitude`, and unique key `uq_areas_name_city`.

- [ ] **Step 2: Create the seeder**

Write the provided 59 rows with `DB::connection($connection)->table('areas')->updateOrInsert(...)`.

- [ ] **Step 3: Run the test to verify it passes**

Run: `php artisan test --filter=JordanAreasMigrationTest`

Expected: PASS.

### Task 3: Apply and verify locally

**Files:**
- No new files

- [ ] **Step 1: Run the isolated landlord migration**

Run: `php artisan migrate --database=landlord --path=database/migrations/2026_06_01_000001_create_areas_table.php --force`

Expected: migration succeeds without running unrelated pending migrations.

- [ ] **Step 2: Seed the landlord table twice**

Run: `php artisan db:seed --database=landlord --class=Database\\Seeders\\JordanAreasSeeder --force`

Run the same command again.

Expected: both runs succeed.

- [ ] **Step 3: Verify local grouped row counts**

Run a Laravel bootstrap query against the landlord connection.

Expected: 59 total rows with grouped counts: Ajloun 2, Amman 18, Aqaba 4, As-Salt 3, Irbid 8, Jerash 3, Karak 3, Ma'an 4, Madaba 3, Mafraq 3, Tafilah 2, Zarqa 6.

### Task 4: Apply and verify in production

**Files:**
- Upload: `database/migrations/2026_06_01_000001_create_areas_table.php`
- Upload: `database/seeders/JordanAreasSeeder.php`

- [ ] **Step 1: Find the remote Laravel root over SSH**

Use the configured `alsolentco-server` SSH alias and locate `artisan`.

- [ ] **Step 2: Upload only the migration and seeder**

Use SCP to place the two files in the matching production directories.

- [ ] **Step 3: Run the isolated production migration and seeder**

Run the same isolated migration and seeder commands with `--force`.

- [ ] **Step 4: Verify production grouped row counts**

Run a Laravel bootstrap query against the production landlord connection.

Expected: the same 59 rows and grouped city counts as local.

### Task 5: Final verification

**Files:**
- No new files

- [ ] **Step 1: Run the focused PHPUnit test**

Run: `php artisan test --filter=JordanAreasMigrationTest`

Expected: PASS.

- [ ] **Step 2: Review changed files**

Run: `git status --short`

Expected: this task adds only the plan, migration, seeder, and focused test, alongside pre-existing workspace changes.
