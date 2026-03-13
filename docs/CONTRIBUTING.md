# Contributing

Contributions are welcome. Please open an issue or a pull request on the [GitHub repository](https://github.com/nowo-tech/serial-number-bundle).

- Follow PSR-12 and the project's PHP-CS-Fixer configuration.
- Add tests for new behaviour. Tests live under `tests/Unit/` (isolated) and `tests/Integration/` (container/kernel). Run all with `make test`, or only `composer test-unit` / `composer test-integration`.
- Keep documentation (README, docs/) in sync with code changes.

Run quality checks locally:

```bash
make install
make cs-check
make test
make phpstan
make rector-dry
```
