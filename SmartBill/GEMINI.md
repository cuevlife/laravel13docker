# SmartBill Workspace - Current Product Context

This file is the main AI context for the current product direction.
Do not fall back to the old subdomain-first architecture unless explicitly requested.

## Product Model
- The main UX is no longer subdomain based.
- Users log in normally and choose a Project from the Project Hub.
- After project selection, the system stores `active_project_id` in the user session.
- Workspace pages use shared routes such as `/workspace/slips`.
- Data isolation still happens per project by resolving the active project from session.

## Access Model
- Super Admin uses a dedicated login at `/admin/login` and dedicated `/admin/...` pages.
- Regular users log in at `/login`.
- Control Plane is for super admin only.
- Workspace App is for normal project work after project selection.

## Approved UX Reference
- The current `Choose Project / Project Hub` page is the approved visual reference.
- Approved direction:
  - the latest navbar / top bar style
  - soft white background with green and red tint
  - the current project card layout and spacing
- Do not redesign the body or card layout of `Choose Project` again unless it is truly necessary.

## Slip UI Direction
- The Slip page should move closer to the approved Project Hub mood.
- Focus on surface design only:
  - soft white base
  - gentle green and red accents
  - top section should feel like the same product family
- Do not make the workflow more complex.
- The main user journey is:
  - login
  - choose project
  - slip
  - scan / review / export

## Information Architecture
- Project = company, client, or work context.
- Slip = main operational page.
- Templates should not become a top-level focus again.

## Guardrails
- Do not reintroduce subdomain UX as the main path.
- Do not force users to switch context through too many layers.
- Prefer the simplest possible interaction model.
- When updating other pages, use the latest Project Hub as the design reference first.

## Slip Workflow Extensions
- Slip Registry now supports Collections, Workflow Status, Archive, Labels, and Bulk Actions.
- Labels are stored on each slip and can be added during scan or via bulk action.
- Bulk actions cover reviewed, approved, exported, archive, restore, add label, and remove label.
- Filtering supports collection, workflow status, archive scope, and label.

- Archived slips are separated into their own workspace page and sidebar entry, instead of being mixed into the main registry view.
- Slip date filters now use a Buddhist Era date picker for display while still submitting standard dates to Laravel.

- UI-facing wording should use Collection / Collections for grouped uploads. Keep the underlying slip_batches storage model for stability unless a full refactor is explicitly requested.

- Slip should avoid duplicate navigation patterns. Use sidebar/bottom navigation for page switching, and keep the page itself focused on search, filters, and CRUD actions.
- Workspace filters should expose only primary filters by default so the table stays dominant.
- Keep search, collection, and workflow status visible first, and place date, profile, labels, sort, and archive scope behind a More Filters toggle.
- The target density is one visible filter row on larger screens, or at most two rows when the layout wraps.

## Modern Workspace Direction
- The workspace should feel like an operations app, not a classic admin CRUD screen.
- Navigation should switch between pages such as Slip and Archived.
- The Slip page itself should focus on search, filters, bulk actions, and the working list.
- Secondary management should move into drawers, panels, or modals instead of stacking more full-page blocks.
- Slip detail should open in a side drawer first, with the full editor as a deeper action when needed.

- Export should live on its own workspace page instead of adding more action blocks to the main Slip screen.
- Collection management should happen through a modal or drawer from Slip, not through another stacked full-page section.

## UI Density Direction
- Workspace pages should now prefer a simple, clean CRUD database table style instead of large card-heavy layouts.
- Keep the approved navbar, sidebar, and bottom bar visual language.
- Reduce visual weight by shrinking headers, cards, buttons, paddings, and modal surfaces.
- Slip and Exports should feel denser, calmer, and more operational, especially on mobile and tablet.

## Current Workspace Snapshot
- The current direction is close to the desired result, but the remaining friction is visual clutter inside the Slip workspace body.
- The user now likes the navbar, sidebar, and bottom bar direction overall.
- The main priority is keeping the workspace body clean and focused on the data table, not adding more visible controls.
- Filters have already been simplified so primary filters remain visible first and secondary filters stay behind a More Filters toggle.

## Navbar and Action Direction
- Page titles should move into the workspace navbar at the top-left instead of taking a separate large header block inside the page body.
- On Slip pages, the primary action should be Scan Receipt in the navbar.
- Secondary actions that used to sit under a More menu should move into the profile dropdown or account drawer when possible.
- The page body should mainly contain collection context when needed, filters, bulk actions, and the main table.
- Avoid oversized intro sections above the table unless they are truly necessary.

## Next Implementation Plan (Completed)
1. ✅ Moved workspace page title into desktop and mobile navbar — Slip, Archived, Exports, Templates, etc. all show dynamic titles.
2. ✅ Moved Scan Receipt primary action into navbar (desktop: labeled button, mobile: icon-only green pill) — triggers existing scan modal via CustomEvent bridge.
3. ✅ Moved Export Center and Scan Settings into profile dropdown (desktop) and Account drawer (mobile).
4. ✅ Removed top hero blocks from Slip and Exports — page starts directly with collection context (if any), inline actions, and filters.
5. ✅ Kept simplified filter model unchanged — primary filters visible first, advanced behind toggle.
6. ✅ Collection-specific actions (New Collection, Manage Collection) live as compact inline buttons at the top of the data card, not in the hero or nav.

## Current State After Migration
- Desktop header shows: dynamic page title (left) → Scan Receipt button (slip pages) → tokens → language → profile dropdown with workspace actions
- Mobile header shows: SB logo + title (left) → Scan Receipt icon (slip pages) → tokens → theme → language
- Mobile account drawer now includes Workspace section with Export Center and Scan Settings links
- Slip page body: collection context (if active) → inline action bar (New Collection, Manage Collection) → filters → bulk actions → table
- Exports page body: inline action bar (Back to Slip, Download Workbook) → filters → stats → table

## Next Direction
- Review mobile and tablet density after the navbar migration to ensure the table remains the visual focus.
- Consider whether the collection inline action bar should be even more compact or merged into the filter row.
- Evaluate if the desktop header needs better visual separation between the page title area and the action/profile area.

## 🛡️ Core Infrastructure Mandates
- **Database**: MUST use **MySQL** (Docker container). **NEVER** use or switch back to SQLite.
- **Vite**: MUST be configured for LAN access via Host IP for mobile testing.
- **Unique ID**: All slips must have a generated `uid` (format: `SB-YYMM-XXXXX`).

## 🎨 UI Standards (The SmartBill Aesthetic)
- **Crystal Clear Backdrop**: All modals and drawers MUST use `fixed inset-0` with `backdrop-blur-xl` and a very subtle tint (`bg-white/5` or `bg-black/5`). Avoid solid dark overlays.
- **Calm Operations Mood**: Use a soft white background (`#fafafa`) with Discord-inspired green/red accents for primary actions.
- **Dense Data Tables**: Prefer high data density in tables with small fonts (`text-[10px]` for headers, `text-sm` for content) and minimal padding.
- **Full-Width Canvas**: All operational and CRUD pages MUST utilize the full width of the screen. Avoid `max-w-7xl` or similar constraints on the main content container to maximize screen real estate on large displays.
- **Minimalist Corner Radius**: Use `rounded-xl` (12px) as the standard for all main container cards and forms. Avoid oversized rounding (like `rounded-[2rem]`) unless explicitly requested for small decorative elements.

## 🚀 NEW ARCHITECTURE MANDATES (Vanilla + Alpine)
- **Data Handling**: Prefer fetching JSON objects over HTML partials. Use Alpine.js `x-for` for rendering lists (e.g., Folder Hub, Slip Registry).
- **Client-Side Search**: For small-to-medium datasets (like Folder Hub), perform filtering on the client-side using the pre-fetched JSON array for instant results without network lag.
- **Optimistic UI**: Implement instant feedback for user actions (Delete, Status Update). Update the UI state immediately before the server request completes, with automatic rollback logic on failure.
- **Server-Side Pagination**: Use for large datasets (Slip Registry). Fetch small chunks (JSON) and update Alpine state to maintain speed and low memory footprint.

## 🚀 ROADMAP & TODO LIST (SaaS + Vanilla/Alpine)
- [x] **Folder Hub**: Converted to Alpine.js JSON-based rendering with client-side instant search.
- [x] **Slip Registry**: Converted to Alpine.js JSON-based rendering with Server-Side Pagination.
- [x] **Optimistic UI**: Implemented for slip deletion.
- [ ] **Data Sync (Polling)**: Add background polling for Slip Registry (Page 1 only).
- [ ] **ScanModal**: Migrate to Vanilla/Alpine architecture.


- [ ] **CollectionManager**: Compact inline actions for managing batches/collections
- [ ] **NotificationSystem**: Real-time toast notifications for background tasks (OCR, Export)

### 📊 SaaS & Billing (Central Control)
- [ ] **Subscription Guard**: Enforce project/scan limits based on user plan
- [ ] **Token System**: Real-time token balance update after each successful scan
- [ ] **Topup Review**: Admin interface for approving manual bank transfer top-ups
- [ ] **Usage Stats**: Dashboard widgets showing scan activity and cost savings

### 🎨 UI/UX Refinement (The "Calm Operations" Mood)
- [x] **Branding Update**: Unified Favicons and Dynamic Tab Titles across all layouts
- [ ] **Soft White Overhaul**: Apply consistent #fafafa background across all workspace pages
- [ ] **Drawer UI**: Implement side-drawers for Slip details instead of full-page navigation
- [ ] **Dense Tables**: Shrink padding and font sizes in data tables for higher data density
- [ ] **Advanced Filter Toggle**: Hide non-primary filters behind a "More" button to keep UI clean
- [ ] **Buddhist Era Datepicker**: Ensure consistency across all date inputs

### 📂 Slip & Export Workflow
- [ ] **Bulk Actions**: Add 'Approve All', 'Archive All', and 'Add Label' to the main registry
- [ ] **Export Center**: Dedicated page to manage and download generated workbooks/sheets
- [ ] **Label Management**: Simple interface to create/edit labels per project
- [ ] **Archived Registry**: Separate view for old/processed slips with restore functionality

### 📱 Mobile Excellence
- [ ] **Bottom Nav Optimization**: Ensure easy reach for main actions (Scan, Hub, Slips)
- [ ] **Mobile Action Bar**: Floating Action Button (FAB) or sticky pill for primary actions
- [ ] **Card to List**: Auto-switch from full table to card-list on small screens

## 🚨 Current Critical Bug (The "$0" Issue)
- **Problem**: `Livewire\Exceptions\PropertyNotFoundException: Property [$0] not found on component: [slip-registry]`
- **Context**: Occurs during Multi-file upload in Livewire v3 when using a public array (currently `fileQueue`) to store `TemporaryUploadedFile` objects.
- **Symptoms**: The error triggers either when deleting an item from the queue or when starting the sequential AI processing loop.
- **Attempted Fixes**: 
    1. Moving the array out of a nested form object to root level.
    2. Re-indexing the array using `array_values()` after deletion.
    3. Adding `wire:key` to all loop elements.
- **Potential Solution for Next Agent**: Consider using a "Buffered" approach where files are processed immediately or stored in a way that doesn't rely on Livewire's public array indexing for Temporary Files, as v3 has known issues with tracking indices of uploaded file objects.

## 🤝 Agent Handover Context
If you are another agent taking over this task, please focus on the **SlipRegistry** Livewire component. The infrastructure (MySQL, Vite LAN, Tailwind Build) is already stable. The main goal is to fix the batch scanning UI/UX without triggering the PropertyNotFound error.

---
*Last Updated: 2026-04-02*

