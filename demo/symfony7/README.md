# Serial Number Bundle - Symfony 7 Demo

Demo application for [nowo-tech/serial-number-bundle](https://github.com/nowo-tech/serial-number-bundle) on Symfony 7.0.

## Quick start

From the **bundle root**:

```bash
make -C demo up-symfony7
```

Or from this directory:

```bash
cp .env.example .env
docker-compose up -d
docker-compose exec php composer install --no-interaction
```

Open http://localhost:8001 (or the `PORT` in `.env`).

## Path repository

The bundle is loaded from `/var/serial-number-bundle` (mounted from repo root). Run from the bundle repository.

## Composer audit

This demo sets `config.audit.block-insecure: false` so `composer update` can resolve dependencies when Packagist security advisories affect transitive packages (e.g. symfony/http-foundation). Use only for local development; do not replicate this in production applications.

## Commands

- `make up` / `make down` – start/stop
- `make update-bundle` – update the bundle from path and clear cache
- `make test` – run tests
- `make shell` – shell in container
