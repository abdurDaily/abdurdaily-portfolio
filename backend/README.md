# abdurDaily — portfolio (Laravel 12 + Filament 4)

Every piece of site copy, image, service, review, FAQ and setting is stored in the database and edited at `/admin`.

## Local setup

```bash
composer install
cp .env.example .env && php artisan key:generate
php artisan migrate --seed      # loads the launch content + images
php artisan storage:link
php artisan make:filament-user  # your admin login (no default account exists)
php artisan serve               # http://127.0.0.1:8000  ·  admin: /admin
```

Requires PHP 8.2+ with the `intl`, `zip`, `pdo_sqlite` (or `pdo_mysql`) extensions.

## Admin

- **Site content**: hero, about, section headings, process, contact details, socials, footer, and SEO (title, description, share image, Search Console code). Wrap words in `*asterisks*` to highlight them.
- **Services**: each one gets its own indexable page at `/services/{slug}` with its own meta title/description.
- **Tech stack, Industries, Gallery, Client reviews, FAQ**: drag rows to reorder; untick *Published* to hide.
- **Inbox**: messages from the contact form (also emailed to the contact address when mail is configured).

## SEO built in

Server-rendered HTML · per-page title/description/canonical · Open Graph + Twitter cards · JSON-LD (`WebSite`, `Person`, `ProfilePage`, `FAQPage`, `Service`, `BreadcrumbList`) · dynamic `/sitemap.xml` and `/robots.txt` · image alt text editable in the admin.
Non-production environments send `noindex` and a blocking robots.txt, so staging never competes with the live site.

## Deploying

Set in `.env`: `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://your-domain` (used for canonical URLs and the sitemap), your database, and `MAIL_*` for inquiry emails. Point the web root at `public/`, then:

```bash
composer install --no-dev --optimize-autoloader
php artisan migrate --force --seed
php artisan storage:link
php artisan optimize
```

Submit `https://your-domain/sitemap.xml` in Google Search Console.

## Tests

```bash
php artisan test
```
