# Roles and Permissions Analysis

This document outlines the current authorization architecture implemented in the PerfectLum/PerfectChroma application.

## Database Structure
The application utilizes a standard Role-Based Access Control (RBAC) system, likely based on the `spatie/laravel-permission` package (evident from the `guard_name` column and standard table structures).

The following tables handle authorization:
1. `roles`: Stores the available roles.
2. `model_has_roles`: The pivot table linking users to their assigned roles.
3. `permissions`: Intended for granular permissions, but currently empty.

## Available Roles
There are exactly **3 roles** defined in the database under the `web` guard:

| ID | Role Name | Description / Context |
|----|-----------|-----------------------|
| 1  | **super** | Super Administrator. Has unrestricted access across all facilities and organizations. |
| 2  | **admin** | Facility/Organization Administrator. Can manage users, workstations, and displays within their scope. |
| 3  | **user**  | Standard User. Has read-only access to most views (e.g., cannot add/edit/delete records in User Management). |

## Permission Usage
Currently, the system **does not use granular permissions** (the `permissions` table is empty). 

Instead, the codebase relies entirely on **Role-based checks**. Throughout the views and controllers, authorization is implemented via direct role comparisons.

**Example from `users_management.blade.php`:**
```blade
@if($role != 'user')
    <button type="button" onclick="user_form(this, 'create')">
        Add User
    </button>
@endif
```

## Summary for Development
When adding new features or modifying the UI (such as separating views for `perfectchroma` and `perfectlum`), you do not need to seed granular permissions. You solely need to ensure that the current user's role is retrieved and checked (e.g., verifying if they are `super`, `admin`, or `user`) to show or hide the appropriate Action buttons and Data Table columns.


PerfectChroma (Dark)	chroma_admin	chroma@test.com	123456	Langsung masuk Dashboard (Gelap)
Platform Ganda (Keduanya)	dual_admin	dual@test.com	123456	Masuk ke Halaman "Choose Platform"