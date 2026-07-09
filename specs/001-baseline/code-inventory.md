# Code inventory — 100% traceability

**Baseline spec**: [`spec.md`](spec.md)  
**Package**: `nowo-tech/serial-number-bundle`  
**Last audited**: 2026-07-07

This file proves that **every production source artifact** under `src/` is referenced by the baseline specification. PHPUnit under `tests/` is out of scope unless promoted in the spec.

## PHP classes (`src/**/*.php`)

| Source file | Spec section | Requirement IDs |
| --- | --- | --- |
| `NowoSerialNumberBundle.php` | Bundle entry | FR-BUNDLE-001 |
| `DependencyInjection/Configuration.php` | Mask defaults config | FR-CFG-001 |
| `DependencyInjection/NowoSerialNumberExtension.php` | DI extension + parameters | FR-CFG-002 |
| `Service/SerialNumberGenerator.php` | Pattern substitution engine | FR-GEN-001 |
| `Twig/SerialNumberTwigExtension.php` | `serial_number` + `serial_number_mask` | FR-TWIG-001 |

## Symfony config (`src/Resources/config/`)

| Source file | Spec section | Requirement IDs |
| --- | --- | --- |
| `Resources/config/services.yaml` | Generator + Twig wiring | FR-DI-001 |

## Coverage summary

| Category | Files | Mapped |
| --- | ---: | ---: |
| PHP classes | 5 | 5 |
| YAML config | 1 | 1 |
| **Total `src/` artifacts** | **6** | **6** |
