# CASE-005 — Slider Editor IME Typing Re-Render Cascade

## Metadata

```text
ID: CASE-005
Category: slider / gutenberg
Component: skvn-marine/slide (editor)
Milestone: V1 / 1.3.6
Observed: 2026-06-19
Environment: Block editor, slide heading (core/heading via InnerBlocks)
Status: PROVEN · FIXED · REGRESSION_GUARDED
Onsite verification: PENDING
Implementation: 2026-06-19
```

## Summary

Gõ heading trong slide (đặc biệt với IME tiếng Việt) bị lag và caret nhảy
(`triênr` → `tr` → `triển`). Không phải do Swiper/parallax trong editor.

Root cause: `slide/edit.tsx` dùng `useSelect` trả **object literal mới** mỗi khi
block editor store đổi → **mọi Slide `Edit` re-render** mỗi keystroke/composition
event → RichText mất sync với IME buffer.

## Symptoms

- Lag khi **bắt đầu** gõ trong slide heading.
- IME composition: text/caret flash giữa các phase (`triênr` → `tr` → `triển`).
- Cảm giác “IME cùi” nhưng cùng máy gõ heading ngoài slider mượt hơn.
- Nhiều slide trong stack → lag cảm giác nặng hơn (N slide re-render).

## State Delta

```text
State A (ổn):
- Gõ core/heading đứng một mình hoặc paragraph ngoài slider
- IME composition liền mạch

State B (lag):
- Gõ heading trong skvn-marine/slide → skvn-marine/slider
- Caret nhảy ngược giữa composition updates

Delta:
- Mỗi store tick (keystroke / compositionupdate) chạy useSelect trên MỌI slide
- Selector trả object mới { clientId, preset } → shallow compare fail → re-render
- Slide Edit re-render kéo InnerBlocks + background img + InspectorControls
```

## Evidence

### Source — unstable useSelect return

```tsx
// wp-content/plugins/skvn-marine-blocks/src/slide/edit.tsx
const parentSlider = useSelect(
  ( select ) => {
    // … getBlockParents, getBlockAttributes …
    return {
      clientId: parentClientId,
      preset: parentClientId
        ? String( editor.getBlockAttributes( parentClientId )?.preset || '' )
        : '',
    };
  },
  [ clientId ]
);
```

`@wordpress/data` `useSelect` re-run selector trên **mọi** store dispatch. Object
literal mới mỗi lần → component re-render dù `preset` string không đổi.

### Context đã khai báo nhưng không dùng

```json
// slide/block.json
"usesContext": [ "skvn-marine/sliderPreset" ]
```

```json
// slider/block.json
"providesContext": { "skvn-marine/sliderPreset": "preset" }
```

Slide `Edit` có thể đọc `preset` từ context **không cần** subscribe store mỗi keystroke.

### Human evidence

- IME flash pattern `triênr → tr → triển` khi gõ slide heading.
- Pattern khớp RichText parent re-render trong composition (không phải behavior
  IME bình thường trên block nhẹ).

## Hypotheses

| # | Giả thuyết | Kết quả |
|---|------------|---------|
| H1 | Swiper/autoplay chạy trong editor | **Loại** — `view.ts` frontend only |
| H2 | IME/hardware | **Một phần** — làm nặng cảm giác, không giải thích caret nhảy |
| H3 | useSelect unstable object → N slide re-render | **Chấp nhận** |
| H4 | Hero CSS paint (text-shadow, clamp) | **Phụ** — tăng cost sau re-render |

## Root Cause

**Store subscription wider than necessary + unstable selector return shape.**

Mỗi lần heading content đổi → block editor store dispatch → tất cả Slide `Edit`
instances chạy `parentSlider` selector → return new object → re-render full slide
preview (ảnh nền, overlay, InnerBlocks wrapper) → IME/RichText caret desync.

## Fix (implemented 2026-06-19)

1. `preset` đọc từ `context['skvn-marine/sliderPreset']`.
2. `useSelect` chỉ trả `parentSliderClientId` string (primitive).
3. `memo()` Slide `Edit` + `SlideBackgroundPreview` memo cho ảnh nền.

Files: `src/slide/edit.tsx`, `tests/slider-block.test.mjs`.

## Regression Guard

| Guard | Mô tả |
|-------|--------|
| `tests/slider-block.test.mjs` | context preset; không unstable `parentSlider` object trong `useSelect` |
| Manual | Gõ IME trong heading: không caret jump `triênr→tr→triển` |

## Verification

### PASS criteria

- [ ] Gõ tiếng Việt IME trong slide heading: composition không nhảy caret.
- [ ] React Highlight updates: chỉ slide đang edit flash (không cả stack).
- [ ] Không regression preset behavior (product-showcase ẩn bg image, v.v.).

### DevTools protocol

```text
1. React DevTools → Highlight updates → gõ 1 ký tự trong slide heading
2. PASS: chỉ 1 slide (hoặc heading block) flash
3. FAIL: mọi slide trong slider flash
```

## General Principle

> **`useSelect` phải trả primitive hoặc referentially stable value.**
>
> Không return `{ … }` mới mỗi tick. Với parent attribute đã có qua
> `providesContext` / `usesContext`, **đừng** walk `getBlockParents` trong
> selector chạy trên mọi keystroke.
>
> Editor typing path phải isolate: content block đổi không được re-render
> toàn bộ decorative preview (ảnh nền, inspector) của sibling slides.

## Related Files

- `wp-content/plugins/skvn-marine-blocks/src/slide/edit.tsx`
- `wp-content/plugins/skvn-marine-blocks/src/slide/block.json`
- `wp-content/plugins/skvn-marine-blocks/src/slider/block.json`
- `docs/decisions/gutenberg-editor-chrome-contract.md`
- `docs/debug-casebook/slider/004_SLIDER_EDITOR_ADD_SLIDE_HIT_TEST_FAILURE.md`