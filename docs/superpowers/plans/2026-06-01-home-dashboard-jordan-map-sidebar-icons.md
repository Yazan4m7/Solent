# Home Dashboard Jordan Map and Sidebar Icons Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Add a static expandable Jordan city map, dental-lab sample activity, and inline sidebar SVG icons.

**Architecture:** Keep the change UI-only. Use Blade partials for the reusable Jordan map and sidebar icons, plus scoped dashboard CSS.

**Tech Stack:** Laravel Blade, Bootstrap 4 modal, CSS, PHPUnit feature tests.

---

### Task 1: Add markup regression tests

**Files:**
- Modify: `tests/Feature/DashboardScopedMarkupTest.php`

- [ ] Add tests for Jordan map modal markup, dental-lab sample activity, and sidebar SVG icon markup.
- [ ] Run `php artisan test --filter=DashboardScopedMarkupTest` and confirm the new tests fail.

### Task 2: Add the static Jordan map

**Files:**
- Create: `resources/views/partials/jordan-dashboard-map.blade.php`
- Modify: `resources/views/dashboard.blade.php`
- Modify: `public/assets/css/elegant-dashboard.css`

- [ ] Add a reusable inline SVG Jordan map with static colored city markers.
- [ ] Add the click target and Bootstrap modal with a larger map.
- [ ] Replace non-dental sample activity rows.

### Task 3: Replace sidebar icon fonts

**Files:**
- Create: `resources/views/layouts/navbars/partials/sidebar-icon.blade.php`
- Modify: `resources/views/layouts/navbars/leftsidebar.blade.php`

- [ ] Add reusable inline SVG sidebar icons.
- [ ] Remove the Material Symbols stylesheet and replace all live sidebar icon-font markup.
- [ ] Preserve routes, permissions, labels, and collapse behavior.

### Task 4: Verify

- [ ] Run `php artisan test --filter=DashboardScopedMarkupTest`.
- [ ] Render `/home`, click the map, and confirm the larger map appears.
- [ ] Check page identity, visible content, console health, and screenshot evidence.
