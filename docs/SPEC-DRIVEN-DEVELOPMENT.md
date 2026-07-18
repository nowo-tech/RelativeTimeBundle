# Spec-driven development

## Purpose

This document describes **what Relative Time Bundle guarantees**, how behavior is proven, and how Spec Kit / Engram fit into the maintainer workflow.

## Product layers

1. **Integrator contract** — Packagist API: `RelativeTimeFormatter`, Twig `relative_time` / `ago`, config `nowo_relative_time`, domain `NowoRelativeTimeBundle`.
2. **Baseline spec** — [`specs/001-baseline/spec.md`](../specs/001-baseline/spec.md) with user stories and `FR-*`.
3. **Repo `REQ-*`** — Makefile, demos, docs, and CI aligned with the Nowo bundles checklist.

## User stories (summary)

| ID | Intent |
| -- | ------ |
| US-01 | Format dates in PHP |
| US-02 | Format dates in Twig |
| US-03 | Override translations in the app |
| US-04 | Keep seven-locale key parity |

## Bundle functional scope

**Goal:** Localized relative time strings for Symfony/Twig.

**In scope:** past/future units, just-now threshold, i18n catalogues, Twig helpers, Flex recipe, dual demos.

**Non-goals:** absolute formatting, live client ticking, full calendar linguistics beyond unit buckets.

## Public API (Packagist contract)

- `Nowo\RelativeTimeBundle\Service\RelativeTimeFormatter::format()` / `ago()`
- Twig filters/functions `relative_time`, `ago`
- Config alias `nowo_relative_time`

## Validation

| Command | Proves |
| ------- | ------ |
| `make test` / `make test-coverage` | Unit + integration behavior |
| `make phpstan` / `make cs-check` / `make rector-dry` | Static quality |
| `make validate-translations` | REQ-I18N-002 key parity |
| `make release-check` | Full pre-release gate |

## Relationship with Engram

See [`ENGRAM.md`](ENGRAM.md) for Cursor MCP memory hooks. Spec Kit operator manual: [`SPEC-KIT.md`](SPEC-KIT.md).

## Spec Kit workflow (summary)

1. Specify / clarify in `specs/`
2. Plan and tasks via Spec Kit skills
3. Implement against `FR-*`
4. Keep `code-inventory.md` at 100% of `src/`
5. Run `make release-check` before tagging
