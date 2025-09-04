---
layout: default
title: Servers (Admin)
nav_order: 4
parent: Overview
---

# Application Servers (src/Applications/Servers)

Admin-level API for managing servers. This section covers the admin Builders, DTOs, and shortcuts to the Client API from a given server context.

Setup

```php
use Gigabait93\Pterodactyl;

$p = Pterodactyl::make('https://panel.example.com', 'ptlc_xxx'); // Client Admin token
```

## List and get

- `Servers::all(): ServersListBuilder` — call `->send()` to get `ServersListResponse` (ListResponse subtype)
- `Servers::get(int $id): ServersItemBuilder` — resolves an item context (admin)
- `Servers::external(string $externalId): ServersItemBuilder` — by external id

Example

```php
$list = $p->servers->all()->perPage(50)->send();
$firstId = $list->ok ? (int)($list->data[0]['attributes']['id'] ?? 0) : 0;
$server = $p->servers->get($firstId); // ServersItemBuilder
```

### Filters & Sort (list)

```php
// Filter servers and order
$list = $p->servers->all()
    ->filterName('prod')
    ->filterExternalId('crm-42')
    ->sort('name')        // asc
    // ->sort('name', true) // desc
    ->send();
```

## DTOs (Params)

### CreateServerParams (POST /servers)

Required in constructor:

- name: string
- userId: int
- eggId: int
- dockerImage: string
- startup: string

Fluent setters:

- setLimits(memory, swap, disk, io, cpu, threads?)
- setFeatureLimits(databases, allocations, backups)
- env(key, value) / envs([...])
- useAllocation(defaultAllocationId, additionalIds = [])
- useDeploy(locationIds = [], dedicatedIp = false, portRanges = [])
- description(text), externalId(text)
- skipScripts(bool), startOnCompletion(bool), oomDisabled(bool)

Call `toArray()` to produce payload.

### ServerDetailsParams (PATCH /servers/{id}/details)

- name(string)
- user(int)
- externalId(?string) — pass null to clear
- description(?string)

`toArray()` includes only set fields.

### ServerBuildParams (PATCH /servers/{id}/build)

- allocation(int)
- limits(memory?, swap?, disk?, io?, cpu?, threads?, oomDisabled?)
- features(databases?, allocations?, backups?)

`toArray()` includes only set fields.

### ServerStartupParams (PATCH /servers/{id}/startup)

- egg(int)
- startup(string)
- image(string)
- env(array<string, scalar>)
- skipScripts(bool)
- dockerImages(array<string,string>) optional

`toArray()` includes only set fields.

## Admin actions (Servers)

Class: `Gigabait93\Applications\Servers\Servers`

- `create(CreateServerParams): ItemResponse`
- `update(int $id, ServerDetailsParams): ItemResponse`
- `build(int $id, ServerBuildParams): ItemResponse`
- `startup(int $id, ServerStartupParams): ItemResponse`
- `suspend(int $id): ActionResponse`
- `unsuspend(int $id): ActionResponse`
- `reinstall(int $id): ActionResponse`
- `destroy(int $id): ActionResponse`
- `forceDelete(int $id): ActionResponse`
- `getUuid(string $uuid): ItemResponse` — returns server item or 404 with error message

## ServersItemBuilder

Namespace: `Gigabait93\Applications\Servers\Builders\ServersItemBuilder`

Represents a single server context (by panel `id` or `external/...`). Provides both admin and client-side shortcuts.

Admin operations:

- `suspend(): ActionResponse`
- `unsuspend(): ActionResponse`
- `reinstall(): ActionResponse` (delegates to client Settings API)
- `destroy(): ActionResponse`
- `forceDelete(): ActionResponse`
- `update(ServerDetailsParams): ItemResponse`
- `build(ServerBuildParams): ItemResponse`
- `startup(ServerStartupParams): ItemResponse`

Client shortcuts (uuidShort auto-resolved and cached):

- Server: `resources()`, `details(array $includes = [])`, `websocket()`, `power(string $signal)`, `command(string $command)`
- Files: `filesList(path)`, `fileRead(file)`, `fileDownload(file)`, `fileRename(old,new,path)`, `fileCopy(file)`, `fileWrite(file,content)`, `fileCompress(files,path)`, `fileDecompress(name,path)`, `fileDestroy(files,path)`, `fileMkdir(name,path)`, `fileExists(path,name)`, `uploadBuilder()`
- Database: `dbList()`, `dbCreate(name, remote='%')`, `dbResetPassword(database)`, `dbDestroy(database)`
- Backups: `backupsList()`, `backupShow(id)`, `backupDownload(id)`, `backupCreate(name, ignored='')`, `backupDestroy(id)`, `backupRestore(id, truncate=false)`, `backupLockToggle(id)`
- Schedules: `schedulesList()`, `scheduleShow(id)`, `scheduleCreate(...)`, `scheduleUpdate(...)`, `scheduleExecute(id)`, `scheduleDestroy(id)`, `scheduleCreateTask(id, task)`, `scheduleUpdateTask(id, taskId, task)`, `scheduleDeleteTask(id, taskId)`
- Network (allocations): `allocationsList()`, `allocationAssign()`, `allocationSetNote(id,note)`, `allocationSetPrimary(id)`, `allocationDestroy(id)`
- Startup (client): `startupVars()`, `startupVarsUpdate(array $data)`
- Settings (client): `settingsRename(name)`, `settingsSetDockerImage(image)`

The builder resolves `uuidShort` on first use via admin `get()` data and caches it.

## Responses and errors

All methods return `ListResponse`, `ItemResponse` or `ActionResponse`. Non-2xx responses expose a short `error` message and `explain()` provides a human-friendly description based on Pterodactyl `errors[]`. Rate-limit headers are parsed when present.

## Examples

Create a server (abridged):

```php
use Gigabait93\Applications\Servers\Params\CreateServerParams;

$params = (new CreateServerParams(
    name: 'Prod-1',
    userId: 42,
    eggId: 15,
    dockerImage: 'ghcr.io/pterodactyl/yolks:java_17',
    startup: 'java -Xms128M -Xmx{{SERVER_MEMORY}}M -jar {{SERVER_JARFILE}}'
))
    ->setLimits(memory: 4096, swap: 0, disk: 20000, io: 500, cpu: 200)
    ->setFeatureLimits(databases: 2, allocations: 2, backups: 5)
    ->env('SERVER_JARFILE', 'server.jar')
    ->useAllocation(defaultAllocationId: 1234);

$resp = $p->servers->create($params);
```

Suspend then rename via client Settings:

```php
$server = $p->servers->get(123);
$server->suspend();
$server->settingsRename('Maintenance');
```
