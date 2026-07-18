# Security

## Scope

This bundle formats dates as human-readable relative strings. It does not persist data, expose HTTP endpoints, or handle authentication.

## Threat model

| Topic | Notes |
| ----- | ----- |
| Input parsing | Invalid date strings raise `InvalidArgumentException`; callers should validate user input before formatting. |
| DoS | Relative calculation uses integer arithmetic and fixed unit tables; no unbounded loops over user-controlled length. |
| XSS | Twig filters are not marked HTML-safe; escape output in templates as usual (`{{ date\|relative_time }}` is auto-escaped). |
| Secrets | No credentials in configuration. |
| i18n | Translation catalogues are static YAML shipped with the package; app overrides follow Symfony Translator precedence. |

Report vulnerabilities privately as described in [`.github/SECURITY.md`](../.github/SECURITY.md).
