# Implemented Security Changes

## Route Compatibility

No `?dest=` route name or existing query parameter was renamed. The application continues to use `index.php` and URLs such as `/?dest=update_event&id_event=...`.

## How To Read Code Comments

New comments beginning with `CHANGE` identify an intentional security or architecture modification. The text after `CHANGE` explains the reason immediately next to the changed code.

Examples:

- `CHANGE (CSRF)`: protects browser requests from being forged by another site.
- `CHANGE (IDOR prevention)`: stops one user changing data that belongs to another user by guessing an ID.
- `CHANGE (XSS)`: prevents stored database content being interpreted as browser HTML or JavaScript.
- `CHANGE (configuration security)`: prevents secret files being loaded from an unsafe path or hard-coded into source code.

## Files Changed and Why

- `commun/init.php`
  - Centralized session hardening, common JSON responses, CSRF validation, role checks, output escaping, and database-backed session refresh.
  - Each protected request now refreshes the user from the database. A removed account or changed role cannot keep stale session access.
  - Old CSRF keys (`pikachu`, `pikachu_csfr`, and `pikachu_csrf`) are still accepted, so current clients continue to work while the names are gradually normalized.

- `require_db.php`
  - Loads `.env` by project path and supports process environment variables.
  - Does not expose database exception details to the browser.
  - Whitelists profile columns inside the database layer.
  - Adds event-owner update/delete methods and an event-existence check.

- `mail_class.php`
  - Uses absolute library/template paths and environment-variable SMTP configuration.
  - Disables SMTP debug output, validates recipients, safely encodes names used in the mail template, and sets a timeout.

- `.gitignore` and `.env.example`
  - Keeps local environment files, vendor packages, and logs out of Git.
  - Provides safe example variable names without secrets.
  - The existing local `.env` was not overwritten because credentials must first be rotated at the database and SMTP provider.

- `templates/externe/authentification/inscription.php`
  - Uses French page metadata and includes a registration CSRF field.

- `templates/externe/authentification/connection.php`
  - Includes the shared bootstrap and a login CSRF field.

- `templates/externe/data_base_request/add_subscriber.php`
  - Validates date, email, names, member type, password length, and field sizes on the server.
  - Fixes the failed-registration destination to `/?dest=erreur_inscription`.

- `templates/externe/data_base_request/fetch_connexion.php`
  - Uses the shared secure session and CSRF workflow.
  - Regenerates the session identifier after a successful login and stores only the user ID as the initial session identity.

- `templates/externe/data_base_request/update_user_info.php`
  - Prevents duplicate emails and oversized passwords.
  - Returns the rotated CSRF token with successful JSON responses.

- `templates/externe/data_base_request/gestion_event/add_event.php`
  - Requires a refreshed authenticated bureau account, shared CSRF validation, and a strictly valid event date.

- `templates/externe/data_base_request/gestion_event/add_inscris.php`
  - Requires authentication/CSRF through shared helpers and verifies that the event exists before registration.

- `templates/externe/data_base_request/gestion_event/update_event.php`
- `templates/externe/data_base_request/gestion_event/suppress_event.php`
  - Add ownership checks directly to the database update/delete condition.

- `templates/externe/data_base_request/gestion_event/fetch_inscris.php`
  - Limits participant contact information to the owner of the selected event.

- `templates/externe/data_base_request/gestion_actualite/*.php`
- `templates/externe/data_base_request/gestion_goodies/*.php`
- `templates/externe/data_base_request/gestion_aide/*.php`
  - Use shared account and CSRF checks. News and goodies links must now be valid URLs.

- `templates/externe/data_base_request/gestion_offres/*.php`
  - Uses shared authentication/CSRF checks and validates offer title, description, type, URL, and specialties before database work.

- `templates/externe/data_base_request/gestion_etudiant/update_adherent.php`
- `templates/externe/data_base_request/fetch_membre.php`
  - Restricts member administration to `Président` and `Web Admin`, revalidated from the database.

- `templates/externe/data_base_request/fetch_aides.php`
- `templates/externe/data_base_request/fetch_actualites.php`
- `templates/externe/data_base_request/fetch_goodies.php`
- `templates/externe/data_base_request/fetch_emploie.php`
  - Use a consistent bootstrap and current-session validation where the endpoint is protected.

- `public/js/recherche_offre.js`
- `public/js/recherche_job_etudiant.js`
- `public/js/recherche_evenement.js`
  - Replace interpolated `innerHTML` with DOM elements and `textContent`.
  - Preserve the pre-existing query-string URLs used by these legacy scripts.

- `public/js/display_actualitev7.js`
  - Validates stored news links as HTTPS and adds safe new-tab link behavior.

## Not Yet Completed

- Credentials previously present in the local `.env` must be revoked and rotated at their providers. A source-code change cannot revoke already exposed passwords.
- PHP is not installed in the current environment, so PHP syntax and integration tests cannot run here.
- The repository still needs a database migration/schema, automated test suite, rate limiting, dependency management, and full CSS/accessibility consolidation described in `AUDIT_PLAN.md`.
