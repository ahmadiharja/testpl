# PerfectLum Application Features

## Overview

PerfectLum is a web-based remote operations platform for managing calibration activity across distributed workstations and displays.

The application acts as the administrative and orchestration layer for a remote client ecosystem. It allows teams to organize assets, monitor display health, configure workstation settings, schedule tasks, review completed work, and react to alerts from a central interface.

The operational hierarchy is:

`Facility -> Workgroup -> Workstation -> Display`

This hierarchy is used consistently across inventory, configuration, task scheduling, reporting, search, and notifications.

---

## Core Product Areas

## 1. Dashboard

The dashboard is the operational home screen for the platform.

Main capabilities:
- Health summary cards for display status, workstation counts, and due tasks
- Attention-focused tables such as `Displays Not OK`
- Recent completed activity through `Latest Performed`
- A dedicated `Due Tasks` section for upcoming scheduled work
- Role-aware content for `super`, `admin`, and `user`
- Interactive summary modals opened from dashboard statistic cards

The dashboard is designed for fast scanning, quick action, and issue triage.

---

## 2. Facility Management

Facilities are the top-level organizational units.

Main capabilities:
- Create, edit, and delete facilities
- Maintain location, timezone, and descriptive metadata
- Filter facilities by overall display health (`All`, `OK`, `Not OK`)
- Open a facility detail modal without leaving the listing page
- Review workgroups registered under a facility

Facilities also define the primary operational scope boundary for non-super users.

---

## 3. Workgroup Management

Workgroups organize workstations inside a facility.

Main capabilities:
- Create, edit, and delete workgroups
- Manage address and phone metadata
- Filter workgroups by inherited display health
- Open workgroup detail modals directly from tables and search
- Review workstation relationships inside a workgroup

Workgroups act as the middle layer between facilities and physical workstation endpoints.

---

## 4. Workstation Management

Workstations represent managed client endpoints.

Main capabilities:
- Browse workstation inventory
- View workstation hierarchy, attached displays, and settings
- Filter workstations by effective display health (`All`, `OK`, `Not OK`)
- Search workstations quickly
- Open workstation detail and settings in modal workflows
- Move a workstation to another workgroup when permitted
- Review workstation connection recency and attached display counts

Workstation rows also include visual health indicators so operators can immediately identify whether a workstation is fully healthy or has at least one display that needs attention.

---

## 5. Display Management

Displays are the core managed devices in the calibration workflow.

Main capabilities:
- Browse the full display inventory
- Filter by hierarchy and by health status (`All`, `OK`, `Not OK`)
- Open display detail modals directly from tables and global search
- Edit display metadata and technical settings
- Move displays within the hierarchy
- Review operational status and recent display-related activity

Displays are treated as managed assets rather than generic free-form records.

---

## 6. Calibrate Display

This area is used for on-demand task creation.

Main capabilities:
- Select target displays through hierarchy filters
- Create calibration tasks for chosen displays
- Review the current calibration task queue
- Use modern action menus for scheduling or deleting tasks

This is the fastest way to create immediate calibration work from the admin console.

Important behavior:
- The task queue is ordered by newest created calibration tasks first
- This makes recent scheduling actions easier to verify immediately after creation

---

## 7. Scheduler

The scheduler is the long-term planning workspace for recurring and future tasks.

Main capabilities:
- Review all scheduled tasks in a table aligned with the legacy system
- See workstation, workgroup, facility, schedule type, and due date together
- Edit scheduled tasks from an action menu
- Delete tasks through a modern confirmation modal
- Filter and review due work in a more planning-oriented view than the dashboard

This area focuses on planned task execution rather than one-off task creation.

Important behavior:
- The default table order prioritizes the nearest due schedules first
- This makes upcoming work easier to triage and act on from the scheduler

---

## 8. Histories & Reports

This section is the audit and review layer of the platform.

Main capabilities:
- Browse completed calibration and QA history records
- Filter by facility, workgroup, workstation, and display
- Open a modern summary modal from `Task Name`
- Print report previews
- Export report datasets
- Review result, timestamp, scope, and related report metadata

This section is intended for historical verification, compliance review, and outcome analysis.

---

## 9. Application Settings

Application Settings is a scoped bulk-configuration workspace for workstation preferences.

Main capabilities:
- Choose a bulk target type:
  - Facility
  - Workgroup
  - Workstation
- Select targets through a hierarchy browser with checkboxes
- Review selected targets as pills/tags
- Open a bulk configuration modal instead of editing inline on the page
- Configure grouped workstation settings such as:
  - Application behavior
  - Display Calibration
  - Quality Assurance
  - Location metadata

Important behavior:
- The page is designed for bulk settings, not general hierarchy editing
- It resolves selected items into workstation scope before saving
- It keeps the original backend save logic intact while improving UX

---

## 10. Alert Settings

Alert Settings controls operational notifications and mail routing.

Main capabilities:
- Manage alert recipient rules
- Configure daily report recipients
- Toggle recipient activity states
- Maintain error threshold settings
- Configure SMTP delivery settings
- Test outbound email behavior

The page uses a modern two-column layout and keeps administrative configuration separated by purpose.

---

## 11. Site Settings

Site Settings is the platform-level administrative configuration area.

Main capabilities:
- Update branding assets such as logo and favicon
- Configure sender identity and outbound SMTP defaults
- Manage release creation from the integrated `Release Builder`

This section is intended for platform administrators rather than operational users.

---

## 12. User Management

User management controls access to the platform.

Main capabilities:
- Create, edit, and delete users
- Assign role and facility scope
- Enable or disable accounts
- Manage profile and account-related metadata

The system primarily uses three role levels:
- `super`
- `admin`
- `user`

---

## 13. Notification Center

The header bell is an in-app notification center available across roles.

Main capabilities:
- Unread badge count
- Dropdown panel with `Unread` and `All`
- Mark-all-read
- Full `All Notifications` page
- Click-through behavior to the related screen or modal context

Supported notification categories currently include:
- Account reminders
- Workspace activity
- Display status changes
- Task completion events
- Due / overdue task reminders
- Workstation disconnection alerts
- Release build completion

The notification system is database-backed and designed as an in-app awareness layer, separate from email delivery.

---

## 14. Global Search

The header search bar is a cross-module workspace search.

Main capabilities:
- Search across:
  - Facilities
  - Workgroups
  - Workstations
  - Displays
- Autocomplete dropdown with type tags
- Keyboard navigation support
- Search results open the existing hierarchy modals instead of redirecting to new pages

This makes global navigation much faster without breaking the current page context.

---

## 15. Role-Aware Behavior

The application is role-sensitive in both data scope and available actions.

### Super
- Cross-facility visibility
- Broad management permissions
- Administrative settings access

### Admin
- Scoped to the assigned facility
- Operational management inside that scope
- Access to facility-relevant configuration

### User
- Narrowest scope
- More observational and read-focused behavior
- Restricted access to system-wide configuration areas

The dashboard and several actions already adapt to these role boundaries.

---

## 16. Remote Sync Model

PerfectLum is not just a CRUD admin panel. It is connected to remote client endpoints.

The remote client workflow is based on sync rather than remote desktop streaming.

Current model:
- Client applications report workstation and display state to the server
- The server stores tasks, settings, history, and status
- The server exposes pending changes back to clients through sync
- Clients execute tasks locally and report results back

This makes the platform a remote operations control plane for distributed display environments.

---

## 17. Reporting, Export, and Review Workflows

The platform supports multiple review and output workflows.

Examples:
- History print preview
- PDF export
- Report export endpoints
- Health-driven drill-down from dashboard cards
- Summary modals for task and history review

This allows the system to serve both day-to-day operations and compliance-oriented reporting needs.

---

## Product Summary

PerfectLum combines:
- hierarchy management
- remote asset visibility
- display health monitoring
- workstation and display configuration
- on-demand task creation
- long-term scheduling
- reporting and history review
- alert routing
- in-app notifications
- global search

In practical terms, it is a remote calibration administration platform for distributed workstation and display environments, with strong emphasis on hierarchy, scope control, task orchestration, and operational visibility.
