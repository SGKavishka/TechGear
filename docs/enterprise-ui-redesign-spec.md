# TechGear Enterprise UI Redesign Specification

Version: 1.0  
Scope: UI/UX redesign only  
Functional constraint: preserve all public PHP routes, forms, database tables, server-side validation, session behavior, and admin actions exactly as they exist.

## 1. Product Direction

TechGear should feel like a serious commerce operations platform with a polished customer storefront. The storefront must make shopping, checkout, and account review simple. The admin panel must support information-dense work without copying common framework patterns from off-the-shelf dashboard kits.

### Experience Goals

- Customer side: fast browsing, clear product comparison, low-friction cart and checkout.
- Admin side: operational clarity, dense but readable data, fast inventory/order/message triage.
- Visual tone: precise, modern, technical, restrained.
- Responsive behavior: every workflow usable from mobile, but admin tables should prioritize tablet/desktop efficiency.
- Accessibility: visible focus states, semantic controls, sufficient contrast, no color-only status communication.

## 2. Existing Route and Functionality Map

Do not change these public routes or their POST actions. The files now live in `public/`, with root `.htaccess` preserving the browser URLs.

| Area | Route | Existing behavior to preserve |
|---|---|---|
| Home | `index.php` | Hero, categories, featured products, newsletter POST to `subscribe.php` |
| Catalog | `products.php` | Search, category filter, price filter, add to cart |
| Product Detail | `product-detail.php?id={id}` | Product display, quantity selector, add to cart |
| Cart | `cart.php` | Session cart lines, quantity update, remove, clear, checkout link |
| Cart Actions | `cart_action.php` | Add/update/remove/clear cart POST handling |
| Checkout | `checkout.php` | Login required, delivery form, create order, reduce stock |
| Login | `login.php` | Email/password login, redirect support |
| Register | `register.php` | Customer account creation |
| Profile | `profile.php` | Profile update, order history, active delivery tracking |
| Contact | `contact.php` | Contact message creation |
| Privacy | `privacy.php` | Policy content |
| Admin Login | `admin-login.php` | Dedicated admin-only authentication portal |
| Admin | `admin.php` | Dashboard, product CRUD/status, user table, order status updates, contact message status |
| Admin Logout | `admin-logout.php` | Ends admin session and returns to admin portal login |
| Install | `install.php` | Database installer |

## 3. Visual Language

### Design Theme

Name: Precision Workbench

The UI uses a dark enterprise workspace with sharp hierarchy, compact controls, and measured accent colors. The design should avoid decorative glass effects, bright gaming gradients, oversized cards, and repeated marketing blocks. The admin console should look purpose-built: a workbench made of panels, ledgers, row states, compact forms, and persistent context.

### Color Palette

Use CSS variables so implementation can evolve without markup churn.

| Token | Value | Usage |
|---|---:|---|
| `--ink-950` | `#080B0F` | App background |
| `--ink-900` | `#0D131A` | Header/sidebar |
| `--ink-850` | `#111A23` | Page panels |
| `--ink-800` | `#162230` | Raised surfaces |
| `--line-soft` | `#263442` | Borders |
| `--line-strong` | `#3A4A5B` | Active/hover borders |
| `--text-strong` | `#F6F8FB` | Primary text |
| `--text-body` | `#B8C2CC` | Secondary text |
| `--text-muted` | `#7F8C99` | Metadata |
| `--signal-cyan` | `#38D9C8` | Primary actions, active nav |
| `--signal-amber` | `#FFB454` | Highlights, warnings, badges |
| `--signal-green` | `#44D07B` | Success/delivered/live |
| `--signal-red` | `#FF5C7A` | Errors/destructive |
| `--focus-ring` | `rgba(56, 217, 200, 0.28)` | Keyboard focus |

Rules:

- Do not use color alone for status. Pair with text, icon, or shape.
- Primary action color should be used sparingly.
- Avoid full-page gradients. Subtle panel tinting is allowed.

### Typography

Recommended stack:

- Headings: `Outfit, Inter, system-ui, sans-serif`
- Body/UI: `Inter, system-ui, sans-serif`
- Numeric data: same body stack with `font-variant-numeric: tabular-nums`

Scale:

| Token | Size | Line Height | Usage |
|---|---:|---:|---|
| Display | 44px | 1.08 | Home hero only |
| H1 | 32px | 1.15 | Page title |
| H2 | 24px | 1.2 | Section title |
| H3 | 18px | 1.25 | Panel title |
| Body | 15px | 1.55 | General text |
| Compact | 13px | 1.45 | Metadata, table secondary text |
| Label | 12px | 1.3 | Badges, table headers |

Guidelines:

- No negative letter spacing.
- Avoid all-caps body labels except table headers and tiny badges.
- Numeric amounts should align consistently in tables and summaries.

### Spacing and Layout

Base spacing unit: 4px.

Common tokens:

- `space-1`: 4px
- `space-2`: 8px
- `space-3`: 12px
- `space-4`: 16px
- `space-5`: 20px
- `space-6`: 24px
- `space-8`: 32px
- `space-10`: 40px
- `space-12`: 48px

Radii:

- Controls: 6px
- Panels: 8px
- Badges: 999px
- Do not use large rounded card styles.

Layout widths:

- Storefront content: max 1240px
- Admin content: full-width workbench with 24px desktop gutters
- Forms: 520px to 720px depending on density
- Tables: use horizontal scroll below 900px, not squeezed columns

## 4. Component System

### Buttons

Variants:

- Primary: filled cyan, used for final or revenue-driving actions.
- Secondary: dark outline, used for navigation or lower-priority actions.
- Quiet: text-like button for inline table actions.
- Danger: red text or red outline, never filled unless confirming destructive action.

States:

- Default, hover, active, disabled, loading, focus-visible.
- Minimum tap target: 44px high on customer pages, 36px high in admin dense tables.

### Inputs and Forms

Patterns:

- Labels always visible above fields.
- Error text appears below the field and must not shift unrelated layout aggressively.
- Required fields may use a subtle `Required` suffix, not only an asterisk.
- Use consistent help text for image path, price, stock, and password fields.

Field states:

- Default: dark input, soft border.
- Focus: cyan border and focus ring.
- Error: red border, red message, preserve readable text.
- Readonly: muted background and lock icon or `Read only` helper label.

### Tables

Admin data tables are dense ledgers, not card lists on desktop.

Table anatomy:

- Header row with sticky or visually heavier background.
- Row hover with subtle background shift.
- First column can act as object identity.
- Secondary metadata is shown under primary text in the same cell.
- Actions live in a final fixed-width column.

Status display:

- Small status chips with text and optional left marker.
- Status tokens:
  - Live: green
  - Disabled: red
  - Processing/Shipped: cyan/amber
  - Delivered: green
  - New message: amber
  - Read message: muted

### Modals

Use modals only for focused create/edit workflows.

Modal anatomy:

- Title row with object mode: `Add Product` or `Edit Product`.
- Two-column form on desktop, one-column on mobile.
- Sticky footer for Save/Cancel if the form is tall.
- Escape key and backdrop close are optional, but visible close action is required.

### Navigation

Customer navigation:

- Top header with brand, primary links, account state, cart.
- Active route visibly marked.
- Mobile menu slides from right with large tap targets.
- Do not expose an admin entry in the customer navbar. Admin access belongs to the dedicated admin portal only.
- Admin authentication should use the dedicated admin portal session, not the customer storefront session.

Admin navigation:

- Dedicated portal login at `admin-login.php`.
- Custom left rail with compact two-letter glyph tiles inside `admin.php`.
- Rail is not a common icon-only framework sidebar.
- Active module has cyan left marker and filled glyph tile.
- Include a `View Store` utility action in the workbench header.

## 5. Customer Storefront Wireframes

### Home: `index.php`

Purpose: introduce product quality, route users to catalog categories, show featured inventory.

Desktop wireframe:

```text
┌──────────────────────────────────────────────────────────────────────┐
│ Header: Logo | Home Products Contact | Profile/Login | Cart          │
├──────────────────────────────────────────────────────────────────────┤
│ Hero image full bleed                                                │
│ ┌───────────────────────────────────────┐                            │
│ │ Badge                                 │                            │
│ │ Build a faster gaming setup           │                            │
│ │ Short value copy                      │                            │
│ │ [Shop Now] [Featured Gear]            │                            │
│ └───────────────────────────────────────┘                            │
│ Slide controls / progress                                            │
├──────────────────────────────────────────────────────────────────────┤
│ Three trust panels: Fast Dispatch | Verified Gear | Real Support     │
├──────────────────────────────────────────────────────────────────────┤
│ Section header: Browse by Category                         View All  │
│ [Mice image] [Keyboard image] [Headset image] [Components image]     │
├──────────────────────────────────────────────────────────────────────┤
│ Featured products grid                                               │
│ [Product] [Product] [Product] [Product]                              │
├──────────────────────────────────────────────────────────────────────┤
│ Newsletter panel                                                     │
└──────────────────────────────────────────────────────────────────────┘
```

Mobile behavior:

- Header becomes logo, cart, menu.
- Hero text stays over image, actions wrap.
- Feature panels stack.
- Product grid becomes one column below 520px.

### Catalog: `products.php`

Purpose: product discovery with server-side filters.

Desktop wireframe:

```text
┌────────────── Filters ──────────────┬────────────────────────────────┐
│ Categories                           │ Catalog header                 │
│ ○ All                                │ Product Catalog                │
│ ○ Gaming Mice                        │ 9 items found                  │
│ ○ Keyboards                          │ [Search input] [Search]        │
│                                      ├────────────────────────────────┤
│ Price Range                          │ Product grid                   │
│ ○ Any                                │ [Card] [Card] [Card]           │
│ ○ Under 50k                          │ [Card] [Card] [Card]           │
│ ○ 50k-150k                           │                                │
└──────────────────────────────────────┴────────────────────────────────┘
```

Card requirements:

- Product image area has fixed height.
- Category, product name, two-line description, stock count, price.
- Add to cart button remains a POST form.

### Product Detail: `product-detail.php`

Desktop wireframe:

```text
┌──────────────────────────────┬───────────────────────────────────────┐
│ Large product image           │ Category                              │
│ Thumbnail row                 │ Product name                          │
│                               │ Price                                 │
│                               │ Description                           │
│                               │ Qty stepper + Add to Cart             │
│                               │ Specifications table                  │
└──────────────────────────────┴───────────────────────────────────────┘
```

Key UI rules:

- Quantity selector uses buttons and input, preserving form POST.
- Specifications table uses readable row separation.
- Product not found state stays centered with return action.

### Cart: `cart.php`

Desktop wireframe:

```text
┌──────────────────────────────────────────┬───────────────────────────┐
│ Your Cart                         Clear  │ Order Summary             │
│ [Image] Item details  Qty stepper Price  │ Subtotal                  │
│ [Image] Item details  Qty stepper Price  │ Shipping                  │
│                                          │ Tax                       │
│ Empty state when needed                  │ Total                     │
│                                          │ [Proceed to Checkout]     │
└──────────────────────────────────────────┴───────────────────────────┘
```

Mobile behavior:

- Summary moves below items.
- Cart rows become image + stacked details.
- Quantity controls keep 44px tap targets.

### Checkout: `checkout.php`

Desktop wireframe:

```text
┌──────────────────────────────────────────┬───────────────────────────┐
│ Delivery Details                         │ Order Summary             │
│ [Name] [Email]                           │ Line item list            │
│ [Phone]                                  │ Subtotal                  │
│ [Delivery Address full width]            │ Tax                       │
│ [Place Order] [Back to Cart]             │ Total                     │
└──────────────────────────────────────────┴───────────────────────────┘
```

Rules:

- Login requirement remains server-side.
- Preserve validation and order creation behavior.
- Payment UI should not be added unless backend support exists.

### Auth: `login.php`, `register.php`

Layout:

- Centered authentication panel.
- Login includes demo credentials area.
- Register supports name, email, phone, address, password, confirmation.
- Do not hide labels inside placeholders.

### Profile: `profile.php`

Desktop wireframe:

```text
┌──────── Account rail ────────┬───────────────────────────────────────┐
│ Avatar, name, email          │ Profile Settings tab                  │
│ [Profile Settings]           │ Update account form                   │
│ [Purchase History]           │                                       │
│ [Order Tracking]             │ Purchase History tab                  │
│                              │ Dense order cards                     │
│                              │                                       │
│                              │ Tracking tab                          │
│                              │ Delivery progress rows                │
└──────────────────────────────┴───────────────────────────────────────┘
```

## 6. Custom Admin Panel Specification

### Admin Concept: Operations Workbench

The admin panel should feel like a control surface for live commerce operations. It should not look like a template dashboard. The custom pattern is:

- Left module rail with compact glyph tiles.
- Workbench header that changes by section.
- Dense panels and ledgers for inventory, users, orders, and support.
- Quick triage cards on the dashboard.
- Inline status editing for orders.
- Product create/edit modal.

### Admin Layout Wireframe: `admin.php`

```text
┌──────────────────────────────────────────────────────────────────────┐
│ Portal header: TechGear Operations | Admin name | View Store | Sign Out│
├───────────────┬──────────────────────────────────────────────────────┤
│ Module Rail   │ Workbench Header                                      │
│ DB Overview   │ Eyebrow + Section title                      Store    │
│ PR Products   ├──────────────────────────────────────────────────────┤
│ US Users      │ Section body                                          │
│ OR Orders     │ Dashboard stats / ledgers / forms                     │
│ MS Messages   │                                                      │
└───────────────┴──────────────────────────────────────────────────────┘
```

### Dashboard Section

```text
┌──────────────────────────────────────────────────────────────────────┐
│ Dashboard Overview                                          View Store│
├──────────────┬──────────────┬──────────────┬────────────────────────┤
│ Revenue      │ Active Orders│ Users        │ Low Stock              │
├──────────────┴──────────────┴──────────────┴────────────────────────┤
│ Quick links: Inventory | Orders | Support Inbox                      │
└──────────────────────────────────────────────────────────────────────┘
```

Dashboard component behavior:

- Revenue uses tabular numbers.
- Stat cards are compact and equal height.
- Quick cards jump to the relevant section without changing backend logic.

### Product Inventory Section

```text
┌──────────────────────────────────────────────────────────────────────┐
│ Product Inventory                                      [Add Product] │
├────┬───────┬──────────────────┬──────────┬──────────┬──────┬───────┤
│ ID │ Image │ Product          │ Category │ Price    │Stock │Status │
├────┼───────┼──────────────────┼──────────┼──────────┼──────┼───────┤
│ 1  │ thumb │ Viper Ultimate   │ Mice     │ Rs...    │ 42   │ Live  │
│    │       │ Brand metadata   │          │          │      │ Edit  │
└────┴───────┴──────────────────┴──────────┴──────────┴──────┴───────┘
```

Product modal:

```text
┌──────────────────────── Add/Edit Product ────────────────────────────┐
│ Product Name                                                         │
│ Category                  Price                                      │
│ Stock                     Tag                                        │
│ Brand                     Warranty                                   │
│ Image Path                                                           │
│ Description                                                          │
│ [ ] Featured              [ ] Live                                   │
│                                            [Cancel] [Save Product]   │
└──────────────────────────────────────────────────────────────────────┘
```

Preserve:

- `admin_action=save_product`
- `admin_action=delete_product`
- Product status disable behavior
- Existing product fields

### User Accounts Section

```text
┌──────────────────────────────────────────────────────────────────────┐
│ Registered Users                                                     │
├────┬───────────────┬───────────────────────┬────────────┬───────────┤
│ ID │ Name          │ Email                 │ Phone      │ Role      │
└────┴───────────────┴───────────────────────┴────────────┴───────────┘
```

Rules:

- Users are read-only in current backend. Do not add edit/delete UI.
- Role chip should clearly distinguish Admin and Customer.

### Orders Section

```text
┌──────────────────────────────────────────────────────────────────────┐
│ Recent Orders                                                        │
├──────────────┬───────────────┬────────────┬──────────┬──────────────┤
│ Order #      │ Customer      │ Date       │ Total    │ Status       │
│ TG-...       │ Name + email  │ May 22     │ Rs...    │ [Select]     │
└──────────────┴───────────────┴────────────┴──────────┴──────────────┘
```

Preserve:

- Inline status dropdown submits `admin_action=update_order_status`.
- Status options stay `processing`, `shipped`, `delivered`, `cancelled`.

### Messages Section

```text
┌──────────────────────────────────────────────────────────────────────┐
│ Contact Messages                                                     │
├─────────┬───────────────┬──────────────┬──────────────┬─────────────┤
│ Date    │ Sender        │ Subject      │ Message      │ Status/Act  │
│ May 22  │ Name + email  │ Warranty     │ Text preview │ New | Read  │
└─────────┴───────────────┴──────────────┴──────────────┴─────────────┘
```

Preserve:

- `admin_action=mark_message_read`.
- No reply feature should be added unless backend changes.

## 7. Responsive Rules

Breakpoints:

- `>= 1200px`: full desktop layout.
- `900px - 1199px`: compact desktop/tablet; admin stat cards become 2 columns.
- `< 900px`: sidebars move above content; admin rail becomes two-column module block.
- `< 560px`: single-column cards and forms.

Admin tables:

- Use horizontal overflow below 900px.
- Do not collapse enterprise tables into unrelated cards unless each row remains complete and scannable.

Customer pages:

- Preserve checkout/cart summaries below primary content on mobile.
- Product grid becomes two columns on tablet, one column on phones.

## 8. Accessibility Requirements

Minimum standards:

- All interactive controls must be keyboard reachable.
- Use `:focus-visible` with a clear cyan outline/ring.
- Text contrast should meet WCAG AA.
- Form errors should be adjacent to fields.
- Status chips must include text labels.
- Modal close control must have visible text or `aria-label`.
- Product image `alt` text must remain meaningful.
- Do not use hover-only information.

Keyboard flow:

1. Header navigation
2. Page filters or primary form
3. Main content actions
4. Secondary summaries/actions
5. Footer

## 9. Implementation Guidelines

Preserve backend logic:

- Do not rename PHP routes.
- Do not rename existing form `name` attributes that are read by PHP.
- Do not change hidden `admin_action`, `action`, `product_id`, `quantity`, `redirect`, or CSRF fields.
- Do not change database schema for this redesign.
- Avoid adding JS-heavy behavior for functions already handled by PHP.

Allowed UI changes:

- CSS variables, spacing, typography, layout wrappers.
- Presentational classes.
- Icons and visual markers.
- Responsive table wrappers.
- Empty state presentation.
- Modal visual layout, as long as existing form fields and POST behavior remain intact.

Recommended file strategy:

- Keep global tokens in `public/assets/css/styles.css`.
- Keep shared components in `public/assets/css/components.css`.
- Keep admin-specific workbench patterns in `public/assets/css/admin.css`.
- Keep page CSS files scoped to their existing routes.
- If HTML changes are needed, limit them to structural wrappers and accessible labels.

## 10. Development Acceptance Checklist

Customer:

- Home hero, categories, featured products, and newsletter still work.
- Catalog search/filter URLs still work.
- Add to cart works from catalog and detail pages.
- Cart quantity update, remove, clear, and checkout link work.
- Checkout creates an order and clears the cart.
- Login/register/profile update work.
- Contact form saves messages.

Admin:

- Admin login reaches `admin.php`.
- Dashboard stats load.
- Product add/edit/disable works.
- User table remains read-only.
- Order status select updates order status.
- Contact message can be marked as read.

Visual:

- No page has overlapping text at 320px, 768px, 1024px, or desktop widths.
- Tables are readable and scrollable on narrow screens.
- Focus states are visible.
- Color use is consistent with the design tokens.
- Admin panel does not visually resemble a generic off-the-shelf dashboard.

## 11. Asset Guidance

Existing product and hero images can be reused. If new imagery is added later:

- Use real product or product-like imagery, not abstract decorative graphics.
- Keep product images on clean dark or transparent backgrounds.
- Maintain consistent image padding and aspect ratios.
- Avoid using image content as the only source of critical information.

## 12. Handoff Notes

This specification is intentionally implementation-ready but backend-neutral. The development team should be able to apply the redesign by changing CSS and presentational markup while preserving every current PHP controller path, POST action, and database interaction.
