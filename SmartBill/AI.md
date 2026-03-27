# AI.md — SmartBill Codebase Context

> สำหรับ AI ตัวใดก็ตามที่เข้ามาทำงานกับโปรเจกต์นี้ — อ่านไฟล์นี้ก่อนเสมอ

## 🏗 Tech Stack ปัจจุบัน

| Layer | เทคโนโลยี | หมายเหตุ |
|---|---|---|
| **Backend** | Laravel (PHP 8.4) | Controller เดียว `AdminController.php` |
| **Frontend Template** | Blade (`.blade.php`) | ใช้ร่วมกับ Alpine.js |
| **Reactivity** | Alpine.js (CDN) | `x-data`, `x-show`, `x-model`, `@click` |
| **Styling** | Tailwind CSS (CDN Play) | ไม่มี build step, config อยู่ใน `<script>` |
| **Icons** | Lucide (CDN) | ใช้ `data-lucide` attributes |
| **Notifications** | SweetAlert2 (CDN) | Toast pattern |
| **Date Picker** | Flatpickr (CDN) | Thai locale |
| **Database** | MySQL 8.0 | Docker |
| **Deploy** | Docker (PHP CLI port 8000) | ไม่ใช้ Apache |

> **ไม่มี Node.js, ไม่มี npm, ไม่มี build step** — ทุกอย่างเป็น CDN

---

## 📁 โครงสร้างไฟล์ (หลัง cleanup)

```
resources/views/
├── layouts/
│   ├── app.blade.php                    ← Layout หลัก (sidebar + header + mobile nav)
│   ├── guest.blade.php                  ← Layout สำหรับ auth pages (login, register)
│   └── parts/
│       ├── sidebar-desktop.blade.php    ← Sidebar icon rail (PC)
│       ├── header-desktop.blade.php     ← Top bar + profile + language dropdown
│       └── navigation-mobile.blade.php  ← Bottom nav bar (Mobile)
├── components/                          ← Blade components (เฉพาะที่ใช้จริง)
│   ├── application-logo.blade.php       ← Logo SVG
│   ├── auth-session-status.blade.php    ← ใช้ใน auth
│   ├── danger-button.blade.php          ← ใช้ใน profile/delete
│   ├── input-error.blade.php            ← ใช้ใน auth + profile
│   ├── input-label.blade.php            ← ใช้ใน auth + profile
│   ├── modal.blade.php                  ← ใช้ใน profile/delete
│   ├── primary-button.blade.php         ← ใช้ใน profile
│   ├── secondary-button.blade.php       ← ใช้ใน profile/delete
│   └── text-input.blade.php            ← ใช้ใน auth + profile
├── admin/                               ← ★ หน้า Admin ทั้งหมด
│   ├── dashboard.blade.php              ← Dashboard (stats + actions)
│   ├── slip.blade.php                   ← Slip Registry (list + filter + scan)
│   ├── slip-edit.blade.php              ← Slip Editor (UI/JSON mode)
│   ├── template-edit.blade.php          ← Profile Editor (headers + AI suggest)
│   ├── templates.blade.php              ← Extraction Profiles list
│   ├── stores.blade.php                 ← Brand Registry (CRUD)
│   └── users.blade.php                  ← User list (admin only)
├── auth/                                ← Laravel Breeze (ไม่แตะ)
├── profile/                             ← Laravel Breeze (ไม่แตะ)
├── dashboard.blade.php                  ← Redirect stub
└── welcome.blade.php                    ← Landing page (ไม่แตะ)

app/Http/Controllers/
├── AdminController.php                  ← Controller เดียวสำหรับทุกหน้า admin
└── Auth/                                ← Breeze auth controllers
```

---

## 🔀 Routes สำคัญ

| Method | URL | Controller Method | หน้าที่ |
|---|---|---|---|
| GET | `/dashboard` | `dashboard()` | Dashboard |
| GET | `/admin/slip` | `slipReader()` | Slip list |
| GET | `/admin/slip/edit/{slip}` | `editSlip()` | Slip editor |
| POST | `/admin/slip/process` | `processSlip()` | AI scan slip |
| POST | `/admin/slip/update/{slip}` | `updateSlip()` | Save slip data |
| DELETE | `/admin/slip/delete/{slip}` | `deleteSlip()` | Delete slip |
| GET | `/admin/slip/export` | `exportExcel()` | Export Excel |
| GET | `/admin/stores` | `stores()` | Stores list |
| POST | `/admin/stores` | `storeStore()` | Create store |
| PATCH | `/admin/stores/{merchant}` | `updateStore()` | Update store |
| DELETE | `/admin/stores/{merchant}` | `deleteStore()` | Delete store |
| GET | `/admin/profiles` | `merchants()` | Templates list |
| GET | `/admin/profiles/{merchant}/edit` | `editMerchant()` | Template editor |
| POST | `/admin/profiles/suggest` | `suggestPrompt()` | AI auto-headers |
| POST | `/admin/profiles/store` | `storeMerchant()` | Create template |
| PATCH | `/admin/profiles/update/{merchant}` | `updateMerchantMapping()` | Update template |
| DELETE | `/admin/profiles/delete/{merchant}` | `deleteMerchant()` | Delete template |
| GET | `/admin/users` | `users()` | Users list |

---

## 🎨 Design Rules (ดู GEMINI.md ด้วย)

- **Color**: Rose Red (`#f43f5e`) + Emerald Green (`#10b981`) บน Slate background
- **Cards**: ใช้ class `.premium-card` (white, rounded-[2.5rem], hover shadow)
- **Font**: Plus Jakarta Sans + Noto Sans Thai, **ห้ามใช้ italic**
- **Headings**: `font-black uppercase tracking-tight`
- **Modals**: ใช้ `x-teleport="body"` + backdrop blur 12px
- **ห้าม**: ใช้คำว่า "AI" ใน UI — ใช้ "Intelligence", "Profiles", "Automation" แทน

---

## 🧹 Cleanup ที่ทำไปแล้ว (26 Mar 2026)

**ลบไฟล์ที่ไม่ได้ใช้ 10 ไฟล์:**
- `layouts/navigation.blade.php` — ไม่มี include (แทนที่ด้วย sidebar + header)
- `layouts/sidebar.blade.php` — ซ้ำกับ `parts/sidebar-desktop.blade.php`
- `components/sidebar-link.blade.php` — ไม่มีที่ใช้
- `components/nav-link.blade.php` — ใช้แค่ใน navigation ที่ลบแล้ว
- `components/dropdown.blade.php` — ใช้แค่ใน navigation ที่ลบแล้ว
- `components/dropdown-link.blade.php` — ใช้แค่ใน navigation ที่ลบแล้ว
- `components/responsive-nav-link.blade.php` — ใช้แค่ใน navigation ที่ลบแล้ว
- `views/spa.php` — ไฟล์ทดลอง migration (ยกเลิก)
- `public/js/admin/dashboard.js` — ไฟล์ทดลอง migration (ยกเลิก)
- `public/js/admin/utils.js` — ไฟล์ทดลอง migration (ยกเลิก)

---

## 📋 สิ่งที่ทำได้ต่อไป (ยังไม่ได้ทำ)

1. **ย้าย inline JS ออกจาก Blade** → สร้าง `public/js/admin/*.js` แยก (ลดขนาด Blade 30-40%)
2. **ปรับ GEMINI.md** ถ้ามี design rules ใหม่
3. **เพิ่ม features** ตาม business requirements

---

## ⚠️ สิ่งที่ AI ต้องระวัง

1. **อย่าสร้าง component files เพิ่ม** — เจ้าของโปรเจกต์ไม่ต้องการไฟล์เยอะ
2. **Component ใน `components/`** ที่เหลือทั้งหมดถูกใช้โดย auth/profile pages (Breeze) — **ห้ามลบ**
3. **Alpine.js directives** (`x-data`, `x-show`, `x-model`, `@click`) เป็น reactive framework หลัก
4. **ไม่มี build step** — ห้าม `npm install`, ห้ามสร้าง `package.json` dependencies ใหม่
5. **ทุกอย่างเป็น CDN** — เพิ่ม library ใหม่ต้องเป็น CDN เท่านั้น
