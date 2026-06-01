# Shared Jordan Areas Table Design

## Scope

Add shared Jordan area reference data only. Do not change API, UI, or tenant databases.

## Storage

Create `areas` on the configured landlord database connection.

- `id`: auto-increment primary key
- `name`: string, maximum 100 characters
- `city`: string, maximum 100 characters
- `latitude`: decimal with 9 digits and 6 decimal places
- `longitude`: decimal with 9 digits and 6 decimal places
- Unique key: `name`, `city`

## Delivery

Add two Laravel files:

1. A landlord migration that creates and drops the `areas` table.
2. A dedicated seeder that inserts the provided 59 rows with `updateOrInsert` so it is safe to rerun.

Run the migration and seeder locally. Run the same migration and seeder in production over SSH when access is approved.

## Verification

- Confirm the local landlord table exists.
- Confirm the table contains 59 rows.
- Confirm grouped city counts match the provided dataset.
- Confirm rerunning the seeder does not add duplicate rows.
- Confirm no API or UI files changed.
