# PerfectLum Remote Calibration Platform Overview

## Purpose

PerfectLum in this repository is the web-based remote management layer that sits on top of an existing paid desktop client application used on Windows and macOS.

The desktop client is the operational endpoint that lives on user machines and connected workstations/displays. This web application adds remote visibility, remote configuration, scheduling, reporting, alerting, and hierarchy management without changing the core client workflow.

In practical terms:

- The desktop client and connected devices generate operational state.
- The web application organizes and manages that state remotely.
- Users work in the web application to monitor, configure, schedule, and review calibration activity across facilities.

---

## Core Domain Model

The platform is organized as a strict hierarchy:

`Facility -> Workgroup -> Workstation -> Display`

### Facility
- Top-level organization boundary.
- Primary scope boundary for non-super users.
- Holds timezone and site-level context.

### Workgroup
- Child of a facility.
- Groups related workstations.

### Workstation
- Child of a workgroup.
- Represents a physical client machine or managed endpoint.
- Holds workstation preferences and application settings.

### Display
- Child of a workstation.
- Represents a physical monitor/display connected to a workstation.
- Stores technical preferences, status, financial metadata, and calibration history.

---

## Major Features

### 1. Dashboard
- Network-wide operational overview.
- Display health summary.
- Due task summary.
- Recent performed history.
- Search and quick navigation into hierarchy entities.

### 2. Facility Management
- Create, edit, and delete facilities.
- Maintain facility metadata such as name, description, location, and timezone.

### 3. Workgroup Management
- Create, edit, and delete workgroups.
- Move and organize workgroups under facilities.

### 4. Workstation Management
- View workstation inventory.
- Edit workstation metadata and application settings.
- Move a workstation to another workgroup when allowed.
- Inspect attached displays and workstation health.

### 5. Display Management
- View display inventory.
- Edit display settings and financial metadata.
- Move a display to another workstation/workgroup/facility path.
- Inspect display health, recent calibration history, scored evaluations, and measurement trends.

### 6. Calibrate Display
- Create on-demand calibration tasks.
- Select scope by facility, workgroup, workstation, or specific displays.

### 7. Scheduler
- Schedule calibration or QA-related tasks.
- Manage future execution timing and recurring behavior.

### 8. Histories & Reports
- Browse completed history records.
- View detailed report content, scores, questions, and graphs.
- Open print preview and exports.

### 9. Settings
- Site settings.
- Alert settings.
- SMTP settings.
- Application settings for workstations.

### 10. User Management
- Manage users, roles, facility assignment, and enable/disable state.

---

## Remote Function Model

This application is not the calibration engine itself. The remote behavior is split across two sides:

### Client-side remote function
The paid desktop client on Windows/macOS is responsible for:
- Representing the real workstation.
- Managing connected displays.
- Running calibration or QA operations locally.
- Sending state, task results, and history data back to the server.

### Server-side remote function
The web application is responsible for:
- Organizing all clients into facility/workgroup/workstation/display hierarchy.
- Exposing settings and remote management UI.
- Creating and scheduling tasks for clients/displays.
- Storing histories and reporting data.
- Sending alerts and notifications.

---

## Client to Server Flow

The dominant direction in the system is client or endpoint data flowing into the server.

Examples:

- A workstation connects and updates its last connected state.
- A display reports current health, status, and error data.
- A completed calibration task generates history records and graphs.
- Workstation/display preferences and sync markers are updated.

Typical data arriving from client-side behavior includes:

- workstation connectivity
- app/client version
- display connected state
- display errors
- calibration result data
- history steps, scores, questions, and graphs

This is why `Workstation` and `Display` behave more like registered assets than manually created business records.

---

## Server to Client Flow

The server also pushes intent back to clients, mainly through tasks and configuration.

Examples:

- A user creates a calibration task in the web app.
- A user schedules future tasks for displays.
- A user updates workstation application settings.
- A user changes display settings or hierarchy placement.

In this direction, the server acts as the control plane:

- task definitions are stored server-side
- settings are stored server-side
- sync flags and preference changes indicate pending client-side pickup

So the web system is effectively a remote orchestration layer for distributed client endpoints.

---

## Data Ownership and Record Behavior

The codebase suggests two different categories of records:

### Manually managed records
- Facility
- Workgroup
- User

These are clearly created and managed from the web application.

### Registered/managed asset records
- Workstation
- Display

These appear to be discovered, synchronized, or maintained by the broader client ecosystem, then edited and organized through the remote platform.

This is why:

- workstation rename is supported
- display does not have a simple free-text `name` field
- display identity is derived from `manufacturer + model + serial`

---

## User Roles

The legacy system uses three role levels:

- `super`
- `admin`
- `user`

Role is resolved in middleware and stored in session.

Source:
- `app/Http/Middleware/auth.php`

### Super
- Global access across all facilities.
- Can view and manage cross-facility data.
- Can change hierarchy more freely.
- Can access site settings and SMTP/alert configuration.

### Admin
- Scoped to the assigned facility.
- Can manage operational data within that facility.
- Can access alert settings.
- Cannot act across all facilities like super.

### User
- Scoped to the assigned facility.
- Most restricted role.
- Cannot access `site-settings`.
- Cannot access `alert-settings`.
- Often read-only or limited-action in operational screens.

---

## Effective Access Model

The important rule is:

**Access is primarily controlled by `role + facility scope`, not by per-record ownership.**

Even though some tables include `user_id`, the dominant permission pattern in the codebase is:

- `super` = global scope
- `admin` = assigned facility scope
- `user` = assigned facility scope with fewer management actions

This pattern appears across:

- dashboard data
- workstation/display listings
- histories and reports
- scheduler
- calendar
- settings screens
- user management

---

## SMTP and Alerting

The application supports SMTP configuration from the database, not just `.env`.

Relevant parts:

- `config/mail.php`
- `app/Providers/MailConfigServiceProvider.php`
- table: `smtp_settings`

This enables:

- test email sending
- alert emails
- daily emails
- display status notifications
- other notification flows

So email behavior is part of the operational remote platform, not only account management.

---

## Current Architectural Direction

The modernized direction in this repository keeps the old business flow but improves the interface and maintainability:

- Blade server-rendered pages remain the base
- Grid.js is used for data tables
- Alpine is used for interaction state
- hierarchy modals provide SPA-like navigation
- X6 is used for structure maps
- route compatibility is preserved
- database schema is preserved

This means the system can be improved significantly without changing how the desktop client and remote operations fundamentally work.

---

## Practical Product Summary

This application is best understood as:

**a remote calibration operations console for deployed workstation/display clients**

It provides:

- hierarchy management
- remote visibility
- remote configuration
- scheduling
- reporting
- alerting
- facility-scoped multi-user administration

while the actual endpoint execution still lives in the desktop client environment.
