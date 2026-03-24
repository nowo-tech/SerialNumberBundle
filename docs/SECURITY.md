# Security – SerialNumberBundle

## Summary

The bundle **does not register HTTP routes or controllers**. It exposes a service (`SerialNumberGenerator`), a Twig extension (`serial_number`, `serial_number_mask`), and configuration. The attack surface is small.

Mitigations address **resource exhaustion (DoS)** and document **XSS** considerations when data comes from users.

---

## 1. Risks addressed (fixed in code)

### 1.1 DoS via `str_repeat` when masking

- **Issue:** A negative `visibleLast` in Twig (e.g. `-1000000`) could make `maskLength = length - visible` huge and `str_repeat($char, $maskLength)` consume excessive memory/CPU.
- **Mitigation:** `visible = max(0, $visible)` in `SerialNumberTwigExtension::maskSerialNumber()`.

### 1.2 DoS via very long serial strings

- **Issue:** A multi-megabyte serial (e.g. from a database or template variable) could make `str_repeat` build a huge string.
- **Mitigation:** Serial input is truncated to **2048** characters (`MAX_SERIAL_LENGTH`) before masking.

### 1.3 DoS via multi-character `mask_char`

- **Issue:** A long string used as the mask character could multiply output size when repeated.
- **Mitigation:** Only the first character is used (multibyte-safe via `mb_substr`). `Configuration` validates `mask_char` as a single character.

### 1.4 DoS via very large `idPadding`

- **Issue:** `str_pad($idStr, $idPadding, '0', STR_PAD_LEFT)` with a huge `$idPadding` could consume excessive memory.
- **Mitigation:** Padding is capped at **32** (`MAX_ID_PADDING`) in `SerialNumberGenerator::generate()`.

---

## 2. XSS and user-controlled data

Twig functions/filters use **`is_safe => ['html']`**, so Twig does not escape the output. That is appropriate when values are **application-controlled** (e.g. system-generated invoice numbers).

- **Recommendation:** Do not pass unsanitized user input (forms, query strings, etc.) directly into `serial_number()` or `serial_number_mask()` without validating/escaping the result for HTML.
- If serials or context values may contain user content:
  - Escape in the template (e.g. `{{ serial|serial_number_mask(4)|e }}` when you need escaping for that value), or
  - Ensure values are sanitized before they reach the bundle.

The bundle does not HTML-escape; the application must use serials safely in HTML context.

---

## 3. Other aspects reviewed

| Topic | Notes |
|-------|--------|
| SQL injection | N/A: the bundle does not execute SQL. |
| Access control | N/A: no routes or controllers. |
| Secrets in config | No passwords or tokens. `mask_char` and `mask_visible_last` are presentation-only. |
| Dependencies | Symfony (config, DI, HttpKernel) and Twig. Run `composer audit` in consuming projects. |
| Configuration loading | Loaded from YAML; not from raw user input. |

---

## 4. Security-related tests

Tests cover:

- Negative `visibleLast` is treated as 0 (fully masked).
- Very long serials are truncated (2048 limit).
- Multi-character `maskChar` is reduced to the first character.
- Excessive `idPadding` is capped at 32.

Run the full suite with `composer test` (or `./vendor/bin/phpunit`) after changes.
