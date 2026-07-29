# Code inventory — 100% traceability

**Baseline spec**: [`spec.md`](spec.md)  
**Package**: `nowo-tech/relative-time-bundle`  
**Last audited**: 2026-07-18

This file proves that **every production source artifact** under `src/` is referenced by the baseline specification. PHPUnit under `tests/` is out of scope unless promoted in the spec.

## PHP classes (`src/**/*.php`)

| Source file | Spec section | Requirement IDs |
| --- | --- | --- |
| `NowoRelativeTimeBundle.php` | Bundle entry | FR-BUNDLE-001 |
| `DependencyInjection/Configuration.php` | Config tree | FR-CFG-001 |
| `DependencyInjection/NowoRelativeTimeExtension.php` | DI extension + parameters | FR-CFG-002 |
| `Service/RelativeTimeFormatter.php` | Relative formatting engine | FR-FMT-001, FR-FMT-002, FR-FMT-003 |
| `Twig/RelativeTimeTwigExtension.php` | Twig API | FR-TWIG-001 |

## Symfony config (`src/Resources/config/`)

| Source file | Spec section | Requirement IDs |
| --- | --- | --- |
| `Resources/config/services.yaml` | Formatter + Twig wiring | FR-DI-001 |

## Translations (`src/Resources/translations/`)

| Source file | Spec section | Requirement IDs |
| --- | --- | --- |
| `NowoRelativeTimeBundle.en.yaml` | Reference catalogue | FR-I18N-001 |
| `NowoRelativeTimeBundle.es.yaml` | Locale parity | FR-I18N-001 |
| `NowoRelativeTimeBundle.it.yaml` | Locale parity | FR-I18N-001 |
| `NowoRelativeTimeBundle.fr.yaml` | Locale parity | FR-I18N-001 |
| `NowoRelativeTimeBundle.pt.yaml` | Locale parity | FR-I18N-001 |
| `NowoRelativeTimeBundle.de.yaml` | Locale parity | FR-I18N-001 |
| `NowoRelativeTimeBundle.nl.yaml` | Locale parity | FR-I18N-001 |

## Coverage summary

| Category | Files | Mapped |
| --- | ---: | ---: |
| PHP classes | 5 | 5 |
| YAML config | 1 | 1 |
| Translation catalogues | 7 | 7 |
| **Total `src/` artifacts** | **13** | **13** |
