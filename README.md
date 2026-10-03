# Desktop_&_Web_Project
This Project Live  - https://cyberlearn-cs.netlify.app/

# CyberLearn

CyberLearn is a cybersecurity learning platform. It has a course catalog, student enrollments, video lessons, per-lesson progress tracking, and course management for administrators.

It runs on **Netlify** and uses:

- **Frontend:** Vite and TypeScript, as a single-page app (`index.html`, `src/`)
- **Backend:** one TypeScript Netlify Function (`netlify/functions/api.mts`)
- **Auth:** Netlify Identity (`@netlify/identity`)
- **Database:** Netlify Database (managed Postgres) through Drizzle ORM

> The original version was written in PHP. Those files are still in the repository for reference, but they are **not deployed** or used at runtime (see [Legacy PHP app](#legacy-php-app)).

---

## Project structure

```
.
├── index.html                  # SPA entry point (Vite)
├── netlify.toml                # Build config, functions dir, redirects, security headers
├── package.json                # Scripts and dependencies
├── tsconfig.json
├── drizzle.config.ts           # Drizzle Kit config (schema, migrations output)
├── readme.md
│
├── src/                        # Frontend source
│   ├── main.ts                 # Client-side router, views, Identity auth flows, API calls
│   └── style.css               # App styles (also imports assets/css/style.css)
│
├── netlify/
│   ├── functions/
│   │   └── api.mts             # REST API mounted at /api/*
│   └── database/
│       └── migrations/         # Generated SQL migrations (applied on deploy)
│           └── 20261003203231_create_learning_tables/
│
├── db/
│   ├── index.ts                # Drizzle client (drizzle-orm/netlify-db)
│   └── schema.ts               # Tables: courses, enrollments, lesson_completions
│
└── (legacy PHP app, not deployed)
    ├── index.php, register.php, logout.php
    ├── admin/dashboard.php
    ├── student/{dashboard,my-courses,course}.php
    ├── api/{courses,enrollments}.php
    ├── includes/{auth,config,courses}.php
    ├── assets/css, assets/js
    └── data/{courses,enrollments,users}.json
```

---

## Frontend routes

| Route | Purpose |
|-------|---------|
| `/` | Sign in |
| `/register` | Sign up |
| `/forgot-password` | Password recovery |
| `/courses` | Course catalog |
| `/my-courses` | The signed-in student's enrolled courses and progress |
| `/course?id=<id>` | Course detail, lessons, and progress |
| `/admin` | Course and lesson management (admins only) |
| `/logout` | Sign out |

A catch-all rule in `netlify.toml` (`/*` → `/index.html`, 200) lets these routes still work when the page is refreshed.

---

## API

Every endpoint is served by `netlify/functions/api.mts` under `/api/*` and **requires a signed-in user**. The old `.php` URLs (for example `/api/courses.php`) still work.

`POST`, `PUT` and `DELETE` requests must come from the same origin (checked with `verifyRequestOrigin`).

### `/api/courses`

| Method | Access | Description |
|--------|--------|-------------|
| `GET` | Any user | List all courses, or get one with `?id=<id>` |
| `POST` | Admin | Create a course |
| `PUT` | Admin | Update a course (completions for lessons that were removed are cleaned up) |
| `DELETE` | Admin | Delete a course (its enrollments and completions are deleted too) |

### `/api/enrollments`

| Method | Description |
|--------|-------------|
| `GET` | The current user's enrollments, with `completed_lessons` and `progress` (%) |
| `POST` | Enroll in a course: `{ "course_id": 1 }` |
| `PUT` | Mark a lesson complete or not complete: `{ "course_id": 1, "lesson_id": 2, "action": "complete" \| "uncomplete" }` |

Responses use the shape `{ "success": boolean, "message"?: string, ... }`.

---

## Data model

Defined in `db/schema.ts`:

- **`courses`**: `id`, `title`, `category`, `category_color`, `thumbnail_url`, `short_description`, `long_description`, `instructor_name`, `difficulty_level`, `lessons` (JSONB array of `{ id, title, content, duration, video_url }`), `created_at`
- **`enrollments`**: `user_id` (Identity user ID), `course_id` → `courses.id`, `enrolled_at`. Primary key is (`user_id`, `course_id`)
- **`lesson_completions`**: `user_id`, `course_id` → `courses.id`, `lesson_id`. Primary key is (`user_id`, `course_id`, `lesson_id`)

The committed migration creates these tables and seeds the five original courses.

---

## Authentication and roles

- Users sign up through Netlify Identity and must confirm their email before signing in.
- Password recovery and invitation links are supported.
- **Admin access:** give a confirmed user the `admin` role in the Netlify project's Identity settings, then have them sign out and back in. Admin rights are checked on the server inside the function. Users can't give themselves the role at signup.
- Legacy PHP accounts and passwords don't carry over. Those users need to register again or be invited.

---

## Development

```bash
npm ci                       # install dependencies
netlify dev --port 8889      # run locally with Functions, Identity, and Database emulation
npm run typecheck            # type-check without building
```

### Database schema changes

1. Edit `db/schema.ts`
2. Generate a migration:
   ```bash
   npm run db:generate -- --name add_descriptive_change
   ```
3. Commit the generated files under `netlify/database/migrations/`. They are applied on deploy.

### Scripts

| Script | Command |
|--------|---------|
| `dev` | `vite --host 0.0.0.0` |
| `build` | `vite build` |
| `typecheck` | `tsc --noEmit` |
| `db:generate` | `drizzle-kit generate` |

---

## Deployment

Netlify runs `npm run build` and publishes `dist/`. Configuration lives in `netlify.toml`:

- **Functions:** `netlify/functions`, bundled with esbuild
- **Redirects:** old `.php` page URLs get a 301 to their new routes (e.g. `/student/dashboard.php` → `/courses`), then the SPA fallback applies
- **Security headers:** `X-Content-Type-Options: nosniff`, `Referrer-Policy: strict-origin-when-cross-origin`, `X-Frame-Options: DENY`

No external database or database credentials are needed. Netlify Database is provisioned for the project.

---

## Legacy PHP app

The PHP files and the `admin/`, `student/`, `api/`, `includes/`, `assets/` and `data/` folders are the original app. Netlify doesn't run PHP, so they aren't published, and the JSON files in `data/` are never read or written.

Old enrollments don't link to new accounts automatically, because PHP user IDs differ from Identity user IDs. To move historical progress across, you'd have to map each old account to its verified Identity account by hand.
