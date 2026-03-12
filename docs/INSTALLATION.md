# Installation

## Composer

```bash
composer require nowo-tech/serial-number-bundle
```

## Enable the bundle

If you use **Symfony Flex**, the bundle is registered automatically and the recipe creates `config/packages/nowo_serial_number.yaml` with default values. Otherwise, add the bundle to `config/bundles.php`:

```php
return [
    // ...
    Nowo\SerialNumberBundle\NowoSerialNumberBundle::class => ['all' => true],
];
```

## Optional configuration

Create or edit `config/packages/nowo_serial_number.yaml` to set default mask character and visible last digits:

```yaml
nowo_serial_number:
    mask_char: '*'          # character used when masking (default: *)
    mask_visible_last: 4    # default number of trailing chars visible when masking (default: 4)
```

See [Configuration](CONFIGURATION.md) and [Usage](USAGE.md) for details.
