# Upgrading

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
