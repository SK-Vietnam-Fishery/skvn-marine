# AGENTS_MODULES.md — SKVN Marine
# Module-Specific Rules
# Đọc section liên quan đến module đang làm việc. Không cần đọc toàn bộ file.

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

### Page Display / Sidebar Controls

- Current 0.5.1 scope: page-level controls for hiding site header/footer and other safe editor/sidebar controls for marketing-owned pages.
- Header/footer visibility belongs to the theme layer because it controls GeneratePress visual/layout output.
- Prefer page-level editor/sidebar toggles over raw class input.
- Do not add a header/footer builder plugin in V1 unless human explicitly changes dependency policy.
- Do not edit GeneratePress parent theme.

### Site Branding / Visual Registry

- Trước khi code bất kỳ thay đổi branding/visual-token nào, agent PHẢI đọc và cập nhật `docs/standards/site-branding-guideline.md` nếu quyết định mới làm thay đổi registry, token, asset ownership, hoặc customization rule.
- Branding source of truth dạng docs hiện tại là `docs/standards/site-branding-guideline.md`.
- Theme owns primary brand system: `style.css`, `theme.json`, patterns, block styles, editor/frontend parity, theme screenshot.
- Plugin chỉ được giữ plugin-specific branding assets/tooling; KHÔNG làm source of truth cho visual system.
- Không code branding rải rác trước khi docs nêu rõ biến/class/asset đó nằm ở file nào.

### Quote Flow / Quote UI

- URL pattern: `/request-a-quote/?product_id=123`
- Quote UI/page surface is deferred to V1 / 0.6.0.
- CF7/CFDB7 implementation is deferred until after 0.6.0.
- n8n automation is deferred until after version 1.0.0.
- Do not custom-code quote form handling while CF7 is deferred.
- Future CF7 markup must use class `skvn-form`, `skvn-quote-form`, `skvn-button`, `skvn-button--primary`.
- Future hidden fields: `product_id`, `product_sku`, `product_name`, `product_url`, `source_url`, UTM fields.
- Future n8n webhook: hard-to-guess URL + optional shared secret header. KHÔNG expose unprotected.
