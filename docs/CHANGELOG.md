# Changelog

All notable changes to this project are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/).

## Table of contents

- [Unreleased](#unreleased)
- [1.0.1 - 2026-07-18](#101---2026-07-18)
  - [Changed](#changed)
- [1.0.0 - 2026-07-18](#100---2026-07-18)
  - [Added](#added)

## [Unreleased]

## [1.0.1] - 2026-07-18

### Changed

- REQ compliance: root only `README.md` (Code of Conduct moved to `docs/`); README Documentation order per REQ-DOCS-002; coverage section uses ~100%; `docs/SECURITY.md` adds Release security checklist (12.4.1); expanded Spec-driven development / Engram cross-links.

## [1.0.0] - 2026-07-18

### Added

- Initial public release of **Relative Time Bundle** (`nowo-tech/relative-time-bundle`).
- **`RelativeTimeFormatter`**: format `DateTimeInterface`, UNIX timestamps, or date strings as localized past/future relative time (`just now`, `5 minutes ago`, `in 2 hours`, …).
- **Twig** filters and functions: `relative_time`, `ago` (optional locale and reference `now`).
- **i18n** catalogues (domain `NowoRelativeTimeBundle`) with key parity for `en`, `es`, `it`, `fr`, `pt`, `de`, `nl`.
- **Configuration** (`nowo_relative_time`): `just_now_threshold_seconds`, `max_unit`, `translation_domain`, `default_locale`, `default_timezone`.
- Symfony Flex recipe, demos for Symfony 7.4 and 8.1 (FrankenPHP), Spec Kit baseline, and Nowo maintainer tooling (CI, `make release-check`, `validate-translations`).

[Unreleased]: https://github.com/nowo-tech/RelativeTimeBundle/compare/v1.0.1...HEAD
[1.0.1]: https://github.com/nowo-tech/RelativeTimeBundle/compare/v1.0.0...v1.0.1
[1.0.0]: https://github.com/nowo-tech/RelativeTimeBundle/releases/tag/v1.0.0
