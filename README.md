# Serial Number Bundle

[![CI](https://github.com/nowo-tech/SerialNumberBundle/actions/workflows/ci.yml/badge.svg)](https://github.com/nowo-tech/SerialNumberBundle/actions/workflows/ci.yml)
[![Packagist Version](https://img.shields.io/packagist/v/nowo-tech/serial-number-bundle.svg?style=flat)](https://packagist.org/packages/nowo-tech/serial-number-bundle)
[![Packagist Downloads](https://img.shields.io/packagist/dt/nowo-tech/serial-number-bundle.svg)](https://packagist.org/packages/nowo-tech/serial-number-bundle)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)
[![PHP](https://img.shields.io/badge/PHP-8.1%2B-777BB4?logo=php)](https://php.net)
[![Symfony](https://img.shields.io/badge/Symfony-6%20%7C%207%20%7C%208-000000?logo=symfony)](https://symfony.com)
[![GitHub stars](https://img.shields.io/github/stars/nowo-tech/SerialNumberBundle.svg?style=social&label=Star)](https://github.com/nowo-tech/SerialNumberBundle) [![Coverage](https://img.shields.io/badge/Coverage-100%25-brightgreen)](#tests-and-coverage)

> ⭐ **Found this useful?** Install from Packagist and give it a star on GitHub.

Symfony bundle to generate and mask serial numbers for invoices, receipts, tickets, etc. Uses a pattern with placeholders (`{year}`, `{prefix}`, `{id}`), a context map, and an optional numeric id padding. Includes a Twig filter to mask the serial for display (e.g. show only last 4 digits).

## Features

- **SerialNumberGenerator** service: build serials from `context` (variables), `pattern` (string with `{var}` and `{id}`), and numeric `id` (optional zero-padding).
- **Twig function** `serial_number(context, pattern, id, padding?)`: generate serial in templates.
- **Twig filter** `serial_number_mask(serial, visibleLast?, maskChar?)`: mask a serial leaving only the last N characters visible (e.g. `***************0042`).

## Documentation

- [Installation](docs/INSTALLATION.md)
- [Configuration](docs/CONFIGURATION.md)
- [Usage](docs/USAGE.md)
- [Contributing](docs/CONTRIBUTING.md)
- [Changelog](docs/CHANGELOG.md)
- [Upgrading](docs/UPGRADING.md)
- [Release](docs/RELEASE.md)
- [Security](docs/SECURITY.md)
- [Engram](docs/ENGRAM.md)

### Additional documentation

- [Demo (Symfony 7 & 8)](demo/README.md) — run `make -C demo up-symfony8` from the bundle root.
- [Demo with FrankenPHP (development and production)](docs/DEMO-FRANKENPHP.md)

## Quick example

```php
// In a controller or service
$serial = $this->serialNumberGenerator->generate(
  ['prefix' => 'FAC', 'year' => 2025, 'office' => '01'],
  '{prefix}-{year}-{office}-{id}',
  42,
  5 // id padding → 00042
);
// → "FAC-2025-01-00042"
```

```twig
{# Generate and mask in Twig #}
{{ serial_number(
  { prefix: 'FAC', year: 2025, office: '01' },
  '{prefix}-{year}-{office}-{id}',
  invoice.id,
  5
)|serial_number_mask(4) }}
{# → "*************0042" #}
```

## Requirements

- PHP >= 8.1, < 8.6
- Symfony 6.0 | 7.0 | 8.0
- Twig 3.8+ or 4.x

## Tests and coverage

- Tests: PHPUnit (PHP)
- PHP: 100%

## Version policy

The Composer package name is [`nowo-tech/serial-number-bundle`](https://packagist.org/packages/nowo-tech/serial-number-bundle). Source code and issues are in the GitHub repository [`nowo-tech/SerialNumberBundle`](https://github.com/nowo-tech/SerialNumberBundle).

We follow [Semantic Versioning](https://semver.org/). See [Changelog](docs/CHANGELOG.md) for release notes. Security support by major version is described in the [Security policy](.github/SECURITY.md#supported-versions).

## License

MIT. See [LICENSE](LICENSE).
