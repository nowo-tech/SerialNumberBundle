# Configuration

Configuration is optional. Defaults are applied if the bundle is not configured.

## Options

| Option               | Type    | Default | Description                                                                 |
|----------------------|---------|---------|-----------------------------------------------------------------------------|
| `mask_char`          | string  | `*`     | Single character used to mask the serial when using the `serial_number_mask` filter. Must be one character (validation error if longer). |
| `mask_visible_last`  | int     | `4`     | Default number of trailing characters to leave visible when masking. Must be ≥ 0. |

## Example

```yaml
# config/packages/nowo_serial_number.yaml
nowo_serial_number:
    mask_char: '•'
    mask_visible_last: 6
```

In Twig, you can still override these per call:

```twig
{{ serial|serial_number_mask(6, '•') }}
```

## Limits (security)

To prevent resource exhaustion, the bundle applies these limits:

- **Mask filter:** Serial strings longer than 2048 characters are truncated before masking. In Twig, if `maskChar` is longer than one character, only the first character is used. Negative `visibleLast` is treated as 0.
- **Generation:** `idPadding` in `generate()` and the Twig function is capped at 32.

See [Security](SECURITY.md) for details.
