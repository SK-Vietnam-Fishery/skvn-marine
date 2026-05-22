# AGENTS.md — SKVN Marine

> Đọc file này TRƯỚC KHI làm bất kỳ task nào.
> Đây là protocol bắt buộc cho mọi AI agent làm việc trong project này.

---

## 0. Milestone hiện tại

Current milestone: **V1 / 0.4.0 — Woo Product Sections**

Milestone source of truth: `.context/MILESTONES.md`.

Quy tắc quản lý tension theo milestone:

- Trong cùng milestone hiện tại, tension đã `RESOLVED_ACTIVE` nằm trong `.context/TENSIONS_ACTIVE.md` để agent thấy decision liên quan khi làm task V1.
- Chỉ khi human tuyên bố chuyển sang milestone/version mới (ví dụ V1 → V2), agent mới move các tensions đã `RESOLVED_ACTIVE` của milestone cũ sang `.context/TENSIONS_HISTORY.md` và đổi `Status: ARCHIVED`.
- Khi chuyển milestone, agent phải update cả `AGENTS.md` và `.context/MILESTONES.md`, đồng thời move milestone cũ sang `.context/MILESTONES_HISTORY.md`.
- `.context/TENSIONS_HISTORY.md` không load mặc định, chỉ đọc khi cần audit quyết định cũ hoặc human yêu cầu.

---

## 1. Đọc trước — Bắt buộc

Mỗi task bắt đầu bằng 4 bước này, không có ngoại lệ:

```
1. Đọc .context/GLOBAL.md       → hiểu stack, module index, invariants
2. Đọc .context/MILESTONES.md   → biết milestone hiện tại và checklist liên quan
3. Đọc .context/TENSIONS_OPEN.md     → check toàn bộ OPEN tensions
4. Đọc .context/TENSIONS_ACTIVE.md   → check RESOLVED_ACTIVE tensions của milestone hiện tại, dùng tag filter
5. Đọc .context/modules/<module>.md  → load context của module sắp sửa
```

Nếu `.context/modules/<module>.md` chưa tồn tại → load `GLOBAL.md` + hỏi lại trước khi tạo file mới.

Nếu `[manual]` section trong module file còn template placeholder (`<!-- Viết tại đây -->`) → **DỪNG, báo lại**, không tự điền assumption.

### Context load rules

- `.context/planning/*.md` chỉ đọc khi task liên quan scope, UX direction, layout strategy, version planning, hoặc quyết định future block boundary.
- `.context/proposals/` là local review/discussion artifact, không phải active protocol. Không load mặc định và không treat như source of truth.
- `.context/MILESTONES_HISTORY.md` không load mặc định. Chỉ đọc khi audit milestone cũ, migrate milestone, hoặc human yêu cầu.
- `.context/TENSIONS_HISTORY.md` không load mặc định. Chỉ đọc khi audit quyết định cũ, migrate tension, hoặc human yêu cầu.
- Folder structure giúp human đọc đỡ rối; startup protocol trong `AGENTS.md` mới là source of truth cho agent load order.

### Encoding rules

- Context, docs, PHP, CSS, JS/TS, JSON, and HTML files must remain UTF-8.
- Do not save text files through a tool path that turns Vietnamese or punctuation into mojibake.
- In PowerShell, read/write text explicitly as UTF-8 when touching context/docs, for example `Get-Content -Encoding UTF8` and .NET `UTF8Encoding(false)` for bulk rewrites.
- If mojibake appears after reading a file, stop and fix encoding before making semantic edits.

---

## 2. Architecture — Biết rõ trước khi code

### Boundary rules

| Thuộc về | Layer | Ví dụ |
|---|---|---|
| `skvn-marine/` | Theme | Visual system, design tokens, block styles, patterns, WooCommerce visual override, animation runtime, media helpers |
| `skvn-marine-blocks/` | Plugin | Custom Gutenberg blocks có logic: slider, accordion, product-grid, product-list |
| External plugins | Không touch | WooCommerce, CF7, CFDB7, Rank Math, Polylang, n8n |
| GeneratePress | Không touch tuyệt đối | `themes/generatepress/**` |

**Rule quyết định nhanh**: nếu thay theme mà feature bị mất → feature thuộc plugin.

### Naming — Không bao giờ đổi

```
Theme slug:         skvn-marine
Plugin slug:        skvn-marine-blocks
Block namespace:    skvn-marine
Theme text domain:  skvn-marine
Plugin text domain: skvn-marine-blocks
Theme PHP prefix:   skvn_marine_
Plugin PHP prefix:  skvn_marine_blocks_
CSS prefix:         skvn-
```

### PHP security — Không thương lượng

```php
// Input — luôn sanitize
$product_id = isset($_GET['product_id']) ? absint($_GET['product_id']) : 0;
$sku        = isset($_GET['sku']) ? sanitize_text_field(wp_unslash($_GET['sku'])) : '';

// Output — luôn escape
echo esc_html($title);
echo esc_attr($value);
echo esc_url($url);
echo wp_kses_post($content);
```

---

## 3. Workflow — Mỗi Task

```
1. Load context (GLOBAL → MILESTONES → TENSIONS_OPEN → TENSIONS_ACTIVE → module)
2. Đọc [manual] của module — hiểu constraints và invariants
3. Nếu [manual] còn placeholder → DỪNG, hỏi lại
4. Detect tensions (xem Section 4)
5. Nếu tension HIGH → generate `TENSIONS_OPEN.md` entry → DỪNG, chờ quyết định
6. Nếu tension LOW hoặc không có → tiếp tục
7. Plan changes — liệt kê files sẽ sửa (≤5 files trừ khi có lý do)
8. Implement — follow coding standards bên dưới
9. Self-check (xem checklist cuối file)
10. Update .context/ nếu có quyết định mới
11. Chỉ khi human chuyển version/milestone mới, update `AGENTS.md` + `.context/MILESTONES.md`, move milestone cũ sang `.context/MILESTONES_HISTORY.md`, và move tension `RESOLVED_ACTIVE` của milestone cũ khỏi `.context/TENSIONS_ACTIVE.md` sang `.context/TENSIONS_HISTORY.md` với `Status: ARCHIVED`
```

### Sau khi implement xong

```bash
# Theme PHP
php -l wp-content/themes/skvn-marine/functions.php

# Plugin JS/TS (nếu có thay đổi block)
cd wp-content/plugins/skvn-marine-blocks && npm run build 2>&1 | tail -10

# Kiểm tra block nếu thay đổi block registration
grep -r "registerBlockType" wp-content/plugins/skvn-marine-blocks/src/
```

---

## 4. Tension Detection

Ghi tension khi phát hiện conflict giữa task yêu cầu và constraint đã có trong `[manual]`.

### Format ghi vào `.context/TENSIONS_OPEN.md`

```markdown
## [YYYY-MM-DD HH:MM] | [module]
Tension:    Mô tả conflict ngắn gọn
Context:    Đang làm task gì
Proposal:   Agent muốn làm gì
Constraint: [manual] rule nào conflict (quote lại)
Severity:   low | high
Tags:       tag-one, tag-two
Milestone:  V1 / 0.x.0
Status:     OPEN
Resolved:
Decision:   OPEN
```

Agent không được parse `Decision` để suy ra state. Chỉ dùng `Status`.

Allowed `Status`: `OPEN`, `RESOLVED_ACTIVE`, `ARCHIVED`.

Allowed `Severity`: `low`, `high`.

Tag taxonomy hiện tại:

```text
agent
a11y
blocks
editor-governance
map
milestone
multilingual
patterns
php
planning
product-data
quote-flow
schema
slider
spam-protection
tailwind
theme
woocommerce
```

Khi cần tag mới, update taxonomy trong file này trước khi dùng.

### Routing

```
Severity LOW  → ghi tension → tiếp tục theo hướng conservative → báo human review sau
Severity HIGH → ghi tension → DỪNG task → chờ human fill Decision
```

### Tension files và archive

`.context/TENSIONS_OPEN.md` chỉ chứa `Status: OPEN`.

`.context/TENSIONS_ACTIVE.md` chỉ chứa `Status: RESOLVED_ACTIVE` của milestone hiện tại.

`.context/TENSIONS_HISTORY.md` chỉ chứa `Status: ARCHIVED`.

Khi human resolve một OPEN tension, agent move entry từ `TENSIONS_OPEN.md` sang `TENSIONS_ACTIVE.md`, set `Status: RESOLVED_ACTIVE`, set `Resolved: YYYY-MM-DD`, và ghi decision.

Khi human tuyên bố chuyển sang version/milestone mới, agent phải move các entry `Status: RESOLVED_ACTIVE` của milestone cũ sang `.context/TENSIONS_HISTORY.md`, đổi `Status: ARCHIVED`.

Không load `.context/TENSIONS_HISTORY.md` mặc định khi bắt đầu task. Chỉ đọc history khi cần audit quyết định cũ hoặc human yêu cầu.

`Status: OPEN` luôn đọc đầy đủ, không filter theo tag. `TENSIONS_ACTIVE.md` được đọc với tag filter: entries match task tag đọc đầy đủ; entries không match chỉ đọc title/status/tags. Nếu không chắc, đọc đầy đủ.

Milestone checklist và trigger chuyển mốc được quản lý trong `.context/MILESTONES.md`. Khi chuyển milestone, milestone cũ phải được archive sang `.context/MILESTONES_HISTORY.md`.

### Ví dụ triggers tension

| Situation | Severity |
|---|---|
| Task yêu cầu sửa file trong `themes/generatepress/` | HIGH |
| Task muốn custom-code quote form handler thay CF7 | HIGH |
| Task muốn thêm animation logic riêng trong block, bỏ qua shared runtime | LOW |
| Task muốn đặt custom block trong theme folder | HIGH |
| Task muốn rename prefix/namespace | HIGH |
| Task muốn add dependency không có rationale | LOW |
| Không chắc feature thuộc theme hay plugin | LOW |

---

## 5. Coding Standards

### PHP (Theme)

- WordPress Coding Standards mindset
- Sanitize input, escape output — luôn luôn
- Prefix mọi function: `skvn_marine_`
- Không dùng `@` để suppress errors
- `filemtime()` cho asset versioning
- Không dùng Composer/PHPCS trong V1 nếu làm chậm dev

### TypeScript / JavaScript (Plugin blocks)

- TypeScript cho tất cả block logic
- @wordpress/scripts build pipeline
- ESLint/Prettier optional V1, expected V2
- Shared animation runtime — KHÔNG tạo animation logic riêng per block
- Swiper chỉ dùng cho slider block
- IntersectionObserver cho scroll reveal

### Gutenberg / Block

- Block attributes định nghĩa rõ trong `block.json`
- Sidebar controls thay vì raw class input cho marketing users (V3 goal)
- Editor view KHÔNG set `opacity: 0` cho reveal elements nếu không có safe fallback
- Slider editor: stacked preview hoặc simplified — KHÔNG run Swiper autoplay trong editor
- Keyboard navigation bắt buộc cho accordion và slider

### Animation

- Tất cả animation phải check `prefers-reduced-motion`:

```javascript
const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
if (!prefersReduced) { /* run animation */ }
```

---

## 6. Module-Specific Rules

### Theme: `inc/media.php`

- `skvn_marine_auto_set_image_alt_from_title()` — chỉ fill khi ALT empty
- KHÔNG overwrite ALT đã có
- KHÔNG auto-generate caption trong V1

### Theme: `inc/enqueue.php`

- Dùng `filemtime()` cho versioning
- Conditional load: Swiper chỉ load khi có slider block trên page
- Block assets: load via block.json `viewScript`, không global

### Plugin: Slider / Slide

- Swiper config qua block attributes (autoplay, delay, loop, arrows, dots, effect, slidesPerView)
- Editor: render slides stacked (không run Swiper carousel trong editor)
- Keyboard nav: Swiper `keyboard: { enabled: true }`
- Pause on hover: `autoplay.pauseOnMouseEnter: true`

### Plugin: Product Grid / Product List

- Dùng WooCommerce native query — KHÔNG custom SQL trực tiếp
- V1: WooCommerce native blocks/patterns trước, custom block sau khi homepage đã xong
- Pagination cho Product List
- Mobile: CTA (Request a Quote) luôn visible, KHÔNG chỉ hiện khi hover

### Quote Flow

- URL pattern: `/request-a-quote/?product_id=123`
- Hidden fields bắt buộc: `product_id`, `product_sku`, `product_name`, `product_url`, `source_url`, UTM fields
- CF7 markup dùng class `skvn-form`, `skvn-quote-form`, `skvn-button`, `skvn-button--primary`
- n8n webhook: hard-to-guess URL + optional shared secret header. KHÔNG expose unprotected.

---

## 7. AI Task Format

Mỗi task đưa cho AI nên có đủ 6 phần:

```markdown
## Context
[Load từ .context/ hoặc mô tả ngắn]

## Goal
[1-2 câu: làm gì]

## Files allowed to change
[List cụ thể]

## Files forbidden to change
[Luôn include: themes/generatepress/**]

## Acceptance checklist
- [ ] PHP syntax ok
- [ ] No fatal errors
- [ ] Sanitize/escape đầy đủ
- [ ] Prefix đúng
- [ ] .context/ updated nếu có decision mới

## Tensions / Conflicts
[Ghi nếu biết trước có conflict, để agent xử lý đúng]
```

---

## 7.1 Layout Translator CLI

Dev-only CLI để dịch HTML/CSS artifact hoặc screenshot reference thành Gutenberg pattern components.

Path:

```text
tools/layout-translator/translate-layout.mjs
```

Lệnh:

```bash
node tools/layout-translator/translate-layout.mjs --input docs/artifacts/example.html
node tools/layout-translator/translate-layout.mjs --input docs/artifacts/example.html --output docs/artifacts/example.translation.md
node tools/layout-translator/translate-layout.mjs --input reference.png --kind screenshot --title "Hero Reference"
```

Output bắt buộc:

```text
gutenberg_markup
required_classes
theme_css_contract
animation_contract
assets_needed
not_translated
risks
```

Rules:

- CLI chỉ là dev tool trong source repo, không chạy trong WordPress runtime.
- Không paste raw `<style>` hoặc `<script>` vào `gutenberg_markup`.
- Text/button/content chính phải là Gutenberg editable content.
- Decorative/background/motion chỉ xuất contract cho theme CSS hoặc shared animation runtime.
- Không tạo custom block nếu core Gutenberg blocks + theme pattern đủ dùng.
- Screenshot mode chỉ tạo scaffold/contract, không tự nhận diện pixel như computer vision.

---

## 8. Self-Check Trước Khi Submit

```
[ ] Không sửa themes/generatepress/
[ ] Không rename namespace/prefix
[ ] Không đặt block logic trong theme
[ ] Input sanitized, output escaped
[ ] Animation có prefers-reduced-motion guard
[ ] Editor không hide content với opacity: 0
[ ] Image ALT: chỉ fill khi empty, không overwrite
[ ] Dependency mới có rationale
[ ] Files sửa ≤ 5 (hoặc có lý do nếu hơn)
[ ] TENSIONS_OPEN.md updated nếu có conflict phát sinh
[ ] TENSIONS_ACTIVE.md updated nếu human resolve tension trong milestone hiện tại
[ ] context-gen check-consistency . passed
[ ] MILESTONES.md updated nếu milestone checklist/status thay đổi
[ ] Chỉ archive resolved tensions khi human chuyển version/milestone mới
[ ] .context/modules/<module>.md updated nếu có decision mới
```

---

## 9. Versioning Milestones (để biết scope hiện tại)

| Version | Scope |
|---|---|
| 0.1.0 | Child theme skeleton |
| 0.2.0 | Design system, block styles, patterns |
| 0.3.0 | Slider/Slide block |
| 0.4.0 | Woo product grid/list |
| 0.5.0 | Quote flow integration |
| 1.0.0 | V1 launch-ready |

---

## 10. Không thuộc phạm vi V1

- Quote cart, multi-product quote table
- Advanced product filtering
- Popup/modal làm primary quote flow
- Polylang activation (chỉ prepare)
- Composer/PHPCS strict enforcement
- Redis / CDN
- GitHub Actions CI/CD
- Technical Product Card với specs table
- Custom CPT cho product

---

## 11. Dev Repo vs WordPress Runtime — Không được nhầm

Repo này (`D:\Github\skvn-marine`) là **source repo để dev**, KHÔNG phải WordPress runtime root.

WordPress runtime root nghĩa là **thư mục cài WordPress đầy đủ để chạy/test website**, ví dụ thư mục có `wp-admin/`, `wp-includes/`, `wp-content/`, `wp-config.php`. Với người không quen technical wording, có thể hiểu đơn giản là: **thư mục chứa toàn bộ bộ cài WordPress của site local/test**.

Nếu có file `.local/ENVIRONMENT.md`, agent phải đọc file đó để biết path môi trường local của máy hiện tại.

Nếu chưa có `.local/ENVIRONMENT.md` và task cần thao tác với WordPress runtime (cài/activate plugin, test theme/plugin, chạy WP-CLI trên site), agent PHẢI hỏi human cung cấp WordPress runtime root trước. Khi hỏi, nói rõ đây là **đường dẫn tới thư mục cài WordPress đầy đủ của site local/test**, không phải source repo theme/plugin. Sau khi human cung cấp path, agent PHẢI tạo `.local/ENVIRONMENT.md` và ghi lại:

```text
WP_RUNTIME_ROOT_WINDOWS=...
WP_RUNTIME_ROOT_WSL=...
```

File `.local/ENVIRONMENT.md` là machine-local, đã được git ignore, KHÔNG commit.

### Local dev runtime — máy hiện tại

Thông tin chi tiết của máy hiện tại nằm trong `.local/ENVIRONMENT.md`. File này local-only, có thể chứa credential, KHÔNG commit.

Agent phải đọc `.local/ENVIRONMENT.md` trước khi chạy WP-CLI, bật server, hoặc chạy build local.

Local hiện tại:

```text
Source repo Windows: D:\Github\skvn-marine
WP runtime Windows:  D:\Github\minhhaifish
WP runtime WSL:      /mnt/d/Github/minhhaifish
WSL distro:          Debian
WSL user:            shinkuro
WP URL:              http://localhost:8080
WP admin:            http://localhost:8080/wp-admin/
```

Toolchain đã cài trong WSL Debian:

```text
PHP:      /usr/bin/php — 8.4.21
WP-CLI:   /usr/local/bin/wp — 2.12.0
MariaDB:  /usr/bin/mariadb — 11.8.6
nvm:      /home/shinkuro/.nvm
Node.js:  20.20.2
npm:      10.8.2
```

Không cài Node/toolchain vào source repo. Không tạo `.local/toolchains/` trong repo.

Bật local WP dev server:

```bash
wsl -d Debian -- bash -lc "cd /mnt/d/Github/minhhaifish && setsid -f wp server --host=0.0.0.0 --port=8080 --allow-root"
```

Kiểm tra server:

```bash
curl -I http://localhost:8080/wp-login.php
```

Chạy plugin build bằng Node trong WSL:

```bash
wsl -d Debian -- bash -lc "source /home/shinkuro/.nvm/nvm.sh && nvm use 20 && cd /mnt/d/Github/skvn-marine/wp-content/plugins/skvn-marine-blocks && npm run build"
```

### Source repo chỉ chứa

- Theme custom: `wp-content/themes/skvn-marine/`
- Plugin custom: `wp-content/plugins/skvn-marine-blocks/`
- Context/docs/dev files: `.context/`, `docs/`, `AGENTS.md`, `README.md`

### Source repo KHÔNG được chứa

Không cài, copy, vendor, commit external WordPress plugins vào repo này:

- `wp-content/plugins/woocommerce/`
- `wp-content/plugins/contact-form-7/`
- `wp-content/plugins/contact-form-cfdb7/`
- `wp-content/plugins/seo-by-rank-math/`
- `wp-content/plugins/rank-math/`
- `wp-content/plugins/antispam-bee/`
- `wp-content/plugins/windpress/`
- `wp-content/plugins/polylang/`
- `wp-content/plugins/akismet/`
- `wp-content/plugins/hello.php`

External plugins thuộc **WordPress runtime site**, không thuộc source repo dev.

### Trước khi cài plugin bằng WP-CLI

Bắt buộc xác định rõ WordPress root thật. WordPress root phải có:

```text
wp-config.php
wp-admin/
wp-includes/
wp-content/
```

Local hiện tại phải được khai báo trong `.local/ENVIRONMENT.md`, ví dụ:

```text
WP_RUNTIME_ROOT_WINDOWS=D:\Github\minhhaifish
WP_RUNTIME_ROOT_WSL=/mnt/d/Github/minhhaifish
```

Nếu task yêu cầu "cài plugin", "activate plugin", "setup môi trường WordPress", agent PHẢI hỏi hoặc xác nhận path runtime root trước.

Không được chạy:

```bash
wp plugin install ...
```

trong source repo này nếu repo không có `wp-config.php`, `wp-admin/`, `wp-includes/`.

### Cách đúng để test theme/plugin

1. Human tạo hoặc cung cấp một WordPress runtime root riêng.
2. Agent cài external plugins vào runtime root bằng WP-CLI:

```bash
wp plugin install woocommerce contact-form-7 contact-form-cfdb7 seo-by-rank-math antispam-bee windpress --activate --path=/path/to/wp-root
```

3. Agent deploy custom code từ source repo sang runtime root bằng copy hoặc symlink:

```text
source repo/wp-content/themes/skvn-marine
-> runtime root/wp-content/themes/skvn-marine

source repo/wp-content/plugins/skvn-marine-blocks
-> runtime root/wp-content/plugins/skvn-marine-blocks
```

4. Agent activate custom theme/plugin trong runtime root:

```bash
wp theme activate skvn-marine --path=/path/to/wp-root
wp plugin activate skvn-marine-blocks --path=/path/to/wp-root
```

### Nếu lỡ copy external plugins vào source repo

DỪNG ngay và báo human.

Chỉ được xóa các thư mục external plugin nếu chắc chắn chúng là untracked/generated và không phải code custom của project. Sau khi xóa, kiểm tra:

```bash
git status --short wp-content/plugins
```

Kết quả mong muốn: chỉ còn `skvn-marine-blocks/` là plugin custom của repo.

### Tension trigger

Nếu không chắc một thư mục/plugin thuộc source repo hay runtime site → ghi LOW tension và hỏi lại trước khi cài/copy/xóa.
