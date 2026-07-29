# Copilot / coding agent instructions — Relative Time Bundle

## Product

- Symfony bundle for **localized relative time** strings (`nowo-tech/relative-time-bundle`).
- Core: `RelativeTimeFormatter`, Twig `relative_time` / `ago`, catalogues under `NowoRelativeTimeBundle`.

## Conventions

- Keep PHPDoc and docs in **English**.
- Preserve deterministic unit selection (fixed seconds table) and document config/API changes in `docs/` + `CHANGELOG.md`.
- Seven locales with key parity: `en`, `es`, `it`, `fr`, `pt`, `de`, `nl`.
- Do not add features that belong in absolute date formatters or client-side live clocks unless specified.

## Quality

- Run / expect `make test`, `make validate-translations`, and static analysis before release.
- Add tests for new units, locales, or config options; maintain coverage targets from CI and README.
