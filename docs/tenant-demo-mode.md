# Tenant sample-data mode

An administrator's **Demo Data** switch selects a separate populated database for
that website. The selection is session-specific. The real account, permissions
and branding stay on the real tenant connection; business queries and their
relationships use `tenant_demo`. Sample mode is read-only.

Administrators start with Demo Data on when they have not chosen a mode in their
current session. An explicit Off choice is respected for that session. Employees
continue to use real data by default; their permissions are unchanged.

## Prepare samples

Set `DEMO_DB_DATABASE` to the existing populated, isolated public demo template.
Never use a customer's live database as that template.

```sh
php artisan tenants:demo-databases --all
php artisan tenants:demo-databases --all --apply
php artisan tenants:demo-databases --tenant=TENANT_ID --apply
```

The first command is a dry run. Re-running preparation leaves existing prepared
copies unchanged. Run it for new tenants after provisioning. Local development
can use `--local --apply` once a separate populated template is configured.

Copies have deterministic, opaque names and an ownership marker. Only selected
clinical/reference tables receive data; identities and free-text notes are
anonymized. Tokens, uploads, branding, sessions and integration data are excluded.
Partial copies are retained without a ready marker and must be inspected manually;
the command never drops or overwrites a database.

If the sample database is unavailable, the app fails closed with a return-to-real-data
form. Switching rotates CSRF/data-context tokens so stale tabs cannot submit
sample identifiers against real records. Browsers should refresh old tabs after
changing modes. The public demo site's existing behavior is unchanged.
