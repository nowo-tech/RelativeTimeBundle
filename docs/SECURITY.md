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

## Release security checklist (12.4.1)

Before tagging a release, confirm:

| Item | Notes |
|------|--------|
| **SECURITY.md** | This document is current and linked from the README where applicable. |
| **`.gitignore` and `.env`** | `.env` and local env files are ignored; no committed secrets. |
| **No secrets in repo** | No API keys, passwords, or tokens in tracked files. |
| **Recipe / Flex** | Default recipe templates do not ship production secrets. |
| **Input / output** | Date inputs validated; Twig output auto-escaped (filters not HTML-safe). |
| **Dependencies** | `composer audit` run; issues triaged. |
| **Logging** | Bundle does not log secrets or PII. |
| **Cryptography** | Not used. |
| **Permissions / exposure** | No routes or admin UI; service/Twig only. |
| **Limits / DoS** | Fixed unit table; invalid dates fail fast with exceptions. |

Record confirmation in the release PR or tag notes.
