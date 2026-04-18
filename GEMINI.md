# smartbill AI Foundational Mandates

## 🌟 Product Vision: "Concise Automation"
smartbill is an AI-powered slip-scanning SaaS designed for internal organizational efficiency. It is NOT an accounting system, but a high-performance data bridge between physical receipts and digital spreadsheets.

### Core Philosophy:
- **Clean & Minimal:** High focus on whitespace, thin borders, and essential data only.
- **Concise (กระชับ):** Zero-friction workflows. Remove unnecessary management overhead.
- **Express-Style Management:** Modeled after "Express Accounting" where data is scoped to specific "Folders" (Entities/Companies) with strict access control.

---

## 🎨 Architectural Standards & UI/UX

### 1. Control Plane (Admin)
- **Design Pattern:** "Admin Grid" (Registry View) for lists and "Form Card Based" for edits.
- **Container:** Always use a **Master Container Card** (`rounded-xl`, shadow-sm) inside a **Full-size Layout** (`w-full`).
- **Constraints:** Maximum corner radius is strictly **`rounded-xl`**. No `max-width` constraints on main admin pages.

### 2. Workspace App (User)
- **Folder-Centric:** Users login and land on the **Folder Hub** (Profile Chooser style).
- **Access Scoping:** Users only see folders they have been granted access to (Owner/Staff).
- **Simplified Workflow:** Scan -> Review -> Export.

### 3. AI & Token System
- **Key Rotation Pool:** Admin manages a pool of Gemini API Keys with real-time usage tracking.
- **Reactive Error Handling:** No proactive local rate-limiting. The system alerts users via SweetAlert2 only if the API returns a 429 status.
- **Manual Topups:** All token refills are handled manually by Admin after user contact via LINE.

---

## 🛠️ Technical Stack
- **Framework:** Laravel 11.
- **Frontend:** Vanilla JS + Alpine.js (Primary) + Tailwind CSS (Play CDN). No Livewire.
- **Database:** MySQL.
- **Terminology:** **"Folder"** is the mandatory term for logical workspaces. Avoid "Project" or "Store" in UI/Logic.

---

## 🚀 Recent Accomplishments (April 2026)
1. **User & Folder Consolidation:** ยุบรวมเมนู Folder Management เข้าไปอยู่ภายใต้ User Management (Single Point of Control) เพื่อความกระชับตามหลัก "Concise Automation"
2. **Dynamic Quota Check:** เพิ่มระบบตรวจสอบโควต้าการสร้างโฟลเดอร์ (`max_folders`) แบบ Real-time ในหน้า User Detail
3. **Enhanced Table Standard:** ปรับปรุงระบบ Pagination และเลย์เอาต์ตารางให้เป็นมาตรฐานเดียวกันทั้งระบบ (เหมือนหน้า Slips) ทั้งในส่วน Admin และ Workspace
4. **Lifecycle Control:** ปรับปรุงส่วน Danger Zone ให้เน้นอำนาจการสั่งการที่เด็ดขาด (Instant Terminate) สำหรับระบบจัดการภายในองค์กร
5. **Comprehensive Refactor:** "Project" semantics fully migrated to "Folder" across routes, controllers, and DB-level session keys.

*This document serves as the ground truth for smartbill's architectural and design direction.*
*Updated: 2026-04-18*
