<?php

declare(strict_types=1);

namespace Gigabait93\Applications\Servers\Builders;

use Gigabait93\Applications\Servers\Params\ServerBuildParams;
use Gigabait93\Applications\Servers\Params\ServerDetailsParams;
use Gigabait93\Applications\Servers\Params\ServerStartupParams;
use Gigabait93\Applications\Servers\Traits\ServerActions;
use Gigabait93\Applications\Servers\Traits\ServerIncludes;
use Gigabait93\Support\Builders\ItemBuilder;
use Gigabait93\Support\Builders\ListBuilder;
use Gigabait93\Support\Responses\ActionResponse;
use Gigabait93\Support\Responses\ItemResponse;
use Gigabait93\Support\SubClient;

class ServersItemBuilder extends ItemBuilder
{
    use ServerIncludes;
    use ServerActions;

    public function suspend(): ActionResponse
    {
        return $this->doSuspendServer($this->getId());
    }

    public function unsuspend(): ActionResponse
    {
        return $this->doUnsuspendServer($this->getId());
    }

    public function reinstall(): ActionResponse
    {
        // Prefer client Settings reinstall to avoid duplicating logic
        $uuid = $this->requireUuidShort();

        return $this->client->ptero()->settings->reinstall($uuid);
    }

    public function destroy(): ActionResponse
    {
        return $this->doDestroyServer($this->getId());
    }

    public function forceDelete(): ActionResponse
    {
        return $this->doForceDeleteServer($this->getId());
    }

    public function update(ServerDetailsParams $params): ItemResponse
    {
        return $this->doUpdateServerDetails($this->getId(), $params->toArray());
    }

    public function build(ServerBuildParams $params): ItemResponse
    {
        return $this->doBuildServer($this->getId(), $params->toArray());
    }

    public function startup(ServerStartupParams $params): ItemResponse
    {
        return $this->doStartupServer($this->getId(), $params->toArray());
    }

    // -------------------- Client API shortcuts (per-server) --------------------

    public function resources(): ItemResponse
    {
        $uuid = $this->requireUuidShort();

        return $this->client->ptero()->server->resources($uuid);
    }

    public function details(array $includes = []): ItemResponse
    {
        $uuid = $this->requireUuidShort();

        return $this->client->ptero()->server->details($uuid, $includes);
    }

    public function websocket(): ItemResponse
    {
        $uuid = $this->requireUuidShort();

        return $this->client->ptero()->server->websocket($uuid);
    }

    public function power(string $signal): ActionResponse
    {
        $uuid = $this->requireUuidShort();

        return $this->client->ptero()->server->power($uuid, $signal);
    }

    public function command(string $command): ActionResponse
    {
        $uuid = $this->requireUuidShort();

        return $this->client->ptero()->server->command($uuid, $command);
    }

    // Files
    public function files(string $path = '/'): ListBuilder
    {
        $uuid = $this->requireUuidShort();

        return $this->client->ptero()->files->list($uuid, $path);
    }

    public function fileRead(string $filePath): ItemResponse
    {
        $uuid = $this->requireUuidShort();

        return $this->client->ptero()->files->read($uuid, $filePath);
    }

    public function fileDownload(string $filePath): ItemResponse
    {
        $uuid = $this->requireUuidShort();

        return $this->client->ptero()->files->download($uuid, $filePath);
    }

    public function fileRename(string $oldName, string $newName, string $path = '/'): ActionResponse
    {
        $uuid = $this->requireUuidShort();

        return $this->client->ptero()->files->rename($uuid, $oldName, $newName, $path);
    }

    public function fileCopy(string $filePath): ActionResponse
    {
        $uuid = $this->requireUuidShort();

        return $this->client->ptero()->files->copy($uuid, $filePath);
    }

    public function fileWrite(string $filePath, string $content): ActionResponse
    {
        $uuid = $this->requireUuidShort();

        return $this->client->ptero()->files->write($uuid, $filePath, $content);
    }

    public function fileCompress(array $files, string $filePath = '/'): ActionResponse
    {
        $uuid = $this->requireUuidShort();

        return $this->client->ptero()->files->compress($uuid, $files, $filePath);
    }

    public function fileDecompress(string $fileName, string $filePath = '/'): ActionResponse
    {
        $uuid = $this->requireUuidShort();

        return $this->client->ptero()->files->decompress($uuid, $fileName, $filePath);
    }

    public function fileDestroy(array $files, string $filePath): ActionResponse
    {
        $uuid = $this->requireUuidShort();

        return $this->client->ptero()->files->destroy($uuid, $files, $filePath);
    }

    public function fileMkdir(string $folderName, string $path = '/'): ActionResponse
    {
        $uuid = $this->requireUuidShort();

        return $this->client->ptero()->files->mkdir($uuid, $folderName, $path);
    }

    public function fileExists(string $path, string $name): bool
    {
        $uuid = $this->requireUuidShort();

        return $this->client->ptero()->files->exists($uuid, $path, $name);
    }

    public function uploadBuilder(): \Gigabait93\Client\Files\Builders\UploadBuilder
    {
        $uuid = $this->requireUuidShort();

        return $this->client->ptero()->files->makeUpload($uuid);
    }

    // Database
    public function dbList(): ListBuilder
    {
        $uuid = $this->requireUuidShort();

        return $this->client->ptero()->database->list($uuid);
    }

    public function dbCreate(string $database, string $remote = '%'): ItemResponse
    {
        $uuid = $this->requireUuidShort();

        return $this->client->ptero()->database->create($uuid, $database, $remote);
    }

    public function dbResetPassword(string $database): ActionResponse
    {
        $uuid = $this->requireUuidShort();

        return $this->client->ptero()->database->resetPassword($uuid, $database);
    }

    public function dbDestroy(string $database): ActionResponse
    {
        $uuid = $this->requireUuidShort();

        return $this->client->ptero()->database->destroy($uuid, $database);
    }

    // Backups
    public function backupsList(): ListBuilder
    {
        $uuid = $this->requireUuidShort();

        return $this->client->ptero()->backups->list($uuid);
    }

    public function backupShow(string $backup): ItemResponse
    {
        $uuid = $this->requireUuidShort();

        return $this->client->ptero()->backups->show($uuid, $backup);
    }

    public function backupDownload(string $backup): ItemResponse
    {
        $uuid = $this->requireUuidShort();

        return $this->client->ptero()->backups->download($uuid, $backup);
    }

    public function backupCreate(string $name, string $ignoredFiles = ''): ItemResponse
    {
        $uuid = $this->requireUuidShort();

        return $this->client->ptero()->backups->create($uuid, $name, $ignoredFiles);
    }

    public function backupDestroy(string $backup): ActionResponse
    {
        $uuid = $this->requireUuidShort();

        return $this->client->ptero()->backups->destroy($uuid, $backup);
    }

    public function backupRestore(string $backup, bool $truncate = false): ActionResponse
    {
        $uuid = $this->requireUuidShort();

        return $this->client->ptero()->backups->restore($uuid, $backup, $truncate);
    }

    public function backupLockToggle(string $backup): ActionResponse
    {
        $uuid = $this->requireUuidShort();

        return $this->client->ptero()->backups->lockToggle($uuid, $backup);
    }

    // Schedules
    public function schedulesList(): ListBuilder
    {
        $uuid = $this->requireUuidShort();

        return $this->client->ptero()->schedules->list($uuid);
    }

    public function scheduleShow(string $scheduleId): ItemResponse
    {
        $uuid = $this->requireUuidShort();

        return $this->client->ptero()->schedules->show($uuid, $scheduleId);
    }

    public function scheduleCreate(string $name, string $minute, string $hour, string $month, string $dayOfWeek, string $dayOfMonth, bool $isActive = true, bool $onlyWhenOnline = true): ItemResponse
    {
        $uuid = $this->requireUuidShort();

        return $this->client->ptero()->schedules->create($uuid, $name, $minute, $hour, $month, $dayOfWeek, $dayOfMonth, $isActive, $onlyWhenOnline);
    }

    public function scheduleUpdate(int $scheduleId, string $name, string $minute, string $hour, string $month, string $dayOfWeek, string $dayOfMonth, bool $isActive = true, bool $onlyWhenOnline = true): ItemResponse
    {
        $uuid = $this->requireUuidShort();

        return $this->client->ptero()->schedules->update($uuid, $scheduleId, $name, $minute, $hour, $month, $dayOfWeek, $dayOfMonth, $isActive, $onlyWhenOnline);
    }

    public function scheduleExecute(int $scheduleId): ActionResponse
    {
        $uuid = $this->requireUuidShort();

        return $this->client->ptero()->schedules->execute($uuid, $scheduleId);
    }

    public function scheduleDestroy(string $scheduleId): ActionResponse
    {
        $uuid = $this->requireUuidShort();

        return $this->client->ptero()->schedules->destroy($uuid, $scheduleId);
    }

    public function scheduleCreateTask(string $scheduleId, array $task): ItemResponse
    {
        $uuid = $this->requireUuidShort();

        return $this->client->ptero()->schedules->createTask($uuid, $scheduleId, $task);
    }

    public function scheduleUpdateTask(string $scheduleId, string $taskId, array $task): ItemResponse
    {
        $uuid = $this->requireUuidShort();

        return $this->client->ptero()->schedules->updateTask($uuid, $scheduleId, $taskId, $task);
    }

    public function scheduleDeleteTask(string $scheduleId, string $taskId): ActionResponse
    {
        $uuid = $this->requireUuidShort();

        return $this->client->ptero()->schedules->deleteTask($uuid, $scheduleId, $taskId);
    }

    // Network
    public function allocationsList(): ListBuilder
    {
        $uuid = $this->requireUuidShort();

        return $this->client->ptero()->network->list($uuid);
    }

    public function allocationAssign(): ActionResponse
    {
        $uuid = $this->requireUuidShort();

        return $this->client->ptero()->network->assignAllocation($uuid);
    }

    public function allocationSetNote(string $allocationId, string $note): ActionResponse
    {
        $uuid = $this->requireUuidShort();

        return $this->client->ptero()->network->setNote($uuid, $allocationId, $note);
    }

    public function allocationSetPrimary(string $allocationId): ActionResponse
    {
        $uuid = $this->requireUuidShort();

        return $this->client->ptero()->network->setPrimary($uuid, $allocationId);
    }

    public function allocationDestroy(string $allocationId): ActionResponse
    {
        $uuid = $this->requireUuidShort();

        return $this->client->ptero()->network->destroy($uuid, $allocationId);
    }

    // Startup (client)
    public function startupVars(): ItemResponse
    {
        $uuid = $this->requireUuidShort();

        return $this->client->ptero()->startup->variables($uuid);
    }

    public function startupVarsUpdate(array $data): ActionResponse
    {
        $uuid = $this->requireUuidShort();

        return $this->client->ptero()->startup->update($uuid, $data);
    }

    // Settings (client)
    public function settingsRename(string $name): ActionResponse
    {
        $uuid = $this->requireUuidShort();

        return $this->client->ptero()->settings->rename($uuid, $name);
    }

    public function settingsSetDockerImage(string $dockerImage): ActionResponse
    {
        $uuid = $this->requireUuidShort();

        return $this->client->ptero()->settings->setDockerImage($uuid, $dockerImage);
    }

    // Subusers (client)
    public function subusersList(): ListBuilder
    {
        $uuid = $this->requireUuidShort();

        return $this->client->ptero()->subusers->list($uuid);
    }

    public function subuserCreate(string $email, array $permissions): ItemResponse
    {
        $uuid = $this->requireUuidShort();

        return $this->client->ptero()->subusers->create($uuid, $email, $permissions);
    }

    public function subuserUpdate(string $subuserUuid, array $permissions): ItemResponse
    {
        $uuid = $this->requireUuidShort();

        return $this->client->ptero()->subusers->update($uuid, $subuserUuid, $permissions);
    }

    public function subuserDestroy(string $subuserUuid): ActionResponse
    {
        $uuid = $this->requireUuidShort();

        return $this->client->ptero()->subusers->destroy($uuid, $subuserUuid);
    }

    // -------------------- internals --------------------
    private ?string $uuidShortCache = null;

    protected function serverClient(): SubClient
    {
        return $this->client;
    }

    private function requireUuidShort(): string
    {
        $u = $this->uuidShortCache ?? $this->resolveUuidShort();
        if (!is_string($u) || $u === '') {
            throw new \InvalidArgumentException('Cannot resolve uuidShort for this server');
        }

        return $this->uuidShortCache = $u;
    }

    private function resolveUuidShort(): ?string
    {
        $r = $this->send();
        if (!$r->ok) {
            return null;
        }
        $a    = is_array($r->data) ? $r->data : [];
        $uuid = $a['uuid'] ?? null;

        return $a['identifier'] ?? ($a['uuidShort'] ?? (is_string($uuid) ? substr($uuid, 0, 8) : null));
    }
}
