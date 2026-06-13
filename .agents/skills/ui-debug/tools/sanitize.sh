#!/usr/bin/env bash
# =============================================================================
# sanitize_html_debug.sh
# Mục đích: Loại bỏ noise khỏi HTML, chỉ giữ CSS + class để debug UI
# Dùng:     ./sanitize_html_debug.sh input.html [output.html]
# =============================================================================

set -euo pipefail

# ── Màu sắc terminal ──────────────────────────────────────────────────────────
RED='\033[0;31m'; GREEN='\033[0;32m'; YELLOW='\033[1;33m'
CYAN='\033[0;36m'; BOLD='\033[1m'; RESET='\033[0m'

# ── Kiểm tra đầu vào ─────────────────────────────────────────────────────────
if [[ $# -lt 1 ]]; then
  echo -e "${RED}Dùng: $0 input.html [output.html]${RESET}"
  exit 1
fi

INPUT="$1"
if [[ ! -f "$INPUT" ]]; then
  echo -e "${RED}Lỗi: Không tìm thấy file '$INPUT'${RESET}"
  exit 1
fi

# Tên file output mặc định nếu không truyền tham số
if [[ $# -ge 2 ]]; then
  OUTPUT="$2"
else
  BASENAME="${INPUT%.*}"
  EXT="${INPUT##*.}"
  OUTPUT="${BASENAME}.debug.${EXT}"
fi

echo -e "${BOLD}${CYAN}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${RESET}"
echo -e "${BOLD} HTML Sanitizer — UI Debug Mode${RESET}"
echo -e "${CYAN}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${RESET}"
echo -e "  Input : ${YELLOW}$INPUT${RESET}"
echo -e "  Output: ${YELLOW}$OUTPUT${RESET}"
echo ""

# ── Kiểm tra Python ───────────────────────────────────────────────────────────
PYTHON=""
for cmd in python3 python; do
  if command -v "$cmd" &>/dev/null; then
    PYTHON="$cmd"; break
  fi
done

if [[ -z "$PYTHON" ]]; then
  echo -e "${YELLOW}Python không có — dùng sed/awk fallback...${RESET}"
  USE_PYTHON=false
else
  USE_PYTHON=true
fi

# =============================================================================
# XỬ LÝ CHÍNH — Python (ưu tiên, xử lý chuẩn hơn)
# =============================================================================
if $USE_PYTHON; then

$PYTHON - "$INPUT" "$OUTPUT" <<'PYEOF'
import sys, re, os

INPUT  = sys.argv[1]
OUTPUT = sys.argv[2]

with open(INPUT, encoding="utf-8", errors="replace") as f:
    html = f.read()

original_size = len(html)
steps = []

# ── 1. Xoá toàn bộ ảnh base64 trong src / url() ──────────────────────────────
before = len(html)
html = re.sub(
    r'(src|href)=["\']data:[^"\']{40,}["\']',
    r'\1="[BASE64_REMOVED]"',
    html, flags=re.IGNORECASE
)
html = re.sub(
    r'url\(["\']?data:[^)]{40,}["\']?\)',
    'url("[BASE64_REMOVED]")',
    html, flags=re.IGNORECASE
)
steps.append(("Base64 inline data", before - len(html)))

# ── 2. Xoá khối <script>...</script> ─────────────────────────────────────────
before = len(html)
html = re.sub(
    r'<script\b[^>]*>.*?</script>',
    '<!-- [SCRIPT REMOVED] -->',
    html, flags=re.IGNORECASE | re.DOTALL
)
steps.append(("Script blocks", before - len(html)))

# ── 3. Rút gọn <style> — giữ nguyên nhưng collapse whitespace thừa ────────────
before = len(html)
def compact_style(m):
    content = m.group(0)
    # Xoá comment CSS
    content = re.sub(r'/\*.*?\*/', '', content, flags=re.DOTALL)
    # Collapse nhiều khoảng trắng / newline liên tiếp thành 1 dòng mỗi rule
    content = re.sub(r'\s{3,}', ' ', content)
    return content
html = re.sub(r'<style\b[^>]*>.*?</style>', compact_style, html,
              flags=re.IGNORECASE | re.DOTALL)
steps.append(("Style compacted", before - len(html)))

# ── 4. Xoá thuộc tính src / href chứa URL dài (>120 ký tự) ──────────────────
before = len(html)
def trim_long_attr(m):
    val = m.group(1)
    if len(val) > 120 and not val.startswith('['):
        short = val[:60] + "…[TRUNCATED]"
        return f'src="{short}"'
    return m.group(0)
html = re.sub(r'src="([^"]{120,})"', trim_long_attr, html)
steps.append(("Long src/href", before - len(html)))

# ── 5. Xoá thuộc tính style="" inline dài (>80 ký tự) ────────────────────────
before = len(html)
def trim_inline_style(m):
    val = m.group(1)
    if len(val) > 80:
        return f'style="[{len(val)}chars — TRIMMED]"'
    return m.group(0)
html = re.sub(r'style="([^"]{80,})"', trim_inline_style, html)
steps.append(("Long inline styles", before - len(html)))

# ── 6. Rút gọn text content dài trong thẻ (giữ cấu trúc tag) ─────────────────
before = len(html)
def trim_text_node(m):
    tag_open = m.group(1)
    text     = m.group(2)
    tag_close= m.group(3)
    stripped = text.strip()
    if len(stripped) > 120:
        short = stripped[:80] + "…[TEXT]"
        return f'{tag_open}{short}{tag_close}'
    return m.group(0)
html = re.sub(
    r'(<(?:p|span|div|li|td|th|h[1-6]|a|button|label)[^>]*>)'
    r'([^<]{120,})'
    r'(</(?:p|span|div|li|td|th|h[1-6]|a|button|label)>)',
    trim_text_node, html, flags=re.IGNORECASE
)
steps.append(("Long text nodes", before - len(html)))

# ── 7. Xoá các thuộc tính không liên quan đến UI/CSS debug ───────────────────
REMOVE_ATTRS = [
    'aria-label', 'aria-describedby', 'aria-expanded', 'aria-controls',
    'aria-hidden', 'tabindex', 'role', 'rel', 'target', 'method',
    'enctype', 'autocomplete', 'spellcheck', 'translate',
    'data-wpel-link',  # WordPress external link
]
before = len(html)
for attr in REMOVE_ATTRS:
    html = re.sub(rf'\s+{re.escape(attr)}="[^"]*"', '', html, flags=re.IGNORECASE)
    html = re.sub(rf"\s+{re.escape(attr)}='[^']*'", '', html, flags=re.IGNORECASE)
    html = re.sub(rf'\s+{re.escape(attr)}(?=[\s>])', '', html, flags=re.IGNORECASE)
steps.append(("Non-UI attributes", before - len(html)))

# ── 8. Xoá toàn bộ comment HTML (trừ comment <!-- [...] --> ta tự thêm) ───────
before = len(html)
html = re.sub(r'<!--(?!\s*\[).*?-->', '', html, flags=re.DOTALL)
steps.append(("HTML comments", before - len(html)))

# ── 9. Collapse blank lines thừa ─────────────────────────────────────────────
html = re.sub(r'\n{3,}', '\n\n', html)

# ── 10. Thêm header debug ─────────────────────────────────────────────────────
header = f"""<!-- ============================================================
  DEBUG BUILD — sanitize_html_debug.sh
  Source : {os.path.basename(INPUT)}
  Removed: base64, scripts, long text, non-UI attrs
  Kept   : CSS (<style>), class, id, layout attrs
============================================================ -->
"""
html = header + html

# ── Ghi file ──────────────────────────────────────────────────────────────────
with open(OUTPUT, "w", encoding="utf-8") as f:
    f.write(html)

final_size = len(html)
saved = original_size - final_size
pct   = saved / original_size * 100 if original_size else 0

print(f"  {'Bước':<30} {'Tiết kiệm':>12}")
print(f"  {'─'*44}")
for name, delta in steps:
    bar = '█' * min(30, max(1, int(delta / max(original_size,1) * 200)))
    print(f"  {name:<30} {delta:>8,} bytes  {bar}")
print(f"  {'─'*44}")
print(f"  Original : {original_size:>10,} bytes")
print(f"  Output   : {final_size:>10,} bytes")
print(f"  Đã giảm  : {saved:>10,} bytes  ({pct:.1f}%)")
PYEOF

# =============================================================================
# FALLBACK — sed thuần nếu không có Python
# =============================================================================
else
  cp "$INPUT" "$OUTPUT"

  # Xoá base64 data URIs
  sed -i 's/src="data:[^"]\{40,\}"/src="[BASE64_REMOVED]"/g' "$OUTPUT"
  sed -i "s/src='data:[^']\{40,\}'/src='[BASE64_REMOVED]'/g" "$OUTPUT"

  # Xoá khối <script>
  perl -0777 -i -pe 's/<script\b[^>]*>.*?<\/script>/<!-- [SCRIPT REMOVED] -->/gis' "$OUTPUT" 2>/dev/null \
    || echo -e "${YELLOW}  Bỏ qua script removal (cần perl)${RESET}"

  # Xoá inline style dài
  sed -i 's/style="[^"]\{80,\}"/style="[INLINE_STYLE_TRIMMED]"/g' "$OUTPUT"

  echo -e "${YELLOW}  Fallback sed — kết quả có thể không hoàn toàn sạch${RESET}"
fi

# ── Kết quả ───────────────────────────────────────────────────────────────────
echo ""
echo -e "${GREEN}${BOLD}✓ Xong!${RESET}  →  ${YELLOW}$OUTPUT${RESET}"
echo ""
echo -e "${CYAN}Tip debug tiếp theo:${RESET}"
echo "  1. Mở $OUTPUT trong trình duyệt → F12 → Elements"
echo "  2. Dùng DevTools Computed tab để xem CSS thực tế:"
echo "     window.getComputedStyle(document.querySelector('.your-class'))"
echo "  3. So sánh class/id với file CSS gốc để tìm rule bị override"
echo ""