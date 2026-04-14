# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/).

## Table of contents

- [Unreleased](#unreleased)
- [1.0.6 - 2026-04-14](#106---2026-04-14)
  - [Added](#added)
  - [Changed](#changed)
- [1.0.5 - 2026-03-30](#105---2026-03-30)
  - [Changed](#changed-1)
- [1.0.4 - 2026-03-13](#104---2026-03-13)
  - [Fixed](#fixed)
- [1.0.3 - 2026-03-13](#103---2026-03-13)
  - [Added](#added-1)
- [1.0.2 - 2026-03-13](#102---2026-03-13)
  - [Added](#added-2)
  - [Changed](#changed-2)
- [1.0.1 - 2026-03-12](#101---2026-03-12)
  - [Added](#added-3)
  - [Fixed](#fixed-1)
- [1.0.0 - 2026-03-12](#100---2026-03-12)
  - [Added](#added-4)
  - [Security](#security)

## [Unreleased]

## [1.0.6] - 2026-04-14

### Added

- **Repository tooling:** `.cursorignore`, Cursor rules under `.cursor/rules/`, Dependabot, scheduled stale issue workflow, optional PR title lint workflow, `FUNDING.yml`, and Copilot instructions for contributors.
- **Demos (FrankenPHP images):** PCOV extension so `composer test-coverage` works inside the Symfony 7 and 8 demo containers.
- **Demos:** `config/packages/test/framework.yaml` with `framework.test: true` for functional tests; PHPUnit `<source>` / `<coverage>` configuration aligned with the bundle layout; `KERNEL_CLASS` / `APP_ENV` for the test kernel (`phpunit.xml.dist` and `tests/bootstrap.php`).

### Changed

- **Bundle (dev):** Refreshed root `composer.lock` for development dependencies.
- **Scrutinizer-CI:** PHP 8.2 build, exclude `demo/*` and `tests/*` from analysis, and align install/coverage with the bundle (see `.scrutinizer.yml`).
- **Demo Symfony 8:** Docker image installs `ext-intl`; demo `composer.json` replaces `symfony/polyfill-intl-normalizer` and `symfony/polyfill-intl-grapheme` (native intl in the container). `docker-compose`: extra DNS resolvers, `mem_limit`, Composer memory/parallelism env vars; Dockerfile entrypoint retries `composer install --prefer-dist`; Makefile uses `--prefer-dist` and the same Composer env vars; updated `composer.lock`.
- **Demo Symfony 7:** `docker-compose` DNS entries for Docker/WSL resolution issues; default published port in Compose when `PORT` is unset is **8007** (matches `.env.example`); updated `composer.lock`.
- **Demo Symfony 8:** Default published port in Compose when `PORT` is unset is **8008** (matches `.env.example`).
- **Demos:** PHPUnit `failOnRisky="false"` and `failOnWarning="false"` so Symfony’s functional test harness does not fail `make release-check` on benign risky-handler notices (coverage is still generated).

## [1.0.5] - 2026-03-30

### Changed

- **Documentation and metadata:** GitHub URLs now use the real repository slug `nowo-tech/SerialNumberBundle` (the previous `nowo-tech/serial-number-bundle` path returned 404). The Composer package name remains `nowo-tech/serial-number-bundle`. Updated root `composer.json` (`homepage`, `support.*`) and demo `composer.lock` metadata for the path package.
- **README:** Added [Version policy](https://github.com/nowo-tech/SerialNumberBundle#version-policy) (package name vs repo, SemVer, links to changelog and security policy). GitHub stars badge uses the correct repo slug.
- **Demos:** `demo/README.md` default ports aligned with `.env.example` (Symfony 7 → 8007, Symfony 8 → 8008). [DEMO-FRANKENPHP.md](DEMO-FRANKENPHP.md) port section updated to match.
- **Bug report template:** Links to version policy and security policy instead of a non-existent `#version-information` anchor.
- **Documentation (carry-over from pre-release notes):** Mask examples in README/USAGE; README PHP constraint aligned with `composer.json` (`< 8.6`); Makefile `help` documents `release-check-demos` under `release-check`; DEMO-FRANKENPHP `bundles.php` example includes Twig Inspector; SECURITY.md in English.

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

[Unreleased]: https://github.com/nowo-tech/SerialNumberBundle/compare/v1.0.6...HEAD
[1.0.6]: https://github.com/nowo-tech/SerialNumberBundle/compare/v1.0.5...v1.0.6
[1.0.5]: https://github.com/nowo-tech/SerialNumberBundle/releases/tag/v1.0.5
[1.0.4]: https://github.com/nowo-tech/SerialNumberBundle/releases/tag/v1.0.4
[1.0.3]: https://github.com/nowo-tech/SerialNumberBundle/releases/tag/v1.0.3
[1.0.2]: https://github.com/nowo-tech/SerialNumberBundle/releases/tag/v1.0.2
[1.0.1]: https://github.com/nowo-tech/SerialNumberBundle/releases/tag/v1.0.1
[1.0.0]: https://github.com/nowo-tech/SerialNumberBundle/releases/tag/v1.0.0
