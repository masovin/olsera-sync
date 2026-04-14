## ADDED Requirements

### Requirement: Trigger Sync via CLI
The system must provide Artisan commands to manually trigger the full product synchronization or a lighter inventory-only sync.

#### Scenario: Manual full sync trigger
- **WHEN** the command `php artisan olsera:sync-products` is executed
- **THEN** the full synchronization process should start immediately

### Requirement: Automated Scheduling
The sync process must be able to run automatically at pre-defined intervals using the Laravel task scheduler.

#### Scenario: Scheduled inventory sync
- **WHEN** the inventory sync is scheduled for "every 15 minutes"
- **THEN** it should be executed automatically by the Laravel scheduler without user intervention
