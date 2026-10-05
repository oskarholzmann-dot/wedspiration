# Project Assessment Criteria

> ⚠️ **Your project = your project**
>
> It is completely up to you to decide what use-case you will build an application for. If you are uninspired or don't really know where to start, I would advise a blog or to-do application.
>
> In class, I'll build a small **demo blog application** that serves as an example. It meets all of the minimal requirements that you'll find below.
>
> A personal project that is just a (literal) copy of the blog application will not satisfy. You do need to demonstrate that you managed to translate the concepts taught into your own application.

---

## Minimal requirements

### A. Code management

- You **use git**, and link your repository to a publicly accessible Git repo (aka **GitHub**)
- You use as much as possible **atomic commits** that group small functional increments

### B. Application

- Your implementation is **in line with the application description** that you submitted.
- Your application data structure is an implementation of the data structure in your description. If your idea goes really broad, it's ok to have only a subset implemented

#### Model

- **At least 3 models** (including the `User` model)
- Implement **at least one 1-N relationship** (but more is recommended)
- For every model, you have a `Model`, a `Migration` and a `Factory`
- In your app, your factories are called in the `DatabaseSeeder`

#### Routes/controllers

- Every route is linked to a controller function
- You implement for **at least one model a full CRUD** (all 7 methods)
- You validate every input coming from a user
- Your forms give feedback to the user if invalid input was submitted

#### Authentication

- A **user can login** and/or register for an account
- You use the info from the logged in user at least once in a view
- You use the info from the logged in user at least once in a controller (authorisation and/or business logic)

#### Views

- You make use of a layout for the common elements
- You have the necessary views for the functionality needed in your app
- You will **not be evaluated on the esthetical qualities** of your designs *(but yes please, a bit of design is always nicer)*

#### Seeded data

- Seed everything that is needed in your `DatabaseSeeder`.
- Additionally, seed a dummy admin user (`admin@admin.com`) with `password` as the password

---

## How your application will be evaluated/tested

- Your web app will be installed locally on my computer
- I will run `php artisan migrate:fresh --seed` in order to have fake data (unless instructed explicitly by you to do something else)
- I will visit the welcome page — this needs to contain something relevant to your application
- I will visit the other routes; for the logged in routes it will be with the `admin@admin.com` user

---

## How you will defend your project

- You bring your computer, with the code editor (IDE) and a browser open on your project
- We will have a 15 minutes chat about your application
  - I can ask you to find a specific functionality and explain it to me
  - I can ask you to make live a small change to the code and show the result
  - …
