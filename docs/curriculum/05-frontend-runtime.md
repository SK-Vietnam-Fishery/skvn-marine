# Module 05 — Frontend runtime: viewScript, Swiper, lifecycle, memory

## Mục tiêu

- Gắn JS runtime (Swiper) vào block dynamic đúng cách: init, config từ attributes, cleanup.
- Không bao giờ tạo memory leak kiểu dự án đã dính.

## Khái niệm

**`viewScript`** trong block.json = JS chỉ load trên frontend, chỉ khi block có mặt.
Pattern chuẩn: PHP render in config vào `data-*` attribute (JSON), view script đọc
`data-*` → parse → validate → init runtime.

**Validate config phía JS**: đừng tin JSON trong data attribute — parse trong try/catch,
clamp số về range hợp lệ, normalize enum về whitelist. Xem `src/slider/view.ts` các hàm
`normalizeChoice` / `clampInteger` / `clampNumber` — mọi giá trị từ attribute đi qua một
hàm chuẩn hóa trước khi dùng.

**Lifecycle là phần người mới bỏ qua:**

```
init:    tìm element → parse config → new Swiper(...) → gắn listeners
cleanup: swiper.destroy() → removeEventListener → disconnect observers → clear inline vars
```

Frontend bình thường ít cần cleanup (trang unload là hết). Nhưng **editor là SPA**:
block re-render liên tục, mỗi lần re-render mà không destroy instance cũ = instance
chồng instance. Dự án dính đúng bug này — slider trong editor iframe tích lũy Swiper
instance đến khi trình duyệt ì (case study 09-1). Fix: MutationObserver phát hiện DOM
node bị gỡ → `swiper.destroy()` + tháo toàn bộ listeners. Đọc
`docs/standards/memory-leak-prevention-rules.md` — file này là luật, không phải gợi ý.

**Shared runtime, không copy-paste per block** (quyết định A5 + `swiper-architecture.md`):
phần dùng chung thật (pause coordinator, reduced-motion check) extract vào `src/shared/`;
phần init Swiper mỗi block giữ riêng vì config phân kỳ. Quy tắc extract: **có ≥2 call-site
thật mới extract, không extract đón đầu** ("abstraction theater" — dự án đã bác bỏ
swiper-adapter chung vì lý do này).

**Accessibility + motion tối thiểu bắt buộc:**
- `prefers-reduced-motion: reduce` → không autoplay, không parallax, không hiệu ứng cưỡng ép.
- Keyboard: Swiper `keyboard: { enabled: true }`; arrows là `<button>` có label.
- Autoplay phải pause khi hover/focus/document hidden — xem `src/shared/autoplay.ts`
  (pause coordinator compose nhiều lý do pause).

## Đọc code thật

1. `src/slider/view.ts` — đọc cả file, đối chiếu: parse config → guards → new Swiper →
   events → cleanup block ở cuối.
2. `src/shared/autoplay.ts`, `src/shared/motion.ts` — hình dạng một shared module đúng mực.
3. `src/collection-view.ts` — consumer Swiper thứ hai: so sánh phần giống/khác với slider
   để hiểu vì sao không gộp adapter.
4. `docs/debug-casebook/gutenberg-plugin-memory-leak-guide.md` — cách reproduce và đo leak.

## Bài tập (capstone bước 5 — gắn Swiper vào Testimonial Slider)

1. `npm i swiper` — viết rationale 3 dòng (purpose/alternative/load scope) vào README
   plugin của bạn: luyện quy tắc A8.
2. PHP render in `data-sshow-config` (JSON: autoplay, delay, showArrows) — `esc_attr`.
3. `view.ts`: parse + clamp config → `new Swiper` (modules: Navigation, Autoplay, A11y,
   Keyboard) → reduced-motion guard tắt autoplay.
4. Pause on hover + khi tab ẩn (`document.visibilitychange`).
5. **Bài kiểm tra leak:** mở editor, duplicate slider 5 lần, xóa 5 lần, chụp heap
   snapshot (DevTools Memory) trước/sau — số Swiper instance phải về 0. Nếu chưa có
   cleanup trong editor preview thì bạn sẽ thấy nó tích lũy — viết cleanup rồi đo lại.

## Checkpoint

- [ ] Mọi giá trị config từ attribute đều qua clamp/normalize phía JS
- [ ] Kể được 4 việc của cleanup và vì sao editor cần nó hơn frontend
- [ ] Reduced-motion: chứng minh bằng emulation trong DevTools (Rendering tab)
- [ ] Heap snapshot trước/sau thao tác editor không tăng instance
