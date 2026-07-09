# Upgrading

## Table of contents

- [From 1.0.8 to 1.0.9](#from-108-to-109)
- [From 1.0.7 to 1.0.8](#from-107-to-108)
- [From 1.0.6 to 1.0.7](#from-106-to-107)
- [From 1.0.5 to 1.0.6](#from-105-to-106)
- [From 1.0.4 to 1.0.5](#from-104-to-105)
- [From 1.0.3 to 1.0.4](#from-103-to-104)
- [From 1.0.2 to 1.0.3](#from-102-to-103)
- [From 1.0.1 to 1.0.2](#from-101-to-102)
- [From 1.0.0 to 1.0.1](#from-100-to-101)
- [From 0.x / pre-1.0 to 1.0](#from-0x--pre-10-to-10)
  - [Behaviour and limits in 1.0](#behaviour-and-limits-in-10)

## From 1.0.8 to 1.0.9

No breaking changes to the bundle API or configuration.

- **Documentation only for integrators:** New GitHub Spec Kit baseline and maintainer docs ([SPEC-KIT.md](SPEC-KIT.md), [`specs/001-baseline/`](../specs/001-baseline/)). No application code or config changes required to upgrade from Packagist.
- **Demo Symfony 7 (Docker):** Rebuild the FrankenPHP image after pulling (`docker compose build` in `demo/symfony7`) so `ext-intl` is available, matching the Symfony 8 demo.
- **Maintainers:** When changing production code under `src/`, update [`specs/001-baseline/spec.md`](../specs/001-baseline/spec.md) and [`code-inventory.md`](../specs/001-baseline/code-inventory.md) per [SPEC-KIT.md](SPEC-KIT.md).

## From 1.0.7 to 1.0.8

No breaking changes to the bundle API or configuration.

- **Demos:** Symfony 7 demo requires **7.4.***; Symfony 8 demo requires **8.1.***. After pulling, run `composer update` in each demo directory, or from the bundle root run `make update-deps` (Docker) to refresh the bundle and both demos.
- **Maintainers:** CI now tests Symfony 6.4, 7.0, 7.4, 8.0, and 8.1 across PHP 8.1–8.5. Use `make update-deps` instead of ad-hoc `composer update` when refreshing locks.

## From 1.0.6 to 1.0.7

No breaking changes to the bundle API or configuration. Demo-only: updated Composer lock files and the auto-generated Symfony 8 `config/reference.php` after dependency refresh. If you use the demos from Git, run `composer install` in each demo directory (or `make install` / `make up`) after pulling.

## From 1.0.5 to 1.0.6

No breaking changes to the bundle API or configuration.

- **Symfony 8 demo (Docker):** Rebuild the FrankenPHP image after upgrading (`docker compose build` in `demo/symfony8`) so `ext-intl` and PCOV are available. The demo replaces the intl polyfills in `composer.json`; **do not** run that demo’s `composer install` on a host PHP without `ext-intl` unless you remove those `replace` entries (the Docker image is the supported path).
- **Docker Compose default host ports:** If you start a demo **without** a `.env` file, Symfony 7 now publishes **8007** and Symfony 8 **8008** by default (same as each `.env.example`). If you relied on the old implicit default **8001** for both, set `PORT` explicitly in `.env`.
- **CI / maintainers:** `make release-check` expects demo functional tests and coverage; see [CHANGELOG](CHANGELOG.md) for PHPUnit and PCOV updates.

## From 1.0.4 to 1.0.5

No breaking changes. Documentation and repository metadata only: correct GitHub URLs (`nowo-tech/SerialNumberBundle`), demo default ports in docs (8007 / 8008 per `.env.example`), README version policy, and bug-report template links. The Composer package name is still `nowo-tech/serial-number-bundle`.

## From 1.0.3 to 1.0.4

No breaking changes. Demo Symfony 7 only: Composer audit config and routing file extensions (`.xml`) so the demo runs with Symfony 7.1.x and `composer update` / `cache:clear` succeed.

## From 1.0.2 to 1.0.3

No breaking changes. Documentation only: new [DEMO-FRANKENPHP.md](DEMO-FRANKENPHP.md) for FrankenPHP demo setup.

## From 1.0.1 to 1.0.2

No breaking changes. Tests are now under `tests/Unit/` and `tests/Integration/`. If you extended or ran tests by path, update to the new structure; `composer test` and `make test` still run all tests. New scripts: `composer test-unit`, `composer test-integration`.

## From 1.0.0 to 1.0.1

No breaking changes. This release adds the GitHub release workflow and fixes the Symfony 7 demo and a test expectation.

## From 0.x / pre-1.0 to 1.0

No breaking changes for the initial 1.0 release. If you were using an alpha/beta, ensure:

- **API:** `SerialNumberGenerator::generate()` signature is unchanged: `(array $context, string $pattern, int|string $id, ?int $idPadding = null)`.
- **Twig:** `serial_number(context, pattern, id, padding?)` and `serial_number_mask(serial, visibleLast?, maskChar?)`.
- **Configuration:** Keys remain `mask_char`, `mask_visible_last`.

### Behaviour and limits in 1.0

- **`mask_char`** must be a single character in config; longer strings are rejected. In Twig, if you pass a multi-character string as `maskChar`, only the first character is used.
- **`serial_number_mask`:** Serial strings longer than 2048 characters are truncated to 2048 before masking. Negative `visibleLast` is treated as 0 (fully masked).
- **`serial_number` / `generate()`:** `idPadding` is capped at 32; larger values are treated as 32.

These limits are for security (DoS prevention). See [Security](SECURITY.md).
