# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Laravel SDK for the **DHL Parcel DE Returns API** — create return labels on demand and look up return receiver locations. Built on **Saloon** (HTTP client) and **Spatie Laravel Data** (DTOs). Requires PHP 8.4+, Laravel 11/12/13.

## Commands

```bash
composer test          # Run Pest tests
composer test -- --filter=TestName  # Run a single test
composer analyse       # Run PHPStan (level 5)
composer format        # Fix code style with Laravel Pint
composer prepare       # testbench package:discover (runs on post-autoload-dump)
```

## Architecture

```
DhlParcelReturns (main service)
├── orders()    → OrdersResource    → create a return label (POST /orders)
├── locations() → LocationsResource → list return receivers (GET /locations)
└── general()   → GeneralResource   → API version, no auth (GET /)
```

**Key layers:**

- **`Auth/DhlParcelReturnsAuthenticator`** — Supports API key (`dhl-api-key` header) + Basic Auth, or OAuth2 password grant with an in-memory cached Bearer token. `Auth/NullAuthenticator` disables auth for the public version endpoint.
- **`Connectors/DhlParcelReturnsConnector`** — Saloon connector with sandbox/production base URLs (`.../parcel/de/shipping/returns/v1`). Throws `DhlParcelReturnsApiException` on errors.
- **`Resources/`** — Three resource classes wrapping the endpoints. All extend `BaseResource`, which tracks the last request/response for debugging.
- **`Requests/`** — Saloon request classes organized by domain (Orders, Locations, General). Each maps the response via `createDtoFromResponse()`.
- **`Data/`** — DTOs extending `Spatie\LaravelData\Data`: request shapes in `Data/Orders/`, response shapes in `Data/Responses/`, location shapes in `Data/Locations/`. `ReturnOrderConfirmationData` maps the API's `sstatus` key to `status`.
- **`Enums/`** — Backed string enums: `LabelType`, `WeightUom`, `Currency`, `Country` (ISO3 lowercase), `CountryOfOrigin` (ISO3 uppercase).

**Multi-tenant support:** `DhlParcelReturns::make($config)` creates instances with different credentials.

## Configuration

Environment variables: `DHL_PARCEL_RETURNS_API_KEY`, `DHL_PARCEL_RETURNS_USERNAME`, `DHL_PARCEL_RETURNS_PASSWORD`, `DHL_PARCEL_RETURNS_CLIENT_SECRET`, `DHL_PARCEL_RETURNS_BASE_URL`, `DHL_PARCEL_RETURNS_OAUTH_BASE_URL`, `DHL_PARCEL_RETURNS_SANDBOX`.

## DHL Parcel DE Returns API

The full OpenAPI spec is `docs/DHL Parcel Returns.yaml`; the Postman onboarding collection is `docs/DHL Parcel Returns Onboarding.json`.

- **Base URLs:** sandbox `https://api-sandbox.dhl.com/parcel/de/shipping/returns/v1`, production `https://api-eu.dhl.com/parcel/de/shipping/returns/v1`.
- **Auth:** API key + Basic, or OAuth2 password grant (token URL `/parcel/de/account/auth/ropc/v1/token` on `api-eu`/`api-sandbox`).
- **Errors** follow RFC 7807 (`application/problem+json`): 400, 401, 403, 422, 429 — parsed by `DhlParcelReturnsApiException::fromResponse()`.

## Testing

- Pest 4 with Orchestra Testbench. The base `TestCase` registers both `LaravelDataServiceProvider` (so DTO `toArray()` works in the test environment) and `DhlParcelReturnsServiceProvider`.
- HTTP is faked with Saloon's `MockClient`/`MockResponse`; test payloads come from the spec's examples.
- Architecture tests enforce no `dd`/`dump`/`ray` calls in source.

## Static Analysis

PHPStan level 5 via Larastan over `src` and `config`. Config ignores `env()` calls in config files.

## Conventions

Follow the Spatie Laravel & PHP guidelines (typed properties, constructor promotion, early returns, no `else`, short `?Type` nullables, string interpolation). Namespace root is `SmartDato\DhlParcelReturns\` → `src/`.
