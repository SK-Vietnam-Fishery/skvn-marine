# 041 — Kế hoạch chi tiết 1.3.17: Carousel Block UI & Visual QA

Ngày: 2026-10-14. Status: **Chuẩn bị handoff; chưa giao agent implementation, chưa runtime PASS**.

Authority: [040 §10–11](040_VER_1_3_17_CAROUSEL_UI_UX_TECHNICAL_PLANNING.md), [MILESTONES](../MILESTONES.md), [039](039_ROADMAP_C_B2B_TOOLKIT_CAROUSEL_FIRST_PLANNING.md). Không bắt đầu 1.3.18 hoặc tự phê duyệt chi tiết visual còn mở.

## 1. Phạm vi

1.3.17: Product/Post visual, responsive, content states và regression tối thiểu. Đã chốt Product full title, CTA cùng hàng, frame 1:1/contain/căn giữa, ba bố trí ảnh, inset đều top/left/right và radius tự tính; mobile arrows giữ khi enabled/có thể trượt. Post duyệt riêng. Giữ rationale 040 §10.

1.3.18: Editor UX/Technical chỉ inventory Content/Style/Layout/Advanced tại 040 §11.4. Không thiết kế panel, control mới, default/reset, migration, full runtime hardening.

Dependency: mẫu visual ba bố trí không chứng minh chọn/lưu được ba bố trí trong WordPress. Trước tích hợp phải xác định đường áp dụng hợp lệ cho content mới, không đổi legacy; không tự dùng cardStyle legacy/raw class hoặc thêm schema. Nếu chỉ hoàn tất artifact, ghi integration pending; không đóng UI-03a.

## 2. Gates trước implementation từng phần

| Gate | Cần khóa | Bằng chứng chuẩn bị | Gói phụ thuộc |
|---|---|---|---|
| G1 | Typography/token/radius/padding/gap/hover/focus | So POC/source/token với artifact cùng dữ liệu | W2/W4 |
| G2 | Inset levels; floating size/offset/headroom; fallback; border/radius | Ba modes với ảnh ngang/dọc/transparent/thiếu ảnh, desktop/mobile | W3 |
| G3 | Mobile arrow placement, ít item hide/disabled, footer wrap | Heading dài, counts 0/1/ít/nhiều; loop/non-loop riêng | W4 |
| G4 | Post ratio/title/date/author/excerpt/CTA/fallback | Artifact Post riêng | W5 |
| G5 | Native query Product/Post → render cards → carousel, dữ liệu đơn giản (human 2026-10-15) | Source path + smoke bằng query newest/không filter phức tạp | Basic integration |
| G-MEDIA | Chọn/lưu ba media modes mới và giữ legacy; dependency riêng, không còn là G5 | Attr/render inventory; chưa thiết kế schema/settings/migration | Tích hợp W3 |

Chỉ gói phụ thuộc mới chờ. Không hỏi lại hướng đã chốt. Executor chuẩn bị Decision Memo 7 mục khi cần quyết scope/architecture, có artifact và nguồn tự kiểm; chưa đủ evidence thì chưa hỏi quyết. Không tự mở Editor UX để giải quyết G5.

Các số POC là baseline phải kiểm lại, chưa tự duyệt toàn bộ: card radius14px, body18px/gap13px, arrow46px, active dot26px, carousel gap20px. Map token hiện có; không tự chọn breakpoint/default/token mới.

## 3. Work packages

Một owner mỗi gói, thường 3–5 files; không song song sửa style.css/cards.php. Không giao prompt làm hết carousel với spec còn mơ hồ.

| Gói / IDs | Công việc và đầu ra | Điều kiện kết thúc |
|---|---|---|
| W0 Baseline | Load AGENTS/context/contracts; git status/diff; commit/dirty/build; đọc lại DOM/cascade/tokens/attrs/config; lập current-target-delta, fixtures, gate list | Baseline và evidence index, không reset dirty work; URL onsite thiếu ghi chờ cung cấp |
| W1 Visual spec | Artifact Product header/media/body/CTA/footer; ba modes/states; Post riêng; bỏ nhãn debug POC; ghi tokens/số đo/delta | Human review từng G1–G5; không mockup editor, không đổi defaults/schema; approval visual khác runtime PASS |
| W2 UI-01/02/02a | Header/card/body/chips/CTA/footer base; full title wrap; CTA cùng hàng; dữ liệu Woo mặc định, optional thiếu không tạo ô giả | Không clip text/CTA, mobile CTA visible, hover/focus/reduce CSS đúng; Grid/shared editor regression |
| W3 UI-03/03a | Sau G2/G-MEDIA: frame square contain; sát mép inner width/top radius; inset đều/radius clamp0; floating reserved headroom gồm hover/shadow | Mỗi mode desktop/mobile có rect/screenshot; không clip/overlap; thiếu ảnh/showImage off đúng spec; legacy/Post không đổi ngầm |
| W4 UI-04/04a/05/06 | Arrows/dots/footer wrap; mobile arrows; states G3; 5 presets; resize/orientation/normal-wide-full | Không overflow/collision; click/swipe/link smoke; không giả active controls hoặc tự refactor runtime |
| W5 UI-07/08 | Sau G4: Post typography/metadata/media/CTA/fallback; on/off date/author/excerpt, title dài/empty | Reference Post riêng được nghiệm thu; không áp Product square/full-title/floating tự động |
| W6 Acceptance | Chạy matrix theo build human cung cấp, ghi expected/actual/evidence; phân biệt defect cũ và regression; cập nhật 040 IDs/checklist | Report/known limitations/carry-over 1.3.18/human visual PASS; không tự release/transition |

Chi tiết W2: không fixed height che title để căn CTA; không bắt MOQ/cert/spec làm điều kiện baseline. Không sửa global theme tokens để chữa component.

Chi tiết W3: width100% frame không cam kết vật thể chạm mép; giữ transparent whitespace. Bottom gap riêng với inset ba cạnh. Không mở overflow toàn track để chữa floating; bảo toàn slide clipping ngang. Fallback thiếu ảnh theo spec đã duyệt, không tự đổi mode lưu. Chưa có compatibility path trong scope thì integration pending.

Chi tiết W4: non-loop đầu/cuối khác ít item và loop. Dùng state runtime thật; thiếu state ghi dependency. Gap hiện có owner spaceBetween ở collection-view.ts, không thêm CSS gap cạnh tranh Swiper. Chỉ đổi config geometry đã duyệt nếu cần, không engine/pause/loop redesign.

## 4. File boundaries

Plugin paths tương đối wp-content/plugins/skvn-marine-blocks/. Đây là candidate list; task thực phải ghi exact allowlist.

- src/collection/style.css: visual chính, shared frontend/editor/Grid regression.
- modules/collection-render/cards.php: DOM visual đã khóa; đọc security contract, giữ escape/link semantics, không query redesign.
- modules/collection-render/product-collection.php và post-collection.php: chỉ khi G5 basic integration hoặc Post/G-MEDIA xác định cần.
- src/collection-view.ts: chỉ config geometry được duyệt nếu CSS không sở hữu; không runtime refactor.
- Child theme adapter: chỉ file cụ thể W0 xác định cần, không mặc định sửa theme/global tokens.
- Docs: 040/041, docs/testing/onsite-collection-carousel-m1.md, artifact visual có tên rõ.

Ngoài scope: controls.tsx/types/constants/block.json/variations cho settings mới; query modules; migration; shared motion/state engine; dependencies; external plugins; GeneratePress parent. Dependency ngoài scope phải nêu trước, không âm thầm mở 1.3.18.

Không sửa build artifacts bằng tay. Metadata/build đọc trạng thái thực và theo workflow khi implementation; không dựa snapshot cũ. Agent giao lệnh build/lint nguyên văn theo AGENTS cho human, không tự chạy. Lượt này docs-only.

## 5. Content & States / Acceptance & Evidence

Trước onsite tạo test doc: target URL **chờ human xác nhận**, setup/fixture IDs, exact steps/expected/pass-fail/evidence. Không tự tạo production page hoặc deactivate Woo production để thử.

Fixtures: Product tên ngắn/dài/rất dài; ảnh square/landscape/portrait/transparent/whitespace/thiếu; có/không chips. Post có/không ảnh/date/author/excerpt. Counts0/1/ít hơn/bằng/nhiều hơn visible; tối thiểu6 item cho desktop preset5 có thể trượt.

| Case | Steps | PASS | Evidence |
|---|---|---|---|
| V01 UI-01/02/02a | So baseline/build cùng fixture | Theo spec, title đầy đủ/CTA cùng hàng, không mất optional content | Screenshot cùng viewport + delta |
| V02 UI-03/03a | Mỗi mode với ảnh ngang/dọc/square/transparent | Frame1:1 sai số rounding≤1 CSS px; không crop/méo; inset đều/radius≥0 | Frame rect + ảnh |
| V03 UI-03/08 | Thiếu/lỗi/tải chậm ảnh, showImage off | Fallback đã duyệt, không overlap/trống ngoài spec | Before/after + fixture |
| V04 UI-03/06 | Floating hover/focus đầu/giữa/cuối + resize | Không clip shadow/media hoặc lộ slide ngoài; headroom đúng | Video/rect/overflow notes |
| V05 UI-04/04a | Arrows/dots on/off, counts, loop/non-loop | G3 đúng/mobile arrows; controls không tạo kỳ vọng sai | State ảnh + click/swipe |
| V06 UI-05/06 | Footer labels dài, CTA on/off, 5 presets | Wrap/gap/count đúng, không che controls | Desktop/tablet/mobile |
| V07 UI-07/08 | Post metadata on/off/thiếu ảnh/empty | Theo reference riêng; Product không đổi | Product/Post ảnh |
| V08 UI-06 | 320/375/768/1024/1440px; breakpoint thực±1px; resize/orientation | scrollWidth≤clientWidth; outer component trong canvas, không collision | Geometry log + viewport/build |
| V09 regression | Grid/editor hiện có, focus/reduce/links | Không regression do diff; focus thấy; CSS motion mới respect reduce; CTA đúng đích | Video/URL/console notes |
| V10 compatibility | Legacy ratio khác square; save/reopen nếu attrs/markup đụng | Không đổi ngầm ratio/mode, không invalid-block/fatal mới | Before/after attrs + ảnh |

Không chạy tích toàn bộ tổ hợp. W2:V01/V09; W3:V02–04/V10; W4:V05/V06/V08/V09; W5:V07/V09; W6 representative matrix và edge cases phần đổi. Ít nhất1 desktop có scrollbar dọc. Performance UI: quan sát layout shift lúc ảnh tải, không thêm dependency/asset nặng; full profiling1.3.18.

Mỗi evidence ghi ID, URL, fixture/settings, commit+dirty diff/build, browser/viewport, steps/expected/actual, artifact path, status/người kiểm/ngày. Source/artifact approval không thành runtime PASS. Defect cũ Deferred1.3.18 không thành đã sửa; defect chặn UI phải nêu dependency.

## 6. Mẫu handoff sau khi sẵn sàng

```text
Context: V1 / 1.3.17 — Product/Post Carousel Block UI & Visual QA; stack M1.
Load AGENTS startup + 040 §10–11 + 041 + CSS safety/review contracts.
Goal: duy nhất W<n>, IDs <...>, approved spec/artifact <...>.
Allowed files: <3–5 exact files>; preserve dirty changes.
Forbidden: GeneratePress/external plugins; editor/schema/query/migration/engine
refactor của1.3.18; không sửa build bằng tay.
Acceptance: exit gói + V cases; test doc URL/setup/steps/expected/evidence.
Tensions/dependencies: gates đã khóa, phần pending; không tự quyết phần mở.
Handoff: diff/source status/evidence; lệnh build/lint cho human; onsite checklist;
output cần gửi lại và phần agent tiếp tục. Không tự chạy build/lint.
```

Thứ tự: W0 → W1/khóa gates → W2 → W3 → W4 → W5 → W6. Gói độc lập có thể tiếp tục khi gate khác mở, tránh shared-file conflict. Chưa dispatch implementation.

## 7. Điều kiện đóng milestone

UI IDs có kết quả riêng, Product/Post human approve; ba media modes có artifact/integration status rõ, chưa tích hợp không đóng acceptance. Cases PASS hoặc human chủ động deferred có target; không tự xóa checklist. Human cung cấp build/lint/onsite evidence; agent kiểm và sửa scope liên quan. Carry-over1.3.18 đầy đủ, không tuyên bố full runtime PASS. Human quyết đóng/chuyển milestone theo Transition Rule.

## W0/W1 — đặc tả đã chuẩn bị 2026-10-15

[042 — Carousel visual specification](042_VER_1_3_17_CAROUSEL_VISUAL_SPEC.md) ghi source/artifact baseline, exact measurements/tokens, human deltas, G1–G5 gaps, content states và acceptance/evidence. POC-02 có Post markup mẫu (reference cũ chưa approved); POC-03 không có Post demo và chỉ dùng Product placeholders. Source integration của ba media modes vẫn pending; không thiết kế schema/settings/migration. W0 source inventory và W1 written spec đã chuẩn bị, chưa đóng gates hoặc chứng minh runtime PASS; artifact states còn thiếu ghi rõ trong 042.
## Dispatch 2026-10-15 — human cho triển khai phần đủ chốt

G5 được human thu hẹp về query native trả kết quả → render cards → carousel hoạt động với dữ liệu đơn giản; không query/filter/data-model redesign. Native pipeline đã có source, chờ smoke evidence. Chọn/lưu ba image layouts và legacy chuyển thành **G-MEDIA**, không mất acceptance và không chặn phần độc lập.

Đợt source đầu đã sửa CSS: Product Carousel full-title wrap kể cả mã không có dấu cách; body min-width0; reduced-motion tắt hover lift/image zoom/CTA active translation và transition card/image/arrows/dots trên Carousel. CTA margin-top:auto/equal-height đã có source, giữ nguyên và đưa vào kiểm regression. Không thay font/token/numeric visual pending, media ratio/fit cũ, Post policy hoặc schema/editor.

Files runtime thực sửa: src/collection/style.css. Test: docs/testing/onsite-collection-carousel-m1.md. Source đã có, chưa build/lint/onsite PASS. Human chạy lệnh build/lint trong test doc rồi gửi G5-P/G5-S/UI-T1/UI-T2/UI-R1/UI-R2/REG evidence. Không coi đợt nhỏ này là toàn bộ W2–W6 hoàn tất.