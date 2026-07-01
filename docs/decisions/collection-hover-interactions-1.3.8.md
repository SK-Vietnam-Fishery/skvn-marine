---
name: collection-hover-interactions-1.3.8
description: Hover/interaction behaviors cho collection card và carousel controls — sourced từ design artifact
metadata:
  type: project
---

# Collection Hover Interactions — 1.3.8

**Source:** `.local/test-artifacts/Seafood Export Carousels Tailwind.htm`
**Status:** Implemented — `src/collection/style.css`

---

## Card hover lift

```css
.skvn-collection-card {
  transition: transform 0.28s cubic-bezier(0.2, 0.7, 0.3, 1),
              box-shadow 0.28s ease,
              border-color 0.28s ease;
}
.skvn-collection-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
  border-color: #99bfcc;
}
```

## Image zoom on card hover

```css
.skvn-collection-card__image {
  transition: transform 0.55s cubic-bezier(0.2, 0.7, 0.3, 1);
}
.skvn-collection-card:hover .skvn-collection-card__image {
  transform: scale(1.045);
}
```

`overflow: hidden` bắt buộc trên `__media` để clip image zoom.

## CTA button

```css
.skvn-collection-card__cta {
  transition: background 0.15s;
}
.skvn-collection-card__cta:hover  { background: #0D9488; }
.skvn-collection-card__cta:active { transform: translateY(1px); }
```

**Tension vs design:** Design dùng `#0E3A66` (navy) cho hover. Project dùng `#0D9488` (teal) để nhất quán với brand color scheme. Giữ teal cho đến khi có quyết định khác.

## Carousel arrow buttons

```css
.skvn-collection__arrow {
  transition: background 0.18s ease, border-color 0.18s ease, color 0.18s ease;
}
.skvn-collection__arrow:hover {
  background: #0A2540;
  border-color: #0A2540;
  color: #fff;
}
```

Inverted fill (navy) khi hover — khớp design artifact.

## Archive link

```css
.skvn-collection__archive-link {
  transition: gap 0.18s ease, color 0.18s ease;
}
.skvn-collection__archive-link:hover {
  gap: 0.55rem;
  color: #0D9488;
}
```

Gap animation tạo hiệu ứng arrow "slides away" khi hover — cần element con (icon/arrow) bên trong link để visible.

## Pagination dots

```css
.skvn-collection__pagination .swiper-pagination-bullet {
  transition: all 0.28s cubic-bezier(0.2, 0.7, 0.3, 1);
}
```

Transition áp dụng lên `background`, `width`, `height` khi active dot thay đổi.

---

## Chưa implement

| Behavior | Lý do |
|---|---|
| Read-more hover `gap` animation | Cần arrow icon con bên trong `__read-more` — hiện tại chỉ có text, gap không có effect. Implement khi thêm icon. |
