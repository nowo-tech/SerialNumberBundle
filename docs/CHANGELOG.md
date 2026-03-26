# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/).

## Table of contents

- [Unreleased](#unreleased)
  - [Changed](#changed)
- [1.0.4 - 2026-03-13](#104---2026-03-13)
  - [Fixed](#fixed)
- [1.0.3 - 2026-03-13](#103---2026-03-13)
  - [Added](#added)
- [1.0.2 - 2026-03-13](#102---2026-03-13)
  - [Added](#added-1)
  - [Changed](#changed-1)
- [1.0.1 - 2026-03-12](#101---2026-03-12)
  - [Added](#added-1)
  - [Fixed](#fixed-1)
- [1.0.0 - 2026-03-12](#100---2026-03-12)
  - [Added](#added-2)
  - [Security](#security)

## [Unreleased]

### Changed

- **Documentation**: Corrected mask examples in README and USAGE (last four characters of `FAC-2025-01-00042` are `0042`). README requirements aligned with `composer.json` (PHP `< 8.6`). Makefile `help` lists `release-check-demos` as part of `release-check`. **DEMO-FRANKENPHP.md**: demo `bundles.php` example includes Twig Inspector (as in the repo demos). **SECURITY.md** rewritten in English to match the rest of the docs.

## [1.0.4] - 2026-03-13

### Fixed

- **Demo Symfony 7:** `composer update` was blocked by Packagist security advisories on transitive packages. Added `config.audit.block-insecure: false` in the demo’s `composer.json` (local development only; see demo README).
- **Demo Symfony 7:** After resolving to Symfony 7.1.x, `cache:clear` failed because routing files were referenced as `.php` (Symfony 7.3+). Updated `config/routes/framework.yaml` and `config/routes/web_profiler.yaml` to use `.xml` routes for compatibility with Symfony 7.1 (`errors.xml`, `wdt.xml`, `profiler.xml`).

## [1.0.3] - 2026-03-13

### Added

- [DEMO-FRANKENPHP.md](DEMO-FRANKENPHP.md): documentation for running demos with FrankenPHP in development and production (Caddyfile.dev, worker mode, ports). Linked from README under Additional documentation.

## [1.0.2] - 2026-03-13

### Added

- Test structure aligned with standards: tests under `tests/Unit/` and `tests/Integration/` (§7.1.1). Integration tests verify bundle wiring in a compiled container (generator and Twig extension with default and custom config).
- Composer scripts `test-unit` and `test-integration` to run each suite separately. See [CONTRIBUTING](CONTRIBUTING.md).

### Changed

- Existing tests moved into `tests/Unit/` with namespace `Nowo\SerialNumberBundle\Tests\Unit\...`. Integration tests live in `tests/Integration/` with namespace `...\Tests\Integration\...`.
- `phpunit.xml.dist` defines two testsuites: `Unit` and `Integration`.

## [1.0.1] - 2026-03-12

### Added

- GitHub Actions workflow `release.yml`: creates a GitHub Release (with “Latest”) when a tag `v*` is pushed; uses tag message and `docs/CHANGELOG.md` for release notes.

### Fixed

- Demo Symfony 7: added missing `bin/console` so `composer install` (and `cache:clear`) succeeds.
- Test expectation in `testMaskSerialNumberMultiCharMaskUsesOnlyFirstChar` (correct mask length for 14‑char serial).

## [1.0.0] - 2026-03-12

### Added

- Initial release: `SerialNumberGenerator` service, Twig function `serial_number`, Twig filter `serial_number_mask`.
- Configuration: `mask_char`, `mask_visible_last` (see [Configuration](CONFIGURATION.md)).
- Security: DoS protections for masking and generation (max serial length 2048, max id padding 32, single-char mask, non-negative visible last). See [Security](SECURITY.md).

### Security

- Limit serial length to 2048 characters when masking to prevent memory exhaustion.
- Cap id padding to 32 characters in `SerialNumberGenerator::generate()`.
- Enforce single-character mask in config and in Twig mask filter (multi-char uses first character only).
- Treat negative `visibleLast` in `serial_number_mask` as zero to prevent huge `str_repeat` output.

[Unreleased]: https://github.com/nowo-tech/serial-number-bundle/compare/v1.0.4...HEAD
[1.0.4]: https://github.com/nowo-tech/serial-number-bundle/releases/tag/v1.0.4
[1.0.3]: https://github.com/nowo-tech/serial-number-bundle/releases/tag/v1.0.3
[1.0.2]: https://github.com/nowo-tech/serial-number-bundle/releases/tag/v1.0.2
[1.0.1]: https://github.com/nowo-tech/serial-number-bundle/releases/tag/v1.0.1
[1.0.0]: https://github.com/nowo-tech/serial-number-bundle/releases/tag/v1.0.0
