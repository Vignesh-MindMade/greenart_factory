-- =====================================================================
-- GAF — live database update
--
-- Brings a database at commit 472b19a ("updated excerpt in blog api")
-- up to 575ce1f. Adds the products detail, gallery, content-block and
-- project case-study schema.
--
-- Generated from the Laravel migrations with `migrate --pretend`, then
-- verified by applying it to a scratch database and diffing the result
-- against a normally-migrated one. Zero differences.
--
-- Adds 2 tables and 18 columns. No column is dropped, renamed or
-- retyped, so existing rows and existing API responses are unaffected.
--
-- BEFORE RUNNING
--   1. Take a backup:
--        mysqldump -u USER -p DBNAME > gaf-backup-$(date +%F).sql
--   2. Confirm your starting point — this must return 0 rows:
--        SELECT migration FROM migrations
--        WHERE migration LIKE '2026_08_30%' OR migration LIKE '2026_08_31%';
--      If it returns rows, part of this is already applied. Stop and check.
--   3. Run inside a transaction if your MySQL supports transactional DDL
--      (MySQL 8 does not — the backup in step 1 is your rollback).
-- =====================================================================

-- ---------------------------------------------------------------------
-- 1. product_variants — detail page blocks
--    Each variant renders as a numbered block with a spec card.
-- ---------------------------------------------------------------------
ALTER TABLE `product_variants` ADD `description` longtext NULL AFTER `slug`;
ALTER TABLE `product_variants` ADD `display_no` varchar(8) NULL AFTER `description`;
ALTER TABLE `product_variants` ADD `spec_label` varchar(255) NULL AFTER `display_no`;
ALTER TABLE `product_variants` ADD `spec_value` varchar(255) NULL AFTER `spec_label`;
ALTER TABLE `product_variants` ADD `spec_tags` json NULL AFTER `spec_value`;
ALTER TABLE `product_variants` ADD `sort_order` smallint unsigned NOT NULL DEFAULT '0' AFTER `spec_tags`;

-- ---------------------------------------------------------------------
-- 2. product_varieties — the "Choose Your Texture & Feel" grid
-- ---------------------------------------------------------------------
CREATE TABLE `product_varieties` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `product_id` bigint unsigned NOT NULL,
  `label` varchar(255) NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text NULL,
  `sort_order` smallint unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL
) DEFAULT CHARACTER SET utf8mb4 COLLATE 'utf8mb4_unicode_ci';

ALTER TABLE `product_varieties`
  ADD CONSTRAINT `product_varieties_product_id_foreign`
  FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

ALTER TABLE `product_varieties`
  ADD UNIQUE `product_varieties_product_id_slug_unique` (`product_id`, `slug`);

-- ---------------------------------------------------------------------
-- 3. products — varieties section copy
-- ---------------------------------------------------------------------
ALTER TABLE `products` ADD `varieties_title` varchar(255) NULL AFTER `description`;
ALTER TABLE `products` ADD `varieties_intro` text NULL AFTER `varieties_title`;
ALTER TABLE `products` ADD `varieties_footer` varchar(255) NULL AFTER `varieties_intro`;

-- ---------------------------------------------------------------------
-- 4. Publish control on variants and varieties
--
--    The column default is 'draft', but existing rows are backfilled to
--    'published'. They are already live, and leaving them at the default
--    would empty every product detail page the moment this runs.
--    The two UPDATEs are not optional.
-- ---------------------------------------------------------------------
ALTER TABLE `product_variants`  ADD `status` varchar(255) NOT NULL DEFAULT 'draft' AFTER `sort_order`;
ALTER TABLE `product_varieties` ADD `status` varchar(255) NOT NULL DEFAULT 'draft' AFTER `sort_order`;

UPDATE `product_variants`  SET `status` = 'published';
UPDATE `product_varieties` SET `status` = 'published';

-- ---------------------------------------------------------------------
-- 5. content_blocks — shared ordered lists
--    One polymorphic table for every "title + description attached to a
--    parent" list: Why Choose Us, project spec rows, and later careers
--    benefits, service FAQs, core values and company stats.
-- ---------------------------------------------------------------------
CREATE TABLE `content_blocks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `blockable_type` varchar(255) NOT NULL,
  `blockable_id` bigint unsigned NOT NULL,
  `group` varchar(40) NOT NULL,
  `display_no` varchar(8) NULL,
  `title` varchar(255) NOT NULL,
  `description` text NULL,
  `value` varchar(255) NULL,
  `sort_order` smallint unsigned NOT NULL DEFAULT '0',
  `status` varchar(255) NOT NULL DEFAULT 'draft',
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL
) DEFAULT CHARACTER SET utf8mb4 COLLATE 'utf8mb4_unicode_ci';

ALTER TABLE `content_blocks`
  ADD INDEX `content_blocks_blockable_type_blockable_id_index` (`blockable_type`, `blockable_id`);

ALTER TABLE `content_blocks`
  ADD INDEX `content_blocks_lookup_index` (`blockable_type`, `blockable_id`, `group`, `sort_order`);

-- ---------------------------------------------------------------------
-- 6. portfolio_projects — case study body copy
--    challenge and solution are separate columns per BRD FR-2.3, even
--    though the design renders them as one block.
-- ---------------------------------------------------------------------
ALTER TABLE `portfolio_projects` ADD `excerpt` text NULL AFTER `slug`;
ALTER TABLE `portfolio_projects` ADD `execution` longtext NULL AFTER `excerpt`;
ALTER TABLE `portfolio_projects` ADD `key_stages` longtext NULL AFTER `execution`;
ALTER TABLE `portfolio_projects` ADD `key_highlights` longtext NULL AFTER `key_stages`;
ALTER TABLE `portfolio_projects` ADD `challenge` longtext NULL AFTER `key_highlights`;
ALTER TABLE `portfolio_projects` ADD `solution` longtext NULL AFTER `challenge`;

-- ---------------------------------------------------------------------
-- 7. portfolio_projects — the dark specification card
--    Its label/value rows live in content_blocks (group 'project_spec').
-- ---------------------------------------------------------------------
ALTER TABLE `portfolio_projects` ADD `spec_eyebrow` varchar(255) NULL AFTER `solution`;
ALTER TABLE `portfolio_projects` ADD `spec_headline` text NULL AFTER `spec_eyebrow`;
ALTER TABLE `portfolio_projects` ADD `spec_body` longtext NULL AFTER `spec_headline`;

-- ---------------------------------------------------------------------
-- 8. Record the migrations as run
--
--    Without this, `php artisan migrate` on the server will try to apply
--    them again and fail on "column already exists".
--
--    Adjust the batch number if your migrations table is further along;
--    any value greater than the current MAX(batch) is fine.
-- ---------------------------------------------------------------------
INSERT INTO `migrations` (`migration`, `batch`) VALUES
  ('2026_08_30_142207_add_detail_fields_to_product_variants_table',      (SELECT * FROM (SELECT COALESCE(MAX(`batch`),0) + 1 FROM `migrations`) AS b)),
  ('2026_08_30_142207_create_product_varieties_table',                   (SELECT * FROM (SELECT COALESCE(MAX(`batch`),0) FROM `migrations`) AS b)),
  ('2026_08_30_142208_add_varieties_section_to_products_table',          (SELECT * FROM (SELECT COALESCE(MAX(`batch`),0) FROM `migrations`) AS b)),
  ('2026_08_30_144333_add_status_to_product_variants_and_varieties_tables', (SELECT * FROM (SELECT COALESCE(MAX(`batch`),0) FROM `migrations`) AS b)),
  ('2026_08_30_151939_create_content_blocks_table',                      (SELECT * FROM (SELECT COALESCE(MAX(`batch`),0) FROM `migrations`) AS b)),
  ('2026_08_30_160334_add_detail_fields_to_portfolio_projects_table',    (SELECT * FROM (SELECT COALESCE(MAX(`batch`),0) FROM `migrations`) AS b)),
  ('2026_08_31_045343_add_spec_card_to_portfolio_projects_table',        (SELECT * FROM (SELECT COALESCE(MAX(`batch`),0) FROM `migrations`) AS b));

-- =====================================================================
-- AFTER RUNNING
--
--   php artisan config:clear
--   php artisan migrate:status     -- all seven should read "Ran"
--
-- Optional seed data, if you want the demo content rather than entering
-- it in the dashboard. Safe to re-run; both are idempotent:
--
--   php artisan db:seed --class=PageSectionSeeder    -- adds 'gallery' and
--                                                       'portfolio' sections
--   php artisan db:seed --class=WhyChooseUsSeeder    -- the 5 products items
--
-- The 'gallery' and 'portfolio' page_sections rows are required for the
-- gallery and portfolio page endpoints to return their heading copy.
-- Without them those endpoints still work, but section fields are null.
-- =====================================================================
