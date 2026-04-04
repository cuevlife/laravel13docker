# SmartBill AI Notes

## Product Shape
- SmartBill is an AI slip-scanning SaaS, not an accounting system.
- The product should feel closer to a modern Express-style SaaS structure:
  - `Control Plane` for system operators
  - `Workspace App` for customer teams

## Target Access Model
- `Super Admin` must have a separate login entry: `/admin/login`
- `User / Workspace` login stays on `/login`
- Knowing the admin URL alone must never grant access

## Security Model
- Separate login pages improve clarity and reduce accidental access attempts
- Real protection comes from server-side checks:
  - admin routes use `auth + role:super_admin`
  - `/admin/login` rejects normal users even if they have valid credentials
  - normal users who type `/admin/...` still get blocked by role middleware
- Conclusion: separate login is good UX and better boundary definition, but role enforcement is the actual security layer

## UX Model
- After normal user login:
  - always land on `Project Hub` first
  - choose the project/company/client to work on
  - once inside, all data stays scoped to that workspace
- After super admin login:
  - land in a separate `Control Plane`
  - manage users, projects, tokens, and topup review from a dedicated UI
  - this area should not feel like the normal workspace app

## Current Implementation Status
- `admin / 123123` is currently `Super Admin`
- Super admin UI now uses a separate layout from workspace UI
- Admin path mode exists at `/admin/...`
- Separate super admin login now exists at `/admin/login`
- Normal login remains at `/login`
- Project selector is shown after login instead of auto-entering the only workspace

## Workspace Session Model
- Active project selection is now stored in session as `active_project_id`
- User enters a project through `projects/open/{project}`
- After selection, the app redirects into generic workspace routes such as:
  - `/workspace/dashboard`
  - `/workspace/slips`
  - `/workspace/templates`
- Workspace URLs should no longer expose project IDs during normal use
- Switching project means updating session state, not changing the app into a different project path

## Product Intent For Users
- The workspace picker should feel like a profile chooser (Netflix style):
  - simple
  - visual
  - safe
  - hard to get lost in
- `Add Folder` should stay lightweight:
  - modal flow
  - ask for folder name and optional logo upload
  - create and enter immediately
- `Delete Folder` should be guarded:
  - available only to folder owners or super admins
  - require typing the exact folder name before deletion
  - remove the active selection if that workspace gets deleted

## Desired Next Direction
- Keep pushing the split between:
  - `Control Plane`
  - `Workspace App`
- Rename labels to match the real product better:
  - avoid generic SaaS/accounting wording
  - prefer wording around profiles, folders, workspaces, scan operations, token control, and review queues. (Successfully renamed Project Hub to Folder Hub).

## Recent UI Updates (04/2026)
- **Terminology Swap:** Fully translated "Project" semantics into "Folder" across the UI, bringing a neutral, profile-like filing cabinet feel for user slips.
- **Visual Overhauls:**
  - `Folder Logo Upload`: Users can optionally add square visual identifiers directly from the Create Modal.
  - `Search Box Refinement`: Compacted the Hub Search container to `h-10` width for a slicker presentation.
  - `Long Name Guardails`: Embedded safe text-truncation (`w-full truncate`) with hover tooltips for extensive Folder names.
- **Authentication Recovery:** Restored the premium split-screen `/login` interface and stabilized the background session logout controller.

## Next Steps To Action
1. Move inwards to the actual workspace views (e.g., Dashboard, Slips) and apply the same "Folder" terminology everywhere `Project` leaks out.
2. Ensure the "Control Plane" UI logic gracefully reflects "Folders" and not basic projects.
3. Address legacy `stdClass` warnings inside the Admin Controller when dealing with these redefined Workspaces.
