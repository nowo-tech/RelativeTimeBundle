# FrankenPHP worker mode audit (kernel not reset between requests)

| Field | Value |
|-------|-------|
| Package | `nowo-tech/relative-time-bundle` (`symfony-bundle`) |
| Audited revision | `v1.0.6` / working tree after this audit |
| Audit date | 2026-09-25 |
| Target runtime | FrankenPHP **worker** with **`FRANKENPHP_RESET_KERNEL=false`** (sticky Kernel / “Friendly Worker”) |
| Method | Manual review of every file under `src/` (formatter service, Twig extension, DI extension, configuration, `Resources/config/services.yaml`, translation catalogues) + PHPStan classic + worker-strict |
| **Verdict** | ✅ **100% compatible** under Scenario B (`reset_kernel: false`) — no remediations required for sticky Kernel |

## Execution model assumed

FrankenPHP worker mode boots the Symfony kernel once per worker and serves many requests with the same container. This audit assumes the **strict** host contract used by Nowo “Friendly Worker” / kernel-isolation E2E:

| Host flag | Meaning |
|-----------|---------|
| `FRANKENPHP_MODE=worker` | Worker keeps the app in memory |
| **`FRANKENPHP_RESET_KERNEL=false`** | Kernel is **not** rebooted; Scenario **B** below |
| `FRANKENPHP_WORKER_NUM=1` | Single worker (isolation tests) |

Two scenarios are evaluated:

- **A — kernel not rebooted, `services_resetter` still runs:** services tagged `kernel.reset` (or implementing `ResetInterface`) are reset between requests. Typical default when `FRANKENPHP_RESET_KERNEL` is truthy / Runtime `worker=2`-style reset.
- **B — no reset at all (`FRANKENPHP_RESET_KERNEL=false`):** nothing is reset; any per-request state kept in a shared service leaks into the next request.

A bundle that is safe under **B** is safe under **A** and under classic mode / PHP-FPM.

## Summary

| Area | Status | Notes |
|------|--------|-------|
| Mutable state in shared services | ✅ | `RelativeTimeFormatter` and `RelativeTimeTwigExtension` only have `readonly` constructor properties and class constants |
| Static properties / `static` locals | ✅ | None |
| `ResetInterface` / `kernel.reset` coverage | ✅ N/A | Nothing to reset |
| Request / user / locale captured in services | ✅ | Locale is resolved per call (`$locale` argument, `default_locale`, or `translator->getLocale()`); "now" is computed per call |
| Superglobals, `$_ENV`, `putenv`, `ini_set`, `setlocale`, timezone | ✅ | None written; `date_default_timezone_get()` is only read per call (W-01) |
| Doctrine / EntityManager | ✅ N/A | No persistence |
| Output, headers, `exit`, shutdown functions | ✅ | None |
| Resources (files, sockets, cURL) held open | ✅ | None |
| Memory growth across requests | ✅ | No caches or accumulating arrays |
| Blocking I/O and timeouts | ✅ N/A | No I/O; pure computation + translator lookup |
| Third-party static state | ✅ | Only Symfony Translator / Twig / DI |
| PHPStan FrankenPHP rulesets | ✅ | `ruleset-classic.neon` + `ruleset-worker-strict.neon` in `phpstan.neon.dist` |

Worker demo: `demo/symfony8/docker/frankenphp/Caddyfile` has a `worker` block; `Caddyfile.dev` runs classic mode.

## Services reviewed

| Service | Shared | Mutable state | Scenario A | Scenario B |
|---------|--------|---------------|------------|------------|
| `Nowo\RelativeTimeBundle\Service\RelativeTimeFormatter` | yes (private) | none (`readonly` translator + config scalars) | ✅ | ✅ |
| `Nowo\RelativeTimeBundle\Twig\RelativeTimeTwigExtension` (`twig.extension`) | yes | none (`readonly` formatter) | ✅ | ✅ |

`Configuration`, `NowoRelativeTimeExtension` and `NowoRelativeTimeBundle` only run at container compile time. `DateTimeImmutable` / `DateTimeZone` objects are created per call and never stored in a property. Translation catalogues are static resources.

## Findings

No open findings that block Scenario B.

### W-01 — Locale and timezone fall back to process-level values (Info)

- **Where:** `src/Service/RelativeTimeFormatter.php` (`$locale ?? $this->defaultLocale ?? $this->translator->getLocale()`), and `date_default_timezone_get()` when parsing numeric timestamps without `default_timezone`.
- **Worker impact:** both values are read at call time, so the bundle never caches a locale or timezone from a previous request. The translator locale is set per request by Symfony's `LocaleAwareListener`, which runs as an event listener and does not depend on `kernel.reset`. The only risk is outside the bundle: if application code calls `date_default_timezone_set()` or `Translator::setLocale()` outside the normal request flow, that value survives into later requests in the same worker and this formatter would use it.
- **Recommendation:** set `default_timezone` explicitly in `nowo_relative_time` config for deterministic output, and pass the locale explicitly (`|relative_time(app.request.locale)`) when rendering outside a normal HTTP request (e.g. emails from a Messenger worker).

Every result depends only on the arguments and on the current time, and "now" is created per call, so there is no stale "now" captured at boot. Unit regression: `RelativeTimeFormatterTest::testSharedInstanceDoesNotLeakLocaleAcrossConsecutiveCalls`.

## Usage recommendations in worker mode

- No special configuration or reset hook is needed for this bundle when `FRANKENPHP_RESET_KERNEL=false`.
- Do not call `date_default_timezone_set()` per request in application code; configure `default_timezone` or `date.timezone` once instead.
- Subclasses or decorators of `RelativeTimeFormatter` must stay stateless (or implement `ResetInterface`) to keep this verdict; in particular, do not memoize "now" or the resolved locale in a property.
- Keeping Symfony’s `services_resetter` enabled in the **application** remains recommended for framework-owned state (Doctrine identity map, security token storage, etc.); this bundle does not depend on it.

## Re-audit triggers

Re-run this audit when a change adds: properties to `RelativeTimeFormatter` or the Twig extension, a result or "now" cache, a request/locale listener, or any runtime call to `setlocale()` / `date_default_timezone_set()` / `Locale::setDefault()`.
