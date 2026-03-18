## Makogai — Portfolio + Blog Platform

Production-ready personal portfolio + blog platform built with:

- Laravel 13 (this repo) + Fortify + Inertia v2
- Vue 3 (Composition API) + Tailwind CSS v4
- Inertia SSR (`composer run dev:ssr`)
- MySQL

This project is designed to feel like a premium, futuristic developer brand (dark-first, glassmorphism, depth, subtle motion).

---

### Local setup

#### Requirements

- PHP 8.3+ (Herd is fine)
- Node 20+
- MySQL 8+

#### Install

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm install
```

#### Demo content

```bash
php artisan migrate:fresh --seed
```

Seeds an admin user:

- Email: `admin@example.com`
- Password: `password`

#### Run (SPA)

```bash
composer run dev
```

#### Run (SSR)

```bash
composer run dev:ssr
```

---

### Core product areas

- **Public**
  - Landing page (hero, skills, featured projects, latest posts, activity feed, contact)
  - Projects (grid + filters, detail pages)
  - Blog (markdown rendering, syntax highlighting, tags/categories, TOC)
  - Activity feed (commit-style timeline)
- **Admin (single user)**
  - Dashboard
  - Manage posts (markdown editor + live preview, draft/publish)
  - Manage projects (gallery, tech stack, links)
  - Manage activities (type/date)
  - Settings (site-wide config)

---

### Data model (target)

- `users` (provided by Laravel/Fortify)
- `posts`
  - `title`, `slug`, `excerpt`, `reading_time_minutes`
  - `content_markdown`, `content_html` (cached)
  - `cover_image_path`, `published_at`, `is_featured`
  - `category_id`, timestamps
- `post_categories` (`name`, `slug`)
- `post_tags` (`name`, `slug`)
- `post_post_tag` (pivot)
- `projects`
  - `title`, `slug`, `description`
  - `content_markdown`, `content_html` (cached)
  - `cover_image_path`, `gallery` (json)
  - `tech_stack` (json), `repo_url`, `demo_url`
  - `published_at`, `is_featured`, timestamps
- `activities`
  - `type` (feature/fix/idea), `title`, `description`, `happened_at`
  - `url`, `meta` (json), timestamps
- `settings`
  - `key` (unique), `value` (json), timestamps

---

### Markdown pipeline

Posts/projects store **raw markdown** in DB. We also cache compiled HTML for faster render.

Pipeline responsibilities:

- **Slug**: generated from title (unique)
- **Excerpt**: derived from markdown (first paragraph / sanitized text)
- **Reading time**: derived from word count
- **HTML**: rendered via CommonMark + extensions (tables, code fences, autolink, heading anchors)
- **Syntax highlighting**: rendered on the frontend (SSR-safe), or pre-rendered in HTML if needed

---

### Single-user auth policy

- Public registration is disabled.
- Admin routes require authentication (single-user system).

---

### Conventions

- **Validation**: Form Requests only
- **Controllers**: thin; put formatting/transform logic in Services
- **DB**: Eloquent relationships, eager loading by default
- **Frontend**: reusable components, layout system, page transitions, subtle motion

---

### Roadmap (implementation order)

1. Domain schema + models + seed data
2. Public routes + pages (Home/Blog/Projects/Activity)
3. Admin routes + CRUD pages + uploads + markdown editor
4. SEO (meta per page + sitemap) + performance polish
5. Final polish pass: motion, micro-interactions, glass + glow system

