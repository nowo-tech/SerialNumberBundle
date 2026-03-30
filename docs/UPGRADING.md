# Upgrading

## Table of contents

- [From 1.0.4 to 1.0.5](#from-104-to-105)
- [From 1.0.3 to 1.0.4](#from-103-to-104)
- [From 1.0.2 to 1.0.3](#from-102-to-103)
- [From 1.0.1 to 1.0.2](#from-101-to-102)
- [From 1.0.0 to 1.0.1](#from-100-to-101)
- [From 0.x / pre-1.0 to 1.0](#from-0x--pre-10-to-10)
  - [Behaviour and limits in 1.0](#behaviour-and-limits-in-10)

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
