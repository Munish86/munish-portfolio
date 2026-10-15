# Portfolio website (WordPress)

Personal portfolio for a web developer / frontend developer / UI/UX designer / web designer. Everything on the site is
managed in wp-admin: projects, case studies, services, testimonials, experience, blog posts, and a Portfolio Settings
page for the rest (profile, home page sections, skills, social links, colours).
Owner's guide (Hinglish): `docs/OWNER-GUIDE.md`. State and decisions: `docs/PROGRESS.md`.

## Local site

| | |
|---|---|
| Site | http://localhost:8090 (Apache vhost in `C:\xampp\apache\conf\extra\httpd-vhosts.conf`) |
| Admin | http://localhost:8090/wp-admin/ (login in `local-login.txt`, not for sharing) |
| WordPress | 7.1.3 in `C:\xampp\htdocs\portfolio` (core copied from the Bulk Uploader site install) |
| Database | `portfolio_wp` on XAMPP MariaDB, user `root` (local only) |

The theme and plugin folders in WordPress are **junctions** to this folder, so editing files here changes the site:

- `C:\xampp\htdocs\portfolio\wp-content\themes\folio` -> `theme/folio`
- `C:\xampp\htdocs\portfolio\wp-content\plugins\folio-core` -> `plugin/folio-core`

## What is here

```
plugin/folio-core/            Content lives in the plugin, so it survives a theme change.
  includes/post-types.php     Projects (/work/), Case Studies (/case-studies/), Services (/services/), Testimonials,
                              Experience; taxonomies "Work types" (filter buttons) and "Tools & tech"; field lists
  includes/fields.php         One renderer + cleaner per field type (text, url, textarea, select, checkbox, colour,
                              image, gallery, file, repeater, post link), shared by meta boxes and settings
  includes/meta-boxes.php     Details box under the editor; values saved as post meta "_folio_<key>"
  includes/settings.php       Portfolio Settings (option "folio_settings"), tabs: Profile, Home page, About & skills,
                              Page headings, Contact, Social links, Colours & footer. Defaults live with the fields
  includes/messages.php       Contact form [folio_contact]; table {prefix}folio_messages; Messages admin page + CSV;
                              honeypot, signed time stamp, 6/hour per IP (hashed)
  includes/admin.php          List columns (image, home page star, order), Dashboard box, "Delete sample content"
  includes/seo.php            Person schema on the home page; meta/OG fallback when no SEO plugin is active
  includes/helpers.php        folio_opt(), folio_meta(), folio_highlight() (*word* = coloured), icons (Lucide/Tabler)
  assets/admin.js|css         Tabs, media pickers, gallery order, repeater rows, colour pickers
theme/folio/                  Classic PHP theme (needs Folio Core; falls back to defaults without it)
  front-page.php              Hero, tools strip, client logos, services, selected work, case studies, about,
                              process, page text, testimonials, blog, CTA (each switchable in settings)
  archive-project.php         Work page with filter buttons (JS, links work without JS); also work-type/tool pages
  single-project.php, single-case_study.php, archive-case_study.php, archive-service.php, single-service.php
  template-about.php          Page template "About" (photo, text, CV, stats, skills, experience timeline, quotes)
  template-contact.php        Page template "Contact" (details, booking link, form; page text below, e.g. FAQ)
  index.php, single.php, page.php, search.php, 404.php
  inc/template-tags.php       Cards and shared pieces; folio_contact_url()
  assets/css/site.css         Design tokens (light/dark), components, responsive
  assets/js/site.js           Light/dark switch, mobile menu, reveal on scroll, counters, filters, lightbox
content/images/               Sample images drawn by tools/make-images.php
tools/
  wp.sh                       WP-CLI for the local site (XAMPP PHP)
  setup-content.php           Pages, menus, front/posts page and sample content ("force" = delete samples first)
  make-images.php             Draws the sample mock-ups and blog covers (PHP GD, Segoe UI)
  shots.mjs                   Screenshots at 1440px and 390px + console/404/overflow checks
  check-site.mjs              Dark mode, filter, lightbox, contact form, mobile menu, admin screens
  split-shot.php              Cuts a tall screenshot into parts for review
```

## Commands (Git Bash, from this folder)

```
tools/wp.sh <any wp-cli command>
tools/wp.sh eval-file tools/setup-content.php --user=folioadmin          # add what is missing
tools/wp.sh eval-file tools/setup-content.php force --user=folioadmin    # delete samples and add them again
php -d memory_limit=1G tools/make-images.php                             # redraw sample images (~3 min)
node tools/shots.mjs work/ about/                                        # no args = home page
node tools/check-site.mjs                                                # sends one test message, delete it after
```

## Conventions

- Sample items and their images carry post meta `_folio_sample`; Dashboard > "Delete sample content" removes them.
- Projects, case studies, services, testimonials and experience use the classic editor on purpose: in WordPress 7's
  block editor the detail fields sit in a collapsed "Meta Boxes" pane that owners miss. Posts and pages use blocks.
- Order on the site = the "Order" number (lower first), then newest.
- No third-party requests: fonts (Space Grotesk, Inter; OFL) are self-hosted, no analytics, no visitor cookies.
