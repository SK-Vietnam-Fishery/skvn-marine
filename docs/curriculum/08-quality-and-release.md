# Module 08 — Chất lượng & release: security, a11y, versioning, đóng gói

## Mục tiêu

- Có quy trình tự kiểm (thay cho AI self-check) trước mỗi lần ship.
- Đóng gói zip cài được trên site khác, versioning nhất quán.

## Khái niệm

**Self-check trước khi ship** — dựng checklist của riêng bạn từ `AGENTS.md` §8, tối
thiểu gồm:

```
[ ] php -l pass toàn bộ file PHP; npm run build pass
[ ] Mọi echo qua esc_*; mọi input qua sanitize; option đọc ra có default
[ ] Bài viết cũ mở không invalid block
[ ] Animation có prefers-reduced-motion guard; editor không opacity:0
[ ] Keyboard đi hết interactive elements; button có label
[ ] Heap không tăng sau thao tác editor lặp (module 05)
[ ] Console sạch trên editor + frontend
[ ] Tắt plugin phụ thuộc (Woo...) → không fatal (function_exists/class_exists guard)
```

**Versioning đồng bộ** — bài học dự án (đã có version drift trong ledger): version phải
khớp ở mọi chỗ: plugin header, `package.json`, `block.json` các block, và (nếu có)
docs. Dự án dùng script `tools/bump-project-version.mjs` để bump một phát ăn hết —
đọc script này và viết bản đơn giản cho capstone. Quy tắc SemVer của dự án
(MILESTONES.md §Version Naming Rule): MAJOR đổi kiến trúc, MINOR thêm feature,
PATCH fix — và **không tick "DONE" khi chưa có evidence chạy thật** (bài học 1.3.8).

**Đóng gói zip** — hố kinh điển: zip thiếu file PHP runtime mà entry `require` →
fatal khi activate trên site sạch. Dự án có quy trình audit riêng
(`docs/workflows/deploy-artifact-workflow.md` + `tools/package-plugin-zip.sh`):
mỗi khi thêm/đổi `require`, kiểm tra zip chứa đúng file. Nguyên tắc chung:

```
zip chứa: plugin.php, build/, modules/, assets tĩnh
zip KHÔNG chứa: src/, node_modules/, tests/, docs/, file .local
test bắt buộc: cài zip lên site sạch (không phải site dev) → activate → smoke test
```

**Asset versioning**: `filemtime()` cho style/script tự enqueue — user không phải
Ctrl+F5 sau mỗi update.

**Test thủ công có kỷ luật** — thay cho "AI viết test": mỗi feature một file test
`.md` theo format dự án dùng trong `docs/testing/`: mục tiêu, preconditions, steps
đánh số, expected, pass/fail, evidence. Viết trước khi code xong — nó chính là spec
kiểm thử của bạn.

## Đọc code thật

1. `tools/bump-project-version.mjs` + `tools/package-plugin-zip.sh` — đọc hiểu từng bước.
2. `docs/workflows/versioning-release-workflow.md`, `docs/workflows/deploy-artifact-workflow.md`.
3. `docs/testing/onsite-dynamic-collections-1.3.3.md` — một file test mẫu tốt: copy
   format này cho capstone.
4. `docs/standards/security-guidelines.md` — đọc hết một lần, sau đó dùng làm tài liệu tra.

## Bài tập (capstone bước 8 — tốt nghiệp)

1. Viết `docs/testing/` cho capstone: 1 file test cho slider, 1 cho settings.
2. Viết script bump version (node hoặc bash) đồng bộ header + package.json + block.json.
3. Đóng zip theo nguyên tắc trên; cài lên một site local **khác** (site sạch);
   activate; chạy 2 file test; ghi evidence (screenshot).
4. Chạy toàn bộ self-check; mục nào fail → fix → chạy lại từ đầu checklist.
5. Nghi thức tốt nghiệp: xóa plugin khỏi site sạch → cài lại từ zip → mọi thứ chạy.

## Checkpoint

- [ ] Zip cài trên site sạch không fatal, không thiếu file
- [ ] Version khớp ở mọi vị trí sau một lần chạy script
- [ ] Có 2 file test .md với evidence thật
