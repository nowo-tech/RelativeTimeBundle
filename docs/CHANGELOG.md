# Changelog

All notable changes to this project are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/).

## Table of contents

- [Unreleased](#unreleased)
- [1.0.6 - 2026-09-25](#106---2026-09-25)
- [1.0.5 - 2026-08-24](#105---2026-08-24)
- [1.0.4 - 2026-08-19](#104---2026-08-19)
- [1.0.3 - 2026-08-18](#103---2026-08-18)
- [1.0.2 - 2026-08-03](#102---2026-08-03)
- [1.0.1 - 2026-07-18](#101---2026-07-18)
- [1.0.0 - 2026-07-18](#100---2026-07-18)

## [Unreleased]


## [1.0.6] - 2026-09-25

### Added

- FrankenPHP worker audit for sticky Kernel (`FRANKENPHP_RESET_KERNEL=false`): [FRANKENPHP-WORKER-AUDIT.md](FRANKENPHP-WORKER-AUDIT.md) — **100% compatible** under Scenario B; no remediations required.
- Spec **FR-WORKER-001** / **US-05** documenting worker + long-lived kernel guarantees.
- Unit regression `testSharedInstanceDoesNotLeakLocaleAcrossConsecutiveCalls` (shared formatter, no kernel reset).

### Changed

- PHPStan now uses `nowo-tech/phpstan-frankenphp` `ruleset-worker-strict.neon` (includes worker rules).
- Docs: README / UPGRADING / RELEASE / baseline spec and code inventory updated for the worker audit.
- Dev lockfiles refreshed via `composer-sync` / demo path reference (release-check).

### Notes

- **No API or configuration changes** for integrators. Continue requiring `nowo-tech/relative-time-bundle: ^1.0`.

[1.0.6]: https://github.com/nowo-tech/RelativeTimeBundle/releases/tag/v1.0.6

## [1.0.5] - 2026-08-24

### Changed

- Raise minimum PHP to **8.2** and sync README badge (REQ-SF-001).
- **Makefile:** detect Docker Compose V2 (REQ-MAKE-010).
- **QA:** add `phpstan-frankenphp` extension (REQ-CS-005).
- **README:** FrankenPHP-friendly worker-mode banner (REQ-DOCS-017).
- **Docs:** PHP-FIG PSR evaluation (REQ-CS-007).

### Notes

- **No API or configuration changes** for integrators unless noted above.

[1.0.5]: https://github.com/nowo-tech/RelativeTimeBundle/releases/tag/v1.0.5

## [1.0.4] - 2026-08-19

### Security

- **CI:** run `composer audit --locked` after dependency install (REQ-SEC / P3).

[1.0.4]: https://github.com/nowo-tech/RelativeTimeBundle/releases/tag/v1.0.4

## [1.0.3] - 2026-08-18

### Changed

- **Demos:** pin `nowo-tech/hot-reload-bundle` to `^1.4` with FrankenPHP Mercure/`hot_reload` (`dev`/`test` only).
- **Demos:** Symfony 8 only; Symfony 6/7 demo apps removed.

[1.0.3]: https://github.com/nowo-tech/RelativeTimeBundle/releases/tag/v1.0.3

## [1.0.2] - 2026-08-03

### Changed

- Demos: FrankenPHP mode via `FRANKENPHP_MODE` (`worker` default / `classic`) and shared `docker/entrypoint.sh` (REQ-DEMO-010); no longer toggled by `APP_ENV`.
- Makefile soft-includes for optional monorepo `update-deps` helpers (REQ-MAKE-009).
- Demo aggregate `release-check` delegates to each demo’s `release-check` target; `ensure-up` creates `.env` from `.env.example` when missing.
- Code of Conduct moved to repository root `CODE_OF_CONDUCT.md` (README / CONTRIBUTING links updated).
- Docs: README structure, DEMO-FRANKENPHP (`FRANKENPHP_MODE`), SECURITY AI audit (REQ-SEC-004), TOC sections on maintainer docs.
- Dev dependencies: `rector/rector` 2.6.0 and Symfony 7.4.15 lock bumps from `composer-sync`.

### Notes

- **No API or configuration changes** for integrators. Continue requiring `nowo-tech/relative-time-bundle: ^1.0`.

[1.0.2]: https://github.com/nowo-tech/RelativeTimeBundle/releases/tag/v1.0.2

## [1.0.1] - 2026-07-18

### Changed

- REQ compliance: root only `README.md` (Code of Conduct moved to `docs/`); README Documentation order per REQ-DOCS-002; coverage section uses ~100%; `docs/SECURITY.md` adds Release security checklist (12.4.1); expanded Spec-driven development / Engram cross-links.

[1.0.1]: https://github.com/nowo-tech/RelativeTimeBundle/compare/v1.0.0...v1.0.1

## [1.0.0] - 2026-07-18

### Added

- Initial public release of **Relative Time Bundle** (`nowo-tech/relative-time-bundle`).
- **`RelativeTimeFormatter`**: format `DateTimeInterface`, UNIX timestamps, or date strings as localized past/future relative time (`just now`, `5 minutes ago`, `in 2 hours`, …).
- **Twig** filters and functions: `relative_time`, `ago` (optional locale and reference `now`).
- **i18n** catalogues (domain `NowoRelativeTimeBundle`) with key parity for `en`, `es`, `it`, `fr`, `pt`, `de`, `nl`.
- **Configuration** (`nowo_relative_time`): `just_now_threshold_seconds`, `max_unit`, `translation_domain`, `default_locale`, `default_timezone`.
- Symfony Flex recipe, demos for Symfony 7.4 and 8.1 (FrankenPHP), Spec Kit baseline, and Nowo maintainer tooling (CI, `make release-check`, `validate-translations`).

[Unreleased]: https://github.com/nowo-tech/RelativeTimeBundle/compare/v1.0.6...HEAD
[1.0.0]: https://github.com/nowo-tech/RelativeTimeBundle/releases/tag/v1.0.0
