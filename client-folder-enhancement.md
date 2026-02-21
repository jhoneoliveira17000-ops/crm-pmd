# Client Folder Enhancement Plan

## Overview
Enhance the client management experience by creating a comprehensive "Client Folder" (Dashboard) view. This view will aggregate all client information, timeline of events, services/platforms management, and project notes into a single, organized interface using tabs.

## Project Type
**WEB** (PHP + Vanilla JS + TailwindCSS)

## Success Criteria
- [ ] New `cliente_dashboard.php` page accessible from client list.
- [ ] **Timeline Tab**: Shows client history (creation, status changes).
- [ ] **Details Tab**: Shows full info + dynamic "Other Links" section.
- [ ] **Services Tab**: Manage Platforms (Meta, Google) and Service Types (Traffic, LP).
- [ ] **Notes Tab**: specific section for project notes/financial follow-up.
- [ ] **Risk Status**: "Flag" system (Green/Yellow/Red) in the header.

## Tech Stack
- **Backend**: PHP (Native)
- **Database**: MySQL (New tables for services, notes, links)
- **Frontend**: HTML5, TailwindCSS, Vanilla JS
- **Icons**: Heroicons (SVG)

## File Structure
```
PMDCRM/
├── cliente_dashboard.php       # [NEW] Main client view
├── api/
│   ├── cliente_detalhes.php    # [NEW] GET full client data
│   ├── cliente_notas.php       # [NEW] CRUD for notes
│   ├── cliente_servicos.php    # [NEW] CRUD for services
│   └── cliente_links.php       # [NEW] CRUD for links
└── src/
    └── db_updates.sql          # Schema changes
```

## Task Breakdown

### Phase 1: Database Architecture
- [ ] **Create Schema Changes** `src/db_updates.sql`
    - Add `status_risco` to `clientes`.
    - Create `client_services` table.
    - Create `client_notes` table.
    - Create `client_links` table.
    - Create `activity_logs` table (for timeline).
- [ ] **Apply Schema**
    - Run SQL against database.

### Phase 2: Backend API
- [ ] **Client Details API** `api/cliente_detalhes.php`
    - Fetch client basic info, services, notes, links, and logs.
- [ ] **Services API** `api/cliente_servicos.php`
    - Add/Remove/Update services.
- [ ] **Notes API** `api/cliente_notas.php`
    - Add/Delete notes.
- [ ] **Links API** `api/cliente_links.php`
    - Add/Delete links.

### Phase 3: Frontend Implementation
- [ ] **Client Dashboard Page** `cliente_dashboard.php`
    - Layout skeleton (Header + Tabs).
    - **Header**: Name, Status, Risk Flags (Green/Yellow/Red), Back button.
- [ ] **Tab: Timeline (Visão Geral)**
    - Render activity log list.
- [ ] **Tab: Details (Detalhes)**
    - Display fields (readonly/edit toggle).
    - Manage "Other Links" section.
- [ ] **Tab: Services (Plataforma e Serviços)**
    - UI to add Platform + Service Type.
    - List active services.
- [ ] **Tab: Notes (Anotações)**
    - Text area for new notes.
    - List of past notes with dates.

### Phase 4: Integration
- [ ] Update `clientes.php` list to link to `cliente_dashboard.php` instead of just opening modal?
    *   *Decision*: Keep Modal for quick edit, add "Abrir Pasta" button to go to Dashboard.

## Phase X: Verification
- [ ] Verify database tables created.
- [ ] Verify "Abrir Pasta" link works.
- [ ] Verify all tabs load data correctly.
- [ ] Verify adding a note appears in timeline.
- [ ] Verify adding a service persists.
