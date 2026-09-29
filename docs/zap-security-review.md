# ZAP security configuration review

Reviewed 22 September 2026 against the existing Laravel 12 application. Existing
uncommitted work was preserved. No changes were made to `.env`, secrets, database
structure, AES-256-CBC messaging, HMAC-SHA256 logging, controllers, or role rules.

## Changes and ZAP mapping

| Alert | Finding and action |
| --- | --- |
| 1. CSP Header Not Set | Added a global enforcing CSP using the resource origins actually present in Blade. See compatibility limits below. |
| 2. Missing Anti-clickjacking Header | Added `X-Frame-Options: DENY` and CSP `frame-ancestors 'none'`. The application does not use embedded frames. |
| 3. Sub Resource Integrity Attribute Missing | Added independently verified SHA-512 integrity and `crossorigin="anonymous"` to all 16 Font Awesome 6.4.0 stylesheet references. Left dynamic Tailwind and Google Fonts URLs unchanged. |
| 4. Big Redirect Detected | Reproduced the current ZAP size heuristic on three live routes. All bodies exactly match Symfony's normal redirect template; no protected page content was included. Redirect behavior is unchanged. |
| 5. Cookie No HttpOnly Flag | Made the session cookie unconditionally HttpOnly. Preserved Laravel's intentionally JavaScript-readable `XSRF-TOKEN` cookie. The original ZAP cookie name was not supplied, so compare its evidence before classifying the original alert. |
| 6. Cross-Domain JavaScript Source File Inclusion | Tailwind's external script is intentional. CSP permits that specific HTTPS origin. This remains a third-party dependency risk, not a reason to delete a required script. |
| 7. X-Powered-By | Live responses contain `X-Powered-By: PHP/8.2.12`; loaded PHP configuration has `expose_php=On`. Requires the server setting below. No Laravel header-removal workaround was added. |
| 8. X-Content-Type-Options Missing | Added `X-Content-Type-Options: nosniff`. |
| 9. Session Management Response Identified | ZAP classifies this as informational: session identification is expected. No change to conceal sessions. |
| 10. User Agent Fuzzer (Systemic) | ZAP classifies this as informational. No application user-agent authorization branch was found. Tests confirm several user agents still redirect unauthenticated requests to login. A changed response alone does not establish an access bypass. |

The same middleware adds `Referrer-Policy: strict-origin-when-cross-origin` to
limit cross-origin referrer detail, and
`Permissions-Policy: camera=(), microphone=(), geolocation=()` because those APIs
are not used. File upload selection and FileReader photo previews still work.

## CSP compatibility and remaining limits

- Same-origin pages, CSS, images, forms, and fetch requests are allowed.
- Scripts may load from `https://cdn.tailwindcss.com`.
- Styles may load from `https://fonts.googleapis.com` and `https://cdnjs.cloudflare.com`.
- Fonts may load from `https://fonts.gstatic.com` and `https://cdnjs.cloudflare.com`.
- `data:` images support existing FileReader previews.
- Inline scripts, event handlers, inline styles, and Tailwind-generated styles
  remain allowed with `'unsafe-inline'`. Consequently, this is a compatibility
  policy, not a strict XSS defense. ZAP may correctly report this limitation.
- `unsafe-eval`, wildcard external origins, automatic HTTP-to-HTTPS upgrading,
  and HSTS were not added. Local HTTP must remain usable.
- Google login is a top-level link and server redirect, not a Google script or
  iframe embedded in these templates. This policy does not block that navigation.
- The current views do not use `@vite`. A future switch to a Vite development
  server will require reviewing its script and websocket origins.

Tailwind's current unversioned CDN response can change, and Google Fonts CSS can
vary with user agent. Attaching a fixed hash to these URLs could break styling.
For production, a separately tested move to locally compiled Tailwind and static
assets would remove this dependency and allow a stricter CSP. The existing npm
configuration uses Tailwind 4 while the Blade CDN is the Tailwind 3 Play CDN;
replacing one with the other without UI testing would be inappropriate here.

Font Awesome source:
`https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css`

Verified by hashing downloaded bytes and comparing cdnjs API metadata on the
review date:

```text
sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==
```

## Sessions and HTTPS

The live HTTP session cookie is `laravel-session` with HttpOnly and SameSite=Lax,
without Secure. `XSRF-TOKEN` is SameSite=Lax without HttpOnly, as Laravel intends
for JavaScript CSRF integrations. It is not the authentication session ID.

`SESSION_SECURE_COOKIE` remains configurable. When unset/null, Symfony marks the
cookies Secure for HTTPS requests; automated tests verify both session and CSRF
cookies. Explicit false prevents that automatic behavior, so HTTPS-only hosting
should set `SESSION_SECURE_COOKIE=true` in its deployment configuration. For
`http://192.168.0.199:8000`, leave it unset/null or false. Keep
`SESSION_SAME_SITE=lax` to allow the session on Google's top-level GET callback.
Do not use Strict merely to silence a scanner.

If TLS ends at a reverse proxy, configure Laravel to trust only the actual proxy
addresses and forwarded protocol headers. No proxy topology was supplied, so no
blanket trust of arbitrary forwarded headers was introduced. No `.env` was read
or modified by this review; normal application/test execution still loads it.

## Redirect investigation

Requests were made without authentication and without following redirects:

| Route | Status | Body length | Current ZAP predicted size | Body content |
| --- | --- | --- | --- | --- |
| `/auth/google` | 302 | 1358 | 562 | Exact standard redirect template, pointing to Google |
| `/student/dashboard` | 302 | 346 | 325 | Exact standard redirect template, pointing to login |
| `/mpp/dashboard` | 302 | 346 | 325 | Exact standard redirect template, pointing to login |

ZAP's current rule compares body length with `Location` length plus 300. Symfony
repeats the escaped destination URL four times in a small HTML redirect page,
which exceeds this heuristic. The Google URL contains only the expected
`client_id`, `redirect_uri`, `scope`, `response_type`, and `prompt` parameters.
It contains neither a client secret nor an access token. No private listing,
message, or user-record content was present in these redirect bodies.

These requests reproduce the condition but are not the original ZAP report.
Confirm its exact URL, method, authentication state, and body before marking its
specific instance a false positive. Other authenticated or error redirects were
not exhaustively replayed. Do not globally empty redirect bodies to hide alerts.

## Server configuration for X-Powered-By

`php --ini` reports `C:\xampp83\php\php.ini` for the current CLI PHP. For
`php artisan serve`, edit that file outside this patch:

```ini
expose_php = Off
```

Stop and restart the existing Artisan development server afterward. Apache or
PHP-FPM may load a different php.ini; change the serving SAPI's configuration and
restart Apache or PHP-FPM as appropriate. Do not publish a phpinfo page to check
this. Verify the actual HTTP response afterward.

Laravel middleware does not cover static files served directly by the web
server, proxy-generated errors, or failures before Laravel boots. If ZAP flags
those responses, apply appropriate headers at the serving layer as well. Avoid
duplicate/conflicting CSP headers. No server files were changed in this patch.

## Commands after applying the changes

Run in the project directory:

```powershell
php artisan config:clear
php artisan view:clear
php artisan test
```

No migrations, key generation, dependency updates, or npm build are required.
Do not add `config:cache` as part of this change: the existing EncryptionHelper
reads `env('AES_KEY')` directly, so config caching needs a separate review to
preserve access to the key. Do not rotate keys; existing ciphertext depends on them.

After stopping the existing development server, restart it if needed:

```powershell
php artisan serve --host=192.168.0.199 --port=8000
```

Check headers without printing Set-Cookie values:

```powershell
curl.exe -s -D - -o NUL http://192.168.0.199:8000/ | Select-String '^(HTTP/|Content-Security-Policy:|X-Frame-Options:|X-Content-Type-Options:|Referrer-Policy:|Permissions-Policy:|X-Powered-By:)'
```

## Verification

Baseline: 28 tests / 441 assertions passed before edits. After the changes,
all 34 tests / 627 assertions passed; both new PHP files also passed Laravel
Pint's formatting check. Six added tests cover
header delivery, both roles' route restrictions, HTTP/HTTPS cookie flags, the
real Socialite provider's authorization URL using synthetic test configuration,
standard redirect content, and user-agent authentication behavior. Existing
tests exercise message encryption/decryption, audit HMAC validation/tampering,
listing editing and creation, saved listings, reporting, moderation, and suspension.

Live HTTP checks confirmed headers, HTTP cookie flags, and redirect bodies. A
browser was not available through this session's browser tool, so visual/CSP
runtime checks and a real Google account login remain manual checks:

1. Open DevTools Network and Console, then hard reload the login page. Confirm
   all six headers on the document and no blocked CSP/SRI resources. Check fonts,
   icons, images, layout, and responsive views.
2. Sign in with a Student account, log out, then sign in with an MPP account.
   Verify the Google account chooser, callback, correct dashboards, session
   persistence, and logout. Test that each role cannot access the other role's
   protected pages. Check invalid-domain and suspended-account rejection.
3. As a Student, create and edit a listing, preview and upload photos, change
   availability, save/unsave listings, and submit a report. Exercise dropdowns,
   photo viewers, forms, and validation errors while checking the Console.
4. Use two Student sessions to send/read/reply to messages and inspect read
   receipts and unread counters. Confirm old encrypted messages still display.
5. As MPP, review reports, hide/restore a listing, suspend/unsuspend a test
   student, and inspect audit entries and their integrity status. Check that the
   suspended test account is denied on a fresh login.
6. In DevTools Application/Cookies, verify the session is HttpOnly and Lax.
   Secure should be absent on local HTTP and present on the HTTPS deployment.
   Leave the XSRF-TOKEN value private and do not force HttpOnly onto it.

## Repeat ZAP and compare

1. Save/export the original scan session and report before starting a fresh
   session. Use the same ZAP/add-on versions, rule thresholds, scan policy,
   context, route coverage, and user roles for a meaningful comparison.
2. Scope the context to `http://192.168.0.199:8000` (for example,
   `http://192\.168\.0\.199:8000/.*`). Keep Google and CDN domains outside the
   active-scan scope. Do not actively scan the OAuth provider or its callback
   with real authorization codes.
3. Browse through ZAP's proxied browser, manually sign in through Google, and
   exercise the checklist. Capture separate guest, Student, and MPP sessions;
   confirm protected requests return real pages rather than login redirects.
4. Allow passive scanning to finish. Repeat the original spider/active scan
   only against a disposable test dataset: active scanning may submit forms,
   create reports, send messages, or trigger moderation actions. Check that
   authentication remains valid throughout each authenticated scan.
5. Export the new report. Compare alert ID + URL + method + parameter/cookie
   name and response evidence, not just totals. Header findings should disappear
   on Laravel responses. Font Awesome SRI findings should disappear; dynamic
   resource SRI, intentional Tailwind inclusion, inline CSP, XSRF-TOKEN, normal
   redirect-size findings, and informational findings may remain.
6. X-Powered-By should disappear only after the PHP/server setting and restart.
   Investigate remaining static-file or proxy responses at that layer. Record
   evidence-based accepted findings/false positives per instance instead of
   globally disabling security rules. Keep reports private; they can contain
   session tokens and application data.

## Exact files changed by this review

New files:

- `app/Http/Middleware/SecurityHeaders.php`
- `tests/Feature/SecurityHeadersTest.php`
- `docs/zap-security-review.md`

Middleware registration and session configuration:

- `bootstrap/app.php`
- `config/session.php`

Only the Font Awesome link attributes were changed in these templates:

- `resources/views/welcome.blade.php`
- `resources/views/student/create-listings.blade.php`
- `resources/views/student/dashboard.blade.php`
- `resources/views/student/listings.blade.php`
- `resources/views/student/listings/edit.blade.php`
- `resources/views/student/messages.blade.php`
- `resources/views/student/profile.blade.php`
- `resources/views/student/room-details.blade.php`
- `resources/views/student/saved-listings.blade.php`
- `resources/views/mpp/audit-logs.blade.php`
- `resources/views/mpp/dashboard.blade.php`
- `resources/views/mpp/listings.blade.php`
- `resources/views/mpp/reports.blade.php`
- `resources/views/mpp/student-reports.blade.php`
- `resources/views/mpp/student-show.blade.php`
- `resources/views/mpp/students.blade.php`

## References

- [ZAP redirect alert](https://www.zaproxy.org/docs/alerts/10044-1/) and
  [its size heuristic](https://github.com/zaproxy/zap-extensions/blob/main/addOns/pscanrules/src/main/java/org/zaproxy/zap/extension/pscanrules/BigRedirectsScanRule.java)
- [ZAP session identification](https://www.zaproxy.org/docs/alerts/10112/)
- [ZAP user-agent fuzzer](https://www.zaproxy.org/docs/alerts/10104/)
- [PHP expose_php configuration](https://www.php.net/manual/en/ini.core.php)
- [Font Awesome 6.4.0 on cdnjs](https://cdnjs.com/libraries/font-awesome/6.4.0)
- [Tailwind Play CDN](https://v3.tailwindcss.com/docs/installation/play-cdn)
- [Subresource Integrity](https://developer.mozilla.org/en-US/docs/Web/Security/Defenses/Subresource_Integrity)
