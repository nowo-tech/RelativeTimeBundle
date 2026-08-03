# Upgrading

## Table of contents

- [From 1.0.1 to 1.0.2](#from-101-to-102)
- [From 1.0.0 to 1.0.1](#from-100-to-101)
- [From nothing → 1.0.0](#from-nothing--100)

## From 1.0.1 to 1.0.2

No breaking changes to the bundle API or configuration.

- **Integrators:** No application code or config changes required. Continue requiring `nowo-tech/relative-time-bundle: ^1.0`.
- **Contributors / demos:** FrankenPHP mode is controlled by `FRANKENPHP_MODE` (`worker` default, `classic` for hot-reload). Recreate demo containers after changing it. Code of Conduct is at repository root `CODE_OF_CONDUCT.md`.

## From 1.0.0 to 1.0.1

No breaking changes to the bundle API or configuration.

- **Integrators:** No application code or config changes required.
- **Docs only:** Code of Conduct lived under `docs/CODE_OF_CONDUCT.md` in 1.0.1 (moved back to repo root in 1.0.2). Release security checklist (12.4.1) lives in [SECURITY.md](SECURITY.md).

## From nothing → 1.0.0

First public release. There is no previous Packagist version to migrate from.

### Install

```bash
composer require nowo-tech/relative-time-bundle
```

With Symfony Flex, the recipe registers the bundle and copies `config/packages/nowo_relative_time.yaml`.

### Integrator checklist

1. Ensure `symfony/translation` is enabled (`framework.translator`).
2. Review defaults in [Configuration](CONFIGURATION.md) (`just_now_threshold_seconds`, `max_unit`, locale/timezone).
3. Use the service or Twig helpers as in [Usage](USAGE.md).
4. Optionally override strings under `translations/NowoRelativeTimeBundle.<locale>.yaml` (REQ-I18N-001).
