# Wedspiration — Roadmap

Step-by-step plan for building Wedspiration, based on [wedspiration_assignment.md](wedspiration_assignment.md) (what we promised to build) and [project_assessment_criteria.md](project_assessment_criteria.md) (what will be graded).

Each phase ends with one or more **atomic commits**. Tick the boxes as you go.

---

## Starting point

Already in place from the starter pack:

- Laravel 13 + Breeze auth (login, register, logout, profile)
- `User` model, migration and factory
- Layouts (`layouts/app`, `layouts/guest`) with navigation
- `WelcomeController`, `Userzone\DashboardController`, `Userzone\ProfileController`
- Pest tests for auth and profile

Not in place yet:

- Git repository (the project folder is not under git yet)
- `WeddingFolder` and `Photo` models, the `folder_photo` pivot, admin flag
- All Wedspiration routes, controllers and views

---

## Phase 0 — Git & GitHub setup

**Criteria:** A. Code management

- [ ] `git init` and check that `.gitignore` excludes `.env`, `vendor/`, `node_modules/`, `database/database.sqlite`
- [ ] First commit: the untouched starter pack
- [ ] Create a **public** GitHub repository and push
- [ ] From here on: one commit per small functional step (e.g. "Add Photo model, migration and factory", not "work on photos")

---

## Phase 1 — Data model

**Criteria:** B. Model (3+ models, 1-N relations, Model + Migration + Factory for each)

### 1.1 User: admin flag
- [ ] Migration: add `is_admin` boolean (default `false`) to `users`
- [ ] Add `is_admin` to the model's casts
- [ ] Factory state `admin()`

### 1.2 WeddingFolder
- [ ] `php artisan make:model WeddingFolder -mf` (model, migration, factory)
- [ ] Columns: `user_id` (FK → users, cascade on delete), `name`, `notes` (nullable text), `wedding_date` (nullable date)
- [ ] Cast `wedding_date` to `date`

### 1.3 Photo
- [ ] `php artisan make:model Photo -mf`
- [ ] Columns: `user_id` (FK → users), `title`, `description` (nullable), `category` (nullable), `image_path`
- [ ] Decide the categories (e.g. Ceremony, Reception, Dress, Flowers, Venue, Details) — keep them in one place (config, constant or enum) so validation and forms share them

### 1.4 Pivot `folder_photo`
- [ ] Migration with `folder_id` and `photo_id` (both FKs, cascade on delete) + composite primary/unique key
- [ ] ⚠️ The model is called `WeddingFolder` but the pivot uses `folder_id` / table `folder_photo` — Laravel would guess `wedding_folder_photo` and `wedding_folder_id`. Pass the table and key names explicitly in both `belongsToMany()` calls (or rename consistently).

### 1.5 Relationships
- [ ] `User` → `hasMany` WeddingFolder, `hasMany` Photo
- [ ] `WeddingFolder` → `belongsTo` User, `belongsToMany` Photo
- [ ] `Photo` → `belongsTo` User, `belongsToMany` WeddingFolder
- [ ] Quick check in tinker that each relation returns what you expect

---

## Phase 2 — Seeding

**Criteria:** B. Seeded data, factories called in `DatabaseSeeder`, evaluation via `php artisan migrate:fresh --seed`

- [ ] `WeddingFolderFactory` and `PhotoFactory` produce realistic wedding data
- [ ] **Images must work right after `migrate:fresh --seed`** on the evaluator's machine. Pick one:
  - seed `image_path` with external placeholder URLs, or
  - commit a few sample images (e.g. in `public/images/seed/`) and point seeded photos at them
- [ ] `DatabaseSeeder`:
  - [ ] admin user `admin@admin.com` / `password` with `is_admin = true`
  - [ ] several regular users, each with a wedding folder
  - [ ] photos per user, each attached to its owner's folder (same rule as the upload flow)
  - [ ] give the admin a folder + photos too, so the userzone isn't empty when the teacher logs in as admin
- [ ] Run `php artisan migrate:fresh --seed` and check the data

---

## Phase 3 — Public pages

**Criteria:** Welcome page relevant to the app; every route → controller; layout used

- [ ] Public layout (or reuse `guest`/`app`) with navigation: Home, Gallery, About, Contact, Login/Register
- [ ] `welcome` — rewrite `welcome.blade.php`: what Wedspiration is + a preview of the latest photos (passed in from `WelcomeController`)
- [ ] `gallery.index` — grid of all public photos (paginate, eager-load the uploader)
- [ ] `gallery.show (id)` — one photo with title, description, category, uploader name
- [ ] `about` and `contact` — static pages, still routed through a controller (not `Route::view`)
- [ ] Commit per page/feature

---

## Phase 4 — Userzone: folders & photo upload

**Criteria:** Auth info in a view and in a controller; validation; form feedback; authorisation

### 4.1 Dashboard & folders
- [ ] Rename/alias the dashboard route to `user.dashboard` to match the application map (update Breeze redirects and nav links)
- [ ] Dashboard shows the logged-in user's folders → **auth info in a view** ("Welcome, {name}")
- [ ] `user.folders.show (id)` — folder details + its photos
- [ ] Only the owner may view a folder → **auth info in a controller** (policy or ownership check, 403 otherwise)
- [ ] Decide: what if a user has no folder yet? (create one on registration, or show a "create folder" prompt)

### 4.2 Photo upload & management
- [ ] `user.photos.create` — upload form (title, description, category, image file)
- [ ] `user.photos.store` — validate (Form Request), store the file on the `public` disk, create the photo with `user_id` = logged-in user, **attach it to the owner's folder**
- [ ] `php artisan storage:link` (and mention it in the README for the evaluator)
- [ ] `user.photos.edit` / `user.photos.update` — only own photos; image replace optional
- [ ] `user.photos.destroy` — only own photos; also delete the stored file
- [ ] Forms show validation errors per field and keep old input (`old()`, `<x-breeze.input-error>`)
- [ ] Success flash messages after store/update/destroy

---

## Phase 5 — Admin area

**Criteria:** At least one model with **full CRUD (all 7 methods)** — admin photos (and users/folders) cover this

- [ ] Middleware (or gate) that only lets `is_admin` users through; register it on an `admin` route group with `admin.` name prefix
- [ ] `admin.dashboard` — counts of users, folders, photos
- [ ] `admin.photos.*` — full resource controller: `index`, `show`, `create`, `store`, `edit`, `update`, `destroy` ✅ full CRUD
- [ ] `admin.users.*` — resource controller (validate unique email, hash password, toggle `is_admin`)
- [ ] `admin.folders.*` — resource controller (choose owner from a select)
- [ ] Admin link in the navigation only visible for admins (another use of auth info in a view)
- [ ] All admin forms validated with feedback

> If time gets short: `admin.photos.*` with all 7 methods is the must-have; users and folders can be reduced.

---

## Phase 6 — Tests

Pest feature tests for the important behaviour:

- [ ] Guests can see the gallery and a photo detail page
- [ ] Guests are redirected from userzone and admin routes
- [ ] Uploading a photo stores it and attaches it to the owner's folder (`Storage::fake()`)
- [ ] Upload validation rejects missing title / non-image file
- [ ] A user cannot edit/delete someone else's photo or view someone else's folder (403)
- [ ] Non-admins get 403 on admin routes; admin can access them
- [ ] Run with `php artisan test --compact`

---

## Phase 7 — Polish

- [ ] Consistent Tailwind styling across public, userzone and admin pages
- [ ] Empty states ("No photos yet — upload your first inspiration")
- [ ] Category filter on the gallery (nice to have)
- [ ] `vendor/bin/pint` for code style
- [ ] Remove leftover `// Todo` comments and unused starter code

---

## Phase 8 — Hand-in & defense

### Final check against the criteria
- [ ] Fresh clone → `composer install` → `.env` → `php artisan key:generate` → `php artisan migrate:fresh --seed` → `php artisan storage:link` works without errors
- [ ] Welcome page shows Wedspiration content
- [ ] Every route in `php artisan route:list` opens without errors when logged in as `admin@admin.com`
- [ ] Every route points to a controller method
- [ ] Every form validates and shows errors
- [ ] App matches the assignment (models, relations, routes) — or the differences are documented
- [ ] README updated: short project description + install steps for the evaluator
- [ ] Final push to GitHub; commit history shows small, meaningful steps

### Defense prep (15 min, live)
- [ ] Be able to point to, and explain:
  - the 1-N relations and the many-to-many pivot (and why it needs explicit key names)
  - where the upload validates input and attaches the photo to the folder
  - where the logged-in user is used in a controller (ownership check) and in a view
  - the admin middleware/gate
  - the `DatabaseSeeder` and factories
- [ ] Practise small live changes: add a field to a form + validation, add a column via migration, change a route, add a category

---

## Criteria → roadmap mapping

| Criterion | Where |
|-----------|-------|
| Git + public GitHub, atomic commits | Phase 0, every phase |
| Implementation matches description | Phases 1–5, final check |
| ≥ 3 models incl. User | Phase 1 (User, WeddingFolder, Photo) |
| ≥ 1 one-to-many relation | Phase 1.5 (User → folders, User → photos) |
| Model + Migration + Factory per model | Phase 1 |
| Factories called in `DatabaseSeeder` | Phase 2 |
| Every route → controller | Phases 3–5 |
| Full CRUD for ≥ 1 model | Phase 5 (`admin.photos.*`) |
| Validate all user input + form feedback | Phases 4.2, 5 |
| Login / register | Starter pack (Breeze) |
| Auth info in a view | Phase 4.1 dashboard, Phase 5 admin nav |
| Auth info in a controller | Phase 4 ownership checks, Phase 5 admin check |
| Layout for common elements | Phase 3 |
| Seeded admin `admin@admin.com` / `password` | Phase 2 |
| Welcome page relevant to the app | Phase 3 |
