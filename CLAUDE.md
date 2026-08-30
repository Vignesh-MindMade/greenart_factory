# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

# GAF — GreenArtFactory (Fun Factory Trading LLC)

Corporate website backend + Filament admin dashboard. Business requirements live in
`docx/Green_Art_Factory_BRD.pdf`; the design is `docx/figma_link.md`.

**Stack (verified against `composer.lock`, not aspirational):**

| Layer | What is actually installed |
|---|---|
| Framework | `laravel/framework` **v13.15** · PHP `^8.3` (8.4 on this machine) |
| Admin panel | **Filament v5.6.7** (+ `filament/spatie-laravel-media-library-plugin`) · Livewire 4.3 |
| Media | `spatie/laravel-medialibrary` 11.23 · **Cloudinary** via `codebar-ag/laravel-flysystem-cloudinary` 13 |
| Database | MySQL (`gaf_dev`) · sessions/cache/queue all on the `database` driver |
| Assets | Vite 8 + Tailwind **v4** (Filament only — there are no custom Blade pages) |
| Tests | Pest 4 (`pestphp/pest`, `pest-plugin-laravel`) |

**Not installed** — do not write code that assumes these: Laravel Scout / Meilisearch, AWS S3,
Cloudflare R2, Sanctum, `spatie/laravel-permission`, `spatie/laravel-activitylog`,
`spatie/laravel-translatable`, `spatie/laravel-sluggable`, Filament Shield.

**Frontend (planned, separate repo):** Next.js App Router · Tailwind · TypeScript.

---

## Project Layout

Root: `C:\Users\Vicky\Documents\Websites\gaf-dev`

```
app/
├── Filament/
│   ├── Resources/{Plural}/            ← One folder per resource (see structure below)
│   └── Widgets/NavigationCards.php    ← The only custom widget
├── Http/Controllers/Api/              ← 7 public JSON controllers. No Middleware/Requests/Resources dirs.
├── Models/                            ← 16 live models, flat, no subfolders
├── Providers/
│   ├── AppServiceProvider.php         ← Cloudinary URL cache busting
│   └── Filament/AdminPanelProvider.php ← The single panel: id 'admin', path /admin
└── Support/Media/CloudinaryUrlGenerator.php  ← Custom Spatie URL generator (read the Cloudinary section)
database/{migrations,seeders,factories}
routes/{api.php,web.php,console.php}
resources/views/                       ← 2 files only: the widget blade + welcome.blade.php
tests/{Feature,Unit}/                  ← Pest
docx/                                  ← BRD PDF + Figma link
```

**Directories that do NOT exist** (referenced by older docs — see *Target State* at the bottom
before creating them): `app/Actions/`, `app/Services/`, `app/Policies/`, `app/Observers/`,
`app/Jobs/`, `app/Events/`, `app/Console/`, `app/Http/Middleware/`, `app/Http/Requests/`,
`app/Http/Resources/`, `app/Filament/Pages/`.

> `AdminPanelProvider` calls `discoverPages(in: app_path('Filament/Pages'))` — that directory
> does not exist, so page discovery is currently a no-op.

---

## Commands

Run from project root.

```bash
composer setup            # install + .env + key:generate + migrate + npm install + build
composer dev              # serve :8000 + queue:listen + vite, concurrently — the normal dev command
composer test             # config:clear then artisan test

php artisan migrate
php artisan migrate:fresh --seed
php artisan make:filament-resource ProductVariant --generate
php artisan make:filament-relation-manager ProductResource variants name

./vendor/bin/pest
./vendor/bin/pest --filter=HomepageApiTest
npm run dev / npm run build
```

`used_cmds.md` and `used_cmds2.md` are an append-only log of every artisan/composer command the
project was built with. `used_cmds2.md` is a superset of `used_cmds.md`.

---

## Filament v5 — read this before touching any resource

Training data skews heavily toward Filament v3/v4. **This project is v5.6, and the existing code
is already correctly v5** — if you write v3 syntax you are introducing the bug, not fixing one.

| v3 / v4 (wrong here) | v5 (correct) |
|---|---|
| `form(Form $form): Form` | `public static function form(Schema $schema): Schema` |
| `Filament\Forms\Form` | **Class deleted.** Use `Filament\Schemas\Schema` |
| `Filament\Forms\Components\{Section,Grid,Tabs}` | `Filament\Schemas\Components\{Section,Grid,Tabs,Wizard,Fieldset,Flex}` |
| `Filament\Tables\Actions\EditAction` | `Filament\Actions\EditAction` — **all** actions unified under `Filament\Actions\*` |
| `->actions([...])` / `->bulkActions([...])` | `->recordActions([...])` / `->toolbarActions([...])` |
| `protected static ?string $navigationIcon` | `protected static string\|BackedEnum\|null $navigationIcon = Heroicon::OutlinedPhoto;` |
| `protected static ?string $navigationGroup` | `protected static string\|UnitEnum\|null $navigationGroup` |

Fields still live in `Filament\Forms\Components\*` (`TextInput`, `Select`, `RichEditor`,
`DatePicker`, `Toggle`, `Repeater`, …). `ExportAction`, `ImportAction`, `RestoreAction` and
`ForceDeleteAction` ship in core — don't reach for a plugin.

**Resource folder structure** (what `--generate` produces in v5, and what every resource here uses):

```
app/Filament/Resources/PortfolioProjects/
├── PortfolioProjectResource.php       ← delegates to the two classes below
├── Schemas/PortfolioProjectForm.php   ← public static function configure(Schema $schema): Schema
├── Tables/PortfolioProjectsTable.php  ← public static function configure(Table $table): Table
├── Pages/{List,Create,Edit}*.php
└── RelationManagers/*.php
```

### Debugging "Method X does not exist"

1. `vendor/filament/schemas/src/Schema.php` — layout/schema-level methods
2. `vendor/filament/forms/src/Components/` — field methods (e.g. `Select.php`)
3. `vendor/filament/tables/src/Table.php` — table-level methods
4. `vendor/filament/actions/src/` — every action class
5. If absent, grep the keyword: the method was usually renamed or folded into another method's
   arguments rather than removed.

Do **not** follow older guidance pointing at v4 paths — the namespaces moved in v5.

---

## Cloudinary — do not "fix" this without reading

This is the least obvious part of the codebase and the decision trail exists nowhere else.

- `cloudinary-labs/cloudinary-laravel` was tried first and **rejected — incompatible with Laravel 13**.
  `codebar-ag/laravel-flysystem-cloudinary` replaced it. Don't reintroduce the former.
- `config/media-library.php` sets `disk_name => env('MEDIA_DISK', 'cloudinary')` and
  `url_generator => App\Support\Media\CloudinaryUrlGenerator::class`.
- That generator emits `https://res.cloudinary.com/{cloud}/image/upload/f_auto,q_auto/{folder}/{path}.{ext}`
  and caches per-media-UUID for 1 day. The cache is busted by a `Media::deleted` listener in
  `AppServiceProvider::boot()`.
- **The double extension (`.png.png`) is deliberate.** codebar-ag stores the extension *inside* the
  `public_id`, so the generator re-appends it. It looks like a bug. It is not. Removing it breaks
  every image URL on the site.
- `f_auto,q_auto` already delivers format negotiation and compression, so **no
  `registerMediaConversions()` is needed** anywhere (this satisfies BRD FR-6.2 — don't rebuild it).
- Max upload 10 MB. Conversions are queued by default. `LIVEWIRE_TEMPORARY_FILE_UPLOAD_DISK=local`.

⚠️ **`.env.example` is missing every Cloudinary key the app requires** (`MEDIA_DISK`,
`CLOUDINARY_CLOUD_NAME`, `CLOUDINARY_API_KEY`, `CLOUDINARY_API_SECRET`, `CLOUDINARY_SECURE_URL`,
`CLOUDINARY_FOLDER`, `LIVEWIRE_TEMPORARY_FILE_UPLOAD_DISK`). A fresh clone running `composer setup`
gets a non-functional media pipeline.

The `s3` disk in `config/filesystems.php` is a leftover stub — `league/flysystem-aws-s3-v3` is not
installed, so using it fatals. There is no R2 disk.

---

## Media collection registry

**The project's most frequent bug class.** A `SpatieMediaLibraryFileUpload` writing to a collection
name the model never registered still *works* (Spatie falls back to the default disk), but the
per-collection config is silently discarded and any reader using the registered name gets `""`.

Always cross-check all three columns before adding or changing an upload.

| Model | `registerMediaCollections()` | Filament writes to | API reads | Match |
|---|---|---|---|---|
| `PortfolioProject` | `cover_image`, `project_images` | same | same | ✅ |
| `ProductVariant` | `variant_images` | same | same | ✅ |
| `HeroSlides` | `hero_slide_images` | same | same | ✅ |
| `Product` | `products` | `cover_image` | `products` *and* `cover_image` | ❌ |
| `Testimonials` | `testimonial_image` | `testimonial_images` | `testimonial_images` | ❌ |
| `Partners` | `logo` | `partners` | `partners` | ❌ |
| `BlogPosts` | `Blog_post_image`, `author_image` | `cover_image`, `author_image` | `cover_image`, `author_image` | ❌ |
| `ServiceItem` | `service_item_images` | `cover_image`, `project_images` | `cover_image`, `project_images` | ❌ |

**Live symptom:** `/api/products` reads `getFirstMediaUrl('products')` and returns an empty string,
while `/api/homepage/products-preview` reads `cover_image` and returns a URL — for the same product.

Rule: **never store raw URLs.** Always `$model->getFirstMediaUrl('collection-name')` /
`getMedia('collection-name')`.

---

## Database Schema

20 domain tables. Every table has `id` + `timestamps` unless noted. **No table has `deleted_at`** —
soft deletes are not implemented anywhere.

### Catalogue

| Table | Columns beyond id/timestamps | Notes |
|---|---|---|
| `products` | `name`, `slug` uniq, `description` longText null, `status` def `draft` | |
| `product_variants` | `product_id` FK cascade, `name`, `slug` | unique(`product_id`,`slug`) |
| `services` | `name`, `slug` uniq, `status` def `draft` | |
| `service_items` | `service_id` FK cascade, `name`, `slug` uniq, `sort_order` def 0 | Global slug unique makes the composite unique unreachable |

### Portfolio

| Table | Columns | Notes |
|---|---|---|
| `portfolio_projects` | `category_id` FK restrict, `location_id` FK restrict, `title`, `slug` uniq, `status` def `draft`, `is_featured` bool, `featured_order` usmallint | |
| `portfolio_categories` | `name`, `slug` uniq | |
| `locations` | `name`, `slug` uniq, `country` (NOT null) | Country is a hard-coded Select: AE, SA, QA, KW, BH, OM, PT, OTHER |
| `sectors` | `name`, `slug` uniq | |
| `installation_types` | `name`, `slug` uniq | |

### Homepage / content

| Table | Columns |
|---|---|
| `hero_slides` | `headline`, `subtext` null, `cta_label` (NOT null), `cta_url` (NOT null), `sort_order`, `status` |
| `testimonials` | `customer_name`, `customer_title` null, `quote` text, `sort_order`, `status` |
| `partners` | `name`, `website_url` null, `sort_order`, `status` |
| `blog_categories` | `name`, `slug` uniq, `sort_order`, `status` |
| `blog_posts` | `blog_category_id` FK restrict, `title`, `slug` uniq, `excerpt` text, `content` longText, `author` null, `blog_date` date null, `published_at` datetime null, `sort_order`, `status` |
| `page_sections` | `section_key` uniq, `title`, `subtitle`, `description`, `cta_label`, `cta_url` — all nullable |

`media` is the stock Spatie v11 schema (`model_type`/`model_id` morph, `collection_name`, `uuid`,
`disk`, `custom_properties` json, `order_column`, …). Plus the framework tables: `users`,
`password_reset_tokens`, `sessions`, `cache`, `cache_locks`, `jobs`, `job_batches`, `failed_jobs`.

### Pivots (no id, no timestamps, composite PK)

| Table | Relates |
|---|---|
| `portfolio_project_variant` | `portfolio_project_id` ↔ `product_variant_id` |
| `portfolio_project_sector` | `portfolio_project_id` ↔ `sector_id` |
| `portfolio_project_installation_type` | `portfolio_project_id` ↔ `installation_type_id` |

### Relationship rules

- `portfolio_projects` belongs to **one** category + **one** location via FK columns, not a pivot.
- All other associations are M:M pivots. Replicate the `sectors` pattern for any new taxonomy:
  migration → model → pivot migration → `belongsToMany` on both sides → Filament `Select`
  `->relationship()->multiple()->searchable()->preload()`.
- `media` is polymorphic across `PortfolioProject`, `ProductVariant`, `Product`, `ServiceItem`,
  `BlogPosts`, `HeroSlides`, `Testimonials`, `Partners`.

### Model gotchas

- **Slugs are not automatic.** No `boot()`, no `HasSlug`, no `Str::slug()` in any model. Slugs are
  generated in the Filament form only, via `->live(onBlur: true)->afterStateUpdated(...)`, and only
  on create. Anything created outside Filament (seeder, factory, API) must set `slug` itself.
- `BlogPosts` — class name is plural; table `blog_posts`. Its category relation is named **`blogs()`**,
  not `category()`. `HomepageController@blogPreview` carries a TODO about renaming it.
- `ServiceItem::$fillable` lists `'order'` but the column is **`sort_order`** — sort order is
  silently not mass-assignable.
- `PortfolioProject` has no `'is_featured' => 'boolean'` cast.
- `Partners`' status Select stores `'Archive'`; every other resource stores `'archived'`.
  `BlogCategory` has no `archived` option at all.
- `Service::serviceitem()` applies `->orderBy('sort_order')` inside the relation.
- Dead files: `app/Models/Media.php` (unused — `config/media-library.php` resolves Spatie's own
  class; its `s3_path`/`alt_text` columns don't exist) and `app/Models/create_media_table.php`
  (a `make:model` typo).

---

## Admin Panel

Single panel: `AdminPanelProvider`, id `admin`, path `/admin`, `->login()` only (no registration,
password reset, email verification, or profile). Primary colour Emerald, GAF logo branding.
No plugins registered. `config/filament.php` was never published.

13 resources. Navigation groups are declared per-resource and `->navigationGroups()` is **not** set
on the panel, so ordering of ungrouped items is non-deterministic:

- **Homepage** — HeroSlides (1), Testimonials (2), Partners (3), PageSections (4)
- **Blogs** — BlogCategory (1), BlogPosts (2)
- **Ungrouped** — Products, PortfolioProjects, PortfolioCategories, Locations, Sectors,
  InstallationTypes, Services

Active relation managers: `ProductResource → VariantsRelationManager`,
`ServiceResource → ServiceitemRelationManager`. `ProductVariantsRelationManager` exists but is
commented out of `PortfolioProjectResource::getRelations()` and its `form()` is an empty schema.

`PageSectionResource` is edit-only: rows are seeded by `PageSectionSeeder` and `canCreate()` /
`canDelete()` return `false`. Note `canDeleteAny()` is *not* overridden, so the bulk-delete action
still bypasses that guard.

### ⚠️ Authorization: there is none

- No `spatie/laravel-permission`, no policies, no roles, no `users.role` column.
- `User` does **not** implement `Filament\Models\Contracts\FilamentUser`, so Filament's
  `Authenticate` middleware falls back to `config('app.env') !== 'local'` and **aborts 403**.
  **The panel is unusable on staging or production today.** In local, any row in `users` gets
  unrestricted CRUD over every resource.
- Seeded login: `test@example.com` / `password` (from `UserFactory`).

BRD §8 specifies a Super Admin / Content Editor / HR Manager matrix. Implementing it is a launch
blocker — see *Target State*.

---

## Public API

Routes in `routes/api.php` are auto-prefixed `/api` by `bootstrap/app.php`. **All 18 are GET and
public** — no auth, no versioning, no throttling, no validation layer. `web.php` serves only
`welcome.blade.php`; `/admin` comes from the panel provider. Health check at `/up`.

| Route | Filters supported |
|---|---|
| `GET /api/filters` | none — returns sectors, locations, installation_types, products+variants |
| `GET /api/portfolio` | `sector`, `location`, `installation_type`, `product_variant` (all by slug) |
| `GET /api/portfolio/{slug}` | — |
| `GET /api/products` · `/api/products/{slug}` | `search`, `page`, `per_page` (def 15) |
| `GET /api/services` · `/api/services/{slug}` | `search`, `page`, `per_page` (def 15) |
| `GET /api/blog` · `/api/blog/{slug}` | `blogs` (category slug — note the param name), `page`, `per_page` (def 12) |
| `GET /api/homepage/{sections,hero,featured,testimonials,partners,blog-preview,products-preview}` | none |
| `GET /api/productcategory/categories` | none — byte-identical to `homepage/products-preview` |

Every public query filters `status = 'published'` — **except `/api/filters`**, which leaks
unpublished products and taxonomies.

**Known API issues** (fix before extending):

- `GET /api/` maps to `HomepageController@index`, which **does not exist** → 500.
- **Five different response envelopes**: bare array (`/api/portfolio`), flat object (`/api/filters`,
  `/api/homepage/sections`), `{data}` (homepage), `{data,meta}` (`/api/blog`),
  `{success,message,data,meta}` (products, services). A typed frontend client needs five schemas.
- `/api/portfolio` is unpaginated. `per_page` is unbounded elsewhere.
- `/api/blog` returns full `content` for all 12 posts in the list response.
- No controller eager-loads `media`, so every list endpoint does N+1 media queries.
- Responses are hand-built `response()->json([...])` arrays — there are no `JsonResource` classes.

### Tests

Pest, but **currently non-functional**: `RefreshDatabase` is commented out in `tests/Pest.php` while
`phpunit.xml` forces SQLite `:memory:`, so the test database has no schema and any DB-touching test
fails. `HomepageApiTest` targets `/api/homepage`, which is not a route, and asserts a `success` key
no homepage endpoint emits. Only `UserFactory` exists — there are no domain factories.

---

## Quick Task Reference

| Task | Files to touch | Rule |
|---|---|---|
| New filter taxonomy | migration, Model, pivot migration, Filament Resource | Follow the `sectors` pattern exactly |
| New API endpoint | `routes/api.php`, `Controllers/Api/` | Match an existing envelope; update Postman |
| New media collection | `Model::registerMediaCollections()` **and** the Filament upload **and** the API reader | All three must use the same string — check the registry table above |
| New Filament resource | `make:filament-resource X --generate` | Then trim; keep the `Schemas/` + `Tables/` split |
| Reorderable list | `->reorderable('sort_order')` on the table | Only `HeroSlides` does this today, though 6 models have `sort_order` |

---

## ⚠️ Risk Flags

| Risk | Rule |
|---|---|
| **Arabic / RTL** | BRD §9 states the site will be "primarily in English and Arabic", but no translation strategy is chosen and `origin/lang` holds an unmerged dashboard language switch. **Decide JSON columns vs. a translations table before writing any new migration** — retrofitting is expensive. Do not build RTL layouts until confirmed. |
| **Panel 403 outside local** | `User` must implement `FilamentUser` before any non-local deploy. |
| **Media collection names** | Must match `registerMediaCollections()` exactly. Five models currently don't. |
| **New tables / relations** | Require client sign-off before migration. |
| **Slug renames** | Coordinate with client — breaks URLs and SEO. Slugs are not auto-generated. |
| **Content unready** | Seeders must work with placeholder data. |
| **Unmerged branches** | `origin/about` (about_stories, team_members, certifications, certification_badges + `AboutSectionResource`, deliberately broken to disable it) and `origin/lang`. Check these before building About Us or i18n from scratch. |

---

## Target State — aspirational, NOT currently enforced

These conventions are goals, not descriptions. **Zero files follow them today.** Don't assume they
exist when reading code; do follow them in genuinely new code, and don't mass-retrofit without asking.

- `declare(strict_types=1);` in new files
- `app/Actions/` for business logic; `app/Services/` for cross-model logic — prefer these over fat controllers
- `SoftDeletes` + `deleted_at` on content models (BRD FR-2.7 requires archive/recovery)
- `JsonResource` / `ResourceCollection` for all API responses, under a single envelope
- `app/Http/Requests/` FormRequests for any write endpoint (there are no write endpoints yet)
- `/api/v1/` versioned routes
- `readonly` properties on DTOs

---

## Skills

| Skill | Read when… | Path |
|---|---|---|
| `postman` | New API endpoint, collection update, spec generation | `.claude/skills/postman/SKILL.md` |

⚠️ The Postman skill is **stale**: it documents `/api/v1/*` paths, a Sanctum `POST /api/v1/login`,
and Meilisearch `?q=` — none of which exist — and omits `/api/services`, `/api/blog`,
`/api/homepage/*` and `/api/productcategory/categories`, which do. The Postman MCP it depends on is
not currently connected. Verify against `routes/api.php` before trusting it.

Trigger phrases: `postman` → "update Postman", "add to collection", "API contract",
"generate API spec", "new endpoint".
