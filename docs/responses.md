---
layout: default
title: Responses and Builders
nav_order: 5
parent: Overview
---

# Responses and Builders

This SDK returns typed responses and provides builder helpers to compose requests.

## BaseResponse (common fields)

- `ok: bool` — HTTP status is 2xx
- `status: int` — HTTP status code
- `headers: array<string,string>` — flattened response headers
- `data: mixed` — normalized body payload (JSON `data` / `attributes` or full decoded body)
- `meta: mixed` — JSON `meta` when present
- `error: ?string` — short error for non-2xx responses
- `raw: ?string` — raw response body
- `errors: array<int,array>` — Pterodactyl `errors[]` when present
- `payload: ?array` — full decoded JSON payload
- Rate limits (when provided by panel):
  - `rateLimit: ?int`
  - `rateRemaining: ?int`
  - `rateReset: ?int`
  - `retryAfter: ?int`
  - `retryAfterSeconds(): ?int` — recommended wait time
- `explain(): ?string` — human-friendly error summary using `errors[]` and status fallback

## ListResponse

Extends BaseResponse; represents collection endpoints.

- `data: array<int,mixed>` — list of items
- `pagination: array` — meta.pagination when provided (e.g., `current_page`, `total_pages`, `per_page`, `total`)

Constructed internally via `ListResponse::fromBase(...)`.

## ItemResponse

Extends BaseResponse; represents single item endpoints.

- `data: array<string,mixed>` — item attributes
- `id: ?int` — convenience field when `data['id']` is present

Constructed via `ItemResponse::fromBase(...)`.

## ActionResponse

Extends BaseResponse; represents mutation results.

- `ok: bool`
- `message: ?string` — computed from `error` or `data['message']` / `data['meta']['message']`

Constructed via `ActionResponse::fromBase(...)`.

## Builders

### GetBuilder

- `param(string $key, mixed $value): self` — add query param
- `includes(string|array|BackedEnum $includes): self` — set includes list (deduplicated)
- `send(): BaseResponse` — performs the GET and returns typed response (using `::fromBase` when available)

### ListBuilder extends GetBuilder

Adds pagination helpers:

- `perPage(int $size): self`
- `page(int $number): self`
- `allPages(): self` — fetches all pages and returns a response with `data` set to the concatenated items

### ItemBuilder extends GetBuilder

Represents `/{id}` endpoints and holds `id` as `readonly`.

- `getId(): int|string`
- `send(): ItemResponse`

## Notes

- All Client and Application modules follow the same patterns:
  - `all()` returns a `ListBuilder`
  - `get(...)` returns an `ItemBuilder`
  - write operations return `ItemResponse` or `ActionResponse`
- Use `->ok` to check success and `->explain()` to get human-friendly error context.
