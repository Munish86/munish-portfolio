# Portfolio website: owner guide (Hinglish)

Ye aapki apni portfolio website hai (WordPress). Website par jo bhi dikhta hai, sab wp-admin se badla ja sakta hai,
code chhuye bina. Abhi ye sirf aapke computer par chalti hai.

## 1. Site kholna

1. XAMPP Control Panel kholo, **Apache** aur **MySQL** Start karo.
2. Website: **http://localhost:8090**
3. Admin: **http://localhost:8090/wp-admin/** (user/password `portfolio-website/local-login.txt` mein; kisi ko mat bhejna)

## 2. Sabse pehle ye 5 kaam (15 minute)

1. **Portfolio > Profile**: apna naam, title, photo (lagbhag 900x1100 px portrait), short bio, location, CV (PDF).
   Photo lagate hi home page ke hero aur About page par aa jayegi.
2. **Portfolio > Contact**: public email, phone (optional), Calendly/booking link (optional).
3. **Portfolio > Social links**: LinkedIn, GitHub, Dribbble, Behance, Instagram... jo bharoge wahi icons dikhenge.
4. **Portfolio > Home page**: hero headline, numbers (6+ years, 80+ projects...). Sirf sach wale numbers likho.
   Headline mein kisi word ko `*stars*` mein likho to woh gradient colour mein dikhega, jaise `I build *websites*`.
5. **Settings > General**: Site Title = aapka naam, Administration Email = aapki email
   (abhi `admin@example.com` hai; contact form ke messages isi par aate hain).

## 3. Website mein kya-kya hai

| Admin menu | Website par | Kaise |
|---|---|---|
| **Projects** | `/work/` (filter buttons ke saath) + home "Selected work" | Title, write-up, Excerpt (card ki 1 line), Cover image, neeche "Project details": client, role, year, links, gallery, "Show on the home page" |
| **Case Studies** | `/case-studies/` + home | Challenge, Solution, Results (numbers jaise +38% / Conversion rate), client quote, gallery; editor mein poori kahani (research, process) |
| **Services** | `/services/` + home | Icon choose karo, "What is included" (har line ek point), starting price (optional) |
| **Testimonials** | Home + About | Title = client ka naam, text = unki baat, role/company, stars, photo (optional) |
| **Experience** | About page timeline | Title = aapka role, company, from/to, type (Job / Freelance / Education / Award) |
| **Posts** | `/blog/` + home "Latest articles" | Normal WordPress post: Category, Featured image (1600x900), Excerpt |
| **Messages** | - | Contact form se aaye saare messages + "Download CSV" |
| **Portfolio** | Poori site | Profile, home page sections (on/off + titles), skills, page headings, colours, footer |

**Order badalna:** kisi bhi project/service ke right side "Order" box mein number daalo (chhota number pehle aata hai).

**Work types** (Projects > Work types): Web Design, Frontend Development, UI/UX Design, WordPress. Inhi se `/work/`
page par filter buttons bante hain. Naya type bhi bana sakte ho (jaise "Shopify").

**Tools & tech**: har project mein Figma, React, WordPress jaise tags; project page par chips dikhte hain.

## 4. Sample content hatana (zaroori)

Abhi jo 6 projects, 2 case studies, testimonials, experience aur 3 blog posts hain woh **sample** hain (admin list
mein "Sample" likha hai). Unke clients, numbers aur quotes **nakli** hain; live website par nahi jaane chahiye.

- Apna asli kaam add karo (Projects > Add new project), phir
- **Dashboard** > "Your portfolio" box > **Delete sample content**: saare sample items aur unki images ek saath hat
  jayengi; aapke apne items safe rahenge.
- Services (UI/UX Design, Web Design...) bhi sample hain: inhe delete karne ki jagah apne text/price se badal sakte ho.
  Agar rakhna hai to pehle edit karo, warna "Delete sample content" inhe bhi hata dega.

## 5. Achha project kaise likhein (tips)

- **Cover image**: 1600x1200 (4:3) mock-up ya screenshot; card par yahi dikhta hai.
- **Card colour**: cover ke background jaisa colour do, card aur page dono neat lagenge.
- **Gallery**: 2-6 screens (desktop, mobile, design system). Click karne par bada khulta hai.
- Bade, important projects ka **case study** bhi banao aur project ke "Full case study" mein link karo.
- Case study ke Results mein asli numbers likho (Google Analytics / client se). Clients ko yahi sabse zyada impress karta hai.

## 6. Look badalna

- **Portfolio > Colours & footer**: accent colour aur doosra (gradient) colour, light/dark default, footer line.
- Visitor header ke sun/moon button se light/dark khud badal sakte hain.
- **Appearance > Menus**: header aur footer menu.
- Home page ka koi section nahi chahiye (jaise Blog): **Portfolio > Home page** mein uska tick hata do.

## 7. Messages (contact form)

- Har message **Messages** menu mein save hota hai (naye bold dikhte hain); CSV download bhi.
- Email tab aayegi jab hosting par mail chal raha ho (local computer se email nahi jaati). Hosting par zaroorat ho to
  "WP Mail SMTP" plugin lagao.
- Spam se bachav already hai (hidden field, time check, 1 ghante mein 6 se zyada nahi), koi captcha nahi.

## 8. Website live karna

1. **Domain** lo (jaise `aapkanaam.com` / `.in`, ~₹800-1,200/saal).
2. **Hosting**: koi bhi WordPress hosting (PHP 8.2+, MySQL, free SSL). Site domain ke root par rakho (subfolder mein nahi).
3. Local wp-admin mein plugin **All-in-One WP Migration** install karo > Export > File. Hosting par fresh WordPress
   install karke wahi plugin > Import. Theme + plugin + content sab chala jayega.
4. Live hone ke baad: Settings > Reading mein "Discourage search engines" ka tick hata hua ho, Yoast SEO > sitemap
   Google Search Console mein submit karo.
5. Live hone se pehle checklist: naam, photo, email, social links, sample content delete, Privacy Policy page apne
   shabdon mein, Services ke prices sahi.

Koi bhi naya section, page design ya feature chahiye ho to Claude ko bolo; theme aur plugin `portfolio-website/` folder mein hain.
