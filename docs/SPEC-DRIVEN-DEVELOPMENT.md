# Spec-driven development

## Purpose

This document describes **what Relative Time Bundle guarantees**, how behavior is proven, and how Spec Kit / Engram fit into the maintainer workflow.

## Product layers

1. **GitHub Spec Kit baseline** — [`specs/001-baseline/`](../specs/001-baseline/) and operator manual [`SPEC-KIT.md`](SPEC-KIT.md).
2. **Integrator contract (product behavior)** — Packagist API: `RelativeTimeFormatter`, Twig `relative_time` / `ago`, config `nowo_relative_time`, domain `NowoRelativeTimeBundle`.
3. **Repo `REQ-*` traceability** — Makefile, demos, docs, and CI aligned with the Nowo bundles checklist ([`BUNDLES_FULL_SPECS_DETAILS.md`](../../BUNDLES_FULL_SPECS_DETAILS.md) in the workspace).

Mechanical proof is **PHPUnit** + **PHPStan** (no separate executable spec language).

## User stories

| ID | Intent | Scope / docs |
| -- | ------ | ------------ |
| US-01 | Format dates in PHP | `RelativeTimeFormatter` · [USAGE](USAGE.md) |
| US-02 | Format dates in Twig | `relative_time` / `ago` · [USAGE](USAGE.md) |
| US-03 | Override translations in the app | Domain `NowoRelativeTimeBundle` · [INSTALLATION](INSTALLATION.md) |
| US-04 | Keep seven-locale key parity | `make validate-translations` · [CONFIGURATION](CONFIGURATION.md) |

## Bundle functional scope

**Goal:** Localized relative time strings for Symfony/Twig.

**In scope:** past/future units, just-now threshold, i18n catalogues, Twig helpers, Flex recipe, dual demos.

**Explicit non-goals:** absolute date formatting, client-side live ticking, full calendar linguistics beyond unit buckets.

**Not part of the Packagist API:** `demo/` applications (local integration samples only).

## Public API (Packagist contract)

| Artifact | Responsibility |
| --- | --- |
| `RelativeTimeFormatter::format()` / `ago()` | Localized relative string from a date input |
| Twig filters/functions `relative_time`, `ago` | Template helpers |
| Config alias `nowo_relative_time` | Threshold, max unit, domain, locale, timezone |

## Validating the functional spec

| Command | Proves |
| ------- | ------ |
| `make test` / `make test-coverage` | Unit + integration behavior (~100% PHP lines) |
| `make phpstan` / `make cs-check` / `make rector-dry` | Static quality |
| `make validate-translations` | REQ-I18N-002 key parity |
| `make release-check` | Full pre-release gate |

Behavior changes **require** tests under `tests/`.

## Requirement identifiers (Makefile / demos)

| ID | Location | Meaning |
| -- | -------- | ------- |
| REQ-MAKE-001 | root `Makefile` | `ensure-up`, `release-check` |
| REQ-MAKE-004 | root `Makefile` | `validate-translations` |
| REQ-MAKE-008 | root + demo Makefiles | `update-deps` |
| REQ-GIT-001 | `.githooks/`, `.scripts/check-no-cursor-coauthor.sh` | No Cursor co-author trailers |
| REQ-I18N-002 | `src/Resources/translations/`, validate script | Seven locales, key parity |
| REQ-DEMO-005 / REQ-DEMO-009 | `demo/*/Makefile`, `demo/*/docker-compose.yml` | Demo `up` + DNS for Packagist |

When scripted behavior changes, update or add the matching `REQ-*` comment/anchor.

## Contributor workflow

1. Clarify intent against baseline `FR-*` / user stories.
2. Implement with tests under `tests/Unit` and `tests/Integration`.
3. Keep `specs/001-baseline/code-inventory.md` at 100% of `src/`.
4. Update integrator docs when behavior or config changes.
5. Run `make release-check` before tagging.

## Relationship with Engram

[`ENGRAM.md`](ENGRAM.md) covers Cursor MCP / org compliance pointers. This file owns **product behavior + local REQ-* traceability**; Engram does not replace it.

## GitHub Spec Kit

Full operator manual: [`SPEC-KIT.md`](SPEC-KIT.md). Baseline: [`specs/001-baseline/spec.md`](../specs/001-baseline/spec.md) + [`code-inventory.md`](../specs/001-baseline/code-inventory.md).

### Spec Kit workflow (summary)

1. Specify / clarify in `specs/`
2. Plan and tasks via Spec Kit skills
3. Implement against `FR-*`
4. Keep inventory at 100% of `src/`
5. Run `make release-check` before tagging

## See also

- [SPEC-KIT.md](SPEC-KIT.md)
- [USAGE.md](USAGE.md)
- [CONFIGURATION.md](CONFIGURATION.md)
- [CONTRIBUTING.md](CONTRIBUTING.md)
- [RELEASE.md](RELEASE.md)
- [DEMO-FRANKENPHP.md](DEMO-FRANKENPHP.md)
