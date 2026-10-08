# Wedspiration

A visual inspiration archive for couples planning a wedding and the photographer working with them.

Couples register, upload the photos that inspire them and collect them in a personal **wedding folder**. Every uploaded photo is public: it appears in the **gallery** for everyone and in the uploader's folder, which doubles as a visual brief for the photographer.

Built for the PHP/Laravel course, based on the [assignment description](wedspiration_assignment.md).

---

## Installation (for the evaluator)

Requirements: PHP 8.3+, Composer. No Node/npm needed — Tailwind CSS and Alpine.js are loaded from a CDN.

```bash
git clone https://github.com/oskarholzmann-dot/wedspiration.git
cd wedspiration

composer install
cp .env.example .env
php artisan key:generate

touch database/database.sqlite
php artisan migrate:fresh --seed

php artisan storage:link
```

> **`php artisan storage:link` is recommended.** Uploaded photos are stored in `storage/app/public/photos`; the link lets the web server send them directly under `/storage/...`. Without it, Laravel serves them itself (`StorageFileController`), which also works, just a little slower.

Then open the site (e.g. with Laravel Herd at `http://wedspiration.test`, or with `php artisan serve` at `http://localhost:8000`).

### Logins

All seeded accounts use the password **`password`**.

| Email | Role |
|-------|------|
| `admin@admin.com` | Admin — sees everything, including the admin area |
| `couple@example.com` | Regular user — useful to check what non-admins can't do |

### Notes

- **Seeded images** are 24 free wedding photos from Unsplash, included in `public/images/seed`, so they work offline. Photographers are credited in [CREDITS.md](public/images/seed/CREDITS.md).
- **Uploads** are limited to **2 MB** per image, PHP's default `upload_max_filesize`.

---

## Features

| Area | What you can do |
|------|-----------------|
| **Public** | Welcome page with the latest photos, gallery with a Photos / Folders switch and category filter, photo and folder pages, about & contact |
| **Account** | Register, log in, edit profile (Laravel Breeze). Every new user gets a wedding folder automatically. |
| **Userzone** | Dashboard, folder page, rename your folder, upload many photos at once (large photos are shrunk in the browser), edit/replace/delete your own photos |
| **Admin** | Dashboard with counts, full CRUD for photos, users and folders, bulk delete, subcategories with drag and drop in the gallery |

## Data model

```
User ──1-N──> WeddingFolder   (name, notes, wedding_date)
User ──1-N──> Photo           (title, description, category, image_path)
WeddingFolder <──N-M──> Photo (pivot table: folder_photo)
Subcategory ──1-N──> Photo    (name; photos.subcategory_id is optional)
```

- An uploaded photo is automatically attached to its owner's folder (`User::defaultFolder()`).
- Photo categories live in one place: the `App\Enums\PhotoCategory` enum.
- `User::is_admin` marks administrators.
- Admins create subcategories in the gallery and drag photos onto them; each photo is in at most one subcategory.

## Routes

| Area | Routes |
|------|--------|
| Public | `welcome`, `gallery.index`, `gallery.show`, `gallery.folders`, `gallery.folders.show`, `about`, `contact` |
| Auth | `login`, `register`, `logout` (+ Breeze password routes) |
| Userzone (`auth`) | `user.dashboard`, `user.folders.show/edit/update`, `user.photos.create/store/edit/update/destroy` |
| Admin (`auth` + `admin`) | `admin.dashboard`, `admin.photos.*`, `admin.users.*`, `admin.folders.*` (all 7 resource methods each) |

See all of them with `php artisan route:list --except-vendor`.

## Authorisation

- **Middleware** `admin` (`App\Http\Middleware\EnsureUserIsAdmin`) protects the whole admin area.
- **Policies** make sure users only see their own folders (`WeddingFolderPolicy`) and only edit or delete their own photos (`PhotoPolicy`).

## Tests

```bash
php artisan test
```

Pest feature tests cover the public pages, upload and validation, ownership checks (403 for other users' content), the admin area and the seeder.

---

## About this project

- Based on the course's **Laravel educational starter pack** (Laravel 13, Breeze, Pest, Tailwind & Alpine via CDN, Debugbar).
- Developed with **Claude Code** as an AI coding assistant. Commits it co-authored are marked with a `Co-Authored-By: Claude` line.

### AI-generated extras beyond the course requirements

Everything that goes far beyond the minimal requirements was written by Claude Code and is marked in the code with a comment that starts with **`AI-GENERATED (beyond course scope)`**. List every marked place with:

```bash
grep -rn "AI-GENERATED" app config database resources routes public/js tests
```

| Extra | Main files |
|-------|------------|
| Multi and folder upload, photos shrunk in the browser | `public/js/photo-upload.js`, `userzone/photos/create.blade.php`, `StorePhotoRequest::prepareForValidation()` |
| Saving other couples' photos into your folder (button / drag and drop) | `SavedPhotoController`, `public/js/save-photos.js`, `components/save-photo-button.blade.php` |
| Gallery folder view and public folder pages | `GalleryController::folders()` / `folder()`, `gallery/folders.blade.php`, `gallery/folder.blade.php` |
| Users editing / deleting their own folder | `Userzone/FolderController::edit()` / `update()` / `destroy()`, `UpdateFolderRequest` |
| Lightbox, infinite scrolling, stable masonry layout | `public/js/lightbox.js`, `public/js/infinite-scroll.js`, `public/js/masonry.js`, `Photo::imageSize` |
| Subcategories with drag-and-drop sorting (incl. selecting several photos) | `Subcategory` model, migrations, factory, `Admin/SubcategoryController`, `public/js/admin-sorting.js` |
| Bulk delete in the admin lists, delete buttons on photo tiles | `bulkDestroy()` in `Admin/FolderController` and `Admin/PhotoController`, `components/photo-card.blade.php` |
| Versioned script URLs, serving uploads without `storage:link` | `components/script.blade.php`, `StorageFileController` |

The parts that implement the course requirements (models, relationships, migrations, factories, seeder, routes and controllers, the full CRUD in the admin area, validation, authentication and authorisation, layouts) are not marked.
