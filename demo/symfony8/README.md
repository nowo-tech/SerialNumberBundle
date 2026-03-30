# Serial Number Bundle - Symfony 8 Demo

Demo application for [`nowo-tech/serial-number-bundle`](https://packagist.org/packages/nowo-tech/serial-number-bundle) ([GitHub](https://github.com/nowo-tech/SerialNumberBundle)) on Symfony 8.0.

## Quick start

From the **bundle root** (so the path repo can resolve):

```bash
make -C demo up-symfony8
```

Or from this directory:

```bash
cp .env.example .env
docker-compose up -d
# wait a few seconds, then:
docker-compose exec php composer install --no-interaction
```

Open http://localhost:8008 (or the `PORT` in `.env`).

## Path repository

The bundle is loaded from `/var/serial-number-bundle` inside the container, which is mounted from the repo root (`../..`). Ensure you run from the bundle repository so that `docker-compose` mounts the correct path.

## Commands

- `make up` / `make down` – start/stop
- `make update-bundle` – update the bundle from the path and clear cache
- `make test` – run tests
- `make shell` – shell in container
