# GAF API Reference

Contract for the Green Art Factory frontend. Every response body below is a real
capture from a running server, trimmed only by removing repeated array items.

**Base URL** — `{{base_url}}/api` · local: `http://localhost:8000/api`
**Auth** — none. Every endpoint is public and `GET`.
**Content type** — `application/json`

---

## Conventions

### Endpoint tiers

The API is organised by *how often data changes and who owns it*, not by database table.

| Tier | Pattern | Fetched by | Purpose |
|---|---|---|---|
| **Page** | `/api/v1/pages/{page}` | `page.tsx` | Everything one screen renders, in one request |
| **Entity** | `/api/v1/{resource}` | components | Search, filtering, pagination |
| **Detail** | `/api/v1/{resource}/{slug}` | `page.tsx` | One record, page-shaped |

Prefer the page endpoint when rendering a screen. Reach for entity endpoints only
when the user is filtering or paginating.

### Envelope

```jsonc
{ "data": ... }            // single record or page payload
{ "data": [...], "meta": {...} }   // paginated collections
```

`meta` carries `current_page`, `last_page`, `per_page`, `total`.

### Rules the backend guarantees

- **Publish state is enforced server-side.** Draft and archived records never appear.
  Do not filter on status in the frontend — it is already done.
- **Image fields are absolute Cloudinary URLs, or `null`.** Never a path, never `""`.
  They carry `f_auto,q_auto`, so the format is negotiated per browser — do not add
  your own transformations. The doubled extension (`.webp.webp`) is intentional.
- **`*_url` fields are frontend routes**, not API URLs. Use them directly in `<Link href>`.
- **`per_page` is capped at 48.** Larger values are silently clamped.
- **Ordering is server-controlled** via `sort_order`. Render arrays in the order given.

### Errors

| Code | Meaning |
|---|---|
| `200` | Success |
| `404` | Slug not found, or the record is not published |
| `500` | Server error — report it, do not retry blindly |

Errors render as JSON for all `api/*` paths.

---

## Products

### `GET /api/v1/pages/products`

The products listing screen. One request renders the whole page.

**Parameters** — none.

**Response**

```json
{
    "data": {
        "section": {
            "title": "OUR PRODUCTS",
            "subtitle": null,
            "description": null,
            "cta_label": null,
            "cta_url": null
        },
        "products": [
            {
                "id": 8,
                "name": "Bark Panels",
                "slug": "bark-panels",
                "description": null,
                "cover_image": null,
                "cta_url": "/products/bark-panels",
                "variants": [
                    {
                        "id": 36,
                        "name": "Natural Bark Panels",
                        "slug": "natural-bark-panels",
                        "image": "https://res.cloudinary.com/dzcfhoulx/image/upload/f_auto,q_auto/gaf/63/bark-1.jpg.jpg",
                        "cta_url": null
                    },
                    {
                        "id": 37,
                        "name": "Decorative Bark Panels",
                        "slug": "decorative-bark-panels",
                        "image": "https://res.cloudinary.com/dzcfhoulx/image/upload/f_auto,q_auto/gaf/64/bark-2.jpg.jpg",
                        "cta_url": null
                    }
                ]
            }
        ],
        "why_choose_us": [
            {
                "id": 1,
                "display_no": "01",
                "title": "Exceptional Craftsmanship",
                "description": "Every installation is meticulously designed and handcrafted to achieve a natural, elegant aesthetic that enhances every space.",
                "value": null,
                "icon": null
            },
            {
                "id": 2,
                "display_no": "02",
                "title": "Premium Quality Materials",
                "description": "We use carefully selected, long-lasting materials that deliver realistic beauty with minimal maintenance and enduring performance.",
                "value": null,
                "icon": null
            }
        ]
    }
}
```

**Notes**

- `section` is editable copy from the admin dashboard. Any field may be `null` —
  render conditionally.
- `products[].variants` are summary cards only. Full variant blocks live on the
  detail endpoint.
- `products[].variants[].cta_url` is **always `null` today.** There is no
  variant-level screen in the design, so it has no target yet — hide the link.
  The product-level `cta_url` is the one to use for "View Now".
- `why_choose_us` is a **content block list** — see below. Pre-sorted, published
  only. May be `[]`; hide the section when empty.

#### Content blocks

`why_choose_us` uses the shared content-block shape. Every ordered list in the
site returns this same object, so you write **one** zod schema and reuse it:

```ts
const ContentBlock = z.object({
  id:          z.number(),
  display_no:  z.string().nullable(),   // "01" — render as given, don't compute
  title:       z.string(),
  description: z.string().nullable(),
  value:       z.string().nullable(),   // "98%" — stat lists only
  icon:        z.string().url().nullable(),
});
```

`value` and `icon` are `null` for `why_choose_us`. They are present because the
same shape will serve careers benefits, hiring steps, service FAQs, service
process, core values and company stats as those pages are built.

---

### `GET /api/v1/products`

Entity list. Use for search and pagination, not for rendering the products page.

**Parameters**

| Name | Type | Default | Notes |
|---|---|---|---|
| `search` | string | — | Partial match on product name |
| `page` | int | `1` | |
| `per_page` | int | `15` | Capped at 48 |

**Response**

```json
{
    "data": [
        {
            "id": 8,
            "name": "Bark Panels",
            "slug": "bark-panels",
            "description": null,
            "cover_image": null,
            "cta_url": "/products/bark-panels",
            "variants": [
                {
                    "id": 40,
                    "name": "Custom Bark Installations",
                    "slug": "custom-bark-installations",
                    "image": "https://res.cloudinary.com/dzcfhoulx/image/upload/f_auto,q_auto/gaf/67/bark-5.jpg.jpg",
                    "cta_url": "/products/bark-panels/custom-bark-installations"
                },
                {
                    "id": 37,
                    "name": "Decorative Bark Panels",
                    "slug": "decorative-bark-panels",
                    "image": "https://res.cloudinary.com/dzcfhoulx/image/upload/f_auto,q_auto/gaf/64/bark-2.jpg.jpg",
                    "cta_url": "/products/bark-panels/decorative-bark-panels"
                }
            ]
        }
    ],
    "meta": {
        "current_page": 1,
        "last_page": 4,
        "per_page": 2,
        "total": 8
    }
}
```

---

### `GET /api/v1/products/{slug}`

The product detail screen — Figma node `545:571`.

**Parameters** — `slug` in path, e.g. `moss-creations`.

**Response**

```json
{
    "data": {
        "id": 1,
        "name": "Moss Creations",
        "slug": "moss-creations",
        "description": "Create captivating interiors with our bespoke Moss Creations, thoughtfully designed to introduce the beauty of nature into modern spaces without the need for ongoing maintenance. From elegant moss walls and artistic feature installations to custom branding elements and decorative panels, every creation is handcrafted using premium preserved moss to deliver exceptional texture, visual depth, and timeless appeal. Ideal for offices, hotels, restaurants, retail environments, and luxury residences, our moss designs enhance interiors with sustainable beauty while creating calming, inspiring, and memorable experiences.",
        "cover_image": "https://res.cloudinary.com/dzcfhoulx/image/upload/f_auto,q_auto/gaf/74/moss1.webp.webp",
        "variants": [
            {
                "id": 1,
                "display_no": "01",
                "name": "Moss Walls",
                "slug": "moss-walls",
                "description": "Each installation is custom-designed to complement the architecture and spatial identity of the space. Ideal for hospitality, corporate, retail, and luxury residential environments seeking a refined green statement.",
                "image": "https://res.cloudinary.com/dzcfhoulx/image/upload/f_auto,q_auto/gaf/22/Moss-wall-for-events.webp.webp",
                "images": [
                    "https://res.cloudinary.com/dzcfhoulx/image/upload/f_auto,q_auto/gaf/22/Moss-wall-for-events.webp.webp",
                    "https://res.cloudinary.com/dzcfhoulx/image/upload/f_auto,q_auto/gaf/76/web.webp.webp"
                ],
                "spec": {
                    "label": "Material",
                    "value": "100% Preserved Natural Moss",
                    "tags": [
                        "Biophilic",
                        "Eco-Certified",
                        "No Water"
                    ]
                },
                "project_url": "/portfolio/corporate-elegance",
                "gallery_url": "/gallery?product=moss-creations&variant=moss-walls"
            },
            {
                "id": 8,
                "display_no": "01",
                "name": "Moss World Maps",
                "slug": "moss-world-maps",
                "description": "Preserved moss walls bring nature indoors with a timeless, maintenance-free aesthetic. They enhance interiors with rich texture, acoustic comfort, and biophilic appeal.",
                "image": "https://res.cloudinary.com/dzcfhoulx/image/upload/f_auto,q_auto/gaf/31/map-world-grass-background-3d-rendering-(1).jpg.jpg",
                "images": [
                    "https://res.cloudinary.com/dzcfhoulx/image/upload/f_auto,q_auto/gaf/31/map-world-grass-background-3d-rendering-(1).jpg.jpg"
                ],
                "spec": {
                    "label": "Material",
                    "value": "100% Preserved Natural Moss",
                    "tags": [
                        "Biophilic",
                        "Eco-Certified",
                        "No Water"
                    ]
                },
                "project_url": null,
                "gallery_url": "/gallery?product=moss-creations&variant=moss-world-maps"
            }
        ],
        "varieties_section": {
            "title": "Choose Your Texture & Feel",
            "intro": "Each moss type brings a distinct character — from dense velvety coverage to sculptural organic forms.",
            "footer": "Choose your favourite texture",
            "items": [
                {
                    "id": 1,
                    "label": "Variety 01",
                    "name": "Flat Moss",
                    "slug": "flat-moss",
                    "description": "A dense, velvety carpet moss offering smooth, uniform coverage. Ideal for large backgrounds and minimalist compositions that demand quiet elegance.",
                    "image": "https://res.cloudinary.com/dzcfhoulx/image/upload/f_auto,q_auto/gaf/77/Rectangle.png.png",
                    "gallery_url": "/gallery?product=moss-creations&variety=flat-moss"
                },
                {
                    "id": 2,
                    "label": "Variety 02",
                    "name": "Reindeer Moss",
                    "slug": "reindeer-moss",
                    "description": "A sculptural lichen with intricate branching forms. Available in a spectrum of hues from natural sage to charcoal, perfect for artistic wall compositions.",
                    "image": null,
                    "gallery_url": "/gallery?product=moss-creations&variety=reindeer-moss"
                }
            ]
        },
        "related": [
            {
                "id": 8,
                "name": "Bark Panels",
                "slug": "bark-panels",
                "description": null,
                "cover_image": null,
                "cta_url": "/products/bark-panels"
            },
            {
                "id": 2,
                "name": "Bespoke Artificial Trees",
                "slug": "bespoke-artificial-trees",
                "description": null,
                "cover_image": null,
                "cta_url": "/products/bespoke-artificial-trees"
            }
        ]
    }
}
```

**Field notes**

| Field | Notes |
|---|---|
| `variants[]` | The numbered blocks down the page, pre-sorted. |
| `variants[].display_no` | The large numeral. **Free text, not a sequence** — the design repeats values (`01, 01, 01, 02, 02`). Render as given; do not compute it from the index. |
| `variants[].image` | First image, used as the block image. |
| `variants[].images` | Full set including `image`. Feeds the block gallery. |
| `variants[].spec` | The small card. `label`/`value` may be `null`; `tags` is always an array, possibly empty. |
| `variants[].project_url` | `null` when no published project is linked — **hide the "View project" CTA in that case.** Most variants are `null` today. |
| `variants[].gallery_url` | This collection's gallery page. The gallery is per collection, so every variant and variety of a product shares one URL. |
| `varieties_section` | The "Choose Your Texture & Feel" grid. `title`/`intro`/`footer` are editable and may be `null`. |
| `varieties_section.items` | May be `[]` — most products have no varieties. Hide the whole section when empty. |
| `related` | Up to 5 other published products, alphabetical. Excludes the current product. |

---

## Gallery

### `GET /api/v1/pages/gallery/{slug}`

One collection's gallery page. **The same screen serves every collection** —
`slug` is a product slug, e.g. `/api/v1/pages/gallery/bespoke-artificial-trees`.

This is BRD section 6 in practice: images are **gathered at read time, never
copied**. An image uploaded once against a project appears on the gallery of
every collection that project is tagged to, and is still stored exactly once.

**Parameters** — `slug` in path.

**Response**

```json
{
    "data": {
        "section": {
            "title": "gallery",
            "subtitle": "view our gallery"
        },
        "collection": {
            "id": 2,
            "name": "Bespoke Artificial Trees",
            "slug": "bespoke-artificial-trees",
            "description": null,
            "hero_image": null,
            "product_url": "/products/bespoke-artificial-trees"
        },
        "images": [
            {
                "url": "https://res.cloudinary.com/dzcfhoulx/image/upload/f_auto,q_auto/gaf/69/projects2.jpg.jpg",
                "alt": "projects2",
                "source": "project",
                "project": {
                    "id": 2,
                    "title": "Corporate Elegance",
                    "slug": "corporate-elegance",
                    "url": "/portfolio/corporate-elegance"
                }
            },
            {
                "url": "https://res.cloudinary.com/dzcfhoulx/image/upload/f_auto,q_auto/gaf/68/projects1.jpg.jpg",
                "alt": "projects1",
                "source": "project",
                "project": {
                    "id": 1,
                    "title": "Serenity Greens",
                    "slug": "serenity-greens",
                    "url": "/portfolio/serenity-greens"
                }
            }
        ],
        "related": [
            {
                "id": 8,
                "name": "Bark Panels",
                "slug": "bark-panels",
                "cover_image": null,
                "gallery_url": "/gallery/bark-panels"
            },
            {
                "id": 4,
                "name": "Biophilic Designs & Indoor Landscapes",
                "slug": "biophilic-designs-indoor-landscapes",
                "cover_image": null,
                "gallery_url": "/gallery/biophilic-designs-indoor-landscapes"
            }
        ]
    },
    "meta": {
        "total_images": 5
    }
}
```

**Field notes**

| Field | Notes |
|---|---|
| `section` | Shared copy for all gallery pages — same design, one copy set. |
| `collection.hero_image` | Dedicated banner if uploaded, otherwise falls back to the collection cover. `null` only when neither exists. |
| `images[].source` | `project` — pulled from a linked portfolio project. `product` — uploaded directly against the collection. |
| `images[].project` | Present only when `source` is `project`; use it to link the tile back to the case study. `null` otherwise. |
| `related[]` | Sibling collections. Carries `gallery_url`, not `cta_url` — these link to other galleries, not product pages. |
| `meta.total_images` | Count before any client-side paging. There is no server-side paging on this endpoint. |

**Behaviour the frontend can rely on**

- Unpublishing, archiving or deleting a project **immediately** removes its
  images from every linked gallery. No cache to bust.
- An admin can hide an individual project image from galleries while it stays
  visible on the project's own page.
- `images` may be `[]` when a collection has no linked published projects and
  no standalone uploads. Render an empty state.

---

## Other endpoints

These predate the v1 restructure and are documented as they currently behave.
They will move under `/api/v1` as each page is rebuilt.

### Homepage

| Endpoint | Returns |
|---|---|
| `GET /api/homepage/sections` | Section copy keyed by `section_key`. **No `data` wrapper.** |
| `GET /api/homepage/hero` | `{ data: [{ id, headline, subtext, cta_label, cta_url, image }] }` |
| `GET /api/homepage/featured` | `{ data: [{ id, title, slug, cover_image, category, sectors[], location }] }` — max 6 |
| `GET /api/homepage/testimonials` | `{ data: [{ id, customer_name, customer_title, quote, avatar }] }` |
| `GET /api/homepage/partners` | `{ data: [{ id, name, website_url, logo }] }` |
| `GET /api/homepage/blog-preview` | `{ data: { categories[], posts[] } }` — max 6 posts |
| `GET /api/homepage/products-preview` | `{ data: [ product with variants ] }` |

> **Known issue.** The homepage currently costs **seven requests**. These should
> collapse into a single `GET /api/v1/pages/home`. Until then, fetch them in
> parallel with `Promise.all` — never sequentially.

### Portfolio

| Endpoint | Notes |
|---|---|
| `GET /api/portfolio` | Filters: `sector`, `location`, `installation_type`, `product_variant` (all by slug). **Returns a bare array — no envelope, and unpaginated.** |
| `GET /api/portfolio/{slug}` | Bare object, no envelope. |

### Services, Blog, Filters

| Endpoint | Notes |
|---|---|
| `GET /api/services` | `{ success, message, data, meta }`. Params: `search`, `page`, `per_page` (default 15) |
| `GET /api/services/{slug}` | Same envelope, single object |
| `GET /api/blog` | `{ data, meta }`. Params: `blogs` (category slug — note the name), `page`, `per_page` (default 12). Returns full `content` for every post in the list. |
| `GET /api/blog/{slug}` | `{ data }` |
| `GET /api/filters` | Flat object: `sectors`, `locations`, `installation_types`, `products`. **Not status-filtered — may include unpublished taxonomies.** |

---

## Deprecated

| Endpoint | Replacement | Notes |
|---|---|---|
| `GET /api/products` | `GET /api/v1/products` | Returns the v1 payload plus `success`/`message`. Additive, so existing code keeps working. |
| `GET /api/products/{slug}` | `GET /api/v1/products/{slug}` | As above. |
| `GET /api/productcategory/categories` | `GET /api/v1/pages/products` | Duplicate of `homepage/products-preview`. |

Legacy product routes are a thin wrapper over the same code path as v1 — there is
no second implementation to drift. They will be deleted once the frontend migrates.

---

## Consuming this from Next.js

Page endpoints are designed for Server Components. One `fetch` per route:

```ts
// app/products/[slug]/page.tsx
const res = await fetch(`${process.env.API_URL}/api/v1/products/${slug}`, {
  next: { revalidate: 300 },
});
if (res.status === 404) notFound();
const { data } = ProductDetailSchema.parse(await res.json());
```

Validate every response with zod — the nullable fields above are genuinely
nullable, and a missing cover image is normal, not an error.

When a screen needs more than one endpoint, fetch in parallel:

```ts
const [hero, featured, partners] = await Promise.all([...]);
```

---

## Known gaps

Tracked so the frontend is not surprised.

1. **Gallery pages have no paging.** `images` returns every match; a collection
   with many linked projects will return a large array.
2. **Variant `cta_url` is always `null`** — no variant-level screen in the design.
3. **Five response envelopes remain** across the unversioned endpoints. v1 uses
   one; the rest are frozen until each page is rebuilt.
4. **`/api/portfolio` is unpaginated** and returns every published project.
5. **`/api/filters` leaks unpublished records.**
6. **No rate limiting** on any endpoint.
