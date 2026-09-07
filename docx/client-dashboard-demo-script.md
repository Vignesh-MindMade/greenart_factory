# GAF Admin Dashboard — Client Demo Script

**Audience:** Client (non-technical)
**Duration:** ~25–30 min demo + Q&A
**Goal:** Show the client they can manage their entire website content — portfolio, products, services, homepage, blog — without touching code.

---

## 0. Before you start (do this, don't say it out loud)

- [ ] Confirm you're demoing on **local**, not staging/production — the panel currently 403s anywhere `APP_ENV` isn't `local` (no roles/auth wired up yet). Don't try this on a shared server.
- [ ] Seed the DB with clean, presentable content beforehand (`php artisan migrate:fresh --seed` + a few real-looking portfolio projects/products with images). Empty tables look broken, not "ready for your content."
- [ ] Have 2–3 real project photos ready to upload live — nothing sells a media library like watching an image go from empty state to rendered thumbnail in 2 seconds (Cloudinary).
- [ ] Log in once beforehand to make sure nothing regressed. Login: `test@example.com` / `password` (swap for a real client-branded account before actual handover).
- [ ] Have a **written punch list of what's NOT built yet** ready in your head (see §6) so if the client clicks into it, you have a confident one-line answer instead of an "uh."

**Known rough edges — steer around these, don't demo them:**
- `Product` and `ServiceItem` cover images can silently fail to match what the public site reads (media collection mismatch on a few models — being cleaned up). Stick to the resources that are solid: Portfolio Projects, Hero Slides, Testimonials, Partners, Blog.
- Don't click "bulk delete" on Page Sections — delete guard isn't applied to the bulk action there.
- Don't demo the public API directly (`/api/...`) unless asked — a couple of endpoints are still being hardened.

---

## 1. Open — set the frame (2 min)

> "Today I want to show you the admin dashboard you'll use to run the site day-to-day — no developer needed for content updates. Everything you see here — your portfolio projects, product catalogue, services, homepage banners, testimonials, blog — you'll manage directly from this screen."

Navigate to `/admin`, log in on screen (don't paste the password into chat/screenshare if recording).

> "This is built on Filament — a modern admin framework — so it's fast, and every screen follows the same pattern: a list, a search bar, and a form to add or edit."

---

## 2. Orientation — the navigation (2 min)

Point at the left sidebar. It's grouped into:

- **Homepage** — Hero Slides, Testimonials, Partners, Page Sections
- **Blogs** — Blog Categories, Blog Posts
- **(Ungrouped, catalogue/portfolio)** — Products, Portfolio Projects, Portfolio Categories, Locations, Sectors, Installation Types, Services

> "Think of it in two halves: content that drives your **homepage and marketing** — banners, testimonials, partner logos, blog — and your **catalogue** — products, services, and your project portfolio with all its filters."

---

## 3. Core walkthrough — Portfolio Projects (the centerpiece, ~8 min)

This is the richest resource and the one worth spending the most time on.

1. Open **Portfolio Projects** → show the list: title, status, featured flag.
2. Click **Create**.
3. Walk through the form live:
   - Title → **show the slug auto-generating as you type** (nice, tangible "wow" moment — mention slugs don't regenerate after creation, so they should get the title right the first time, or ask you to fix it).
   - Category and Location — pick from the dropdown (mention: Location captures country too, from a fixed list of GCC + "Other").
   - Sectors / Installation Types / Product Variants — **multi-select, searchable** — this is how a project can show up under multiple filters on the public site at once.
   - **Cover image + gallery images** — drag/drop an upload, watch it land in the Cloudinary-backed media library. Point out this is what powers the fast-loading images on the live site.
   - **Project Specification card** — show the spec details (location, category, etc.) pulling straight from what was just entered — no duplicate data entry.
   - Status: draft vs published, and Featured + featured order (controls homepage placement).
4. Save → back to list → open it again to prove it persisted.

> "Every portfolio project you publish here appears instantly on the live site's project listing and filters — sector, location, installation type — all driven by what you picked in this form."

---

## 4. Quick tour — Homepage content (5 min)

Move faster here — these are simpler, repetitive CRUD screens, and repetition reinforces "this is easy."

- **Hero Slides** — show reordering (drag handles) — "this is literally the banner rotation order on your homepage."
- **Testimonials** — add one: name, title, quote, status.
- **Partners** — logo + website link, sort order.
- **Page Sections** — explain this is different: these rows are pre-seeded, editable but not creatable/deletable — "these map to fixed sections of your homepage copy — you're editing the words, not the layout."

---

## 5. Quick tour — Catalogue (5 min)

- **Products** → open one → show the **Variants** relation manager (a product can have multiple variants, each with its own name, slug, and images).
- **Services** → open one → show the **Service Items** relation manager (same pattern — one service, multiple sub-items, each with images).
- **Blog Posts** → create/open one → show the rich text editor (RichEditor toolbar), category select, author, and publish date/status. Mention: this feeds the blog listing and detail pages on the live site.

---

## 6. If they ask "what's left" / "what's next" (have this ready, don't volunteer it)

Be honest and confident, framed as roadmap not defect:

- **User roles/permissions** — right now any dashboard login has full access to everything. A Super Admin / Content Editor / HR Manager split is scoped and coming before go-live (per the BRD).
- **Arabic/RTL support** — planned; translation strategy (per-field vs. separate table) is being finalized before it's built, so it's done once, correctly.
- **Public site (Next.js frontend)** — separate repo, in progress; this dashboard is the content engine feeding it via the API.
- A few backend polish items (media consistency, API response formatting) are being tightened up as part of hardening before launch — invisible to what you do in the dashboard.

---

## 7. Close (1 min)

> "So to summarize: anything on your website — a new project, a new product line, a homepage banner, a blog post — you add here, save, and it's live. No code, no developer needed for day-to-day updates. We'll follow up with login credentials and a short written guide once we lock the final content."

Stop screen share. Open floor for questions.

---

## Appendix — fast reference of what maps to what (for your own Q&A confidence)

| Client asks about... | Dashboard section |
|---|---|
| "Our project gallery / case studies" | Portfolio Projects (+ Categories, Locations, Sectors, Installation Types as filters) |
| "Our product catalogue" | Products → Variants |
| "Our services page" | Services → Service Items |
| "Homepage banner/slider" | Hero Slides |
| "Customer reviews" | Testimonials |
| "Client/partner logos" | Partners |
| "Blog / news" | Blog Categories, Blog Posts |
| "Homepage text (headings, intro copy)" | Page Sections |
