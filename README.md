# Global F.O.R backend

Laravel 12 application for the public content API and the approved Global F.O.R Studio administration experience.

## Requirements

- PHP 8.2 or newer
- Composer 2
- MySQL 8 for local development and production
- A separate MySQL database for the isolated automated test suite

## Local setup

```powershell
Copy-Item .env.example .env
composer install
npm install
php artisan key:generate
php artisan migrate
php artisan storage:link
php artisan studio:admin
npm run build
php artisan serve
```

Configure the MySQL connection in `.env` before running migrations against a persistent local or production database. Never commit `.env`.

The automated test suite uses `global_for_test` by default. Keep it separate from `global_for`; the test runner refreshes its schema and data.

## Quality checks

```powershell
composer test
composer format -- --test
php artisan route:list
npm run build
```

The complete production deployment and rollback procedure is in [`../../docs/deployment.md`](../../docs/deployment.md).

## Public API foundation

- `GET /api/v1/status`
- `GET /api/v1/site-settings` — public identity and contact settings used by Nuxt
- `GET /api/v1/activities` — published Activities, newest first, cursor-paginated 10 at a time
- `GET /api/v1/news` — published News; add `?homepage=1` for homepage-visible ordering
- `GET /api/v1/news/{slug}` — one published News detail record

Draft and future-dated records are excluded from every public response. Activity records intentionally do not contain category or language fields. News records do not contain a language field.

## Global F.O.R Studio

Open `/studio/login`. The Studio is rendered by Laravel Blade and Livewire with Fortify-backed session authentication. It includes responsive Activity and News editors with live preview, media browsing, profile/password settings and reversible administrator management. Public registration is disabled and no credentials are seeded.

Use `php artisan studio:admin` to create or reactivate an administrator securely from the command line.
