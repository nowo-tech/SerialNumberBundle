# FrankenPHP worker mode audit (kernel not reset between requests)

| Field | Value |
|-------|-------|
| Package | `nowo-tech/serial-number-bundle` (`symfony-bundle`) |
| Audited revision | `v1.0.16` (this release) |
| Audit date | 2026-09-25 |
| Method | Manual review of every file under `src/` (services, Twig extension, DI extension, config, `services.yaml`) plus PHPUnit coverage of process-wide `mb_internal_encoding` isolation |
| **Verdict** | ✅ **Viable (100%)** — both services are pure functions over their arguments and immutable config; safe with or without kernel reset |

## Execution model assumed

FrankenPHP worker mode boots the Symfony kernel once per worker and serves many requests with the same container. This audit assumes the **strict** variant: the kernel is **not** rebooted between requests, so every shared service, static property and PHP global survives from one request to the next. Two scenarios are evaluated:

- **A — kernel not rebooted, `services_resetter` still runs:** services tagged `kernel.reset` (or implementing `ResetInterface`) are reset between requests.
- **B — no reset at all:** nothing is reset; any per-request state kept in a service leaks into the next request.

A bundle that is safe under **B** is safe under **A** and under classic mode / PHP-FPM.

## Summary

| Area | Status | Notes |
|------|--------|-------|
| Mutable state in shared services | ✅ | `SerialNumberGenerator` has no properties; `SerialNumberTwigExtension` only has `readonly` config |
| Static properties / `static` locals | ✅ | None (only class constants) |
| `ResetInterface` / `kernel.reset` coverage | ✅ N/A | Nothing to reset |
| Request / user / locale captured in services | ✅ | Context, pattern and id are passed as call arguments |
| Superglobals, `$_ENV`, `putenv`, `ini_set`, `setlocale`, timezone | ✅ | None used; config is compiled into container parameters |
| Process-wide multibyte encoding | ✅ | All `mb_*` calls pass explicit `UTF-8` (`MB_ENCODING`); independent of `mb_internal_encoding()` |
| Doctrine / EntityManager | ✅ N/A | No persistence; the caller supplies the id |
| Output, headers, `exit`, shutdown functions | ✅ | None |
| Resources (files, sockets, cURL) held open | ✅ | None |
| Memory growth across requests | ✅ | No caches or accumulating arrays; output sizes are capped (`MAX_ID_PADDING`, `MAX_SERIAL_LENGTH`) |
| Blocking I/O and timeouts | ✅ N/A | No I/O |
| Third-party static state | ✅ | Only Twig and Symfony DI/Config |
| PHPStan FrankenPHP rulesets | ✅ | `ruleset-classic.neon` + `ruleset-worker.neon` included in `phpstan.neon.dist` |

Worker demo: `demo/symfony8/docker/frankenphp/Caddyfile` has a `worker` block, and `FRANKENPHP_MODE=worker` is the default in `demo/symfony8/docker-compose.yml`.

## Services reviewed

| Service | Shared | Mutable state | Scenario A | Scenario B |
|---------|--------|---------------|------------|------------|
| `Nowo\SerialNumberBundle\Service\SerialNumberGenerator` | yes | none (no properties) | ✅ | ✅ |
| `Nowo\SerialNumberBundle\Twig\SerialNumberTwigExtension` (`twig.extension`) | yes | none (`readonly` generator, mask char, visible count) | ✅ | ✅ |

`Configuration`, `NowoSerialNumberExtension` and `NowoSerialNumberBundle` only run while the container is compiled.

## Findings

No blocking findings. `SerialNumberGenerator::generate()` and `SerialNumberTwigExtension::maskSerialNumber()` build their result only from their arguments and the `readonly` config values injected at construction. Nothing is stored between calls.

**Hardened in 1.0.16:** masking and config validation pass encoding `'UTF-8'` to every `mb_strlen` / `mb_substr` call so a third-party library that changes `mb_internal_encoding()` in a long-lived worker cannot alter multibyte serial masking. Covered by `testMaskSerialNumberIgnoresProcessMbInternalEncoding`.

## Usage recommendations in worker mode

- No special configuration or reset hook is needed.
- The bundle does not allocate the numeric id. The host must get the id from a transactional source (database sequence or row lock) on every request; do not keep a "next id" counter in a service property or a static variable, because each worker would keep its own copy and produce duplicate serials.
- Subclasses or decorators of `SerialNumberGenerator` must stay stateless (or implement `ResetInterface`) to keep this verdict.

## Re-audit triggers

Re-run this audit when a change adds: properties to `SerialNumberGenerator` or the Twig extension, id allocation or a counter inside the bundle, a cache of generated serials, an event listener, any use of `$_SERVER` / `$_ENV` at runtime, or `mb_*` calls without an explicit encoding argument.
