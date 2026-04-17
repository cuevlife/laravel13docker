# smartbill - Project State & Directives

## 🎯 Current Focus: Secure Organizational Scanning
The system has been transformed into a professional internal tool following the **Express Accounting** philosophy.

### Key Logic:
1. **Scoped Access:** Users must be explicitly linked to a **Folder** to see or interact with its data.
2. **Accountability:** All actions (scanning, tokens, exports) are linked to individual accounts, providing clear tracking of who performed which action and from where.
3. **Master Card UI:** All Admin/Edit pages use the **Master Container Card** pattern for a unified, clean, and minimal feel.

---

## 📐 Design Guidelines (Mandatory)
- **Full-Size:** Use `w-full px-4 py-8 sm:px-6 lg:px-8` for all main containers.
- **Master Card:** Wrap content in a single white card with `rounded-xl` and `shadow-sm`.
- **Aesthetic:** Clean & Minimal. Use `#f8fafb` for subtle backgrounds and thin borders (`border-black/[0.03]`).
- **Corner Radius:** Strictly **`rounded-xl`** maximum.

---

## 🔧 Component Status

### Folder Management
- **Hub:** Profile-chooser style. Shows slips volume.
- **Access Control:** Managed via `IdentifyTenant` middleware. Any authenticated member can access their linked folder.

### AI Extraction (Gemini)
- **API Key Pool:** Rotates between multiple keys in `Admin Settings`.
- **Usage Tracking:** Tracks call counts per key in real-time.
- **Reactive Limits:** SweetAlert2 popup triggers on actual API 429 errors.

### Tokens & Billing
- **Integrated Settings:** Token balance, usage history, and LINE topup CTA are all consolidated in the **Profile Settings** page.
- **History Grouping:** Usage logs are grouped by **Hour** to keep the list concise.

---

## 📝 Terminology Mapping
- **Folder:** The logical unit for a company/workspace (formerly Project/Merchant).
- **Profile:** AI Extraction Schema (formerly Template).
- **Inbox:** The main slip management area.

*Record of truth for smartbill architecture.*
*Last Sync: 2026-04-17*
