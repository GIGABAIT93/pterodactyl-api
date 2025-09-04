# Pterodactyl API PHP SDK — Docs

This folder documents the public SDK surface. It focuses on:

- Client API (per-server operations under `src/Client`)
- Application Servers API (admin side under `src/Applications/Servers`)
- Common response types and builders

Quick links

- [Client API](client.md)
- [Application API](applications.md)
- [Servers (admin, details + DTOs)](servers.md)
- [Responses and Builders](responses.md)

## Quickstart

```php
use Gigabait93\Pterodactyl;

$p = Pterodactyl::make(
    baseUrl: 'https://panel.example.com',
    clientAdminApiKey: 'ptlc_xxx', // Client Admin token only
    timeout: 30,
);

// Resolve a server uuidShort (8 chars) via admin list
$first = $p->servers->list()->send();
$uuid = $first->ok && isset($first->data[0]['attributes'])
    ? ($first->data[0]['attributes']['identifier'] ?? substr((string)($first->data[0]['attributes']['uuid'] ?? ''), 0, 8))
    : null;
```

## Responses

- `ListResponse`: `data` is an array of items; `pagination` contains pagination meta if provided by panel.
- `ItemResponse`: `data` is a single object (attributes map).
- `ActionResponse`: `ok` boolean and `message` (if available), `data` may contain additional info.

For all responses:

- `ok` indicates 2xx HTTP status.
- `status` is the HTTP status code.
- `headers` is a flattened map of response headers.
- `error` is a short error description for non-2xx.
- Rate limit hints (when panel provides them): `rateLimit`, `rateRemaining`, `rateReset`, `retryAfter` and `retryAfterSeconds()` helper.

## Authentication

- The SDK requires a Client Admin API token that starts with `ptlc_`.
- Configure your token in code or via env (tests use `PTERO_CLIENT_API_KEY`).
- Application endpoints are supported, but authorization is performed using the same client admin token.

## Builders

- `ListBuilder`: call fluent methods (e.g., `param()`, `includes()`, `perPage()`, `page()`, `allPages()`) then `send()` to receive a `ListResponse`.
- `ItemBuilder`: represents an item endpoint; call `send()` to receive an `ItemResponse`.

See specific docs for per-module usage and examples.
