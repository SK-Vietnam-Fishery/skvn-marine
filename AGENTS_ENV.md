# AGENTS_ENV.md — SKVN Marine
# Environment, Runtime, WSL, WP-CLI
# Đọc khi task liên quan đến chạy server local, WP-CLI, build plugin, hoặc deploy.

---

## 11. Dev Repo vs WordPress Runtime — Không được nhầm

Repo này (`D:\Github\skvn-marine`) là **source repo để dev**, KHÔNG phải WordPress runtime root.

WordPress runtime root nghĩa là **thư mục cài WordPress đầy đủ để chạy/test website**, ví dụ thư mục có `wp-admin/`, `wp-includes/`, `wp-content/`, `wp-config.php`. Với người không quen technical wording, có thể hiểu đơn giản là: **thư mục chứa toàn bộ bộ cài WordPress của site local/test**.

Nếu có file `.local/ENVIRONMENT.md`, agent phải đọc file đó để biết path môi trường local của máy hiện tại.

Nếu chưa có `.local/ENVIRONMENT.md` và task cần thao tác với WordPress runtime (cài/activate plugin, test theme/plugin, chạy WP-CLI trên site), agent PHẢI hỏi human cung cấp WordPress runtime root trước. Khi hỏi, nói rõ đây là **đường dẫn tới thư mục cài WordPress đầy đủ của site local/test**, không phải source repo theme/plugin. Sau khi human cung cấp path, agent PHẢI tạo `.local/ENVIRONMENT.md` và ghi lại:

```text
WP_RUNTIME_ROOT_WINDOWS=...
WP_RUNTIME_ROOT_WSL=...
```

File `.local/ENVIRONMENT.md` là machine-local, đã được git ignore, KHÔNG commit.

### Local dev runtime — máy hiện tại

Thông tin chi tiết của máy hiện tại nằm trong `.local/ENVIRONMENT.md`. File này local-only, có thể chứa credential, KHÔNG commit.

Agent phải đọc `.local/ENVIRONMENT.md` trước khi chạy WP-CLI, bật server, hoặc chạy build local.

Local hiện tại:

```text
Source repo Windows: D:\Github\skvn-marine
WP runtime Windows:  D:\Github\minhhaifish
WP runtime WSL:      /mnt/d/Github/minhhaifish
WSL distro:          Debian
WSL user:            shinkuro
WP URL:              http://localhost:8080
WP admin:            http://localhost:8080/wp-admin/
```

Toolchain đã cài trong WSL Debian:

```text
PHP:      /usr/bin/php — 8.4.21
WP-CLI:   /usr/local/bin/wp — 2.12.0
MariaDB:  /usr/bin/mariadb — 11.8.6
nvm:      /home/shinkuro/.nvm
Node.js:  20.20.2
npm:      10.8.2
```

Không cài Node/toolchain vào source repo. Không tạo `.local/toolchains/` trong repo.

Bật local WP dev server:

```bash
wsl -d Debian -- bash -lc "cd /mnt/d/Github/minhhaifish && setsid -f wp server --host=0.0.0.0 --port=8080 --allow-root"
```

Kiểm tra server:

```bash
curl -I http://localhost:8080/wp-login.php
```

Chạy plugin build bằng Node trong WSL:

```bash
wsl -d Debian -- bash -lc "source /home/shinkuro/.nvm/nvm.sh && nvm use 20 && cd /mnt/d/Github/skvn-marine/wp-content/plugins/skvn-marine-blocks && npm run build"
```

### Source repo chỉ chứa

- Theme custom: `wp-content/themes/skvn-marine/`
- Plugin custom: `wp-content/plugins/skvn-marine-blocks/`
- Context/docs/dev files: `.context/`, `docs/`, `AGENTS.md`, `README.md`

### Source repo KHÔNG được chứa

Không cài, copy, vendor, commit external WordPress plugins vào repo này:

- `wp-content/plugins/woocommerce/`
- `wp-content/plugins/contact-form-7/`
- `wp-content/plugins/contact-form-cfdb7/`
- `wp-content/plugins/seo-by-rank-math/`
- `wp-content/plugins/rank-math/`
- `wp-content/plugins/antispam-bee/`
- `wp-content/plugins/windpress/`
- `wp-content/plugins/polylang/`
- `wp-content/plugins/akismet/`
- `wp-content/plugins/hello.php`

External plugins thuộc **WordPress runtime site**, không thuộc source repo dev.

### Trước khi cài plugin bằng WP-CLI

Bắt buộc xác định rõ WordPress root thật. WordPress root phải có:

```text
wp-config.php
wp-admin/
wp-includes/
wp-content/
```

Local hiện tại phải được khai báo trong `.local/ENVIRONMENT.md`, ví dụ:

```text
WP_RUNTIME_ROOT_WINDOWS=D:\Github\minhhaifish
WP_RUNTIME_ROOT_WSL=/mnt/d/Github/minhhaifish
```

Nếu task yêu cầu "cài plugin", "activate plugin", "setup môi trường WordPress", agent PHẢI hỏi hoặc xác nhận path runtime root trước.

Không được chạy:

```bash
wp plugin install ...
```

trong source repo này nếu repo không có `wp-config.php`, `wp-admin/`, `wp-includes/`.

### Cách đúng để test theme/plugin

1. Human tạo hoặc cung cấp một WordPress runtime root riêng.
2. Agent cài external plugins vào runtime root bằng WP-CLI:

```bash
wp plugin install woocommerce contact-form-7 contact-form-cfdb7 seo-by-rank-math antispam-bee windpress --activate --path=/path/to/wp-root
```

3. Agent deploy custom code từ source repo sang runtime root bằng copy hoặc symlink:

```text
source repo/wp-content/themes/skvn-marine
-> runtime root/wp-content/themes/skvn-marine

source repo/wp-content/plugins/skvn-marine-blocks
-> runtime root/wp-content/plugins/skvn-marine-blocks
```

4. Agent activate custom theme/plugin trong runtime root:

```bash
wp theme activate skvn-marine --path=/path/to/wp-root
wp plugin activate skvn-marine-blocks --path=/path/to/wp-root
```

### Nếu lỡ copy external plugins vào source repo

DỪNG ngay và báo human.

Chỉ được xóa các thư mục external plugin nếu chắc chắn chúng là untracked/generated và không phải code custom của project. Sau khi xóa, kiểm tra:

```bash
git status --short wp-content/plugins
```

Kết quả mong muốn: chỉ còn `skvn-marine-blocks/` là plugin custom của repo.

### Tension trigger

Nếu không chắc một thư mục/plugin thuộc source repo hay runtime site → ghi LOW tension và hỏi lại trước khi cài/copy/xóa.
