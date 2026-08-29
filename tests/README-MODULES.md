# Stock + Financing module tests

Run:

```bash
php artisan test --testsuite=Feature --filter='(StockModuleContractTest|FinancingModuleContractTest|ModulesSourceIntegrityTest|OptionalModulesIntegrationContractTest)'
node --test tests/js/modules-frontend-contracts.test.mjs
```

The database-backed module tests switch only their own test connection to an in-memory SQLite database and manually load the module tables they exercise. They do not migrate or write to the normal Solent database.
