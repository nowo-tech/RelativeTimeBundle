# Configuration

Root key: `nowo_relative_time`.

| Option | Type | Default | Description |
| ------ | ---- | ------- | ----------- |
| `just_now_threshold_seconds` | int (≥0) | `10` | Absolute difference below which the `just_now` message is used |
| `max_unit` | enum | `year` | Largest unit: `year`, `month`, `week`, `day`, `hour`, `minute`, `second` |
| `translation_domain` | string | `NowoRelativeTimeBundle` | Symfony translation domain |
| `default_locale` | string\|null | `null` | Locale when none is passed to `format()`; otherwise translator locale |
| `default_timezone` | string\|null | `null` | Timezone used when parsing date strings |

## Example

```yaml
nowo_relative_time:
    just_now_threshold_seconds: 30
    max_unit: day
    translation_domain: NowoRelativeTimeBundle
    default_locale: es
    default_timezone: Europe/Madrid
```

## Supported locales (REQ-I18N-002)

Bundled catalogues (key parity with `en`):

`en`, `es`, `it`, `fr`, `pt`, `de`, `nl`

Validate with:

```bash
make validate-translations
```
