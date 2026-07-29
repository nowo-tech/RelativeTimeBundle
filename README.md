# Relative Time Bundle

[![CI](https://github.com/nowo-tech/RelativeTimeBundle/actions/workflows/ci.yml/badge.svg)](https://github.com/nowo-tech/RelativeTimeBundle/actions/workflows/ci.yml)
[![Packagist Version](https://img.shields.io/packagist/v/nowo-tech/relative-time-bundle.svg?style=flat)](https://packagist.org/packages/nowo-tech/relative-time-bundle)
[![Packagist Downloads](https://img.shields.io/packagist/dt/nowo-tech/relative-time-bundle.svg)](https://packagist.org/packages/nowo-tech/relative-time-bundle)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)
[![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?logo=php)](https://php.net)
[![Symfony](https://img.shields.io/badge/Symfony-7.4%2B%20%7C%208.0%20%7C%208.1%2B-000000?logo=symfony)](https://symfony.com)
[![GitHub stars](https://img.shields.io/github/stars/nowo-tech/RelativeTimeBundle.svg?style=social&label=Star)](https://github.com/nowo-tech/RelativeTimeBundle)
[![Coverage](https://img.shields.io/badge/Coverage-100%25-brightgreen)](#tests-and-coverage)

> ⭐ **Found this useful?** Install from [Packagist](https://packagist.org/packages/nowo-tech/relative-time-bundle) and give it a star on [GitHub](https://github.com/nowo-tech/RelativeTimeBundle).

Symfony bundle that formats a `DateTime` (or timestamp / date string) as a **localized relative time** string: *just now*, *5 minutes ago*, *in 2 hours*, *hace un momento*, *hace 3 días*, and so on.

## Features

- **RelativeTimeFormatter** service with past and future wording
- **Twig** filters/functions: `relative_time` and `ago`
- **i18n** catalogues for `en`, `es`, `it`, `fr`, `pt`, `de`, `nl` (domain `NowoRelativeTimeBundle`)
- Configurable `just_now_threshold_seconds`, `max_unit`, locale and timezone defaults
- App-level translation overrides (REQ-I18N-001)

## Documentation

- [Installation](docs/INSTALLATION.md)
- [Configuration](docs/CONFIGURATION.md)
- [Usage](docs/USAGE.md)
- [Contributing](docs/CONTRIBUTING.md)
- [Changelog](docs/CHANGELOG.md)
- [Upgrading](docs/UPGRADING.md)
- [Release](docs/RELEASE.md)
- [Security](docs/SECURITY.md)
- [Engram](docs/ENGRAM.md)
- [Spec-driven development](docs/SPEC-DRIVEN-DEVELOPMENT.md)
- [GitHub Spec Kit](docs/SPEC-KIT.md)

### Additional documentation

- [GitHub Actions CI requirements](docs/GITHUB_CI.md)
- [Code of Conduct](docs/CODE_OF_CONDUCT.md)
- [Demo (Symfony 7 & 8)](demo/README.md) — run `make -C demo up-symfony8` from the bundle root.
- [Demo with FrankenPHP (development and production)](docs/DEMO-FRANKENPHP.md)

## Quick example

```php
use Nowo\RelativeTimeBundle\Service\RelativeTimeFormatter;

public function __construct(private RelativeTimeFormatter $relativeTime) {}

public function label(\DateTimeInterface $createdAt): string
{
    return $this->relativeTime->format($createdAt, 'es');
    // → "hace 5 minutos"
}
```

```twig
{{ entity.createdAt|relative_time }}
{{ entity.createdAt|ago('es') }}
{{ relative_time(entity.publishedAt, 'fr') }}
```

## Requirements

- PHP >= 8.2, < 8.6
- Symfony 7.4+ or 8.x (`symfony/translation`, Twig)
- Twig 3.8+ or 4.x

## Tests and coverage

| Language | Lines (approx.) | Command |
| --- | --- | --- |
| PHP | ~100% | `make test-coverage` |
| TypeScript | N/A | — |
| Python | N/A | — |

```bash
make test
make test-coverage
make validate-translations
make release-check
```

PHP coverage target is ~100% (currently ~100%; see [Release](docs/RELEASE.md)).

## Demo

```bash
make -C demo up-symfony8
# Demo started at: http://localhost:8008
```

FrankenPHP worker mode for production demos is documented in [DEMO-FRANKENPHP.md](docs/DEMO-FRANKENPHP.md).

## Version policy

The Composer package name is [`nowo-tech/relative-time-bundle`](https://packagist.org/packages/nowo-tech/relative-time-bundle). Source code and issues are in the GitHub repository [`nowo-tech/RelativeTimeBundle`](https://github.com/nowo-tech/RelativeTimeBundle).

We follow [Semantic Versioning](https://semver.org/). See [Changelog](docs/CHANGELOG.md) for release notes. Security support by major version is described in the [Security policy](.github/SECURITY.md#supported-versions).

## License

MIT. See [LICENSE](LICENSE).
