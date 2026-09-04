# Audit and Change Plan

## 1. Purpose and Important Constraint

This document is the implementation plan for the current EEA association website. It is written from a security, correctness, maintainability, HTML, CSS, JavaScript, and deployment perspective.

### Route compatibility rule

The existing public URLs must not be renamed. In particular, keep:

- `/?dest=...`
- Additional query parameters such as `&id_event=...`, `&id_actu=...`, `&token=...`, and `&id_job=...`
- Existing route values such as `acceuil`, `connection`, `info_conn_v1`, `add_subscriber`, `update_data`, `manage_job`, and all other keys currently declared in `index.php`

The recommended refactoring may move code internally, but the controller must continue accepting the same query strings. If a new internal structure is introduced, `index.php` remains a compatibility router. Do not replace the URLs with a different routing system during this project.

This is an audit and plan, not an assertion that every item should be implemented in one commit. Work in the phases below and test after each phase.

## 2. Current Inventory

The application is a PHP website using:

- PHP templates that also contain HTML
- A single `?dest=` router in `index.php`
- PDO and MariaDB/MySQL
- PHP sessions for authentication
- JSON and form-data AJAX endpoints
- Custom CSS and browser JavaScript
- PHPMailer copied into `commun/PHPMailer/src`
- Environment variables read from `.env`

There is no SQL schema, migration directory, Composer manifest, automated test suite, or tracked deployment definition in the Git file list. The workspace displayed a `docker-compose.yaml`, but it is not present in the current filesystem/Git inventory. This must be checked before deployment documentation is written.

## 3. Priority Summary

### P0: Fix before exposing the site

1. Revoke and rotate the database and SMTP credentials currently exposed in `.env` and Git history.
2. Make sure `.env` is never committed and provide a safe example configuration.
3. Add server-side authorization to every write endpoint, including object ownership checks.
4. Correct the inconsistent CSRF parameter names and centralize CSRF verification.
5. Validate every input on the server, especially registration dates, URLs, email addresses, IDs, roles, and numeric prices.
6. Remove unsafe client-side HTML construction for database-controlled content.
7. Ensure production errors never reveal database paths, credentials, SQL, or stack traces.

### P1: Fix immediately after P0

1. Establish a single bootstrap file with reliable absolute paths.
2. Add security headers without relying on `unsafe-inline` as the permanent CSP solution.
3. Add rate limiting for login, registration, email checking, event registration, and public forms.
4. Define database constraints, indexes, foreign keys, uniqueness rules, and deletion behavior.
5. Add automated endpoint, authorization, XSS, CSRF, and validation tests.

### P2: Improve quality and user experience

1. Remove duplicated navigation and page initialization logic.
2. Consolidate CSS tokens and repeated layout rules.
3. Consolidate duplicate or obsolete JavaScript files.
4. Improve accessibility, responsive behavior, loading states, and error messages.
5. Add dependency management and deployment documentation.

## 4. Files To Change or Add

The following list names the application files that should be reviewed or changed. A file should only be changed when the corresponding phase requires it.

### 4.1 Core PHP and configuration

- `index.php`: preserve the route table while adding method-aware routing, consistent error responses, and safe controller loading.
- `commun/init.php`: become the one bootstrap for session settings, headers, CSRF helpers, authentication state, and error behavior.
- `require_db.php`: use a safe configuration loader, absolute paths, typed database methods, and generic production exceptions.
- `mail_class.php`: load mail settings safely, validate recipients, prevent debug output, and report mail failures without leaking credentials.
- `.gitignore`: keep `.env`, local logs, local uploads, and generated files ignored.
- `.env`: remove from version control, rotate all current secrets, and replace local copies with newly generated credentials.
- `.env.example` (new): document variable names only, with fake/example values and no secrets.
- `README.md`: document the real directory structure, required PHP extensions, environment setup, routes, security requirements, and deployment steps.
- `docker-compose.yaml` (only if it exists outside this checkout or is intentionally reintroduced): document web, PHP, and MariaDB services without hard-coded passwords.

### 4.2 Shared PHP components

- `commun/barre_navigation.php`
- `commun/barre_navigation_conn.php`
- `commun/barre_navigation_pres.php`
- `commun/barre_conn_etu.php`
- `commun/barre_conn_ancien.php`
- `commun/footer.php`
- `commun/acceuil_pres.php`
- `commun/propos_nous.php`
- `commun/contact_eea.php`
- `commun/mention_legale.php`
- `commun/uuid_v4.php`

These files should use shared escaping helpers, correct HTML language metadata, accessible links/buttons, and consistent absolute asset URLs. Navigation visibility is not authorization; backend checks must remain mandatory.

### 4.3 Authentication and registration

- `templates/externe/authentification/inscription.php`
- `templates/externe/authentification/connection.php`
- `templates/externe/authentification/confirmation_inscription.php`
- `templates/externe/authentification/succes_token.php`
- `templates/externe/authentification/success.php`
- `templates/externe/authentification/echec_inscription.php`
- `templates/externe/authentification/logout.php`
- `templates/externe/data_base_request/add_subscriber.php`
- `templates/externe/data_base_request/fetch_connexion.php`
- `templates/externe/data_base_request/fetch_same_email.php`
- `templates/externe/error.php`
- `templates/externe/mailer/mail_welcome.php`
- `templates/externe/mailer/mailer_test.php` (remove from production or protect it completely)

### 4.4 Read endpoints

- `templates/externe/data_base_request/fetch_actualites.php`
- `templates/externe/data_base_request/fetch_goodies.php`
- `templates/externe/data_base_request/fetch_events.php`
- `templates/externe/data_base_request/fetch_emploie.php`
- `templates/externe/data_base_request/fetch_membre.php`
- `templates/externe/data_base_request/fetch_aides.php`
- `templates/externe/data_base_request/gestion_event/fetch_inscris.php`

These endpoints need strict request validation, predictable JSON status codes, pagination or limits, and minimum-data responses. Participant and member endpoints expose personal information and therefore need especially strict role checks and field filtering.

### 4.5 Write endpoints

- `templates/externe/data_base_request/update_user_info.php`
- `templates/externe/data_base_request/gestion_etudiant/update_adherent.php`
- `templates/externe/data_base_request/gestion_actualite/add_actualite.php`
- `templates/externe/data_base_request/gestion_actualite/update_actualite.php`
- `templates/externe/data_base_request/gestion_actualite/delete_actualite.php`
- `templates/externe/data_base_request/gestion_aide/add_aide.php`
- `templates/externe/data_base_request/gestion_aide/suppress_aides.php`
- `templates/externe/data_base_request/gestion_event/add_event.php`
- `templates/externe/data_base_request/gestion_event/add_inscris.php`
- `templates/externe/data_base_request/gestion_event/update_event.php`
- `templates/externe/data_base_request/gestion_event/suppress_event.php`
- `templates/externe/data_base_request/gestion_offres/ajout_contrat.php`
- `templates/externe/data_base_request/gestion_offres/update_offre.php`
- `templates/externe/data_base_request/gestion_offres/suppress_offre.php`
- `templates/externe/data_base_request/gestion_goodies/add_goodies.php`
- `templates/externe/data_base_request/gestion_goodies/update_goodies.php`
- `templates/externe/data_base_request/gestion_goodies/suppress_goodies.php`

All write endpoints need the same order: bootstrap, HTTP method, content type, authentication, role/object authorization, CSRF, input validation, business rules, database transaction, response. The existing routes and payload field names can remain as compatibility aliases while the spelling is cleaned internally.

### 4.6 Feature pages

- `templates/externe/features/commun/accueil_interface.php`
- `templates/externe/features/commun/actualite_interface.php`
- `templates/externe/features/commun/evenements_interface.php`
- `templates/externe/features/commun/goodies_interface.php`
- `templates/externe/features/commun/recherche_job.php`
- `templates/externe/features/commun/gestion_offres.php`
- `templates/externe/features/commun/parametres.php`
- `templates/externe/features/etudiant/aides_interface.php`
- `templates/externe/features/etudiant/depot_job_etudiant.php`
- `templates/externe/features/ancien/depot_contrat.php`
- `templates/externe/features/bureau/ajout_event.php`
- `templates/externe/features/bureau/manage_event.php`
- `templates/externe/features/bureau/gestion_actualite.php`
- `templates/externe/features/bureau/gestion_goodies.php`
- `templates/externe/features/bureau/gestion_aides.php`
- `templates/externe/features/president/gestion_adherent.php`

The pages should keep their existing `dest` links, but authorization should be checked server-side before rendering and again in every endpoint. Repeated inline styles and repeated navigation-selection code should be moved to shared helpers/CSS.

### 4.7 JavaScript

- `public/js/connection_v1.js`
- `public/js/connect_interface.js`
- `public/js/inscription_v2.js`
- `public/js/changement_information.js`
- `public/js/switch_control.js`
- `public/js/aide_demande.js`
- `public/js/aide_demande_v2.js`
- `public/js/depot_offre.js`
- `public/js/recherche_offre.js`
- `public/js/recherche_job_etudiant.js`
- `public/js/fetch_offres_eea.js`
- `public/js/gestion_offres_eea_v1.js`
- `public/js/recherche_evenement.js`
- `public/js/new_event_fetch.js`
- `public/js/update_add_event.js`
- `public/js/gestion_event_eea.js`
- `public/js/display_actualitev7.js`
- `public/js/gestion_actualitesv1.js`
- `public/js/affichage_goodies.js`
- `public/js/gestions_goodies.js`
- `public/js/gestion_aide_js.js`
- `public/js/membres.js`
- `public/js/acceuil_page.js`
- `public/js/gestion_slidebar_1.js`
- `public/js/gestion_slide_bar_4.js`

These scripts need safe DOM rendering, one shared fetch helper, consistent CSRF handling, response-status checks, abortable requests, and graceful behavior when an element is absent. The two similarly named sidebar files and duplicate aide/login scripts should be consolidated only after verifying which pages load each one.

### 4.8 CSS

- `public/css/index.css`
- `public/css/logo_gestion.css`
- `public/css/footer.css`
- `public/css/barre_navigation_1.css`
- `public/css/barre_navigation_v2.css`
- `public/css/presentation_acceuil.css`
- `public/css/propos_nous.css`
- `public/css/connection_page.css`
- `public/css/connection_page_v1.css`
- `public/css/inscription_st_v1.css`
- `public/css/inscription_st_v2.css`
- `public/css/success.css`
- `public/css/parameter_user.css`
- `public/css/switch_inp.css`
- `public/css/change_statut.css`
- `public/css/modal.css`
- `public/css/style_carte.css`
- `public/css/actualite_style.css`
- `public/css/evenement_add.css`
- `public/css/fetch_event.css`
- `public/css/aide_style.css`
- `public/css/depot_offre.css`
- `public/css/recherche_job.css`
- `public/css/cross_change.css`

The CSS should be reviewed for duplicated selectors, inline-style conflicts, fixed elements that block mobile content, insufficient contrast, missing focus states, overflow problems, inconsistent breakpoints, and unnecessary `!important`. Do not change the visual identity or route structure as part of this cleanup.

### 4.9 Third-party library files

- `commun/PHPMailer/src/Exception.php`
- `commun/PHPMailer/src/OAuth.php`
- `commun/PHPMailer/src/OAuthTokenProvider.php`
- `commun/PHPMailer/src/DSNConfigurator.php`
- `commun/PHPMailer/src/PHPMailer.php`
- `commun/PHPMailer/src/POP3.php`
- `commun/PHPMailer/src/SMTP.php`

Do not manually edit vendor source to fix application behavior. Prefer Composer and a pinned, supported PHPMailer version. If the copy is retained temporarily, record its version, verify its integrity, and disable debug output.

## 5. Detailed Security Plan

### 5.1 Secrets and configuration

**Confirmed issue:** `.env` contains database and SMTP credentials and is tracked by Git despite `.gitignore`. `.gitignore` only prevents future untracked additions; it does not remove an already tracked file or erase Git history.

Changes:

1. Immediately revoke the displayed database password and SMTP password.
2. Change credentials at the database and mail provider, not only in the file.
3. Remove `.env` from Git tracking and rewrite history if the repository has been shared.
4. Check logs, mail accounts, database users, and CI variables for reuse of the exposed secrets.
5. Load configuration from process environment or a secret manager. Do not depend on the current working directory when reading `.env`.
6. Fail closed when a required variable is missing. Never use empty fallback credentials in production.
7. Create `.env.example` with names such as `DATABASE_HOST`, `DATABASE_USERNAME`, `DATABASE_PASSWORD`, `DATABASE_NAME`, `SMTP_HOST`, `SMTP_USERNAME`, and `SMTP_PASSWORD`.

Files: `.env`, `.gitignore`, `require_db.php`, `mail_class.php`, `.env.example`, `README.md`.

### 5.2 Sessions

`commun/init.php` already attempts to set `HttpOnly`, `Secure`, cookie-only, and `SameSite=Strict` settings. The settings must be applied before `session_start()` and checked for failure. The login endpoint currently duplicates session setup instead of using the same bootstrap.

Changes:

- Set `session.use_strict_mode=1`.
- Set a deliberate cookie path, domain, `SameSite`, `Secure`, and `HttpOnly` policy.
- Use `session_set_cookie_params()` before starting the session.
- Rotate the session ID after login and privilege changes.
- Regenerate the session ID when a member becomes an alumni or when roles change.
- Clear the cookie using the exact same parameters on logout.
- Do not put session identifiers in URLs.
- Consider idle timeout plus an absolute lifetime.
- Revalidate the user and role from the database for privileged requests; do not trust stale session role data.

Files: `commun/init.php`, `fetch_connexion.php`, `logout.php`, `update_adherent.php`, all protected feature pages.

### 5.3 CSRF

The project uses a session token, but the field is spelled in multiple ways: `pikachu_csrf`, `pikachu_csfr`, and `pikachu`. This can make valid actions fail and encourages endpoint-specific exceptions.

Changes:

- Keep accepting the existing payload names temporarily for compatibility, but normalize them immediately to one internal name.
- Generate a cryptographically random token with at least 32 random bytes.
- Add one shared `csrf_token()` and `require_csrf()` helper.
- Validate CSRF on every state-changing request, including registration if the application uses a session cookie, profile updates, event registration, and logout if logout is treated as POST.
- Prefer returning the new token in JSON after rotation, or stop rotating per request and use a session-bound token with a controlled lifetime. Current rotation can cause parallel browser requests to fail.
- Check `Origin`/`Referer` as an additional defense where appropriate.
- Never treat `SameSite=Strict` as a replacement for CSRF validation.

Files: `commun/init.php`, all write endpoint files, all forms and JavaScript files that send writes.

### 5.4 Authentication and registration

`add_subscriber.php` hashes passwords, which is good, but it trims the password, performs very little validation, and calls `$d->format()` even when date parsing failed. It also has no visible CSRF validation and redirects with `?erreur_inscription` without `dest=` on failure.

Changes:

- Never trim or silently alter passwords. Validate length and optionally a breach-resistant policy.
- Validate all required fields and allowed enum values server-side.
- Validate email with `filter_var`, normalize its case, and enforce a database unique constraint.
- Validate date strictly with `DateTime::createFromFormat()` and check parsing errors before calling `format()`.
- Validate phone numbers server-side; browser `intl-tel-input` is not security validation.
- Reject unexpected fields where useful and impose maximum lengths before database work.
- Add registration CSRF protection and rate limiting.
- Use a single generic response for existing email checks to reduce account enumeration.
- Store confirmation tokens hashed in the database where feasible, with expiry and one-use semantics.
- Handle duplicate email and mail delivery failure transactionally. Do not report successful registration if no confirmation path exists.
- Fix the failure redirect while preserving the intended route: use `/?dest=erreur_inscription`.
- Disable or remove `mailer_test.php` from production; it must never be publicly callable.

Files: `inscription.php`, `inscription_v2.js`, `add_subscriber.php`, `fetch_same_email.php`, `confirmation_inscription.php`, `fetch_connexion.php`, `logout.php`, `mail_welcome.php`, `success.php`, `echec_inscription.php`, `mailer_test.php`.

### 5.5 Authorization and IDOR prevention

The UI hides actions, but that is not access control. Several endpoints use `empty($user['membre_bureau'])`, which allows any non-empty bureau value instead of explicitly allowing a role. Event update/delete and actualite update/delete also need object-level checks. Updating a member must protect role escalation and self-lockout rules.

Define explicit policies:

- Public: read published news, public events, public jobs, and public goodies only.
- Authenticated member: update only their own profile; register only once per event.
- Student: submit their own aid request and permitted job type.
- Bureau member: manage content only if the business rule allows that exact bureau role.
- `Président` and `Web Admin`: manage members and roles.
- Owner: update/delete only their own job or event when that is the intended rule.

For every endpoint:

1. Load the current user from the database using the session ID.
2. Check the exact permitted role, not merely a non-empty field.
3. Load the target row.
4. Verify ownership or administrative permission in the same transaction where possible.
5. Perform the update/delete with an authorization condition in the SQL `WHERE` clause.
6. Return `404` or a non-revealing `403` consistently.

Files: every write endpoint, especially `update_adherent.php`, `update_event.php`, `suppress_event.php`, `update_actualite.php`, `delete_actualite.php`, `update_goodies.php`, `suppress_goodies.php`, `update_offre.php`, `suppress_offre.php`, `fetch_inscris.php`, `fetch_membre.php`, and `fetch_aides.php`.

### 5.6 XSS and output encoding

PHP pages generally use `htmlspecialchars`, but this must be consistent and context-aware. JavaScript files such as `recherche_offre.js` and `recherche_job_etudiant.js` build cards using `innerHTML` with database fields. A malicious title or description can therefore execute script in the browser.

Changes:

- Use `textContent` for text nodes.
- Build DOM elements with `document.createElement()` and set attributes explicitly.
- If a URL must be rendered, allow only `https:` (and an explicitly documented safe scheme), parse it with `URL`, and set `rel="noopener noreferrer"` for new tabs.
- Escape HTML attributes with `ENT_QUOTES` and an explicit UTF-8 encoding.
- Never render user content as trusted HTML unless it passes a strict sanitizer and the product explicitly supports formatting.
- Do not place raw database values in JavaScript strings, inline event handlers, CSS, or URLs.

Files: all templates that echo values, `public/js/recherche_offre.js`, `public/js/recherche_job_etudiant.js`, `public/js/recherche_evenement.js`, `public/js/display_actualitev7.js`, and any script that constructs markup.

### 5.7 Database safety and integrity

PDO prepared statements are used in many places, but `update_user_info()` interpolates `$field` into SQL. It is currently called with a whitelist, but the database layer should enforce the whitelist too. Dynamic filter lists should use bound placeholders instead of string-built integer lists.

Changes in `require_db.php`:

- Move the allowed-field map into the database/service layer or use explicit update methods.
- Use parameter placeholders for every dynamic value.
- Validate integer IDs as positive integers before the database layer.
- Add transactions and check affected-row counts for update/delete operations.
- Use foreign keys for member, event, offer, speciality, actualite, goodies, and aid relationships.
- Define unique constraints for email, event/email registration, and offer-speciality pairs.
- Decide whether deletes should cascade, be restricted, or soft-delete records.
- Select only required columns; avoid `SELECT *`, especially for member records containing password hashes.
- Add indexes for email, IDs, dates, ownership fields, and common search filters.
- Add pagination and maximum result limits to all list queries.
- Log detailed exceptions server-side with a correlation ID, but return only generic messages.

### 5.8 Email security

`mail_class.php` reads SMTP credentials and uses STARTTLS, which is appropriate if certificate verification is enabled by the runtime.

Changes:

- Pin and update PHPMailer through Composer.
- Configure SMTP host, port, encryption, timeout, and certificate verification explicitly.
- Keep SMTP debug disabled in production.
- Validate recipient addresses and encode names safely.
- Do not use a user-provided email as the envelope sender. Use a configured sender and `Reply-To` only after validation.
- Add SPF, DKIM, and DMARC records for the sending domain.
- Avoid exposing confirmation tokens in logs or analytics.

Files: `mail_class.php`, `templates/externe/mailer/mail_welcome.php`, `templates/externe/mailer/mailer_test.php`, new `composer.json` and `composer.lock` if Composer is adopted.

### 5.9 Security headers and CSP

`commun/init.php` sends useful headers, but the CSP allows `unsafe-inline`, many CDNs, and scripts without consistent integrity attributes. `X-XSS-Protection` is obsolete and should not be treated as modern protection.

Changes:

- Add `Strict-Transport-Security` only after HTTPS is confirmed everywhere.
- Add `X-Content-Type-Options: nosniff`.
- Add a suitable `frame-ancestors 'none'` or documented framing policy.
- Keep `Referrer-Policy` and `Permissions-Policy`.
- Replace inline scripts/styles with external files or nonces/hashes.
- Self-host or pin third-party libraries where practical.
- Add SRI to every remaining CDN stylesheet/script, including Font Awesome and icon libraries.
- Define `object-src 'none'`, `base-uri 'self'`, and `frame-ancestors`.
- Test CSP in report-only mode before enforcement.

Files: `commun/init.php` and every template that currently has inline `style`, inline JavaScript, or third-party assets.

## 6. PHP and Endpoint Implementation Plan

### Phase A: Bootstrap and response helpers

In `commun/init.php`, add shared helpers for:

- `json_response($data, $status)`
- `require_method('POST')`
- `require_authenticated_user()`
- `require_role([...])`
- `require_csrf()`
- `e($value)` for HTML escaping
- safe redirect generation that still emits `/?dest=...`

Use absolute paths based on `__DIR__` when including `require_db.php`, `mail_class.php`, and templates. Do not rely on the current working directory.

### Phase B: Authentication

Unify login and all protected endpoints on the bootstrap. Use generic authentication failures, rate limit by IP and account identifier, regenerate the session ID after successful login, and re-read the user record for every privileged operation.

### Phase C: Authorization

Implement explicit role constants and object ownership services. Apply them to all content management and member-management endpoints. Never accept role fields from a normal member profile update.

### Phase D: Validation and transactions

Create small validation functions for IDs, text lengths, URLs, email, dates, phone numbers, contract types, association types, and specialties. Use transactions for multi-table operations and return an error when an expected row was not changed.

### Phase E: Error handling and logging

Configure production PHP with display errors disabled and logging enabled. Add a global exception handler for HTML and JSON requests. Responses should contain stable error codes/messages, not exception text.

## 7. HTML Plan

Apply to all PHP files that output HTML:

- Change `<html lang="en">` to the actual page language, likely `fr`.
- Add a consistent charset and viewport declaration.
- Use one `<main>` landmark per page and logical heading order.
- Give every form an explicit action/method while preserving its existing `dest` URL.
- Give every input a label, suitable `autocomplete`, type, and server-side validation message.
- Do not use `disabled` for values that must be submitted; use readonly plus server-side identity when needed.
- Add visible focus states and keyboard-accessible modal behavior.
- Mark modal dialogs with appropriate ARIA attributes and return focus to the triggering button.
- Add `rel="noopener noreferrer"` to external links opened in a new tab.
- Add meaningful alt text to all informative images; use empty alt text for decorative images.
- Replace inline styles with semantic classes where possible.
- Ensure hidden CSRF inputs have quoted attributes and are not treated as authorization data.
- Do not expose email, phone, or participant data to users who do not need it.

Files: all feature pages, shared navigation/footer files, authentication pages, and mail template where HTML email compatibility permits.

## 8. JavaScript Plan

Create one small shared client utility, for example `public/js/app.js`, only if the deployment can load it on every relevant page. It should provide:

- `fetchJson()` that checks `response.ok`, content type, timeout, and JSON parsing
- CSRF retrieval from the existing hidden field names
- consistent error display
- safe DOM text insertion helpers
- disabled/loading button handling

Specific changes:

- Replace every database-controlled `innerHTML` interpolation with DOM construction and `textContent`.
- Use `encodeURIComponent` for all query parameter values while keeping the same parameter names.
- Do not trust browser validation; mirror it on the server.
- Abort stale search requests to prevent out-of-order results.
- Return and update CSRF tokens consistently after writes.
- Avoid putting sensitive data in local storage, session storage, or URLs.
- Add null checks to scripts loaded on multiple pages.
- Remove duplicate listeners and duplicate implementations after page-by-page verification.
- Provide accessible status messages with `aria-live`.

Files: all files in `public/js`, with highest priority for `recherche_offre.js`, `recherche_job_etudiant.js`, `recherche_evenement.js`, `gestion_event_eea.js`, `gestion_actualitesv1.js`, `gestions_goodies.js`, `membres.js`, `switch_control.js`, `connection_v1.js`, and `inscription_v2.js`.

## 9. CSS Plan

First preserve the current visual result, then refactor safely:

- Add a small set of CSS custom properties for colors, spacing, type scale, borders, and focus rings.
- Remove obsolete duplicate files only after confirming no page references them.
- Move all inline styles into the relevant stylesheet.
- Normalize `box-sizing`, body margins, form controls, buttons, tables, and links.
- Ensure minimum contrast for normal text and controls.
- Add `:focus-visible` styles that are not removed by resets.
- Use responsive table wrappers rather than forcing the whole page to overflow horizontally.
- Check fixed navigation, modal, and footer stacking contexts on small screens.
- Respect `prefers-reduced-motion` for modal and navigation animations.
- Avoid color-only status indicators.
- Test at 320px, 375px, 768px, 1024px, and desktop widths.
- Replace arbitrary colors such as `red`, `gray`, `antiquewhite`, and `aliceblue` with documented design tokens where appropriate.

Files: all files in `public/css`; highest priority is `index.css`, the two navigation stylesheets, `modal.css`, `change_statut.css`, `parameter_user.css`, `depot_offre.css`, `recherche_job.css`, `aide_style.css`, and page-specific mobile media queries.

## 10. Data Privacy and Legal Review

The application handles names, emails, telephone numbers, dates of birth, jobs, aid requests, and event participants. The legal pages should be reviewed for actual data practices, not only template text.

Files: `commun/mention_legale.php`, `commun/contact_eea.php`, `README.md`, and relevant feature pages.

Required decisions:

- Which personal data is required and why?
- How long are inactive accounts, aid requests, participant records, and job applications retained?
- Who can view participant emails and aid messages?
- How can a member export, correct, or delete their data?
- Are analytics, cookies, external fonts, and CDN requests legally documented?
- Is the university hosting and mail infrastructure correctly identified?

## 11. Database and Deployment Files Needed

The current repository has no visible SQL schema or migration files. Add these before changing database behavior:

- `database/migrations/001_initial_schema.sql` (new, if no external schema exists)
- `database/migrations/002_constraints_and_indexes.sql` (new)
- `database/seed/development.sql` (new, fake data only)
- `composer.json` and `composer.lock` (new, if Composer is adopted)
- `.env.example` (new)
- `phpunit.xml` (new, if PHPUnit is adopted)
- `tests/` (new)

Never put real names, passwords, email credentials, production tokens, or production participant data in these files.

## 12. Testing and Acceptance Criteria

### Automated checks

- PHP syntax check for every PHP file in an environment with the supported PHP version.
- Static analysis for undefined variables, invalid includes, unsafe SQL, and type errors.
- Dependency vulnerability scan.
- JavaScript linting and browser compatibility check.
- CSS linting and unused-selector review.
- Unit tests for validators, CSRF helpers, authorization policies, and URL validation.

### Security tests

- Anonymous users cannot call protected endpoints.
- A normal member cannot call bureau or president actions.
- A bureau member cannot change or delete another user's owned content unless explicitly allowed.
- Changing a request ID never grants access to another record.
- Missing, invalid, reused, and stale CSRF tokens are rejected.
- Malicious HTML in titles, descriptions, names, URLs, and query parameters is displayed as text.
- Login and registration are rate limited.
- Invalid dates, emails, URLs, prices, enum values, and oversized bodies are rejected.
- Error responses do not reveal SQL, filesystem paths, secrets, or stack traces.
- Participant and aid data are not returned to unauthorized roles.

### Functional regression tests

For every route currently in `index.php`, verify:

- The same `?dest=` URL still resolves.
- Existing `&` parameters still work.
- Existing success and error redirects still use the same route names.
- Login, logout, registration confirmation, profile updates, event registration, job management, event management, news management, goodies management, aid management, and member management still work.
- Two browser tabs do not randomly fail because a CSRF token was rotated by the other tab.

### Browser and accessibility checks

- Keyboard-only navigation works for menus, forms, tables, and modals.
- Screen readers receive labels, errors, loading states, and modal state changes.
- Pages work on mobile without clipped dialogs or inaccessible horizontal content.
- External resources failing to load do not break the primary forms.

## 13. Recommended Implementation Order

1. Rotate exposed secrets and remove `.env` from Git.
2. Add `.env.example` and document the actual environment.
3. Build the shared bootstrap, error handler, JSON response, escaping, and CSRF helpers.
4. Fix login, logout, registration, confirmation, and rate limiting.
5. Fix exact role and object authorization for every write/read endpoint.
6. Fix registration/date/URL/phone/price validation and database constraints.
7. Remove XSS-prone JavaScript rendering.
8. Fix headers, third-party loading, CSP, and HTTPS deployment settings.
9. Refactor HTML and CSS without changing routes or product behavior.
10. Consolidate duplicated JavaScript and navigation code.
11. Add schema/migrations, tests, CI checks, and deployment documentation.
12. Run the full regression and security checklist before production release.

## 14. Final Scope Rule

Do not change route names, query parameter names, or the `?dest=` architecture while implementing this plan. Internal code quality, security, HTML, CSS, JavaScript, database, and deployment improvements must remain compatible with the existing links and forms.
