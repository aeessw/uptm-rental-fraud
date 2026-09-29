# Application column rename report

Implemented the complete requested mapping, including custom primary keys and timestamps, and applied it to the configured MariaDB 10.4.32 database on 2026-09-18.

## Migration and data preservation

- New migration: `database/migrations/2026_09_18_000001_rename_application_columns.php`.
- Uses Laravel `renameColumn` with a reverse `down()` migration. Existing migration files were not edited by this task.
- Before/after SHA-256 comparisons of every application row confirmed unchanged values and row counts, normalizing only column names.
- Preserved 3 users, 11 listings, 3 listing photos, 8 messages, 3 reports, 191 audit logs, 2 saves, and the empty user-block and notification-read tables (221 rows total).
- Primary and foreign key numeric values remain unchanged. MariaDB updated foreign-key references to the renamed primary keys, including the notification-read table.
- Framework tables and their fields remain unchanged. No `.env` edits. No destructive migration command was run against the configured database; automated tests use an isolated in-memory SQLite database.

## Models and relationships

- Configured custom primary keys, `CREATED_AT`, and `UPDATED_AT` on User, Listing, ListingPhoto, Message, Report, and AuditLog.
- Updated fillable attributes, casts, Google OAuth, middleware role reads, queries, validations, encryption storage/read fields, audit signature field reads, factories, seeders, and Blade model references.
- Added ListingSave and UserBlock pivot models with their custom primary keys and timestamp methods. Pivot timestamp methods explicitly override Laravel's default inheritance from the parent model.
- Explicit foreign/local/owner keys now cover belongsTo, hasMany, belongsToMany, and the receivedReports hasManyThrough relationship.
- Model ID reads use `getKey()`; Auth::id(), find(), whereKey(), and route binding use configured primary keys.
- User password-reset/verification email getters and mail notification routing use user_email.
- Photo deletion now reads the existing photo_path field consistently.
- Form input names, route names, JSON response fields, CSS, markup layout, and JavaScript interfaces are preserved.

## Validation

- Original baseline: 25 tests passed (298 assertions).
- Final suite: 28 tests passed (441 assertions). Five old migration-setup assertions were replaced by the standard RefreshDatabase setup in GoogleLoginTest.
- New coverage verifies populated migration up/down/up, exact stored values, ciphertext, signed audit entries, relationships, foreign-key cascades/null-on-delete, framework columns, session authentication, filters, availability, saves, notifications, and pivot timestamps.
- `php artisan optimize:clear`: passed.
- `php artisan migrate`: passed on the configured MariaDB database.
- `php artisan route:list`: passed, 42 routes.
- `php artisan view:cache`: passed.
- PHP syntax checks of changed/new PHP source and tests: passed.
- Read-only verification of all renamed columns, foreign-key targets, and Eloquent relationship loading on the configured MariaDB database: passed.

## Remaining old names and limitations

No stale application database query references were found in the final source scan. Old names intentionally remain in historical migrations, the new migration's old-to-new mapping, and the populated migration regression fixture. HTTP inputs such as title, availability, reason, and message; notification JSON fields such as id, title, description, and created_at; Google provider fields; HTML attributes; and framework columns retain their existing names because they are not application database-column references.

The optional `migrate --pretend` check failed because Laravel's legacy MariaDB rename compiler could not retrieve column metadata in pretend mode. The actual migration completed successfully and the data comparison passed. No remaining errors require manual attention. Rollback was tested on isolated SQLite data, not on the configured MariaDB database. Browser visual inspection and a real Google-provider sign-in were not performed; OAuth behavior is covered with provider mocks.

## Files changed by this task

This list compares against the workspace at task start, preserving pre-existing edits.

- `app/Models/ListingSave.php`
- `app/Models/UserBlock.php`
- `app/Helpers/AuditLogger.php`
- `app/Http/Controllers/AuthController.php`
- `app/Http/Controllers/ListingController.php`
- `app/Http/Controllers/MessageController.php`
- `app/Http/Controllers/MppController.php`
- `app/Http/Controllers/MppNotificationController.php`
- `app/Http/Controllers/ReportController.php`
- `app/Http/Middleware/MppMiddleware.php`
- `app/Http/Middleware/StudentMiddleware.php`
- `app/Models/AuditLog.php`
- `app/Models/Listing.php`
- `app/Models/ListingPhoto.php`
- `app/Models/Message.php`
- `app/Models/Report.php`
- `app/Models/User.php`
- `database/migrations/2026_09_18_000001_rename_application_columns.php`
- `database/factories/UserFactory.php`
- `database/seeders/DatabaseSeeder.php`
- `resources/views/mpp/audit-logs.blade.php`
- `resources/views/mpp/dashboard.blade.php`
- `resources/views/mpp/listing-details-modals.blade.php`
- `resources/views/mpp/listings.blade.php`
- `resources/views/mpp/reports.blade.php`
- `resources/views/mpp/sidebar.blade.php`
- `resources/views/mpp/student-activity.blade.php`
- `resources/views/mpp/student-details.blade.php`
- `resources/views/mpp/student-reports.blade.php`
- `resources/views/mpp/student-show.blade.php`
- `resources/views/mpp/students.blade.php`
- `resources/views/student/account-menu.blade.php`
- `resources/views/student/dashboard.blade.php`
- `resources/views/student/listings.blade.php`
- `resources/views/student/listings/edit.blade.php`
- `resources/views/student/messages.blade.php`
- `resources/views/student/profile.blade.php`
- `resources/views/student/room-details.blade.php`
- `resources/views/student/saved-listings.blade.php`
- `routes/web.php`
- `tests/Feature/ColumnRenameMigrationTest.php`
- `tests/Feature/RenamedColumnFlowsTest.php`
- `tests/Feature/AuditIntegrityTest.php`
- `tests/Feature/BlockedConversationTest.php`
- `tests/Feature/BlockedListingVisibilityTest.php`
- `tests/Feature/DuplicateReportTest.php`
- `tests/Feature/GoogleLoginTest.php`
- `tests/Feature/ListingDetailsTest.php`
- `tests/Feature/MessageReadTest.php`
- `tests/Feature/MppListingReviewTest.php`
- `tests/Feature/MppNotificationTest.php`
- `tests/Feature/MppStudentInvestigationTest.php`
- `tests/Feature/SavedListingsTest.php`
- `tests/Feature/StudentProfileTest.php`
