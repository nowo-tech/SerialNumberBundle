# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/).

## [Unreleased]

_No changes yet._

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

[Unreleased]: https://github.com/nowo-tech/serial-number-bundle/compare/v1.0.0...HEAD
[1.0.0]: https://github.com/nowo-tech/serial-number-bundle/releases/tag/v1.0.0
