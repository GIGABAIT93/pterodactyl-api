---
layout: default
title: Client API
nav_order: 2
parent: Overview
---

# Client API (src/Client)

This section documents per-server operations available to a regular client via the panel’s client API. All methods require a server’s short UUID (first 8 chars) a.k.a. `uuidShort` unless noted.

Setup

```php
use Gigabait93\Pterodactyl;

$p = Pterodactyl::make('https://panel.example.com', 'ptlc_xxx');
$uuid = '54f52795'; // server identifier (uuidShort)
```

## Common responses

- `ListBuilder` → `send()` returns `ListResponse` (`data[]`, `pagination`)
- `ItemResponse` → `data` object
- `ActionResponse` → `ok`, `message` and optional `data`

## Modules

### Server

Namespace: `Gigabait93\Client\Server\Server`

- `resources(string $uuidShort): ItemResponse` — live stats/resources
- `details(string $uuidShort, array $includes = ['egg','subusers']): ItemResponse`
- `websocket(string $uuidShort): ItemResponse` — token and socket URL
- `power(string $uuidShort, string $signal): ActionResponse` — signals: start|stop|restart|kill
- `command(string $uuidShort, string $command): ActionResponse`

Example

```php
$p->server->power($uuid, 'restart');
$p->server->command($uuid, 'say hello');
```

### Files

Namespace: `Gigabait93\Client\Files\Files`

- `list(string $uuidShort, string $path = '/'): ListBuilder`
- `read(string $uuidShort, string $filePath): ItemResponse`
- `download(string $uuidShort, string $filePath): ItemResponse`
- `rename(string $uuidShort, string $old, string $new, string $path = '/'): ActionResponse`
- `copy(string $uuidShort, string $filePath): ActionResponse`
- `write(string $uuidShort, string $filePath, string $content): ActionResponse`
- `compress(string $uuidShort, array $files, string $path = '/'): ActionResponse`
- `decompress(string $uuidShort, string $fileName, string $path = '/'): ActionResponse`
- `destroy(string $uuidShort, array $files, string $path): ActionResponse`
- `mkdir(string $uuidShort, string $folderName, string $path = '/'): ActionResponse`
- `exists(string $uuidShort, string $path, string $name): bool`
- `uploadUrl(string $uuidShort, string $directory = '/'): ItemResponse`
- `makeUpload(string $uuidShort): UploadBuilder`

UploadBuilder (namespace `Gigabait93\Client\Files\Builders`):

- `dir(string $directory): self`
- `signedUrl(string $url): self`
- `addFile(string $localPath, ?string $asName = null, ?string $mime = null): self`
- `addContents(string $name, string $contents, ?string $mime = null): self`
- `addMany(array $items): self`
- `send(?string $signedUrl = null): ActionResponse`
- `sendAndVerify(?string $signedUrl = null): ActionResponse` — verifies with Files::list()

### Database

Namespace: `Gigabait93\Client\Database\Database`

- `list(string $uuidShort): ListBuilder`
- `create(string $uuidShort, string $database, string $remote = '%'): ItemResponse`
- `resetPassword(string $uuidShort, string $database): ActionResponse`
- `destroy(string $uuidShort, string $database): ActionResponse`

### Backups

Namespace: `Gigabait93\Client\Backups\Backups`

- `list(string $uuidShort): ListBuilder`
- `show(string $uuidShort, string $backup): ItemResponse`
- `download(string $uuidShort, string $backup): ItemResponse`
- `create(string $uuidShort, string $name, string $ignored = ''): ItemResponse`
- `destroy(string $uuidShort, string $backup): ActionResponse`
- `restore(string $uuidShort, string $backup, bool $truncate = false): ActionResponse`
- `lockToggle(string $uuidShort, string $backup): ActionResponse`

### Schedules

Namespace: `Gigabait93\Client\Schedules\Schedules`

- `list(string $uuidShort): ListBuilder`
- `show(string $uuidShort, string $scheduleId): ItemResponse`
- `create(string $uuidShort, ...): ItemResponse`
- `update(string $uuidShort, int $scheduleId, ...): ItemResponse`
- `execute(string $uuidShort, int $scheduleId): ActionResponse`
- `destroy(string $uuidShort, string $scheduleId): ActionResponse`
- `createTask(string $uuidShort, string $scheduleId, array $task): ItemResponse`
- `updateTask(string $uuidShort, string $scheduleId, string $taskId, array $task): ItemResponse`
- `deleteTask(string $uuidShort, string $scheduleId, string $taskId): ActionResponse`

### Network (Allocations)

Namespace: `Gigabait93\Client\Network\Network`

- `list(string $uuidShort): ListBuilder`
- `assignAllocation(string $uuidShort): ActionResponse`
- `setNote(string $uuidShort, string $allocationId, string $note): ActionResponse`
- `setPrimary(string $uuidShort, string $allocationId): ActionResponse`
- `destroy(string $uuidShort, string $allocationId): ActionResponse`

### Startup (variables)

Namespace: `Gigabait93\Client\Startup\Startup`

- `variables(string $uuidShort): ItemResponse`
- `update(string $uuidShort, array $data): ActionResponse`

### Settings

Namespace: `Gigabait93\Client\Settings\Settings`

- `rename(string $uuidShort, string $name): ActionResponse`
- `reinstall(string $uuidShort): ActionResponse`
- `setDockerImage(string $uuidShort, string $dockerImage): ActionResponse`

### Subusers

Namespace: `Gigabait93\Client\Subusers\Subusers`

- `list(string $uuidShort): ListBuilder`
- `create(string $uuidShort, string $email, array $permissions): ItemResponse`
- `update(string $uuidShort, string $subuserUuid, array $permissions): ItemResponse`
- `destroy(string $uuidShort, string $subuserUuid): ActionResponse`

## Examples

List files and download one:

```php
$files = $p->files->list($uuid, '/')->send();
foreach ($files->data as $it) {
    $name = $it['attributes']['name'] ?? '';
    if ($name === 'server.properties') {
        $content = $p->files->read($uuid, '/server.properties');
        break;
    }
}
```

Create a backup and wait for it to appear:

```php
$create = $p->backups->create($uuid, 'nightly');
if ($create->ok) {
    // poll list until it shows up
}
```

Send a power signal and a console command:

```php
$p->server->power($uuid, 'restart');
$p->server->command($uuid, 'say Update deployed');
```
