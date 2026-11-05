# 042 — Đặc tả visual Carousel 1.3.17 (W0/W1)

Ngày: **2026-10-15**. Status: **Đặc tả source/artifact đã chuẩn bị — chờ review G1–G5; chưa implementation/onsite PASS**.

Authority: [041 execution plan](041_VER_1_3_17_CAROUSEL_BLOCK_UI_EXECUTION_PLAN.md), [040 §10–11](040_VER_1_3_17_CAROUSEL_UI_UX_TECHNICAL_PLANNING.md), [MILESTONES](../MILESTONES.md). Current **V1 / 1.3.17 — Product/Post Carousel Block UI & Visual QA**; 1.3.18 Editor UX/Technical PLANNED, chưa start.

## 1. Phạm vi, provenance và baseline

Đây là đặc tả đọc từ HTML/CSS/TS/PHP, không phải số đo rendered browser. Số px dưới đây là giá trị khai báo artifact, chưa chứng minh kết quả computed style trên WordPress. Human đã chọn POC-03 làm reference, nhưng 041 yêu cầu khóa G1–G5 trước gói phụ thuộc; không suy ra mọi chi tiết artifact hoặc đề xuất mới đã duyệt.

Nhãn dùng xuyên tài liệu:

- **A — Artifact:** giá trị trích nguyên từ POC; candidate target khi không có delta human.
- **H — Human locked direction:** quyết định đã chốt tại 040; không hỏi lại hướng.
- **S — Source:** implementation đang có, không đồng nghĩa behavior PASS.
- **P — Proposed / pending:** phần artifact thiếu hoặc mâu thuẫn cần review; không tự đặt default/token mới.

Baseline W0: HEAD `4b8b463`; working tree dirty gồm docs, cards.php, package/build và theme style.css. Package/theme metadata đọc được vẫn **1.3.9**, dependency `swiper ^11.1.15`. Không reset/ghi đè thay đổi sẵn có; trước code executor phải chụp lại diff. Không build/lint/runtime/onsite trong lần đặc tả này; URL/site/build QA chờ human cung cấp.

### Evidence index (đường dẫn từ repo root)

| Ref | Nguồn | Phạm vi bằng chứng |
|---|---|---|
| A03 | `.local/product-carousel-swiper-poc-03.html` | Product canonical: tokens 12–37; component CSS 85–412; DOM 433–663; presets/runtime 700–742 |
| A02 | `.local/product-carousel-swiper-poc-02.html` | **Có Post artifact cũ thật**: section articles 716–844, 6 article cards; chưa được duyệt làm Post baseline hiện hành |
| A01 | `.local/product-carousel-swiper-poc.html` | Reference cũ có articles section :522, article data :583, renderer :636; không thay A03 |
| S-CSS | `wp-content/plugins/skvn-marine-blocks/src/collection/style.css` | Header :7, card :144, image :171, body/title :184, ratio :220, carousel :267, controls :298, CTA :485 |
| S-CARD | `wp-content/plugins/skvn-marine-blocks/modules/collection-render/cards.php` | Post :18–68; Product :77–200; badge :213; header :302; footer/carousel phần sau; image fallback :457 |
| S-PROD / S-POST | Plugin `modules/collection-render/product-collection.php` / `post-collection.php` | Wrapper attrs/ratio/equalHeight, header/empty/render paths |
| S-JS | Plugin `src/collection-view.ts`:48–75 | Count/canLoop, arrow scope, spaceBetween16, breakpoints640/1024 |
| S-ATTR | Plugin `src/product-collection/block.json`, `src/post-collection/block.json`:61–85; `src/collection/types.ts`:34 | Defaults/ratio/cardStyle/equalHeight; chưa media mode persistence |
| T-CSS | `wp-content/themes/skvn-marine/style.css`:19–71, :99–116 | Token bridge, aliases/font/radius/width; theme heading cascade |
| T-JSON | `wp-content/themes/skvn-marine/theme.json`:4–6, :28–78, :82–145 | Canvas760/1200, actual WP palette/font sizes |
| H | 040 §10, §11.3 | Full Product title, CTA align, square/contain, 3 media layouts, padding/radius direction, mobile arrows |

Các local artifact có thể không có trên checkout khác. Phải mở đúng file để tự kiểm, không dùng bản ghi này thay artifact khi visual review. A03 có placeholder media, **không có ảnh Product thật**, không có inset/floating hay Post demo. A02 có Post DOM mẫu, chưa render/kiểm browser trong lượt này; không được nói Post artifact hoàn toàn không tồn tại.

## 2. Cấu trúc visual và ownership

```text
Collection (nằm trong page canvas, không tự full-bleed)
  Header: eyebrow? → heading? → intro? | prev/next nếu áp dụng
  Carousel outer
    Track viewport (che slide ngoài vùng ngang có chủ đích)
      Slide / Card
        Media frame? → image contain / fallback + badge overlay?
        Body → full Product title → optional Woo chips/content → CTA
    Footer: pagination | catalog CTA? + archive link?
```

DOM production hiện header nằm ngoài carousel-outer; footer nằm trong outer; DOM A03 đặt footer ngoài track trực tiếp. Spec khóa kết quả nhìn, không bắt DOM giống hệt. Không đặt một link chứa link khác; defect badge archive-link đang có là TECH-03 → 1.3.18, nhưng mọi sửa DOM visual phải không tạo regression/link hỏng mới.

Width owner: page canvas sở hữu normal/wide/full; collection `width:100%`; track tính width card theo preset. Gap carousel do `spaceBetween` sở hữu; không thêm CSS gap cạnh tranh. A03 page wrapper max1200/padding32px 20px 80px và section padding-top40/margin-bottom64 là **demo composition**, không tự đưa vào component.

## 3. G1 — bảng trích số đo/token theo component

Các hàng A là baseline để review, không là xác nhận mọi giá trị đã locked. Rem của source giữ nguyên đơn vị, không đổi sang px khi chưa biết root font thực.

| Surface | A03 exact declaration / source line | Source hiện tại và delta | Target/provenance |
|---|---|---|---|
| Header | flex-end; gap24px; margin-bottom28px (:85) | flex-start; margin1.25rem; không gap tương ứng (S-CSS:7) | A candidate; heading dài cần G3 |
| Eyebrow | 12px/600, tracking .16em; gap8; margin-bottom12; uppercase; line22×2px (:92) | .8rem/600, .08em, gap.5rem, margin.5rem | A; teal token |
| Heading | 32px/800, line-height1.1, tracking-.02em, margin0 (:104); 24px <=767 (:399) | Plugin chỉ margin; theme font/cascade có ảnh hưởng | A sizes; font family còn G1 |
| Intro | 15px, line-height1.5; margin-top10; max560px (:123) | max64ch, margin0 | A |
| Header arrows | gap10; 46×46px; radius50%; border1px; SVG18×18; stroke2.2; hover navy/white; .18s (:130, :442) | 2.5rem circle, glyph ‹/›, shadow; gap.5rem | A chrome; H mobile không ẩn |
| Card shell | white; border1px #E2E8F0; radius14px; flex column/flex1/min-width0; overflow hidden (:159) | radius8, border#d9e2e8; flex column | A radius/shell; floating cần geometry khác |
| Card hover | translateY(-6px), shadow0 8px 24px rgba(8,47,73,.12), border#CBD5E1; transform .28s cubic-bezier(.2,.7,.3,1) (:168) | lift-6; shadow rgba(0,0,0,.1); image scale1.045 | A lift; scale ảnh không tự kế thừa, xem §4 |
| Media | square, background#EEF2F6, overflow hidden (:176) | image ratio4/3 base, object-fit cover; ratio class overrides (:171/:220) | H Product mới square/contain; legacy G5 |
| Body | padding18; gap13; flex1 column (:226) | padding1rem, gap.65rem | A; không fake empty B2B slots |
| Product title | 16.5px/700, line-height1.28, tracking-.01em, margin0 (:235) | 1.05rem/1.3; linked title | H full wrap/no clamp + A typography candidate |
| Chips | flex-wrap gap6; 11px/500; pad4px 9px; radius6; bg#F1F5F9/border1px #E8EEF3; nowrap (:245) | .78rem, radius4, .15rem .45rem (S-CSS:235) | A; long single chip wrapping P pending |
| Overlay badge | top/left12; 10px/700/.08em; pad5px 9px; radius6; white .92/blur4; shadow0 1px 3px rgba(10,37,64,.12) (:203) | badge list may have many terms; source values differ | A one badge; many/long term layout pending |
| Product CTA | full body width; pad12; radius9; 14px/600; navy→trust-blue hover; white text (:293) | radius4, pad.55rem 1rem, navy-ish→teal | H align CTA across row; A style |
| Carousel spacing | gap20 (:716); track padding6px 0 8px (:154) | JS gap16; track no same padding | A gap20; track padding not enough proof for floating/shadow |
| Footer | margin-top24; gap20; center/space-between (:350) | margin1.25rem, no equivalent gap | A |
| Footer actions | row gap12; flex-shrink0 (:357) | gap1rem | A; long labels need wrap P |
| Dots | 8×8 inactive, active width26; gap7; neutral→navy; transition .28s (:359) | .6rem circles; active width unchanged | A pill appearance; shape partly from imported Swiper CSS, no computed proof |
| Catalog CTA | bg sky50, border1px neutral300; radius8; 13px/600; pad9px 16px; hover white/navy (:374) | radius4; teal treatment | A, token conflict below |
| Archive link | 13.5px/600, gap8; navy→teal hover; SVG16 (:386) | green hover, .3rem gap | A |
| Mobile footer | <=767: column, align-start, gap14 (:398–402) | No corresponding collection rule in read source | A; nested actions wrapping still missing |
| Reduce/focus | reduce disables transitions on card/arrows/dots/track, hover transform none (:404) | reduce only track (:368); no component focus-visible match found | H/project motion rule; focus style P must be reviewed |

### Token mapping — exact match vs mismatch

| Role | Artifact | Existing token/source | Conclusion |
|---|---|---|---|
| Navy | #082f49 | --skvn-color-navy → blue-950 → WP skvn-blue-950 (#082f49) | Exact fallback match |
| Trust blue | #0f5c8c | --skvn-color-trust-blue hardcoded, no WP equivalent | Existing token, not blue-700 (#0369a1) |
| Teal / mint | #0d9488 / #ddfaf4 | teal-600 / mint-100 | Exact match |
| Text / dark / white | #334155 / #0f172a / #fff | slate-700 / slate-900 / white | Exact match |
| Sky50 | A03 #f0f9ff | theme CSS fallback #f0f9ff **but WP preset actual #eaf7ff** | G1 mismatch; do not claim exact mapping |
| Card radius | A03 14px | --skvn-radius-card 8px | No exact global token match; don't change global radius to fix collection |
| Card hover shadow | 0 8px 24px .12 | --skvn-shadow-card =0 10px 30px .1 | Different shape; no exact mapping |
| Neutrals | #F1F5F9/#E8EEF3/#CBD5E1/#94A3B8; border#E2E8F0; surface/line#EEF2F6; cert border#CCFBF1 | A03 --tmp-*; not present in palette shown T-JSON | Component fallback/token proposal pending, no global token additions approved |
| Typography | A03 body Inter then system stack | Theme body system; heading Instrument Serif default, Customizer may override | G1 font decision needed; not silently load Inter/CDN or override whole site |
| Spacing | 18/13/20/28px etc | No explicit spacingSizes section in current theme.json | Cannot label these existing spacing tokens |

**P recommendation for G1:** retain exact POC component measurements as candidate; reuse matching color aliases with fallbacks; treat neutrals/radius/shadow as scoped collection values rather than changing global theme tokens. Choose semantic palette fidelity vs literal sky50 knowingly. Preserve theme font choice until a scoped collection typography decision is made; this means “pixel match Inter” is not yet promised. Theme heading selector `body:not(.wp-admin) h2/h3` (T-CSS:99+) can outrank a single class for font-family/color; verify actual cascade before consuming rule. No global CSS override has been approved.

## 4. G2 — Product media contract and missing states

**H locked:** Product mới frame1:1, image contain centered, no crop/distortion; full image/native transparency remains; three layouts Sát mép / Có khoảng đệm / Nổi phía trên. Padding inset vài mức token đều top/left/right; radius auto theo geometry. Values/defaults/border/offset limits still pending. Post and legacy not automatically square.

Let W be card inner width, P the equal inset, R card outer radius, D actual distance from outer curved edge to frame edge (includes border when applicable):

- Edge: frame width W, height W; upper corners align with shell. No inset value implied.
- Inset: frame width W−2P, height equal width; top gap P. Bottom spacing belongs body/media separation, not automatically a fourth P.
- Radius candidate: max(0,R−D). If D=8 and R=20 →12; D=12/R16 →4; D16/R14 →0. These are explanations, **not chosen presets**.
- Floating: separate card-background and media layers; square frame shifted relative to background, reserved headroom inside item/track; no chosen size/offset. Headroom must cover upward displacement + hover lift + measured shadow extent; shadow blur value alone isn't a strict painted bounding box.

| State | Required outcome | A03 evidence / gap | Acceptance proposal |
|---|---|---|---|
| Landscape | Whole image visible, width fits, blank above/below | A03 only square placeholder, no image | Compare original bounds, frame rect1:1, no crop |
| Portrait | Whole image visible, height fits, blank left/right | Missing | Same at desktop/mobile/inset |
| Transparent / white-space asset | Preserve file whitespace; no auto remove-background | Missing | Use object with asymmetric transparent margins; visual scale need not match different asset |
| No image | Stable, understandable fallback, no ghost badge or gap outside spec | Placeholder is demo decoration, not real no-image state | P: neutral square fallback; Floating fallback behavior still needs approval |
| showImage=false | No media frame or reserved media headroom; body coherent | Not shown | Existing source omits media; propose preserve this behavior, don't force blank square |
| Slow/broken image | Geometry reserved, no title/CTA jump beyond declared fallback behavior | Not shown | Before/after rect + screenshot; broken source URL differs missing attachment |
| Floating first/middle/last | No crop of object/shadow; no adjacent-slide leak or header collision | No floating in A03/A02 | Hover/resize with headroom; don't set whole track overflow visible |
| Hover image | Contain/no-crop remains true in every state | A03 prose says scale, but no actual img/scale rule; source scale1.045 clips | P: omit image zoom for contain product; retain only approved card hover/reduce policy |

Frame width100% does not mean visible product touches edges. Radius clips frame, not the transparent object's silhouette. Human prepares assets; food circular reference doesn't authorize circular crop. The separate frame makes implementation/change/test responsibilities clearer.

G2 **not closed**: no local artifact demonstrating all three modes with real images, no approved P/offset/radius bounds or no-image Floating rule. This document specifies expected states; it does not pretend screenshots exist.

## 5. G3 — navigation/header/footer states

**H:** mobile arrows remain when showArrows on and content can move. A03 :400 display:none below768 is explicitly superseded. Placement and hide/disabled details remain open.

**P candidate (review, no new breakpoint locked):** allow header controls to wrap to their own row when text and 46px buttons + gap cannot fit; retain text reading order and visible focus. For footer, inherit A03 <=767 column as baseline, also allow action group wrap for long labels. Do not use fixed card count alone to decide whether current viewport can move.

| Content/runtime state | Visual requirement | Pending detail / evidence |
|---|---|---|
| 0 results | Clear empty message; no fake active navigation | Existing header renders before query; hiding controls needs actual state, G5 dependency |
| 1 / fewer / equal to visible | Do not imply movement that cannot happen | Hide vs disabled remains pending; test actual viewport, not only desktop |
| More than visible, loop | Both arrows when enabled; no fake start/end disabled | Use current state, no loop refactor in UI task |
| More than visible, non-loop | First/last state feedback distinguish disabled direction | A03 disabled opacity.4 vs source.35; choose presentation at G3 |
| showArrows=false / pagination=false | Omit corresponding visual affordance | No forced show despite saved toggle |
| Header long or heading off | No text/control collision; eyebrow/intro only still coherent | POC has one fixed short heading; needs representative fixture |
| Footer no/one/two actions | Preserve reading order, don't leave ghost gaps | Footer-right flex-shrink0 in artifact can overflow; wrap proposal pending |
| Many pagination bullets | Remain reachable, don't overlap actions | A03 fixed six products not proof for higher item count |
| Autoplay Pause enabled | Existing control must not be obscured by visual change | A03 intentionally no autoplay/pause; full state correctness deferred1.3.18 |

G3 full loop/keyboard/pause hardening is not scope1.3.17. Test UI state on configurations already supported; if UI cannot access correct state, report dependency instead of fabricating active/disabled CSS.

## 6. G4 — Post reference exists, approval separate

Correction/precision to earlier planning: **POC-03 has only article CSS**, but **POC-02 contains six actual article markup cards** at :736–835 (placeholder images). This is a valid review input, not a newly approved Post baseline.

A02 first card structure :736: media placeholder16:9 + category badge → body date → title → excerpt → read-more arrow. No author in that sample. A03 article-only CSS supplies ratio16:9 (:182), body gap10 (:233), title line-height1.32 (:243), date12px/500 (:324), excerpt13px/1.55 clamped3 lines (:325), read-more13.5px/600 gap7/padding-top4/SVG15 (:335). **All are candidate reference values**, not lock.

Source Post uses `--post` class while artifact uses `--article`; do not blindly copy selectors. Source order date/optional author/title/excerpt/read action (S-CARD:24–65); source excerpt trimmed22 words, artifact visual clamp3. Product full-title/square/floating decisions do not settle Post.

**P recommendation:** use A02 article composition with current shared A03 chrome as a review candidate; prepare real Post with/without image/date/author/excerpt and long title. G4 still needs human selection of title behavior, ratio, metadata spacing, excerpt limit and fallback. No new Post artifact has been created or browser-verified in this docs-only task.

## 7. G-MEDIA — media-mode persistence/legacy dependency (tách khỏi G5 ngày2026-10-15)

| Path | Source finding | Consequence for1.3.17 |
|---|---|---|
| Product block.json :66–81 | showImage true, imageRatio1:1, cardStyle default, equalHeight true | Saved old ratio must not change silently; default square alone is not a 3-mode contract |
| Post block.json same section | imageRatio16:9; shared types | Don't change Post while addressing Product |
| Product/Post render :25/:37–38 | Ratio class built from attr; equal-height class emitted | Changing CSS on common image class affects legacy and Grid |
| S-CSS :171/:220–224 | Base image ratio4/3/cover; wrapper ratio overrides target image | Frame square/contain requires distinct scope/approved integration, not global replacement |
| S-CARD Product :83/:105–114 | showImage gate; anchor media; image helper; badges inside | No existing inset/floating frame selection shown |
| S-CARD :457–468 | Woo image→Woo placeholder→fallback span | Missing attachment differs broken URL; fallback geometry still to validate |
| S-ATTR + types | No mediaLayout/inset/floating persisted attribute found in examined definitions | Cannot claim editor can select/save 3 modes; don't repurpose legacy cardStyle/raw class |
| S-PROD :49–67 | Header before Woo/query empty check | Zero-result navigation visual needs state integration evidence |
| S-JS :50–69 | canLoop based desktop count; gap16; closest collection arrow scope | Geometry gap may be narrow change after G1; no loop/state redesign |
| S-CSS :227/:288–294/:491 | Equal-height flex path and CTA margin-top:auto exist | Source support for aligned CTA is partial evidence; long title/mixed optional slots still need screenshot |

**Integration status: pending.** No schema/control/migration strategy is designed here. Candidate safe work while G5 open: reference/static visual specification and shared base chrome/body fixes that preserve saved attrs. New-mode integration must wait for an approved path within scope; do not mark UI-03a finished just because a mockup exists. If current UI requires a new persisted setting, present dependency; 1.3.18 does not start implicitly.

## 8. Acceptance/evidence package — ready to execute after relevant gates

All cases below **Chờ kiểm chứng**, no runtime PASS. URL/page/build/fixture IDs to be supplied in onsite test doc before execution. This is spec preparation, not actual onsite check.

| ID | Nhóm | Hành vi mong đợi | Hiện trạng | Việc cần sửa / chốt | Cách kiểm | Evidence |
|---|---|---|---|---|---|---|
| VS-G1 | Block UI | G1 measurements/token/cascade agreed | Chờ kiểm chứng — A/S extracted | Resolve font/sky50/scoped values/focus | Compare A03 and build same content, inspect computed styles | A03 refs + T-CSS/T-JSON; later paired images |
| VS-G2 | Block UI | 3 modes square/contain, no crop/overlap | Chưa làm — H direction, missing visual states | Approve values/fallback/headroom | Portrait/landscape/transparent/missing/slow/off, hover/resize | Frame width-height difference≤1 CSS px per041 V02; rect/video |
| VS-G3 | Block UI | Correct responsive arrows/footer presentation | Chờ kiểm chứng — state spec pending | Approve placement/hide-disabled/wrap | Counts0/1/less/equal/more at each preset; long heading/action labels | Screenshot state + click/swipe smoke |
| VS-G4 | Block UI | Post separately approved | Chờ kiểm chứng — A02 candidate found | Approve Post content/ratio/title/fallback | Metadata toggles + missing image/long title | A02 source; later separate Post visual |
| VS-MEDIA | Technical (media dependency) | No silent legacy/schema drift | Chờ kiểm chứng — dependency identified | Approve valid application path | Inspect stored attrs before/after when integration exists | S-ATTR/render/CSS inventory; no mechanism designed |
| VS-R | Cross-surface minimum | Shared Grid/editor unaffected by UI diff | Chờ kiểm chứng | Narrow regression only | Same fixture Grid+Carousel, existing editor render, focus/reduce, URLs/no-fatal | Build/diff, paired screenshots, console |
| VS-UX | Editor UX | Truthful preview/help direction preserved | Deferred1.3.18 | Inventory only | No editor mockup or control design now | 040 §11.4 |

Viewport matrix (041 baseline): **320/375/768/1024/1440**, plus **639/640/641**, **767/768**, **1023/1024/1025** because artifact chrome and slide breakpoints differ; resize/orientation continuously. Normal/wide/full canvas, desktop scrollbar visible. Presets1-1-1/2-1-1/3-2-1/4-2-1/5-3-1; enough products for each state (≥6 for preset5 movement), not all cartesian combinations.

Pass boundaries: component rect inside canvas, page scrollWidth≤clientWidth; no hidden overflow as geometry fix; full Product title readable; CTA row aligned with mixed title/data lengths; approved motion respects reduce; fallback state matches spec; no collision/clip or new invalid-block/fatal caused by diff. Store URL, fixture/settings, viewport/browser, commit+dirty diff/build, expected/actual, screenshot/video/rect path, reviewer/date.

## 9. Review bundle G1–G5 — what can be locked, what cannot

No request to reconfirm H decisions. Human can review A baseline and deltas in one document; numerical choices missing from artifacts remain open rather than invented.

| Gate | Ready evidence | Pending lock | Work that must wait |
|---|---|---|---|
| G1 | Exact A03 declarations + source/token mismatches | Font/cascade, sky50 role, scoped radius/neutrals/focus target | Dependent W2/W4 details |
| G2 | H geometry/contain and explicit edge-state contract | Actual inset levels/floating size-offset/headroom/fallback demonstration | W3 |
| G3 | A03 chrome/mobile footer + H mobile arrows | Placement and hide/disabled/wrap states | W4 state details |
| G4 | Existing A02 Post reference, A03 article CSS, current Post renderer | Post visual approval/real-content states | W5 |
| G5 | Native query → render cards → carousel source path (§10) | Basic runtime smoke với dữ liệu đơn giản | Basic integration |
| G-MEDIA | Attr/render/legacy inventory showing missing persistence | Approved application path without silently opening1.3.18 | New media-mode integration |

Decision Memo summary for unresolved bundle:
1. **Problem:** A03 gives baseline component look but not all new media/state/integration cases.
2. **Options:** (A) reuse artifact values where supported and review missing deltas; (B) redesign all components; (C) implement directly from incomplete baseline. B expands work; C risks rework/implicit choices.
3. **Recommendation:** A, preserving H and marking unsupported specifics pending; no automatic approval by silence.
4. **Evidence:** §§3–7 exact file/line sources and gaps; no fake screenshots.
5. **Self-check ≤10 minutes:** open A03 :85–412, A02 :716–750, T-CSS :19–71/T-JSON palette, S-ATTR :66–81 and S-PROD :25. Compare listed values, presence of Post and absence of media mode.
6. **Reversibility:** scoped visual values reversible; saved attr/migration changes materially affect existing content and need evidence, left out.
7. **Default while undecided:** retain locked directions/current content; don't implement gate-dependent details. Continue independent spec work only. No deadline invented.

W0 source baseline and W1 written specification prepared. **W1 visual-state artifacts and G1–G5 approvals remain incomplete** where stated; docs preparation is not closure of implementation/acceptance. No runtime/build/onsite/release/milestone change performed.
## 10. G5 minimum và đợt source đầu — human 2026-10-15

Human xác nhận G5 tối thiểu là **query trả kết quả → show cards thành carousel**, chưa cần dữ liệu phức tạp; đồng thời cho agent triển khai phần UI đã chốt/đủ evidence. Các chỗ trước đây gọi media persistence/legacy là G5 nay đọc thành **G-MEDIA**, dependency riêng, không chặn toàn bộ UI.

Source path kiểm được:
- Product query-products.php → wc_get_products; product-collection.php gọi query rồi render_collection_carousel ở layout carousel.
- Post query-posts.php → WP_Query; post-collection.php gọi query rồi cùng renderer carousel.
- cards.php:359 tạo wrapper/config, swiper-wrapper/slides và dispatch card Product/Post; collection-view.ts đọc data config và init Swiper.
- Plugin register callbacks tại skvn-marine-blocks.php:278/282; maybe_enqueue_collection_view cho carousel. Đây là Đã có source, chưa runtime PASS.

G5 fixture:6 Product Woo mặc định +6 Post published, newest, không filters phức tạp,4-2-1/autoplay off; verify results/cards/next-prev/swipe bằng test doc. Không thêm fields/meta/query builder hoặc settings.

Source delta đợt đầu:
- Product Carousel title/link bảo đảm full wrapping, không ellipsis/clamp; body có min-width0. Không chọn font/size/token mới.
- Reduce motion CSS tắt card lift/image zoom/CTA active translation và transition card/image/arrows/dots trên carousel; runtime pause/autoplay unchanged.
- CTA flex/margin-auto/equalHeight source đã có, cần test mixed-title alignment; không claim alignment đã PASS.
- Không sửa media fit/ratio hoặc ba mode persistence vì G-MEDIA chưa chốt, không tự đổi Post.
- Trạng thái UI-02a: Đã có source phần full-title; visual/runtime Chờ kiểm chứng. Motion safety: Đã có source; chưa build/onsite.

Test plan: [onsite-collection-carousel-m1.md](../../docs/testing/onsite-collection-carousel-m1.md). Source/artifact baseline trong §§1–9 là trước đợt CSS này; references line numbers có thể dịch. Không milestone transition/release/bump; human chạy build/lint và cung cấp evidence.

## 11. Human lock 2026-10-16 — attribute summary, local skeleton, alignment

Human: “OK chốt, với 3B thì Normal/Wide/Full và mặc định là wide đi”. Chốt hướng và hành vi dưới đây sau phân tích screenshot + báo cáo Muse `test-product-carousel-analysis-report.md`; lượt này chỉ lưu đặc tả, chưa giao sửa source các mục 2/3. Không coi design approval là runtime PASS hoặc bắt đầu milestone 1.3.18. Quyết định này bổ sung §11 của planning040; settings được ghi vào inventory, không tự mở toàn bộ thiết kế Editor UX/Technical.

### 11.1 Product: tóm tắt giá trị theo từng thuộc tính

- Một setting cấp block “Số giá trị tối đa mỗi thuộc tính”, lựa chọn **2 / 3 / 4**, áp dụng chung cho từng nhóm thuộc tính. Không phải giới hạn tổng chip của card; chưa chọn default 2/3/4.
- Giữ tên nhóm và thứ tự dữ liệu cấu hình. Ví dụ chọn2: `Size: 100–200 g · 200–300 g · +3`; `Packing: 10 kg bulk · Vacuum pack`.
- Chỉ báo **+N** cho số giá trị còn lại, chỉ hiện khi có giá trị bị rút gọn. Đây là phương án đã được chấp nhận thay dấu `...` ban đầu; không tự thêm thao tác expand/collapse, tooltip hoặc modal. Trang sản phẩm là nơi xem đầy đủ.
- Không tự xóa giá trị trùng giữa các nhóm: cùng nhãn có thể có ý nghĩa khác khi giữ tên thuộc tính. Chưa chốt chính sách loại trùng trong một nhóm.
- Giới hạn số giá trị giảm mật độ nhưng không đảm bảo chiều cao cố định: nhiều nhóm hoặc giá trị dài vẫn làm card cao. Không tự giới hạn số nhóm, cắt tên sản phẩm, clip nội dung hoặc thu nhỏ chữ để ép vừa card.
- Source baseline: cards.php gom mọi visible attribute value vào một mảng phẳng, không giữ tên nhóm; CSS specs đã flex-wrap. Screenshot cho thấy dày đặc/lặp IQF, chưa chứng minh geometric overlap. Không kết luận nguyên nhân đè chữ nếu chưa đo DOM tại state lỗi.

### 11.2 Skeleton riêng vùng carousel

- Skeleton theo cùng responsive preset, breakpoint, số cột, gap và media geometry của carousel; không tạo bộ cấu hình skeleton độc lập dễ lệch. Chỉ vùng carousel loading, không phủ toàn trang; heading/intro có thể hiện thật ngay.
- Skeleton tĩnh, không tương tác và không nhận focus. Controls chỉ tương tác khi carousel sẵn sàng; tôn trọng reduced-motion, skeleton trang trí không đọc như nội dung thật, vùng nội dung có trạng thái busy phù hợp.
- Chuyển sang nội dung thật khi carousel init và có geometry hợp lệ; không đợi window.load, toàn trang hoặc toàn bộ ảnh tải xong. Media có khung giữ chỗ để ảnh đến sau không làm đổi tỷ lệ.
- Phải giữ geometry tương đương trước/sau chuyển. Nội dung thật cần được layout/đo được, hoặc có cơ chế giữ chỗ tương đương; không chỉ vẽ skeleton chiều cao tùy ý rồi show card cao hơn. Cách triển khai cụ thể chưa thiết kế.
- Có fallback nội dung thật dạng tĩnh khi JS lỗi/không chạy; không để skeleton vô hạn. Timing/failure detection chưa chốt.
- Rationale: skeleton che trạng thái chưa sẵn sàng nhưng không thay thế pre-init layout. `visibility:hidden` riêng lẻ vẫn giữ layout, không loại bỏ dịch chuyển chiều cao. Báo cáo Muse 1–3s và chiều cao ~1000→450px là ước lượng, chưa là performance evidence.

### 11.3 Alignment: Normal / Wide / Full, mặc định Wide

- **Normal** theo content width của theme; **Wide** theo wide width của theme; **Full** theo chiều rộng page canvas. Mặc định lựa chọn **Wide** cho carousel. Không hard-code 760/1200px vào plugin và không dùng100vw/margin âm để thoát container.
- Áp dụng direction alignment cho Product/Post Carousel; không suy rộng thành approval chi tiết card Post hoặc tự đổi default Grid.
- Chọn alignment cần nối được editor → saved attribute → frontend wrapper; source hiện chưa hỗ trợ đường này đầy đủ. Không chỉ thêm supports.align mà bỏ renderer.
- Giữ lựa chọn Normal/Wide/Full được lưu rõ ràng. Quyết định default Wide không tự phê duyệt bulk migration nội dung cũ. Block legacy thiếu attribute cần chốt cơ chế tương thích trước implementation; hướng conservative là giữ bố cục cũ cho tới khi có lựa chọn rõ. Không tự đổi toàn bộ block cũ sang Wide.
- Theme content-width760px hiện tại là constraint có chủ đích, không phải bằng chứng theme lỗi. Thiếu lựa chọn alignment ở collection là khoảng trống cần giải quyết. Mở rộng width có thể giảm wrap chip nhưng không sửa mất nhóm/lặp dữ liệu.

### 11.4 Review và acceptance

| ID | Nhóm | Hành vi mong đợi | Hiện trạng | Việc cần sửa | Cách kiểm | Evidence |
|---|---|---|---|---|---|---|
| VS-ATTR | Block UI | Nhóm rõ, tối đa2/3/4 giá trị mỗi nhóm, +N đúng | Chưa làm — direction approved | Group-aware summary; không clip | Nhóm0/1/2/3/4/5+ giá trị, nhiều nhóm, nhãn dài/trùng, narrow card; CTA/full title | Screenshot human + cards.php baseline; output DOM/screenshots chờ kiểm |
| VS-ATTR-UX | Editor UX | Chọn2/3/4 và save/reopen nhất quán | Chưa làm — inventory | Chốt default, control/preview semantics khi triển khai | Toggle/save/reopen/so frontend cùng fixture | Chưa có runtime evidence |
| VS-SKELETON | Block UI | Skeleton cục bộ, preset khớp, không phình/co do chuyển trạng thái | Chưa làm — direction approved | Geometry/fallback strategy | Cold/warm load, slow/blocked JS, ảnh chậm/mất, title dài/nhiều thuộc tính, resize và tất cả preset | Video/rect trước-sau chờ kiểm; Muse timing chỉ ước lượng |
| VS-SKELETON-TECH | Technical | Ready/failure đúng, nội dung accessible, không skeleton vô hạn | Chưa làm — dependency | Thiết kế readiness/fallback tối thiểu khi được giao | JS off/error, keyboard, reduced-motion, busy state và không focus vào nội dung ẩn | Chưa runtime PASS |
| VS-ALIGN | Block UI + Editor UX | Normal/Wide/Full đúng canvas; default Wide | Chưa làm — human locked | Persist/render alignment; legacy rule còn mở | New/explicit/legacy attrs, save/reopen, nested Group, desktop scrollbar/mobile, no overflow | block.json/render + theme width baseline; geometry chờ kiểm |

Settings inventory delta: **Content** → số giá trị mỗi thuộc tính2/3/4; **Layout** → Normal/Wide/Full default Wide. Skeleton là hành vi loading tự động, chưa có yêu cầu setting bật/tắt cho editor. Chỉ chuyển status sang Đã có source/Chờ kiểm chứng/PASS khi có bằng chứng tương ứng.
