# smartbill - Project State & Design Mandates (Final Sync: April 2026)

## 🎯 Current Focus: High-Performance Internal Scanning Tool
The system is now a production-ready internal tool following the **Express Accounting** philosophy (scoped access) with a **Premium Minimal** aesthetic.

---

## 🎨 Design Philosophy & UI Standards (MEMORIZED)
1. **Aesthetic: "Premium Minimal"**
   - High information density (Density over Distance).
   - No unnecessary gray backgrounds or thick dividers.
   - Use ultra-thin borders (`border-black/[0.03]`) and whitespaces for separation.
2. **Layout: "Full-Size Control Plane"**
   - No `max-width` constraints on main admin pages (Always `w-full`).
   - Content must be wrapped in a **Master Container Card** (`bg-white`, `rounded-xl`, `shadow-sm`).
   - Horizontal balance: Use 2 or 3 Column Grids for forms to prevent stretched inputs.
3. **Corner Radius:** Strictly **`rounded-xl`** maximum across the entire system.

---

## 🔧 Core Component Status

### Folder & User Management (Express Style)
- **Scoped Access:** Users see only folders they are members/owners of.
- **Form Layouts:** Linear, Row-based structure with clear section headers and status dots.
- **Simplified Terminology:** Replaced "Provision/Initialize/Identity" with "Create/Register/User Details".

### AI & API Control (Gemini)
- **API Key Pool:** Rotation system supporting multiple keys in Admin Settings.
- **Usage Tracking:** Real-time "Calls Counter" next to each key in the pool (Stored in Cache).
- **Reactive Errors:** No proactive rate-limits. Triggers **SweetAlert2** only upon actual 429 API errors.

### Tokens & Billing (Integrated)
- **Consolidated Profile:** Balance, LINE Topup CTA, and Usage History are all nested within the User Profile settings.
- **Smart History:** Usage logs are **Grouped by Hour** to maintain a concise list.

---

## 📝 Terminology Mapping (Global)
- **Folder:** Mandatory term for logical workspaces (Merchants/Projects).
- **Profile:** AI Extraction Schema (Templates).
- **Inbox:** The main slip management registry.

---

## 🚀 Git Status
- **Remote:** `https://github.com/cuevlife/smartbill-docker.git`
- **Visibility:** Private.
- **Last Sync:** Committed and Pushed latest UI refinements.

*End of Session Report. Project is stable and strictly follows the Clean & Minimal vision.*
