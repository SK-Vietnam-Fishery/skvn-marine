# Agent + Human — UI Layout Collaboration Method

Status: **ACTIVE PROTOCOL** (handoff Grok → Haiku)  
Date: 2026-06-19  
Case study: Single post hero layout + mobile island surfaces (`minhhaifishery.com`)

**Đọc kèm:**

- Skill: `.agents/skills/ui-debug/SKILL.md` (State Delta — bắt buộc cho UI bug)
- Contract: `docs/standards/css-layout-safety-contract.md`
- Routing: `AGENTS.md` § Agent response routing (`CODE_NOW` / `ASK_ARCHITECTURE` / `EXPLAIN`)
- Security (output path): `docs/standards/security-guidelines.md`
- Decisions đã chốt: `docs/decisions/single-post-layout-and-mobile-surfaces.md`
- Brainstorm chưa quyết: `docs/workflows/ideation-end-user-deep-customization.md`

---

## 1. Mục đích

Ghi **phương pháp làm việc** giữa human và agent khi xử lý bug layout/UI — không chỉ *kết quả* cuối. Haiku (hoặc agent implement) follow workflow này thay vì nhảy thẳng vào sửa CSS/PHP.

**Case SKVN single post** là mẫu chuẩn:

| Phase | Ai | Output |
|-------|-----|--------|
| Diagnose + prove | Agent (Grok) | Root cause có evidence, không đoán |
| Plan implement | Agent (Haiku) + planning file | `030` — files, grid rules |
| So sánh phương án | Agent + human | Bảng 2 vs 3, human chọn số |
| Implement | Agent sau khi human chọn | CSS/PHP, 1 owner width |
| Verify | **Dev** DevTools + agent đọc số | Pass criteria rõ |
| Ghi quyết định | Agent | `docs/decisions/` + HTML evidence đã sanitize |

---

## 2. Quy trình 8 bước (bắt buộc)

```text
1. Load context     GLOBAL → MILESTONES → TENSIONS → module → css-layout-safety
2. State Delta      State ĐÚNG vs SAI + 5 trục — KHÔNG đoán fix
3. Prove onsite     Fetch HTML / DevTools / URL thật — local thiếu page ≠ source fail
4. Tách scope       Fix tức thời vs brainstorm future — 2 file khác nhau
5. Plan hoặc so sánh  Một hướng rõ → CODE_NOW | Nhiều hướng → ASK_ARCHITECTURE
6. Human chọn       Human gõ số / A+B / "3" — agent không tự chọn thay
7. Implement        Ít file, token/owner rõ, audit shared component scope
8. Verify + docs    Script + pass table → human chạy → decision doc + test md
```

---

## 3. Bước 1–3: Diagnose đúng (State Delta + Prove)

### 3.1 Đọc triệu chứng thành clue

Human: *"hero/thumbnail bên trái thay vì trên content"*

| Clue | Hướng điều tra |
|------|----------------|
| Chỉ single post | `single.php` + GP body class |
| ~30–50% width | Flex/grid sibling, không phải ảnh nhỏ |
| Markup SKVN đúng | Theme/GP **composition**, không thiếu template |

### 3.2 State Delta (điền trước khi sửa)

```text
State A (ĐÚNG):  Hero trên → wrap 2-col dưới (artifact Style C / magazine intent)
State B (SAI):   Hero cột trái + wrap cột phải
Delta:           GP .site-content flex row + 2 SKVN siblings không bọc chung grid owner
```

### 3.3 Prove — không assume

- Fetch onsite HTML: đếm **direct children** của `.site-content`
- Ghi `body` classes (`single-post`, `right-sidebar`)
- **Không** fix bằng `!important` / `100vw` trước khi có delta

**Output bắt buộc trước implement:** 1 câu root cause + 1 đoạn HTML chứng minh.

Case SKVN (đã prove — **structural snippet**, đã sanitize):

```html
<div class="site-content" id="content">
  <div class="skvn-post-hero">…</div>
  <div class="skvn-single-wrap">…</div>
</div>
```

> Chỉ lưu **khung DOM** chứng minh sibling order — không paste full `<body>`, không copy nội dung bài, form, script.

---

## 3A. Cộng tác với Dev — debug qua DevTools

Dev (human) là **runtime owner**: browser thật, deploy, login, responsive, visual judgment. Agent **không** giả lập output DevTools và **không** assume onsite đã deploy bản mới.

### Vai trò tách bạch

| Việc | Agent | Dev |
|------|-------|-----|
| Viết script đo geometry / layout | ✅ | |
| Chạy script trên URL production/staging | | ✅ |
| Hard refresh, clear cache, chọn breakpoint | | ✅ |
| Paste **kết quả** (object JSON, screenshot) | | ✅ |
| Đọc số, so pass criteria, kết luận PASS/FAIL | ✅ | |
| Sửa theme/plugin sau khi có evidence | ✅ (hoặc Haiku) | Deploy / confirm |

### Quy tắc script DevTools (agent viết, dev chạy)

1. **Read-only** — chỉ `querySelector`, `getComputedStyle`, `getBoundingClientRect()`. Không `fetch` admin, không POST, không đổi DOM/CSS trên production trừ khi dev đồng ý probe tạm.
2. **Một URL + một điều kiện** mỗi verify — ghi rõ: desktop vs mobile width, sau deploy commit/theme version nào.
3. **Return object** — script cuối `result;` hoặc `console.log` để dev copy nguyên object (như Firefox expand).
4. **Pass table** — mỗi field có ngưỡng (ví dụ `heroAboveMain: true`, `bodyPad: 0px`).
5. **Không retry vô hạn** — dev chạy một lần; agent tiếp tục từ output, không bắt dev lặp cùng script nếu đã đủ field.

### Template agent gửi dev (copy-paste)

```markdown
## DevTools verify — [tên check]

**URL:** https://…
**Trước khi chạy:** Hard refresh (Ctrl+Shift+R). Viewport: [360px / desktop full].
**Sau deploy:** [theme version / “chưa deploy — baseline only”]

**Console — paste cả block:**

\`\`\`javascript
// … script …
\`\`\`

**Gửi lại cho agent:**
- Object JSON copy từ Console (nguyên văn)
- (Tuỳ chọn) screenshot responsive
- Nếu FAIL: ghi browser + `innerWidth` thực tế

**Pass khi:**
| Field | Pass |
|-------|------|
| … | … |
```

### Luồng hai chiều (case SKVN)

```text
Agent: State Delta + script Verify 2 + pass table
   ↓
Dev: chạy Console trên /iqf-technology-for-seafood-industry/
   ↓
Dev: { wrapW: 1200, heroAboveMain: true, … }
   ↓
Agent: PASS → ghi evidence vào docs/testing → không reopen 030
   ↓
Dev: nhắc mobile → agent research padding stack → script baseline
   ↓
Dev: { viewport: 360, bodyTextW: 278, … }
   ↓
Agent: quantify → so sánh hướng 2 vs 3 → dev chọn 3 → implement
```

### Agent khi nhận output từ dev

- **Tin số dev gửi** — không “làm tròn” pass nếu field fail (ví dụ `heroAboveMain: false`).
- **Đối chiếu công thức** — `viewport - wrapPad*2 - bodyPad*2 ≈ bodyTextW` (sai lệch vài px OK).
- **Ghi evidence block** trong `docs/testing/*.md` — object nguyên văn + bảng đọc từng field.
- Nếu thiếu field (dev chỉ expand 4 key) — ghi rõ *chưa verify* `sameColumn`, v.v.; không suy PASS.

### DevTools bổ sung (khi geometry chưa đủ)

| Mục đích | Gợi ý dev inspect |
|----------|-------------------|
| Flex/grid owner | `.site-content` → Computed `display`, direct children |
| Padding stack | Chọn wrap → body → island, đọc `padding-inline` |
| Tràn ngang | `document.documentElement.scrollWidth > innerWidth` |
| Sau fix mobile | `islandBorders` toàn `0px`, `navyMargin` âm khớp gutter |

---

## 3B. Sanitize HTML — evidence onsite & docs

HTML lấy từ production (fetch, View Source, copy Elements) là **untrusted input**. Trước khi đưa vào repo / plan / reply cho agent khác → **chỉ giữ phần chứng minh layout**, loại phần nhạy cảm và executable.

### Được phép lưu trong `docs/testing/` và `docs/decisions/`

| Được | Không |
|------|-------|
| Cây DOM **rút gọn** 3–15 node (class + thứ tự sibling) | Full page HTML |
| `body` class list (layout debug) | Nội dung post, comment, email |
| Ghi chú `…` / `<!-- children omitted -->` | `<script>`, `<iframe>`, inline `on*` |
| URL public reproduce (single post slug) | URL có `?nonce=`, preview token, wp-admin |
| Số DevTools (JSON) | Cookie, localStorage dump |

### Quy tắc rút gọn (structural proof)

```html
<!-- ✅ ĐÚNG — chứng minh GP flex sibling -->
<div class="site-content" id="content">
  <div class="skvn-post-hero">…</div>
  <div class="skvn-single-wrap">
    <div class="skvn-single-layout">…</div>
  </div>
</div>

<!-- ❌ SAI — không commit -->
<!-- full article HTML, CF7 forms, Rank Math JSON-LD, user emails, … -->
```

**Thay text node bằng `…`** — không quote paragraph tiếng Việt dài từ onsite vào docs.

### Agent fetch URL (WebFetch / curl)

1. Coi response là **read-only untrusted** — không `eval`, không chạy script trong snippet.
2. Trích xuất: tag name, `class`, thứ tự con trực tiếp, `body class` — đủ cho State Delta.
3. Không copy block Gutenberg content, form fields, hidden inputs (`product_sku`, UTM, v.v.) vào context files.
4. Nếu fetch 403 (preview/login) — **dừng**, nhờ dev paste structural snippet hoặc chạy script đã login; không đoán markup.

### Map sang PHP output (khi implement)

Evidence HTML chỉ để **prove composition**. Code theme vẫn tuân `AGENTS.md`:

- `esc_html()`, `esc_attr()`, `esc_url()`, `wp_kses_post()` trên mọi output động
- Không paste HTML onsite thô vào `single.php` / template

Sanitize HTML trong **docs** ≠ thay thế escape trong **PHP**.

### Checklist trước khi commit evidence

```
[ ] Chỉ structural snippet, không full page
[ ] Không script / iframe / event handler
[ ] Không PII, token URL, admin path
[ ] Text content thay bằng …
[ ] DevTools evidence là JSON số — không screenshot có email khách
```

---

## 4. Bước 4: Tách fix vs ideation

| Loại | File | Agent được làm gì |
|------|------|---------------------|
| Fix tức thời | `.context/planning/030_...` | Implement đúng plan |
| Future customize | `docs/workflows/ideation-end-user-deep-customization.md` | Chỉ brainstorm, link — **không** implement |
| Decisions đã chốt | `docs/decisions/single-post-layout-and-mobile-surfaces.md` | Tổng hợp sau khi xong |

**Rule:** Human nói *"tùy biến sâu sau"* → ghi ideation, **không** gộp vào fix 030.

---

## 5. Bước 5–6: Research & so sánh trước khi code

Khi human hỏi *"có thể bỏ island mobile không?"* — **research trước**, chưa sửa.

### 5.1 Quantify inset (padding stack)

Đọc CSS, liệt kê **mỗi lớp** gây hẹp:

```text
viewport
  → .skvn-single-wrap      (--skvn-page-gutter / 1rem)
  → .skvn-post-body        (card padding 24px)
  → .skvn-island           (card padding 16px + border)
```

Tính số: `360 - wrap - body = bodyTextW` — so khớp DevTools human gửi.

### 5.2 Đưa phương án có nhãn (A / B / C)

Không một fix mơ hồ. Mỗi phương án:

- Làm gì (1–2 câu)
- `bodyTextW` dự kiến
- Ưu / nhược / rủi ro layout
- Files đụng

### 5.3 So sánh khi human yêu cầu (ví dụ hướng 2 vs 3)

| Tiêu chí | Hướng 2 (A+B) | Hướng 3 (A+B+C) |
|----------|---------------|-----------------|
| Gain text width | ~+18px | ~+26–34px |
| Độ phức tạp | Thấp | Trung bình (sync navy margin) |
| Rủi ro | Thấp | Hero edge, archive scope |

**Chờ human chọn số** → mới `CODE_NOW`.

---

## 6. Bước 7: Implement — quy tắc Haiku phải theo

### 6.1 Structural fix (030) — grid owner, không patch GP

- Move DOM vào **một** grid owner (`.skvn-single-layout`)
- Không `display: block` lên `.site-content` nếu human đã confirm layout magazine khác hướng Grok alternative

### 6.2 CSS mobile — một gutter owner

Dùng token thay hardcode khi có coupling:

```css
.skvn-single-wrap {
  --skvn-page-gutter: 1rem;
  padding-inline: var(--skvn-page-gutter);
}

.skvn-island--navy {
  margin-inline: calc(-1 * var(--skvn-page-gutter));
}
```

Breakpoint đồng bộ: ≤900px `0.75rem`, ≤600px `0.5rem`.

### 6.3 Shared component audit

`.skvn-island` dùng **archive + single** (+ product gutter). Rule mobile áp global → **báo human** scope, không chỉ sửa single trong đầu.

### 6.4 Layout safety checklist

- [ ] Một owner width per level
- [ ] Không `100vw` / `50vw` mới
- [ ] Không `overflow-x` che tràn
- [ ] Files ≤ 5 (hoặc giải thích)

### 6.5 Anti-patterns (Haiku tránh)

| ❌ Sai | ✅ Đúng |
|--------|---------|
| Implement plan xong, không nghĩ mobile | Research padding stack + breakpoint ≤900 |
| Chỉ giảm `padding` một chỗ | Liệt kê lớp + token gutter |
| Gộp customization vào fix bug | Ideation riêng |
| Đoán Verify pass | Script + bảng pass criteria cho human |
| Reopen bug đã pass Verify 2 | Chỉ reopen khi evidence mới fail |

---

## 7. Bước 8: Verify — Dev chạy DevTools, agent đọc số

Chi tiết cộng tác: **§3A**. Sanitize khi ghi HTML evidence: **§3B**.

### 7.1 Viết script + pass table (trước khi dev test)

Lưu trong `docs/testing/*.md` — section **CHO HAIKU / DEV ĐỌC**:

- URL, điều kiện (sau deploy theme X)
- Template §3A (dev copy-paste)
- Script Console (read-only)
- Bảng field → Pass khi
- Evidence block — JSON nguyên văn dev trả; HTML chỉ structural §3B

### 7.2 Verify 2 (desktop hero) — đã PASS (dev evidence)

Dev paste từ Firefox Console:

```javascript
{ wrapW: 1200, heroTop: 220.2, mainTop: 580.2, heroAboveMain: true }
```

→ **Không** sửa lại 030 trừ khi dev gửi evidence mới fail (`heroAboveMain: false`, `wrapW` hẹp bất thường).

### 7.3 Verify mobile baseline (trước fix) — dev evidence

```javascript
{
  viewport: 360,
  wrapPad: "16px",
  bodyPad: "24px",
  bodyTextW: 278,
  islandCount: 2,
  islandPads: ["16px", "16px"],
  islandBorders: ["1px", "1px"]
}
```

→ Agent dùng để quantify §5.1; không đoán nếu dev chưa gửi.

### 7.4 Verify mobile (sau hướng 3) — chờ dev

```javascript
const wrap = document.querySelector('.skvn-single-wrap');
const body = document.querySelector('.skvn-post-body');
const cs = (el) => el && getComputedStyle(el);
({
  viewport: innerWidth,
  wrapPad: cs(wrap)?.paddingInline,
  bodyPad: cs(body)?.paddingInline,
  bodyTextW: body?.querySelector('p')?.getBoundingClientRect().width,
  islandBorders: [...document.querySelectorAll('.skvn-island')].map((el) => cs(el).borderWidth),
});
```

**Pass:** `bodyPad: 0px`, `wrapPad: 12px` (≤900) hoặc `8px` (≤600), `bodyTextW` ≈ 336–344 @ 360px, island thường `borderWidth: 0px`.

### 7.5 Collaboration rule (tóm tắt)

- Agent **chuẩn bị** script + pass table + template §3A
- Dev **chạy** onsite / responsive DevTools, **gửi JSON** (và screenshot nếu FAIL visual)
- Agent **ghi evidence đã sanitize** → tiếp tục; không bắt dev lặp context đã xong

---

## 8. Ghi docs — 3 loại file

| File | Khi nào | Ví dụ |
|------|---------|-------|
| `docs/testing/*.md` | Evidence, handoff, verify scripts | `single-post-hero-layout-review-1.3.6.md` |
| `docs/decisions/*.md` | **Đã chốt** + implemented | `single-post-layout-and-mobile-surfaces.md` |
| `docs/workflows/ideation-*.md` | Brainstorm, câu hỏi mở | `ideation-end-user-deep-customization.md` |

**Rule:** Decision doc trỏ ideation cho ý tưởng mới — **không** duplicate bảng brainstorm.

---

## 9. Phân vai agent (gợi ý cho human)

| Vai trò | Strength | Task fit |
|---------|----------|----------|
| Grok / agent có ui-debug | State Delta, onsite prove, so sánh 2 vs 3 | Diagnose, review implementation, mobile research |
| Haiku | Implement theo plan ngắn | `030` grid restructure, CSS theo spec human chọn |
| **Dev (human)** | DevTools onsite, deploy, sanitize paste | Chạy script §3A, gửi JSON, chọn hướng 2/3 |

**Handoff Haiku tối thiểu phải có:**

1. Planning file hoặc decision doc (source of truth)
2. Files allowed / forbidden
3. Verify script + pass criteria
4. Explicit: *không reopen* bug đã pass trừ evidence mới

---

## 10. Checklist nhanh — Haiku trước mỗi task layout/UI

```
[ ] Đã đọc ui-debug skill + css-layout-safety-contract
[ ] Đã có State Delta (không chỉ symptom)
[ ] Root cause prove bằng structural HTML (§3B) hoặc dev JSON — không assume
[ ] Dev đã chọn phương án nếu >1 hướng hợp lệ
[ ] Biết shared component nào bị ảnh hưởng (island, wrap…)
[ ] Có token/owner cho gutter nếu negative margin
[ ] Có verify script + template §3A cho dev sau deploy
[ ] Evidence HTML đã sanitize; JSON dev gửi ghi nguyên văn trong docs/testing
[ ] Decisions ghi docs/decisions — ideas ghi ideation
```

---

## 11. Timeline case study (tham chiếu)

| Thứ tự | Việc | Method step |
|--------|------|-------------|
| 1 | UI debug hero trái | §3 State Delta + onsite HTML |
| 2 | Plan 030 Haiku implement | §4 tách fix / §6.1 structural |
| 3 | Review Haiku vs 030 | Verify criteria §7.2 |
| 4 | Dev Verify 2 DevTools | §3A + §7.2 evidence JSON |
| 5 | Dev nhắc mobile island | §5 research, chưa code |
| 6 | Dev gửi `bodyTextW: 278` | §3A + §5.1 quantify |
| 7 | So sánh hướng 2 vs 3 | §5.3 ASK_ARCHITECTURE |
| 8 | Human chọn `3` | §5.6 → CODE_NOW |
| 9 | Implement A+B+C + `--skvn-page-gutter` | §6.2 |
| 10 | Decision doc + link ideation | §8 |

---

## Changelog

| Date | Note |
|------|------|
| 2026-06-19 | Tạo từ thread Grok + human — handoff phương pháp cho Haiku (single post hero + mobile surfaces) |
| 2026-06-19 | Thêm §3A DevTools collaboration với dev + §3B sanitize HTML evidence |