// End-to-end checks of the local portfolio: dark mode toggle, mobile menu, work filter, lightbox, contact form
// (message saved in Messages), and the admin screens (Portfolio Settings, Project editor, Messages) without PHP errors.
// Usage: node tools/check-site.mjs   (needs the login in local-login.txt)
import fs from "node:fs";
import path from "node:path";
import { createRequire } from "node:module";
import { fileURLToPath } from "node:url";

const here = path.dirname(fileURLToPath(import.meta.url));
const base = ["../../restock-app", "../../shopify-bulk-uploader-app"].map((p) => path.resolve(here, p)).find((p) => fs.existsSync(path.join(p, "node_modules/playwright-core")));
const { chromium } = createRequire(path.join(base, "package.json"))("playwright-core");
const site = process.env.SITE || "http://localhost:8090";
const login = fs.readFileSync(path.join(here, "../local-login.txt"), "utf8");
const user = login.match(/User: (.+)/)[1].trim();
const pass = login.match(/Password: (.+)/)[1].trim();
const out = path.join(here, ".shots");
let failed = 0;
const check = (ok, label) => { console.log(`${ok ? "ok  " : "FAIL"} ${label}`); if (!ok) failed++; };

const browser = await chromium.launch({ executablePath: "C:/Program Files (x86)/Microsoft/Edge/Application/msedge.exe" });
const ctx = await browser.newContext({ viewport: { width: 1440, height: 900 } });
const page = await ctx.newPage();
const errors = [];
page.on("pageerror", (e) => errors.push(e.message));

// Dark mode.
await page.goto(site + "/", { timeout: 90000 });
const before = await page.getAttribute("html", "data-theme");
await page.click(".theme-toggle");
const after = await page.getAttribute("html", "data-theme");
check(before !== after && ["light", "dark"].includes(after), `theme toggle ${before} -> ${after}`);
await page.evaluate(() => document.querySelectorAll(".reveal").forEach((e) => e.classList.add("is-in")));
await page.waitForTimeout(800);
await page.screenshot({ path: path.join(out, "dark-home-top.png") });
await page.reload({ timeout: 90000 });
check((await page.getAttribute("html", "data-theme")) === after, "theme choice remembered after reload");
await page.click(".theme-toggle");

// Work filter.
await page.goto(site + "/work/", { timeout: 90000 });
await page.click('.filters [data-filter="ui-ux-design"]');
const visible = await page.$$eval(".work-card", (cards) => cards.filter((c) => !c.classList.contains("is-hidden")).length);
const all = await page.$$eval(".work-card", (c) => c.length);
check(visible > 0 && visible < all, `filter UI/UX shows ${visible} of ${all}`);

// Lightbox.
await page.goto(site + "/work/northwind-coffee/", { timeout: 90000 });
await page.click("[data-lightbox]");
check(await page.isVisible(".lightbox img"), "gallery image opens in lightbox");
await page.keyboard.press("Escape");
check(await page.isHidden(".lightbox"), "lightbox closes with Escape");

// Contact form.
await page.goto(site + "/contact/", { timeout: 90000 });
await page.fill('[name="folio_name"]', "Test Person");
await page.fill('[name="folio_email"]', "test@example.com");
await page.selectOption('[name="folio_service"]', { index: 1 });
await page.selectOption('[name="folio_budget"]', { index: 2 });
await page.fill('[name="folio_message"]', "Automated check message " + Date.now());
await page.waitForTimeout(3500); // the form refuses sends faster than 3 seconds (bots)
await Promise.all([page.waitForNavigation({ timeout: 90000 }), page.click(".form button[type=submit]")]);
check(await page.isVisible(".alert--ok"), "contact form: success message");

// Mobile menu.
const phone = await browser.newContext({ viewport: { width: 390, height: 844 } });
const pp = await phone.newPage();
await pp.goto(site + "/", { timeout: 90000 });
await pp.click(".nav-toggle");
await pp.waitForTimeout(400);
check(await pp.isVisible(".site-nav .menu a >> nth=0"), "mobile menu opens");
await pp.screenshot({ path: path.join(out, "phone-menu.png") });
await phone.close();

// Admin.
await page.goto(site + "/wp-login.php", { timeout: 90000 });
await page.fill("#user_login", user);
await page.fill("#user_pass", pass);
await Promise.all([page.waitForNavigation({ timeout: 120000 }), page.click("#wp-submit")]);
for (const [p, sel, label] of [
  ["/wp-admin/admin.php?page=folio-messages", "text=Automated check message", "Messages page lists the test message"],
  ["/wp-admin/admin.php?page=folio-settings", ".folio-tabs", "Portfolio Settings page"],
  ["/wp-admin/edit.php?post_type=project", "text=Northwind Coffee", "Projects list"],
  ["/wp-admin/index.php", "#folio_dashboard", "Dashboard box"],
]) {
  await page.goto(site + p, { timeout: 120000 });
  check(await page.isVisible(sel), label);
}
await page.goto(site + "/wp-admin/admin.php?page=folio-settings", { timeout: 120000 });
await page.click('.folio-tabs [data-tab="home"]');
await page.screenshot({ path: path.join(out, "admin-settings.png"), fullPage: false });
const pid = await page.evaluate(async (s) => (await (await fetch(s + "/wp-json/wp/v2/project?slug=atlas-travel")).json())[0].id, site);
await page.goto(`${site}/wp-admin/post.php?post=${pid}&action=edit`, { timeout: 120000 });
await page.waitForSelector("#folio-details", { timeout: 120000 });
await page.evaluate(() => document.querySelector("#folio-details").scrollIntoView());
await page.waitForTimeout(1500);
await page.screenshot({ path: path.join(out, "admin-project.png") });
check(true, "Project editor shows the details box");

check(errors.length === 0, "no JavaScript errors" + (errors.length ? ": " + errors.join(" | ") : ""));
await browser.close();
process.exit(failed ? 1 : 0);
