# Hybrid SaaS Multi-Tenancy Design

## Goal

Turn the dental lab management app into a SaaS platform where a central landlord layer manages tenants, domains, plans, provisioning, and status, while every dental lab tenant runs in its own isolated application database.

## Current Context

The app is a Laravel 8 application with a legacy business schema spread across users, cases, clients/doctors, jobs, invoices, payments, materials, devices, reports, and supporting tables. Most controllers and models query those tables directly, so adding `tenant_id` filters across the existing business code would be high-risk.

There is already useful groundwork:

- `App\Http\Middleware\ApplyDomainContext` resolves host-based context and can switch the active database connection.
- `config/domain_context.php` maps known hosts to databases and currencies.
- Branding already supports tenant keys through `BrandSetting`, `BrandingManager`, and `RequestBrandingResolver`.
- Feature flags already accept tenant keys.

The design should preserve existing tenant-internal workflows and focus the first SaaS milestone on reliable tenant resolution and automated provisioning.

## Chosen Approach

Use a hybrid model:

- A central landlord database stores tenant metadata.
- Each tenant has a separate tenant database containing normal app data.
- Requests resolve the tenant from the host before authentication.
- After tenant resolution, the app switches the tenant database connection and existing controllers continue to work against the selected tenant database.

This avoids a broad `tenant_id` retrofit while still creating a SaaS control plane.

## Data Model

### Landlord Database

Add a landlord connection, for example `landlord`, configured separately from the tenant app connection.

Create landlord tables:

- `tenants`
- `tenant_domains`
- `tenant_provisioning_events`

`tenants` fields:

- `id`
- `uuid`
- `slug`
- `name`
- `database_name`
- `status`: `provisioning`, `active`, `suspended`, `failed`
- `plan`: nullable string such as `starter`, `growth`, `enterprise`
- `country_code`
- `country_name`
- `currency_code`
- `currency_display`
- `currency_symbol`
- `currency_unit_ar`
- `currency_name_ar`
- `currency_name_en`
- `branding`: nullable JSON for initial branding payload
- `provisioning_error`: nullable text
- timestamps

`tenant_domains` fields:

- `id`
- `tenant_id`
- `host`
- `is_primary`
- timestamps

`tenant_provisioning_events` fields:

- `id`
- `tenant_id`
- `step`
- `status`: `pending`, `running`, `succeeded`, `failed`
- `message`
- `context`: nullable JSON
- timestamps

### Tenant Databases

Tenant databases keep the existing business tables. No broad `tenant_id` columns are required in v1.

The tenant provisioning flow creates or selects a tenant database, runs normal app migrations against it, seeds foundation data, and creates the first tenant admin user.

## Tenant Resolution

`ApplyDomainContext` should evolve from config-only host maps into a resolver that checks the landlord database first.

Resolution order:

1. Local/testing environment keeps the current local bypass unless a tenant override is supplied.
2. Query/header override is allowed only in local/testing for development.
3. Landlord lookup by normalized host in `tenant_domains`.
4. Existing `config/domain_context.php` host map remains as a fallback during migration.
5. Unknown host returns the existing domain-selection/error page.

Resolved context should include:

- tenant id, uuid, slug, and name
- tenant database name
- host
- status
- country and currency fields
- branding tenant key

Suspended tenants should not reach auth or app controllers. They should receive a clear workspace-unavailable page.

Failed or provisioning tenants should receive a provisioning-unavailable page with a generic message for normal users and detailed errors only for super-admins.

## Database Switching

Add a reusable tenant connection, for example `tenant`, based on the normal MySQL connection settings. After host resolution:

1. Set `database.connections.tenant.database` to the tenant database name.
2. Set `database.default` to `tenant` for the request.
3. Purge and reconnect the tenant connection.
4. Share the tenant context in the container and views.

The landlord connection must never be pointed at a tenant database. Tenant resolution and provisioning use `landlord`; business workflows use `tenant`.

## Provisioning

Create a shared `TenantProvisioningService` used by both an Artisan command and a super-admin UI.

Provisioning steps:

1. Validate tenant slug, name, domain, database name, country, currency, admin user, and optional branding.
2. Create `tenants` row with status `provisioning`.
3. Create `tenant_domains` rows.
4. Create tenant database if it does not exist.
5. Run app migrations against the tenant connection.
6. Seed foundation data for job types, materials, impression types, demo basics if requested.
7. Create first tenant admin user.
8. Create or update tenant `brand_settings` row in the tenant database.
9. Mark tenant `active`.
10. Record provisioning events for every step.

If any step fails after the tenant row exists:

- Mark tenant `failed`.
- Store the exception message in `provisioning_error`.
- Record a failed provisioning event.
- Do not delete the database automatically.

Provisioning should be idempotent where practical. Re-running for the same slug/domain should refuse unless a `--resume` or explicit retry action is used.

## Artisan Command

Add:

```bash
php artisan tenants:create
```

Supported options:

- `--slug=al-solent`
- `--name="Solent Dental Lab"`
- `--domain=alsolent.example.test`
- `--database=tenant_al_Solent`
- `--country-code=JO`
- `--country-name=Jordan`
- `--currency-code=JOD`
- `--currency-display=JOD`
- `--admin-name="Admin User"`
- `--admin-email=admin@example.test`
- `--admin-password=secret`
- `--seed-foundation`
- `--plan=starter`

The command should print the tenant id, slug, domain, database name, final status, and failed step if provisioning fails.

## Super-Admin UI

Add a small super-admin tenant area:

- `GET /system/tenants`: tenant list
- `GET /system/tenants/create`: create form
- `POST /system/tenants`: provision tenant
- `GET /system/tenants/{tenant}`: tenant detail with domains and provisioning events
- `POST /system/tenants/{tenant}/suspend`: suspend
- `POST /system/tenants/{tenant}/activate`: activate

The UI should be a thin wrapper over `TenantProvisioningService`.

Access should be restricted to a platform-level super-admin. Tenant admins should not see landlord tenant management.

## Super-Admin Authorization

V1 can use an environment-configured super-admin email list:

```env
PLATFORM_ADMIN_EMAILS=owner@example.com,ops@example.com
```

Create middleware `PlatformAdminMiddleware` that checks authenticated user email against this list while running in a resolved tenant context. This is simple and avoids building a full landlord-auth subsystem in v1.

Longer term, platform users can move to landlord-auth accounts.

## Branding and Feature Flags

Branding should resolve from the tenant slug by default after domain resolution. The existing `BrandingManager` can continue to read `brand_settings` from the selected tenant database.

Feature flags should receive the tenant slug from the resolved tenant context. Config defaults still apply when a tenant-specific flag is absent.

## Local Development

Localhost keeps the current local bypass so existing development remains usable.

For tenant testing, support an explicit local override such as:

```http
X-Tenant-Domain: alsolent.example.test
```

or a query parameter enabled only in local/testing:

```http
?tenant_domain=alsolent.example.test
```

The override resolves through the landlord database and switches to the matching tenant database.

## Error Handling

Unknown domain:

- Show domain selection/error page.
- Do not authenticate.

Suspended tenant:

- Show workspace-unavailable page.
- Do not authenticate.

Provisioning or failed tenant:

- Show workspace-unavailable page.
- Log the detailed reason.

Database unavailable:

- Show 503 tenant unavailable page.
- Report the exception.

Provisioning failure:

- Mark tenant failed.
- Persist failed step and error.
- Leave database intact for inspection.

## Testing

Add feature tests for:

- Known domain resolves tenant and switches the database connection.
- Unknown domain returns domain-selection response.
- Suspended tenant returns workspace-unavailable response.
- Local override resolves a tenant in testing.
- Provisioning service records step events.
- Provisioning command creates landlord tenant/domain records.
- Failed provisioning marks tenant failed and stores the error.
- Platform admin middleware allows configured emails and blocks others.

Add unit tests for:

- Host normalization.
- Database name validation.
- Slug validation.
- Tenant context normalization and currency defaults.

Manual QA:

- Provision one tenant from command.
- Provision one tenant from UI.
- Log into each tenant through its domain override.
- Confirm cases, users, and branding stay isolated between tenant databases.

## Migration Strategy

V1 does not migrate existing production data automatically.

Recommended rollout:

1. Add landlord tables and tenant resolver while preserving config fallback.
2. Register existing country/domain databases as landlord tenants.
3. Verify existing domains still resolve to the same databases.
4. Use provisioning for new tenants.
5. Treat data import tooling as a separate feature if existing single-database records need to be split.

## Out of Scope for V1

- Billing/subscription collection.
- Per-tenant database users/passwords.
- Self-service signup.
- Tenant data import/export wizard.
- Moving all tenants into one shared database.
- Full landlord-auth identity system.

## Acceptance Criteria

- A platform admin can create a tenant from the command.
- A platform admin can create a tenant from the UI.
- Tenant provisioning creates a database, runs migrations, seeds foundation data, creates the first admin, and activates the tenant.
- Requests for a tenant domain use that tenant database.
- Unknown, suspended, failed, and provisioning tenants do not reach normal app controllers.
- Existing local development still works without a tenant registry.
- Existing configured domains continue working through fallback or landlord registration.
