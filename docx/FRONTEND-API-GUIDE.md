# GAF — Frontend Integration Guide

For the Next.js team. **Screen-first**: start from the Figma panel you are building,
find its route and endpoint, copy the code.

For the endpoint-first contract — every field, every parameter, full response
bodies — see `API-REFERENCE.md` in this folder. This guide tells you *which* API
to call and *what renders from it*; that one tells you what comes back.

---

## 1. The map

Every screen in the Figma file (`DESIGNS 🫶🏻💕`), what it maps to, and whether the
backend is ready.

| Figma screen | Node | Next.js route | Endpoint | Status |
|---|---|---|---|---|
| products | `180:287` | `/products` | `GET /api/v1/pages/products` | ✅ Ready |
| product detail | `545:571` | `/products/[slug]` | `GET /api/v1/products/[slug]` | ✅ Ready |
| portfolio(project) | `545:1216` | `/portfolio` | `GET /api/v1/pages/portfolio` | ✅ Ready |
| project detail | `640:2214` | `/portfolio/[slug]` | `GET /api/v1/projects/[slug]` | ✅ Ready |
| our gallery | `644:2644` | `/gallery/[slug]` | `GET /api/v1/pages/gallery/[slug]` | ✅ Ready |
| HOME | `154:2829` | `/` | 7 × `GET /api/homepage/*` | ⚠️ Legacy — see §6 |
| BLOG | `306:599` | `/blog` | `GET /api/blog` | ⚠️ Legacy |
| BLOG DETAIL | `333:1531` | `/blog/[slug]` | `GET /api/blog/[slug]` | ⚠️ Legacy, missing fields |
| SERVICES | `154:3785` | `/services/[slug]` | `GET /api/services/[slug]` | ⚠️ Partial — see §7 |
| ABOUT US | `154:4260` | `/about` | — | ❌ Not built |
| CAREERS | `208:3185` | `/careers` | — | ❌ Not built |
| CONTACT US | `154:4083` | `/contact` | — | ❌ Not built |

**Build the five ✅ screens now.** The ⚠️ ones work but their shape will change when
each page is rebuilt on `/api/v1`. The ❌ ones have no database tables yet — stub
them with static markup and do not model data against them.

---

## 2. Setup

```bash
# .env.local
API_URL=http://localhost:8000
```

Keep it server-side. Every endpoint is public and read-only, so there is no token,
but there is also no reason to ship the origin to the browser — fetch in Server
Components.

### `lib/api/client.ts`

```ts
import { z } from 'zod';

const BASE = process.env.API_URL;

export class ApiError extends Error {
  constructor(public readonly status: number, message: string) {
    super(message);
    this.name = 'ApiError';
  }
}

/**
 * Every GAF endpoint is a GET returning { data } or { data, meta }.
 * Responses are validated — the nullable fields in this guide are genuinely
 * nullable, and a silent shape change should fail loudly in dev.
 */
export async function apiGet<T>(
  path: string,
  schema: z.ZodType<T>,
  revalidate = 300,
): Promise<T> {
  const res = await fetch(`${BASE}/api${path}`, {
    headers: { Accept: 'application/json' },
    next: { revalidate },
  });

  if (!res.ok) {
    throw new ApiError(res.status, `GET ${path} responded ${res.status}`);
  }

  return schema.parse(await res.json());
}
```

### 404s

A slug that does not exist — or points at an unpublished record — returns `404`.
Map it to Next's `notFound()`:

```ts
import { notFound } from 'next/navigation';
import { ApiError } from '@/lib/api/client';

export async function getOr404<T>(fn: () => Promise<T>): Promise<T> {
  try {
    return await fn();
  } catch (error) {
    if (error instanceof ApiError && error.status === 404) notFound();
    throw error;
  }
}
```

---

## 3. Shared schemas

`lib/api/schemas.ts`. These shapes repeat across screens — define once.

```ts
import { z } from 'zod';

/** Editable copy for a page header. Every field can be null. */
export const Section = z.object({
  title: z.string().nullable(),
  subtitle: z.string().nullable(),
  description: z.string().nullable().optional(),
  cta_label: z.string().nullable().optional(),
  cta_url: z.string().nullable().optional(),
});

/**
 * One item in any ordered list on the site — Why Choose Us today, plus careers
 * benefits, hiring steps, service FAQs and company stats as those ship.
 * One schema, every list.
 */
export const ContentBlock = z.object({
  id: z.number(),
  display_no: z.string().nullable(),   // "01" — render as given, never compute
  title: z.string(),
  description: z.string().nullable(),
  value: z.string().nullable(),        // "98%" — stat lists only
  icon: z.string().nullable(),
});

/** A product card. Same shape in grids and in related rails. */
export const ProductSummary = z.object({
  id: z.number(),
  name: z.string(),
  slug: z.string(),
  description: z.string().nullable(),
  cover_image: z.string().nullable(),
  cta_url: z.string(),
  variants: z.array(z.object({
    id: z.number(),
    name: z.string(),
    slug: z.string(),
    image: z.string().nullable(),
    cta_url: z.null(),   // see §4, product detail
  })).optional(),
});

/** A project card. */
export const ProjectSummary = z.object({
  id: z.number(),
  title: z.string(),
  slug: z.string(),
  excerpt: z.string().nullable(),
  cover_image: z.string().nullable(),
  category: z.object({ name: z.string().nullable(), slug: z.string().nullable() }).nullable().optional(),
  location: z.object({
    name: z.string(),
    slug: z.string(),
    country: z.string().nullable(),
  }).nullable(),
  sectors: z.array(z.object({ name: z.string(), slug: z.string() })).optional(),
  meta_line: z.string().nullable(),   // "Commercial · Dubai · AE" — pre-joined
  cta_url: z.string(),
});

export const Pagination = z.object({
  current_page: z.number(),
  last_page: z.number(),
  per_page: z.number(),
  total: z.number(),
});
```

---

## 4. The five ready screens

### `/products` — Figma `180:287`

```ts
const ProductsPage = z.object({
  data: z.object({
    section: Section,
    products: z.array(ProductSummary),
    why_choose_us: z.array(ContentBlock),
  }),
});

export const getProductsPage = () =>
  apiGet('/v1/pages/products', ProductsPage);
```

| Renders | From |
|---|---|
| Page heading and blurb | `section.title`, `section.description` |
| The collection grid — "Moss Creation", "Vertical Garden" … | `products[]` |
| Each card's **View Now** button | `products[].cta_url` |
| "Why Choose Us?" — the five numbered items | `why_choose_us[]` |

One request renders the page. Do **not** also call `/api/v1/products` — that is the
search/pagination endpoint, not this screen.

---

### `/products/[slug]` — Figma `545:571`

```ts
const Spec = z.object({
  label: z.string().nullable(),
  value: z.string().nullable(),
  tags: z.array(z.string()),
});

const ProductDetail = z.object({
  data: z.object({
    id: z.number(),
    name: z.string(),
    slug: z.string(),
    description: z.string().nullable(),
    cover_image: z.string().nullable(),
    variants: z.array(z.object({
      id: z.number(),
      display_no: z.string().nullable(),
      name: z.string(),
      slug: z.string(),
      description: z.string().nullable(),
      image: z.string().nullable(),
      images: z.array(z.string()),
      spec: Spec,
      project_url: z.string().nullable(),
      gallery_url: z.string(),
    })),
    varieties_section: z.object({
      title: z.string().nullable(),
      intro: z.string().nullable(),
      footer: z.string().nullable(),
      items: z.array(z.object({
        id: z.number(),
        label: z.string().nullable(),
        name: z.string(),
        slug: z.string(),
        description: z.string().nullable(),
        image: z.string().nullable(),
        gallery_url: z.string(),
      })),
    }),
    related: z.array(ProductSummary),
  }),
});

export const getProduct = (slug: string) =>
  apiGet(`/v1/products/${slug}`, ProductDetail);
```

| Renders | From |
|---|---|
| Each numbered block — MOSS WALLS, MOSS LOGOS … | `variants[]` |
| The large numeral beside the heading | `variants[].display_no` |
| The small spec card (Material / 100% Preserved Natural Moss + chips) | `variants[].spec` |
| **View project** link | `variants[].project_url` |
| **view gallery** link | `variants[].gallery_url` |
| "Choose Your Texture & Feel" + the 4 variety tiles | `varieties_section` |
| Related Products rail | `related[]` |

**Three things to handle:**

- `project_url` is `null` on most variants — **hide the View project button** rather
  than rendering a dead link.
- `display_no` repeats (`01, 01, 01, 02, 02`). It is a design label, not a sequence.
  Render the string; never derive it from the array index.
- `varieties_section.items` is `[]` for most products — hide the whole section.
- Variant `cta_url` is always `null`. There is no variant-level screen in the
  design, so variant cards are not links.

---

### `/portfolio` — Figma `545:1216`

```ts
const Option = z.object({ name: z.string(), slug: z.string() });

const PortfolioPage = z.object({
  data: z.object({
    section: Section,
    filters: z.object({
      categories: z.array(Option),
      installation_types: z.array(Option),
      locations: z.array(Option.extend({ country: z.string().nullable() })),
      sectors: z.array(Option),
    }),
    projects: z.array(ProjectSummary),
  }),
  meta: Pagination,
});

export const getPortfolioPage = (params?: URLSearchParams) =>
  apiGet(`/v1/pages/portfolio${params?.size ? `?${params}` : ''}`, PortfolioPage);
```

| Renders | From |
|---|---|
| Hero — "Creating Living Spaces Inspired by Nature…" + View Products | `section` |
| Filter bar — Project Type / Installation Type / Location / Sector | `filters` |
| "Total projects (120)" | `meta.total` |
| The masonry card grid | `projects[]` |
| The line under each card title — "Commercial · Dubai · AE" | `projects[].meta_line` |

The filter bar populates from `filters` in the same response — no second request,
and **do not use `/api/filters`**, which is unversioned and includes unpublished
records.

Filter params (all optional, all slugs): `search`, `category`, `sector`,
`installation_type`, `location`, `product_variant`, `page`, `per_page`.

> `category` is the filter the design labels **Project Type**.

When the user changes a filter, call `/api/v1/projects` with the same params —
identical `projects` shape and `meta`, without re-sending the hero and filter lists.

---

### `/portfolio/[slug]` — Figma `640:2214`

```ts
const ProjectDetail = z.object({
  data: z.object({
    id: z.number(),
    title: z.string(),
    slug: z.string(),
    excerpt: z.string().nullable(),
    hero_image: z.string().nullable(),
    category: z.object({ name: z.string().nullable(), slug: z.string().nullable() }).nullable(),
    location: z.object({
      name: z.string(), slug: z.string(), country: z.string().nullable(),
    }).nullable(),
    sectors: z.array(Option),
    installation_types: z.array(Option),
    breadcrumb: z.array(z.object({
      label: z.string(),
      url: z.string().nullable(),
    })),
    content: z.object({
      execution: z.string().nullable(),
      key_stages: z.string().nullable(),
      key_highlights: z.string().nullable(),
      challenge: z.string().nullable(),
      solution: z.string().nullable(),
    }),
    spec_card: z.object({
      eyebrow: z.string().nullable(),
      headline: z.string().nullable(),
      body: z.string().nullable(),
      rows: z.array(z.object({
        label: z.string(),
        value: z.string().nullable(),
      })),
    }),
    gallery: z.array(z.object({ url: z.string(), alt: z.string() })),
    collections: z.array(z.object({
      name: z.string(),
      slug: z.string(),
      product_url: z.string(),
      gallery_url: z.string(),
    })),
    related: z.array(ProjectSummary),
  }),
});

export const getProject = (slug: string) =>
  apiGet(`/v1/projects/${slug}`, ProjectDetail);
```

| Renders | From |
|---|---|
| "Home > Interior Projects > Artificial tree installation" | `breadcrumb[]` |
| "our challenge & solution" — the green block | `content.challenge` + `content.solution` |
| The **dark card** on its right | `spec_card` |
| Its Area / Location / System / Client rows | `spec_card.rows[]` |
| "Our execution", "Key stages", "Key highlights" | `content.*` |
| PROJECT GALLERY grid | `gallery[]` |
| Related Projects rail | `related[]` |

**Notes:**

- `content.*` and `spec_card.body` are **HTML** from the admin's rich-text editor.
  `key_stages` and `key_highlights` come through as `<ul>` lists.
- **Challenge and solution are two fields but one visual block.** They are stored
  separately because the business requirements ask for it — concatenate them under
  the single "our challenge & solution" heading.
- `breadcrumb`'s last entry has `url: null` — it is the current page, render it as
  plain text.
- `spec_card.rows[]` labels are free text and differ per project ("Moss Coverage"
  on one, something else on another). Render them generically; do not map to fixed
  keys. The **Location** row is derived from the project's own location record, so
  it always agrees with `location.name`.
- Hide `spec_card` entirely when eyebrow, headline, body and rows are all empty.

---

### `/gallery/[slug]` — Figma `644:2644`

`slug` is a **product** slug. One design serves every collection.

```ts
const GalleryPage = z.object({
  data: z.object({
    section: Section,
    collection: z.object({
      id: z.number(),
      name: z.string(),
      slug: z.string(),
      description: z.string().nullable(),
      hero_image: z.string().nullable(),
      product_url: z.string(),
    }),
    images: z.array(z.object({
      url: z.string(),
      alt: z.string(),
      source: z.enum(['project', 'product']),
      project: z.object({
        id: z.number(),
        title: z.string(),
        slug: z.string(),
        url: z.string(),
      }).nullable(),
    })),
    related: z.array(z.object({
      id: z.number(),
      name: z.string(),
      slug: z.string(),
      cover_image: z.string().nullable(),
      gallery_url: z.string(),
    })),
  }),
  meta: z.object({ total_images: z.number() }),
});

export const getGallery = (slug: string) =>
  apiGet(`/v1/pages/gallery/${slug}`, GalleryPage);
```

| Renders | From |
|---|---|
| Hero banner | `collection.hero_image` |
| The image grid | `images[]` |
| "Related Images" rail — other collections | `related[]` |

`images[].source` tells you where a tile came from: `project` means it was pulled
from a linked case study, and `project` carries the link back to it. `product`
means it was uploaded straight onto the collection. Use it to decide whether a
tile is clickable.

`images` may be `[]` — render an empty state. There is **no paging** on this
endpoint; a collection linked to many projects returns one large array.

---

## 5. Page skeleton

```tsx
// app/products/[slug]/page.tsx
import { getOr404 } from '@/lib/api/client';
import { getProduct } from '@/lib/api/products';

export const revalidate = 300;

export default async function ProductPage(
  { params }: { params: Promise<{ slug: string }> },
) {
  const { slug } = await params;
  const { data } = await getOr404(() => getProduct(slug));

  return (
    <main>
      <h1>{data.name}</h1>

      {data.variants.map((variant) => (
        <section key={variant.id}>
          {variant.display_no && <span>{variant.display_no}</span>}
          <h2>{variant.name}</h2>
          {variant.image && <img src={variant.image} alt={variant.name} />}

          {variant.spec.label && (
            <dl>
              <dt>{variant.spec.label}</dt>
              <dd>{variant.spec.value}</dd>
            </dl>
          )}
          {variant.spec.tags.map((tag) => <span key={tag}>{tag}</span>)}

          {/* null on most variants — no link rather than a dead one */}
          {variant.project_url && <a href={variant.project_url}>View project</a>}
          <a href={variant.gallery_url}>view gallery</a>
        </section>
      ))}

      {data.varieties_section.items.length > 0 && (
        <section>
          <h2>{data.varieties_section.title}</h2>
          {/* … */}
        </section>
      )}
    </main>
  );
}
```

---

## 6. HOME — still seven requests

The homepage has no page endpoint yet. It currently costs **seven** calls:

```
/api/homepage/sections          section headings, keyed by section_key
/api/homepage/hero              hero slides
/api/homepage/featured          featured projects (max 6)
/api/homepage/testimonials
/api/homepage/partners          the "Our Clients" strip
/api/homepage/blog-preview      { categories, posts }
/api/homepage/products-preview  products with variants
```

Fetch them **in parallel** — never in sequence:

```ts
const [sections, hero, featured, testimonials, partners, blog, products] =
  await Promise.all([
    apiGet('/homepage/sections', SectionsSchema),
    apiGet('/homepage/hero', HeroSchema),
    // …
  ]);
```

These collapse into one `GET /api/v1/pages/home` when the homepage is rebuilt.
Keep the fetching in one module so the swap is a single edit.

`/api/homepage/sections` has **no `data` wrapper** — it returns the object directly.
It is the only endpoint that does.

---

## 7. Screens you cannot finish yet

| Screen | What is missing |
|---|---|
| **SERVICES** | `/api/services` returns name, slug and item images only. The design needs a heading and body, six capability chips, a five-step process, two stats and six FAQs. None are modelled. |
| **BLOG DETAIL** | No read-time, no author handle, no tags. Author name and date exist. |
| **ABOUT US** | No tables. Needs mission/vision, team, certifications, core values, company stats. |
| **CAREERS** | No tables. Job postings, applications, benefits, hiring process. |
| **CONTACT US** | No tables. Enquiry form, studio locations, business hours. |

The enquiry form appears on **four** screens — Contact, Services, Blog detail and
Project detail. There is no write endpoint yet; it is the first one planned. Do not
wire the form up until it exists.

---

## 8. Rules

1. **Never filter on publish status.** Drafts are excluded server-side. If you
   receive it, it is meant to be public.
2. **Image fields are absolute Cloudinary URLs or `null`** — never a path, never
   `""`. They already carry `f_auto,q_auto`, so the format is negotiated per
   browser. Do not append your own transformations. The doubled extension
   (`.webp.webp`) is intentional; leave it alone.
3. **`*_url` fields are frontend routes**, not API URLs. Use them directly in
   `<Link href>`.
4. **Render arrays in the order given.** Ordering is controlled in the admin.
5. **`per_page` is capped at 48.**
6. **Trust `null`.** A missing cover image is normal, not an error.
7. **Do not hardcode copy that comes from `section`.** Editors change it.

## Known gaps

- No paging on gallery pages.
- Variant `cta_url` is always `null`.
- Legacy endpoints still use several different envelopes; v1 uses one.
- `/api/portfolio` (legacy) is unpaginated — use `/api/v1/projects`.
- `/api/filters` includes unpublished records — use the `filters` block on
  `/api/v1/pages/portfolio`.
- No rate limiting on any endpoint.
