# PostgreSQL Integration Tests

This directory contains integration tests that run against a real PostgreSQL database. These tests validate that our custom DBAL types work correctly with an actual PostgreSQL instance.

## Running Tests Locally

### Prerequisites

- Docker (for running PostgreSQL)
- PHP 8.1+ with the `pdo_pgsql` extension

### Start PostgreSQL

You can use Docker Compose to start PostgreSQL:

```bash
# Start PostgreSQL using Docker Compose
docker-compose up -d
```

Or use a plain Docker command:

```bash
docker run --name postgres-doctrine-test -e POSTGRES_PASSWORD=postgres -e POSTGRES_USER=postgres -e POSTGRES_DB=postgres_doctrine_test -p 5432:5432 -d postgres:14
```

### Run the Tests

```bash
# Run the integration tests
composer run-integration-tests
```

### Environment Variables

The tests use the following environment variables which can be customized:

- `POSTGRES_HOST` (default: localhost)
- `POSTGRES_PORT` (default: 5432)
- `POSTGRES_DB` (default: postgres_doctrine_test)
- `POSTGRES_USER` (default: postgres)
- `POSTGRES_PASSWORD` (default: postgres)

You can set these variables before running the tests:

```bash
POSTGRES_HOST=custom-host POSTGRES_PORT=5433 composer run-integration-tests
```

### Cleanup

If you used Docker Compose:

```bash
docker-compose down -v
```

If you used plain Docker:

```bash
docker stop postgres-doctrine-test
docker rm postgres-doctrine-test
```

## CI Integration

These tests are automatically run in GitHub Actions against PostgreSQL 16 and 17 for all supported PHP versions.

The workflow is defined in `.github/workflows/integration-tests.yml`.
