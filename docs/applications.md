---
layout: default
title: Application API
nav_order: 3
parent: Overview
---

# Application API (src/Applications)

Administrative (panel) API to manage servers, users, nodes, locations, nests/eggs, and allocations.

Setup

```php
use Gigabait93\Pterodactyl;

$p = Pterodactyl::make('https://panel.example.com', 'ptlc_xxx'); // Client Admin token
```

Conventions

- List endpoints return a `...ListBuilder`; call `->send()` to get a `ListResponse`.
- Item endpoints return a `...ItemBuilder`; call `->send()` to get an `ItemResponse`.
- Create/Update/Destroy methods return `ItemResponse` or `ActionResponse` directly.
- See [Responses and Builders](responses.md) for response fields and helper methods.

Filters & Sort

- Filters use `filter[<key>]` under the hood; SDK надає зручні методи `filterXxx(...)` на відповідних ListBuilder.
- Sort приймає назву поля, опціонально напрямок: `->sort('id')` або `->sort('id', true)` (desc).
- Дозволені поля для сортування (може відрізнятися залежно від версії панелі):
  - Users: `id, uuid, username, email, created_at, updated_at`
  - Servers: `id, uuid, name, created_at, updated_at`
  - Nodes: `id, uuid, name, created_at, updated_at`
  - Locations: `id, short, long, created_at, updated_at`

## Servers (admin)

Detailed documentation and DTOs are in [Servers (admin)](servers.md).

Key entry points:

- `all(): ServersListBuilder`
- `get(int $id): ServersItemBuilder`
- `external(string $externalId): ServersItemBuilder`
- `create(CreateServerParams): ItemResponse`
- `update(int $id, ServerDetailsParams): ItemResponse`
- `build(int $id, ServerBuildParams): ItemResponse`
- `startup(int $id, ServerStartupParams): ItemResponse`
- `suspend(int $id): ActionResponse`
- `unsuspend(int $id): ActionResponse`
- `reinstall(int $id): ActionResponse`
- `destroy(int $id): ActionResponse`
- `forceDelete(int $id): ActionResponse`
- `getUuid(string $uuid): ItemResponse`

Filters & sort

```php
$servers = $p->servers->all()
    ->filterName('prod')
    ->filterUuid('54f52795-...')
    ->filterExternalId('ext-123')
    ->filterImage('ghcr.io/...')
    ->sort('name')           // asc
    // ->sort('name', true)  // desc
    ->send();
```

## Users (admin)

Namespace: `Gigabait93\Applications\Users\Users`

- `all(): UsersListBuilder` → `ListResponse`
- `get(int $id): UsersItemBuilder` → `ItemResponse`
- `external(string $externalId): UsersItemBuilder`
- `create(UserCreateParams $params): ItemResponse`
- `update(int $id, UserUpdateParams $params): ItemResponse`
- `destroy(int $id): ActionResponse`

DTOs

- `UserCreateParams` (required): `email, username, firstName, lastName`
  - Optional: `password(?), externalId(?), language(?), rootAdmin(?)`
- `UserUpdateParams` (all optional setters): `email, username, firstName, lastName, password(?), externalId(?), language(?), rootAdmin(?)`

Example

```php
$params = (new \Gigabait93\Applications\Users\Params\UserCreateParams(
    email: 'john@example.com',
    username: 'john',
    firstName: 'John',
    lastName: 'Doe',
))->password('secret')->rootAdmin(false);
$create = $p->users->create($params);

$upd = (new \Gigabait93\Applications\Users\Params\UserUpdateParams())
    ->firstName('Johnny')->language('en');
$update = $p->users->update(42, $upd);

// Filters & sort
$users = $p->users->all()
    ->filterEmail('john@example.com')
    ->filterUsername('john')
    ->sort('created_at', true)
    ->send();
```

## Nodes (admin)

Namespace: `Gigabait93\Applications\Nodes\Nodes`

- `all(): NodesListBuilder` → `ListResponse`
- `get(int $id): NodesItemBuilder` → `ItemResponse`
- `create(NodeCreateParams $params): ItemResponse`
- `update(int $id, NodeUpdateParams $params): ItemResponse`
- `destroy(int $id): ActionResponse`

DTOs

- `NodeCreateParams(name, locationId, fqdn, memory, memoryOver, disk, diskOver)`
  - Optional setters: `scheme(), behindProxy(), maintenance(), uploadSize(), daemon(listen,sftp), description(?)`
- `NodeUpdateParams` — all fields optional via setters: `name, locationId, fqdn, scheme, behindProxy, maintenance, memory, memoryOver, disk, diskOver, uploadSize, daemon(listen,sftp), description(?)`

Example

```php
$create = new \Gigabait93\Applications\Nodes\Params\NodeCreateParams(
    name: 'eu-1', locationId: 1, fqdn: 'node1.example.com',
    memory: 32768, memoryOver: 0, disk: 500000, diskOver: 0,
);
$p->nodes->create($create);

$upd = (new \Gigabait93\Applications\Nodes\Params\NodeUpdateParams())
    ->maintenance(true)->uploadSize(200);
$p->nodes->update(10, $upd);

// Filters & sort
$nodes = $p->nodes->all()
    ->filterName('eu-')
    ->filterFqdn('node1.example.com')
    ->sort('name')
    ->send();
```

## Locations (admin)

Namespace: `Gigabait93\Applications\Locations\Locations`

- `all(): LocationsListBuilder` → `ListResponse`
- `get(int $id): LocationsItemBuilder` → `ItemResponse`
- `create(LocationParams $params): ItemResponse`
- `update(int $id, LocationParams $params): ItemResponse`
- `destroy(int $id): ActionResponse`

DTO

- `LocationParams(short, long)`

```php
$loc = $p->locations->all()
    ->filterShort('EU')
    ->sort('created_at', true)
    ->send();
```

## Nests (admin)

Namespace: `Gigabait93\Applications\Nests\Nests`

- `all(): NestsListBuilder` → `ListResponse`
- `get(int $id): NestsItemBuilder` → `ItemResponse`

## Eggs (admin)

Namespace: `Gigabait93\Applications\Eggs\Eggs`

- `all(int $nestId): EggsListBuilder` → `ListResponse`
- `get(int $nestId, int $eggId): EggsItemBuilder` → `ItemResponse`

## Allocations (admin)

Namespace: `Gigabait93\Applications\Allocations\Allocations`

- `all(int $nodeId): ListBuilder` (returns `ListResponse`)
- `create(int $nodeId, AllocationCreateParams $params): ActionResponse`
- `destroy(int $nodeId, int $allocationId): ActionResponse`

DTO

- `AllocationCreateParams(ip, array<int,int> ports)`

## Includes and pagination

- Use `->includes(...)` on any `ListBuilder`/`ItemBuilder` that accepts includes (see enums in `src/Enums`).
- For lists: `->perPage(n)`, `->page(n)`, `->allPages()` to aggregate all items.
- `ListResponse->pagination` contains `current_page`, `total_pages`, `per_page`, `total` when provided by the panel.

## Error handling and rate limits

All responses share fields:

- `ok`, `status`, `headers`, `error`, `errors[]`, `data`, `meta`
- Rate-limit hints: `rateLimit`, `rateRemaining`, `rateReset`, `retryAfter` and `retryAfterSeconds()` helper.
- `explain()` returns a concise, human-friendly error summary.
