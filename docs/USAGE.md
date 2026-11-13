# Usage

## Table of contents

- [Service: SerialNumberGenerator](#service-serialnumbergenerator)
  - [Parameters](#parameters)
- [Twig function: serial_number](#twig-function-serial_number)
- [Twig filter: serial_number_mask](#twig-filter-serial_number_mask)
- [Combined: generate and mask in Twig](#combined-generate-and-mask-in-twig)
- [FrankenPHP worker mode](#frankenphp-worker-mode)
- [Standard Symfony and DDD hosts](#standard-symfony-and-ddd-hosts)

## Service: SerialNumberGenerator

Inject `Nowo\SerialNumberBundle\Contract\SerialNumberGeneratorInterface` (implemented by the final `Service\SerialNumberGenerator`, which still works as a type hint) and call `generate()`:

```php
use Nowo\SerialNumberBundle\Contract\SerialNumberGeneratorInterface;

public function __construct(
    private readonly SerialNumberGeneratorInterface $serialNumberGenerator,
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
{# "FAC-2025-01-00042" → "*************0042" #}

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

## FrankenPHP worker mode

Services are **stateless**: safe when FrankenPHP keeps the Symfony kernel warm without reset between requests. Multibyte masking always uses UTF-8 and does not depend on `mb_internal_encoding()`. Obtain the numeric id from a transactional source on every request (the bundle does not allocate ids). Full audit: [FRANKENPHP-WORKER-AUDIT.md](FRANKENPHP-WORKER-AUDIT.md).

## Standard Symfony and DDD hosts

The bundle works with no configuration in a standard Symfony app: autowire `SerialNumberGeneratorInterface` or use the Twig function. It has no entities, no events and no database access, so there is nothing to map. The generator is pure: the id always comes from the caller (a sequence, a database id, an aggregate counter).

A DDD / hexagonal host keeps its domain free of framework types:

- **Port to depend on:** `Nowo\SerialNumberBundle\Contract\SerialNumberGeneratorInterface`. Its signature uses only arrays of scalars, `string`, `int` and `?int`; no Symfony, Doctrine or HTTP type. Declare your own port in the application layer (for example `App\Application\Port\InvoiceNumbers`) and implement it in Infrastructure by delegating to the interface:

  ```php
  final class InvoiceNumbersAdapter implements InvoiceNumbers
  {
      public function __construct(private readonly SerialNumberGeneratorInterface $generator) {}

      public function next(int $sequence, int $year): string
      {
          return $this->generator->generate(['prefix' => 'FAC', 'year' => $year], '{prefix}-{year}-{id}', $sequence, 5);
      }
  }
  ```

- **Replace the implementation:** re-alias the interface to your own service (the Twig extension and every autowired consumer follow the alias):

  ```yaml
  services:
      Nowo\SerialNumberBundle\Contract\SerialNumberGeneratorInterface: '@App\Infrastructure\Numbering\MyGenerator'
  ```

- **Persistence and events:** none (REQ-ARCH-001 rules 2, 4, 6 and 7 do not apply). Configuration comes from the bundle config tree only.
