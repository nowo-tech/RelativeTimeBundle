# Installation

## Table of contents

- [Composer](#composer)
- [Configuration](#configuration)
- [Translation overrides (REQ-I18N-001)](#translation-overrides-req-i18n-001)
- [Next steps](#next-steps)

## Composer

```bash
composer require nowo-tech/relative-time-bundle
```

With Symfony Flex, the recipe registers the bundle and copies `config/packages/nowo_relative_time.yaml`.

Without Flex, add to `config/bundles.php`:

```php
Nowo\RelativeTimeBundle\NowoRelativeTimeBundle::class => ['all' => true],
```

## Configuration

```yaml
# config/packages/nowo_relative_time.yaml
nowo_relative_time:
    just_now_threshold_seconds: 10
    max_unit: year
    translation_domain: NowoRelativeTimeBundle
    # default_locale: es
    # default_timezone: Europe/Madrid
```

The bundle requires `symfony/framework-bundle`: the formatter uses the `translator` service, and FrameworkBundle is what loads the bundle catalogues. Ensure the translator is enabled (`framework.translator`, on by default in Flex apps).

## Translation overrides (REQ-I18N-001)

Create files in the application under `translations/` using the same domain:

```yaml
# translations/NowoRelativeTimeBundle.es.yaml
relative_time:
  just_now: 'hace nada'
```

Missing keys fall back to the bundle catalogues.

## Next steps

- [Configuration](CONFIGURATION.md)
- [Usage](USAGE.md)
