# Portfolio website: progress and handoff

Read this first when starting a new session. Last updated: 2026-10-15 (built locally).
Technical overview: `../README.md`. Owner's guide (Hinglish): `OWNER-GUIDE.md`.

## Goal

The owner's own portfolio as a web developer + frontend developer + UI/UX designer + web designer: professional and
attractive, with everything (projects, case studies, services, etc.) added from wp-admin.

## Decisions

| Topic | Decision |
|---|---|
| Platform | WordPress 7.1.3, own classic PHP theme `folio` + own plugin `folio-core` (content types, fields, settings, form). No page builder, no ACF |
| Why classic theme | Every section is driven by post types and settings; PHP templates keep that simple and the owner never edits markup |
| Local URL | http://localhost:8090 (vhost), database `portfolio_wp`, admin user `folioadmin` |
| Content types | Projects `/work/`, Case Studies `/case-studies/`, Services `/services/` (public); Testimonials, Experience (shown in pages) |
| Editor | Classic editor for the portfolio types (fields visible), blocks for posts/pages |
| Look | Light + dark (visitor toggle, default in settings), accent colours from settings (default violet `#6c4cf5` + coral `#ff6a3d`), Space Grotesk + Inter self-hosted |
| SEO | Yoast SEO active; plugin adds Person schema; fallback meta tags when no SEO plugin |
| Sample content | Clearly marked "Sample" in admin; fictional clients, numbers and quotes. Must be replaced or deleted before going live |

## State on 2026-10-15

- Pages: Home, About (template About), Contact (template Contact + FAQ text), Blog, Privacy Policy; header + footer menus.
- Samples: 6 projects (with galleries), 2 case studies, 6 services, 3 testimonials, 5 experience items, 3 blog posts.
- Site title "Your Name" and no photo yet (hero shows a code card, About shows a monogram until a photo is set).
- Verified: `tools/shots.mjs` home + 14 pages 200 at 1440px and 390px, no console errors, no overflow;
  `tools/check-site.mjs` all passed (dark mode + remembered, work filter, lightbox, contact form saved and listed in
  Messages, mobile menu, settings, project editor, dashboard box, no JS errors); no PHP notices with WP_DEBUG on.

## Open points for the owner

1. Name, photo, email, social links, real numbers (Portfolio > Profile / Home page / Contact / Social links).
2. Replace the sample projects/case studies/testimonials with real work, then Dashboard > "Delete sample content".
   The sample numbers and quotes are made up and must not go live.
3. Admin email is `admin@example.com` (Settings > General): change it, contact form notifications go there.
4. Domain + hosting, then move with All-in-One WP Migration (OWNER-GUIDE.md section 8). Mail on hosting / SMTP plugin.
5. Privacy policy text and prices in Services are placeholders.

## Gotchas

- Same XAMPP/Git Bash notes as `../reorder-desk-website/docs/PROGRESS.md` (path rewriting: pass `work/` not `/work/`
  to shots.mjs; Edge for headless; Apache restart = kill httpd and start `C:\xampp\apache\bin\httpd.exe`).
- Screenshot tools must scroll slowly (lazy images) and force `.reveal` to `.is-in`; a quick full-page shot shows
  empty sections otherwise (real browsers are fine).
- WP-CLI `eval-file` runs the file in a function: use constants, not globals (see setup-content.php).
- `make-images.php` draws rounded shapes from non-overlapping pieces; overlapping pieces show seams with transparent colours.
