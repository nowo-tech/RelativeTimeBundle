# Baseline specification — Relative Time Bundle

**Package:** `nowo-tech/relative-time-bundle`  
**Status:** Implemented (v1.0.6)  
**Last updated:** 2026-09-25

## Product summary

Format a date/time as a localized relative string (past and future) for Symfony applications, with Twig helpers and seven bundled locales. Compatible with FrankenPHP **worker** mode including **`FRANKENPHP_RESET_KERNEL=false`** (long-lived kernel, no reboot between requests).

## User stories

| ID | Story | Acceptance |
| -- | ---- | ---------- |
| US-01 | As a developer, I can format a `DateTime` in PHP as “5 minutes ago” | `RelativeTimeFormatter::format()` returns localized string |
| US-02 | As a Twig author, I can use `|relative_time` / `|ago` | Filters and functions registered |
| US-03 | As an integrator, I can override strings per locale | App `translations/NowoRelativeTimeBundle.<locale>.yaml` wins |
| US-04 | As a maintainer, catalogues stay in key parity | `make validate-translations` exits 0 |
| US-05 | As an operator on FrankenPHP worker with sticky Kernel, formatting stays request-safe | Shared formatter holds no per-request state; consecutive calls with different locales/`now` do not leak (see FR-WORKER-001) |

## Scope

### In scope

- Past (`ago`) and future (`in`) relative messages
- Units: second, minute, hour, day, week, month, year
- `just_now` threshold
- Twig filters/functions
- Config: threshold, max unit, domain, default locale/timezone
- Locales: en, es, it, fr, pt, de, nl
- FrankenPHP worker / `reset_kernel: false` request safety for bundle-owned services

### Explicit non-goals

- Calendar “yesterday/tomorrow” special cases beyond unit buckets
- Absolute date formatting (use Symfony Intl / Twig `date` filter)
- Client-side live ticking (Stimulus); server-side formatting only
- Replacing `knplabs/knp-time-bundle` feature-for-feature
- Host-level EntityManager / security reset (application-owned)

## Functional requirements

- **FR-BUNDLE-001**: Bundle class MUST expose `NowoRelativeTimeExtension` via `getContainerExtension()`.
- **FR-CFG-001**: Config MUST define `just_now_threshold_seconds`, `max_unit`, `translation_domain`, `default_locale`, `default_timezone`.
- **FR-CFG-002**: Extension MUST publish `%nowo_relative_time.*%` parameters matching FR-CFG-001.
- **FR-DI-001**: `services.yaml` MUST wire `RelativeTimeFormatter` and `RelativeTimeTwigExtension` (Twig tag).
- **FR-FMT-001**: Formatter MUST accept `DateTimeInterface|string|int|float|null`, return `''` for null/empty, throw `InvalidArgumentException` for invalid strings.
- **FR-FMT-002**: Absolute diff below threshold MUST use `relative_time.just_now`.
- **FR-FMT-003**: Diff MUST select the largest unit ≤ `max_unit` and translate `relative_time.ago.*` or `relative_time.in.*` with `%count%`.
- **FR-TWIG-001**: Filters/functions `relative_time` and `ago` MUST delegate to the formatter.
- **FR-I18N-001**: Ship catalogues for en/es/it/fr/pt/de/nl with key parity (REQ-I18N-002).
- **FR-WORKER-001**: **FrankenPHP worker / `reset_kernel: false`:** `RelativeTimeFormatter` and `RelativeTimeTwigExtension` MUST keep only `readonly` constructor config (no mutable request state, no result/"now"/locale cache). Locale and reference `now` MUST be resolved per call. No `ResetInterface` is required. Documented in [`docs/FRANKENPHP-WORKER-AUDIT.md`](../../docs/FRANKENPHP-WORKER-AUDIT.md).

## Validation

```bash
make qa
make validate-translations
make test-coverage
make release-check
```

## Engram

Operator notes and memory hooks: [`docs/ENGRAM.md`](../../docs/ENGRAM.md).
