# SKVN Marine — 1.3.x Architecture & Decisions

Status: living document — updated through 1.3.5.  
Milestone series: V1 / 1.3.0 → 1.3.5.  
Date: 2026-06-17.

---

## 1. Mục tiêu series 1.3.x

Series 1.3.x là giai đoạn hoàn thiện nền tảng V1 trước khi bước sang 1.4.x và các milestone QA. Mỗi milestone giải quyết một nhóm concern riêng biệt:

| Milestone | Concern chính |
|---|---|
| 1.3.0 | Dynamic rendering cho Slider — server-rendered PHP thay vì static JS |
| 1.3.1 | Slider navigation/pagination UX contract |
| 1.3.2 | Feature Showcase autoplay + panel links |
| 1.3.3 | Dynamic Product/Post Collection blocks |
| 1.3.4 | Core Control foundation + Core Button Hover |
| 1.3.5 | Post, Product & Archive page improvements |

---

## 2. Dependency chain 1.3.x

```mermaid
graph TD
    A[1.3.0 — Slider Dynamic Rendering] --> B[1.3.1 — Slider Nav/Pagination]
    B --> C[1.3.2 — Feature Showcase Autoplay]
    A --> D[1.3.3 — Dynamic Collections]
    D --> E[1.3.4 — Core Control + Button Hover]
    E --> F[1.3.5 — Post/Product/Archive]
    F --> G[1.3.9 — Onsite QA — deferred]
```

1.3.0 là foundation bắt buộc cho toàn bộ nhánh Slider. 1.3.3 độc lập với nhánh Slider nhưng dùng chung Swiper adapter cho carousel layout. 1.3.5 độc lập hoàn toàn — chỉ đụng theme layer.

---

## 3. Kiến trúc tổng thể — Theme + Plugin

```mermaid
graph LR
    subgraph GP["GeneratePress Parent Theme"]
        GP_HOOKS["generate_* hooks<br/>Page canvas<br/>Header/footer shell"]
    end

    subgraph THEME["skvn-marine (Child Theme)"]
        STYLE["style.css<br/>Token bridge + components"]
        CUSTOMIZER["inc/customizer.php<br/>Font presets"]
        WOO["inc/woocommerce.php<br/>Visual overrides"]
        ARCHIVE["archive.php<br/>Style C Hybrid"]
        SINGLE["single.php<br/>Style C"]
        TYPO["inc/typography.php<br/>Palette + heading scale"]
        ENQUEUE["inc/enqueue.php<br/>Assets"]
    end

    subgraph PLUGIN["skvn-marine-blocks (Plugin)"]
        BLOCKS["Custom blocks<br/>slider, accordion,<br/>product-collection,<br/>post-collection"]
        CORE_CTRL["modules/core-control/<br/>Core Button Hover"]
        PLUGIN_CSS["Plugin baseline CSS<br/>(fallback tokens)"]
    end

    subgraph WP["WordPress Core + WooCommerce"]
        THEME_JSON["theme.json<br/>WP preset colors/fonts"]
        WC["WooCommerce<br/>Product data + templates"]
    end

    THEME_JSON -->|"--wp--preset--color--*"| STYLE
    STYLE -->|"--skvn-color-* tokens"| WOO
    STYLE -->|"--skvn-color-* tokens"| ARCHIVE
    STYLE -->|"--skvn-color-* tokens"| SINGLE
    PLUGIN_CSS -->|fallback tokens| BLOCKS
    STYLE -->|override| PLUGIN_CSS
    GP_HOOKS --> THEME
    WC --> WOO
    CUSTOMIZER -->|"inline CSS --skvn-font-*"| STYLE
    TYPO -->|"inline CSS palette"| STYLE
```

**Cascade rule:** Plugin CSS (fallback) → Theme `style.css` (override) → Inline CSS từ PHP (highest specificity cho runtime config).

---

## 4. CSS Token System

### 4.1 Token cascade

```mermaid
flowchart TD
    A["theme.json\n(WP preset colors — source of truth)"]
    B["style.css :root\n(bridge: --skvn-color-* → var(--wp--preset--color--*))"]
    C["Semantic aliases\n(--skvn-color-primary, --skvn-color-navy, --skvn-color-accent…)"]
    D["inc/typography.php inline CSS\n(palette override từ plugin admin settings)\npriority 20"]
    E["inc/customizer.php inline CSS\n(--skvn-font-heading / --skvn-font-body)\npriority 15"]
    F["Component CSS\n(archive, single post, product, cards…)"]
    G["Plugin baseline CSS\n(fallback nếu theme không active)"]

    A -->|"--wp--preset--color--skvn-*"| B
    B --> C
    C --> F
    D -->|"overrides --skvn-color-primary/accent/surface/text"| C
    E -->|"overrides --skvn-font-heading/body"| B
    G -->|"fallback tokens khi theme deactivated"| F
```

### 4.2 Mapping palette tokens → WP presets

| `--skvn-color-*` token | `--wp--preset--color--*` slug | Hex fallback |
|---|---|---|
| `--skvn-color-blue-950` | `skvn-blue-950` | `#082f49` |
| `--skvn-color-blue-900` | `skvn-blue-900` | `#0c4a6e` |
| `--skvn-color-blue-700` | `skvn-blue-700` | `#0369a1` |
| `--skvn-color-mint-100` | `skvn-mint-100` | `#ddfaf4` |
| `--skvn-color-gold-300` | `skvn-gold-300` | `#e9c766` |
| `--skvn-color-teal-600` | `skvn-teal-600` | `#0d9488` |
| `--skvn-color-sky-50` | `skvn-sky-50` | `#f0f9ff` |
| `--skvn-color-slate-700` | `skvn-slate-700` | `#334155` |
| `--skvn-color-slate-900` | `skvn-slate-900` | `#0f172a` |
| `--skvn-color-white` | `skvn-white` | `#ffffff` |
| `--skvn-color-trust-blue` | *(no preset)* | `#0f5c8c` |
| `--skvn-color-fresh-sky` | *(no preset)* | `#eaf7ff` |

### 4.3 Semantic role aliases

```css
--skvn-color-primary: var(--skvn-color-blue-700);
--skvn-color-navy:    var(--skvn-color-blue-950);
--skvn-color-accent:  var(--skvn-color-teal-600);
--skvn-color-text:    var(--skvn-color-slate-700);
--skvn-color-surface: var(--skvn-color-sky-50);
```

Plugin và component CSS dùng alias này — không reference `--wp--preset--*` trực tiếp. Điều này đảm bảo plugin có thể hoạt động độc lập nếu theme thay đổi.

---

## 5. Font Preset System (1.3.5)

### 5.1 Flow

```mermaid
flowchart LR
    A["WP Customizer\nAppearance → Customize\n→ Typography (SKVN)"]
    B["get_theme_mod('skvn_font_preset')"]
    C{"Preset key"}
    D1["instrument\n(default)\nInstrument Serif + system-ui"]
    D2["lora-inter\nLora + Inter"]
    D3["barlow\nBarlow"]
    D4["system\nNo Google Fonts"]
    E["wp_enqueue_style\nGoogle Fonts link\n(khi cần)"]
    F["wp_add_inline_style\n→ skvn-marine-style\n:root {\n  --skvn-font-heading: …;\n  --skvn-font-body: …;\n}"]
    G["h1-h6, body\ndùng token"]

    A --> B --> C
    C --> D1 & D2 & D3 & D4
    D1 & D2 & D3 & D4 --> E
    D1 & D2 & D3 & D4 --> F
    F --> G
```

### 5.2 Preset table

| Key | Heading | Body | Google Fonts |
|---|---|---|---|
| `instrument` *(default)* | `'Instrument Serif', Georgia, serif` | `system-ui, sans-serif` | Instrument Serif |
| `lora-inter` | `'Lora', Georgia, serif` | `'Inter', system-ui, sans-serif` | Lora + Inter |
| `barlow` | `'Barlow', system-ui, sans-serif` | `'Barlow', system-ui, sans-serif` | Barlow |
| `system` | `system-ui, -apple-system, sans-serif` | same | Không cần |

### 5.3 Rationale

**Instrument Serif** làm default vì client positioning gần luxury food brand (cá grouper, mahi-mahi xuất khẩu cao cấp) — serif editorial phù hợp hơn sans-serif industrial. Customizer cho phép thay đổi mà không cần đụng code, đủ cho marketing team.

---

## 6. WordPress Template Hierarchy — 1.3.5

```mermaid
flowchart TD
    WP["WordPress Template Hierarchy"]

    WP --> IS_SINGLE{"is_single()"}
    WP --> IS_ARCHIVE{"is_archive()"}
    WP --> IS_PRODUCT{"is_product()\n(WooCommerce)"}

    IS_SINGLE -->|"Yes"| SINGLE["single.php\n(child theme — 1.3.5)"]
    IS_ARCHIVE -->|"Yes — post type post"| ARCHIVE["archive.php\n(child theme — 1.3.5)"]
    IS_PRODUCT -->|"Yes"| WC_SINGLE["WooCommerce\nsingle-product template\n+ theme WC overrides\n(inc/woocommerce.php)"]

    SINGLE --> HERO["Post Hero\n(navy gradient + title)"]
    SINGLE --> LAYOUT_S["skvn-single-layout\ngrid 2fr 1fr"]
    LAYOUT_S --> CONTENT["Post body card\n+ Tags\n+ Related posts grid"]
    LAYOUT_S --> SIDEBAR_S["Sidebar islands\nQuote CTA\nRelated posts list\nCategories"]

    ARCHIVE --> BANNER["Archive banner\n(navy gradient)"]
    ARCHIVE --> FEATURED["Featured post\n(first post — 2-col)"]
    ARCHIVE --> LAYOUT_A["skvn-archive-layout\ngrid 2fr 1fr"]
    LAYOUT_A --> GRID["Post card grid\n2-col"]
    LAYOUT_A --> SIDEBAR_A["Sidebar islands\nQuote CTA\nCategories\nCertifications"]

    WC_SINGLE --> PRODUCT_LAYOUT["WooCommerce div.product\ngrid 3fr 1fr"]
    PRODUCT_LAYOUT --> PRODUCT_MAIN["Product main zone\nGallery 1fr + Details 1fr"]
    PRODUCT_MAIN --> CTA["Quote CTA full-width\n+ Trust signals"]
    PRODUCT_LAYOUT --> TABS["Product tabs\nMô tả / Thông số kỹ thuật\nTài liệu & Chứng nhận"]
    PRODUCT_LAYOUT --> RELATED_P["Related products\nrepeat(4,1fr)"]
```

---

## 7. WooCommerce Integration Layer (1.3.5)

### 7.1 Quyết định

**Reviews tab:** Ẩn hoàn toàn qua `woocommerce_product_tabs` filter. Không cần reviews UI trong B2B context — mua theo hợp đồng, không phải consumer review.

**Tabs đổi tên:** `description` → "Mô tả sản phẩm", `additional_information` → "Thông số kỹ thuật". Thêm tab mới "Tài liệu & Chứng nhận" — V1 static, mời liên hệ.

**Product placeholder:** `woocommerce_placeholder_img` filter trả về `.skvn-product-placeholder` (navy gradient + text "Hình sắp cập nhật") thay vì ảnh placeholder mặc định của WC.

**Quote CTA:** Replace `woocommerce_template_single_add_to_cart` bằng `skvn_marine_woocommerce_single_quote_cta()` — full-width primary button + secondary contact link + 3 trust badges (VSATTP / Cold Chain / Bảo hành).

**Hotline number color:** `var(--skvn-color-accent)` — không hardcode màu vàng/gold như trong artifact prototype.

### 7.2 Hook map

```mermaid
flowchart LR
    subgraph REMOVED["Hooks bị remove"]
        R1["woocommerce_template_single_add_to_cart\n(priority 30)"]
    end

    subgraph ADDED["Hooks được add"]
        A1["skvn_marine_woocommerce_single_quote_cta\n(priority 30)"]
        A2["woocommerce_product_tabs filter\n(priority 98)\n— remove reviews\n— rename tabs\n— add documents tab"]
        A3["woocommerce_placeholder_img filter\n— navy branded placeholder"]
        A4["woocommerce_loop_add_to_cart_link filter\n— catalog: quote URL"]
    end

    R1 -.->|replaced by| A1
```

---

## 8. Island Pattern — Sidebar Component

`.skvn-island` là shared component dùng chung cho archive, single post, và product sidebar. Không phải Gutenberg pattern — là PHP-rendered static HTML cho V1.

```mermaid
graph TD
    ISLAND[".skvn-island\n(shared CSS component)"]

    ISLAND --> VARIANT1[".skvn-island--navy\nnavy background\nQuote CTA context"]
    ISLAND --> VARIANT2[".skvn-island (default)\nwhite background\nCategories / Certifications / Posts"]

    VARIANT1 --> USAGE1["archive.php sidebar\nsingle.php sidebar"]
    VARIANT2 --> USAGE2["archive.php sidebar\nsingle.php sidebar"]

    ISLAND --> ELEMENTS["Sub-elements:\n__eyebrow\n__label\n__heading\n__cta\n__note\n__cat-list / __cat-row\n__cert-list / __cert-row\n__post-list / __post-item"]
```

**Quyết định:** Dùng PHP static sidebar thay vì Gutenberg Pattern vì:
- Sidebar archive cần dynamic WordPress data (categories từ `get_categories()`, related posts từ `WP_Query`)
- Không cần editor control — marketing team không cần edit sidebar archive/post
- Gutenberg Pattern dùng cho Product single sidebar nếu cần customize hotline/certifications per client (V2 scope)

---

## 9. Comment + Review Suppression

### 9.1 Single Post

```php
// functions.php
add_filter( 'comments_open', '__return_false', 20 );
add_filter( 'pings_open',    '__return_false', 20 );
```

Kết hợp với việc `single.php` không gọi `comments_template()` — đảm bảo không có comment form và không có existing comments hiện.

**Rationale:** B2B seafood export context. Comments không phù hợp — conversation xảy ra qua quote form, hotline, và direct sales.

### 9.2 Single Product

```php
// inc/woocommerce.php
add_filter( 'woocommerce_product_tabs', function( $tabs ) {
    unset( $tabs['reviews'] );
    return $tabs;
}, 98 );
```

Reviews tab bị xóa khỏi tab array trước khi WooCommerce render. Không cần template override.

---

## 10. CSS Layout Safety — Constraints áp dụng

Theo `docs/standards/css-layout-safety-contract.md`:

| Rule | Áp dụng trong 1.3.5 |
|---|---|
| Layout width có một owner | `.skvn-archive-layout`, `.skvn-single-layout`, `div.product` — mỗi cái là grid owner riêng |
| Không dùng `100vw` / margin âm để breakout | Post hero dùng `position: relative`, bounded bởi GP canvas. Không full-bleed breakout |
| Không dùng `overflow-x: hidden` che overflow | Không dùng |
| Layout sizing dùng `fr`/`%` | `grid-template-columns: 2fr 1fr`, `3fr 1fr`, `repeat(2,1fr)` — không hardcode px/em cho grid |
| Viewport units mới cần rationale | Chỉ dùng `clamp()` với `vw` cho font-size hero title — đây là fluid typography, không phải layout breakout |

**Tension đã nhận diện nhưng chấp nhận:** Post hero không full-bleed so với artifact prototype. Artifact dùng `position: absolute; inset: 0; width: 100vw` — vi phạm CSS safety contract. V1 dùng hero bounded bởi GP content width. Full-bleed hero là V2 candidate khi chuyển sang standalone theme (2.0.0).

---

## 11. Responsive Breakpoints

| Breakpoint | Hành vi |
|---|---|
| `> 900px` | Full layout: 2-col featured post, 2fr/1fr content+sidebar, 2-col card grid |
| `≤ 900px` | Single column: sidebar xuống dưới main, featured post stack, card grid 3-col (archive) hoặc 2-col (related) |
| `≤ 600px` | Card grid 1-col, trust signals 1-col |

---

## 12. Files changed — 1.3.5

| File | Status | Role |
|---|---|---|
| `inc/customizer.php` | Mới | Font preset Customizer control |
| `functions.php` | Sửa | Load customizer.php + comment filters |
| `style.css` | Sửa | Token bridge + archive + post + product CSS |
| `archive.php` | Mới | Archive template Style C Hybrid |
| `single.php` | Mới | Single post template Style C |
| `inc/woocommerce.php` | Sửa | Reviews filter, placeholder, tabs, CTA upgrade |

Tổng: 6 files — 3 mới, 3 sửa. Vượt giới hạn 5 files vì chia thành 5 steps riêng biệt có concerns khác nhau; từng step trong giới hạn 2-3 files.

---

## 13. Deferred — V2+

| Item | Lý do defer |
|---|---|
| Full-bleed post hero | Cần standalone theme (2.0.0) — GP canvas limit |
| Sidebar JS toggle | Không cần cho V1 — Option A (always visible) đủ |
| Gutenberg Pattern sidebar cho product | Marketing team chưa cần; PHP static đủ |
| Product archive (shop page) | Separate concern, chưa scope |
| JSON-LD schema markup | Rank Math handle; custom schema là V2 |
| Trust signals taxonomy động | V1 static đủ |
| Font self-hosted mode | 1.6.0 — Surface Presets milestone |
