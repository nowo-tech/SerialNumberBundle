# Usage

## Service: SerialNumberGenerator

Inject `Nowo\SerialNumberBundle\Service\SerialNumberGenerator` and call `generate()`:

```php
use Nowo\SerialNumberBundle\Service\SerialNumberGenerator;

public function __construct(
    private readonly SerialNumberGenerator $serialNumberGenerator,
) {}

public function createInvoice(): string
{
    return $this->serialNumberGenerator->generate(
        [
            'prefix' => 'FAC',
            'year'   => (int) date('Y'),
            'office' => '01',
        ],
        '{prefix}-{year}-{office}-{id}',
        42,   // numeric id
        5     // optional: zero-pad id to 5 digits → 00042
    );
    // → "FAC-2025-01-00042"
}
```

### Parameters

- **context** (`array<string, scalar>`): key-value map for placeholders in the pattern (e.g. `['year' => 2025, 'prefix' => 'FAC']`).
- **pattern** (`string`): pattern with `{name}` for context keys and `{id}` for the numeric id (e.g. `"{prefix}-{year}-{id}"`).
- **id** (`int|string`): the numeric id to inject into `{id}`.
- **idPadding** (`?int`): if set, the id is zero-padded to this length (e.g. `5` → `00042`).

## Twig function: serial_number

Generate a serial in templates:

```twig
{{ serial_number(
    { prefix: 'FAC', year: 2025, office: '01' },
    '{prefix}-{year}-{office}-{id}',
    invoice.id,
    5
) }}
```

Arguments: `(context, pattern, id, padding?)`. `padding` is optional.

## Twig filter: serial_number_mask

Mask an existing serial string (e.g. for display in lists) so only the last N characters are visible:

```twig
{{ invoice.serialNumber|serial_number_mask(4) }}
{# "FAC-2025-01-00042" → "***************0042" #}

{{ invoice.serialNumber|serial_number_mask(6, '•') }}
{# optional: visible last 6, mask char '•' #}
```

Arguments: `(visibleLast?, maskChar?)`. Both are optional; defaults come from bundle configuration (`mask_visible_last`, `mask_char`).

## Combined: generate and mask in Twig

```twig
{{ serial_number(
    { prefix: 'FAC', year: invoice.year, office: invoice.officeCode },
    '{prefix}-{year}-{office}-{id}',
    invoice.id,
    5
)|serial_number_mask(4) }}
```

This generates the full serial and then masks it for display (e.g. in tables or emails where you do not want to expose the full number).
