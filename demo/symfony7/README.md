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

## Commands

- `make up` / `make down` – start/stop
- `make update-bundle` – update the bundle from path and clear cache
- `make test` – run tests
- `make shell` – shell in container
