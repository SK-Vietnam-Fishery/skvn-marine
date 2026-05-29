# AGENTS_STANDARDS.md — SKVN Marine
# Coding Standards
# Đọc khi task liên quan đến viết code mới, review code, hoặc animation.

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
