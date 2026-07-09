# Feature Specification: SerialNumberBundle baseline (100% code coverage)

**Feature Branch**: `001-baseline`  
**Created**: 2026-07-07  
**Status**: Active  
**Input**: Backfill GitHub Spec Kit baseline documenting 100% of production code in `src/`.

**Related docs**: [`docs/SPEC-DRIVEN-DEVELOPMENT.md`](../../docs/SPEC-DRIVEN-DEVELOPMENT.md), [`docs/CONFIGURATION.md`](../../docs/CONFIGURATION.md), [`docs/USAGE.md`](../../docs/USAGE.md)  
**Code inventory (traceability)**: [`code-inventory.md`](code-inventory.md)

---

## Summary

**Package**: `nowo-tech/serial-number-bundle`  
**Configuration root**: `nowo_serial_number`

Generate invoice/receipt-style serial numbers from a **pattern** (`{year}`, `{prefix}`, `{id}`, …), a **context** map, and optional zero-padded numeric id. Twig helpers generate and **mask** serials for display with configurable defaults.

---

## User Scenarios & Testing

See user stories US-01…US-05 in [`docs/SPEC-DRIVEN-DEVELOPMENT.md`](../../docs/SPEC-DRIVEN-DEVELOPMENT.md).

### User Story 1 — Pattern-based generation (Priority: P1)

**Given** pattern `{prefix}-{year}-{id}`, context `['prefix' => 'FAC', 'year' => 2025]`, id `42`, padding `5`, **When** `SerialNumberGenerator::generate()` runs, **Then** result is `FAC-2025-00042`.

### User Story 2 — Twig function (Priority: P1)

**Given** a template calls `serial_number(context, pattern, id, padding)`, **When** rendered, **Then** output matches service generation without controller boilerplate.

### User Story 3 — Mask for privacy (Priority: P1)

**Given** serial `FAC-2025-01-00042` and defaults `mask_visible_last=4`, **When** filter `serial_number_mask` applies, **Then** only last four characters remain visible (e.g. `*************0042`).

---

## Requirements

### Bundle & configuration

- **FR-BUNDLE-001**: `NowoSerialNumberBundle` MUST register alias `nowo_serial_number`.
- **FR-CFG-001**: Config MUST define `mask_char` (single character, default `*`) and `mask_visible_last` (int ≥0, default `4`).
- **FR-CFG-002**: Extension MUST publish `%nowo_serial_number.mask_char%` and `%nowo_serial_number.mask_visible_last%`.
- **FR-DI-001**: `SerialNumberGenerator` MUST be public/autowired; Twig extension MUST receive generator + mask defaults.

### Generation

- **FR-GEN-001**: `SerialNumberGenerator` MUST substitute `{key}` from context, replace `{id}` with optional zero-padding capped at 32 chars (`MAX_ID_PADDING`), and accept int|string id.

### Twig

- **FR-TWIG-001**: Function `serial_number(...)` MUST delegate to generator; filter `serial_number_mask(...)` MUST mask with per-call or config defaults, cap input length at 2048 (`MAX_SERIAL_LENGTH`), and treat multi-char `mask_char` as first grapheme only.

---

## Edge Cases

- Empty serial or `visibleLast=0`: full mask or empty visible part per documented rules.
- Serial shorter than visible tail: return unchanged.
- Invalid config `mask_char` length >1: rejected at config compile time.

---

## Success Criteria

- **SC-001**: **6/6** files mapped in [`code-inventory.md`](code-inventory.md).
- **SC-002**: PHPUnit covers pattern substitution, padding cap, and mask edge cases.
- **SC-003**: Config keys match [`docs/CONFIGURATION.md`](../../docs/CONFIGURATION.md).

---

## Validation

`composer qa`, PHPUnit, PHPStan.

---

## Out of scope

- Database sequences, concurrency-safe counters, locale-specific formatting beyond pattern placeholders.
