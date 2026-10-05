# Wedspiration — Assignment 1

*Short app concept, data structure and routes*

---

## 01 — Assignment Description

A short description of the app idea and its main functionality.

### Assignment Idea

A visual inspiration archive for couples planning a wedding and the photographer working with them.

### Scope

Users, folders, photos and a public gallery.

### Description

Wedspiration is a web application for wedding photography clients. Couples can register, log in, create a personal wedding folder and upload inspirational photos. Every uploaded photo is public and appears both in the public gallery and in the uploader's personal folder. The folder can be used as a visual brief for the photographer.

### Main Functionalities

| # | Feature | Description |
|---|---------|-------------|
| 01 | **Account** | Register, log in and manage own content. |
| 02 | **Upload** | Add a public photo with title, description and category. |
| 03 | **Gallery** | Every uploaded photo appears in the public gallery. |
| 04 | **Folder** | Each upload also appears in the uploader's personal folder. |

### Requirements Covered

- The app has three models, including `User`.
- Each model relates to another model, including one-to-many relationships.
- Test data can be created with factories for users, folders and photos.

---

## 02 — The Data Model

*Models and their properties.* Only the data needed for the core user journey is included.

### Overview

```
User ──1-N──> WeddingFolder
User ──1-N──> Photo
WeddingFolder <──N-M──> Photo   (via folder_photo)
```

### User

Registered account.

| Field | Type | Notes |
|-------|------|-------|
| `id` | PK | |
| `name` | string | |
| `email` | string | unique |
| `password` | string | hashed |
| `is_admin` | boolean | |

### WeddingFolder

A couple's personal inspiration folder.

| Field | Type | Notes |
|-------|------|-------|
| `id` | PK | |
| `user_id` | FK | → `users` |
| `name` | string | |
| `notes` | text | nullable |
| `wedding_date` | date | nullable |

### Photo

A public inspirational image.

| Field | Type | Notes |
|-------|------|-------|
| `id` | PK | |
| `user_id` | FK | → `users` |
| `title` | string | |
| `description` | text | nullable |
| `category` | string | nullable |
| `image_path` | string | |

Public by default.

### folder_photo

Pivot between a folder and a photo.

| Field | Type | Notes |
|-------|------|-------|
| `folder_id` | FK | → `folders` |
| `photo_id` | FK | → `photos` |

Every uploaded photo is added automatically to its owner's folder.

### Relationships

- **User** `hasMany` folders and photos.
- **WeddingFolder** `belongsTo` user and `belongsToMany` photos.
- **Photo** `belongsTo` user and `belongsToMany` folders.

An uploaded photo is public and is automatically added to its owner's folder.

---

## 03 — The Application Map

*Pages (routes)*

### Public

| Route | Description |
|-------|-------------|
| `welcome` | Landing page |
| `gallery.index` | List of public photos |
| `gallery.show (id)` | Details of one photo |
| `about` · `contact` | Static pages |

### Authentication

- `login`
- `register`
- `logout`

### Logged-in User

| Method | Route | Description |
|--------|-------|-------------|
| | `user.dashboard` | Folder overview |
| | `user.folders.show (id)` | Selected folder |
| `GET` | `user.photos.create` | Upload form |
| `POST` | `user.photos.store` | Create photo and add it to the owner folder |
| `GET` | `user.photos.edit (id)` | Edit own photo |
| `PATCH` | `user.photos.update (id)` | Update own photo |
| `DELETE` | `user.photos.destroy (id)` | Delete own photo |

### Admin

| Route | Description |
|-------|-------------|
| `admin.dashboard` | Admin overview |
| `admin.users.*` | Manage accounts |
| `admin.folders.*` | Manage folders |
| `admin.photos.*` | Manage photos |

> `*` = standard CRUD routes: `index`, `show`, `create`, `store`, `edit`, `update`, `destroy`
