<?php

declare(strict_types=1);

namespace Gigabait93\Client\Schedules;

use Gigabait93\Pterodactyl;
use Gigabait93\Support\Builders\ListBuilder;
use Gigabait93\Support\Responses\ActionResponse;
use Gigabait93\Support\Responses\ItemResponse;
use Gigabait93\Support\Responses\ListResponse;
use Gigabait93\Support\SubClient;

class Schedules extends SubClient
{
    public function __construct(Pterodactyl $ptero)
    {
        parent::__construct($ptero, 'api/client/servers');
    }

    public function list(string $uuidShort): ListBuilder
    {
        return new ListBuilder($this, '/' . $uuidShort . '/schedules', ListResponse::class);
    }

    public function show(string $uuidShort, string $scheduleId): ItemResponse
    {
        $r = $this->requestResponse('GET', '/' . $uuidShort . '/schedules/' . $scheduleId);

        return ItemResponse::fromBase($r);
    }

    public function create(string $uuidShort, string $name, string $minute, string $hour, string $month, string $dayOfWeek, string $dayOfMonth, bool $isActive = true, bool $onlyWhenOnline = true): ItemResponse
    {
        $r = $this->requestResponse('POST', '/' . $uuidShort . '/schedules', [
            'name'             => $name,
            'minute'           => $minute,
            'hour'             => $hour,
            'month'            => $month,
            'day_of_week'      => $dayOfWeek,
            'day_of_month'     => $dayOfMonth,
            'is_active'        => $isActive,
            'only_when_online' => $onlyWhenOnline,
        ]);

        return ItemResponse::fromBase($r);
    }

    public function update(string $uuidShort, int $scheduleId, string $name, string $minute, string $hour, string $month, string $dayOfWeek, string $dayOfMonth, bool $isActive = true, bool $onlyWhenOnline = true): ItemResponse
    {
        $r = $this->requestResponse('POST', '/' . $uuidShort . '/schedules/' . $scheduleId, [
            'name'             => $name,
            'minute'           => $minute,
            'hour'             => $hour,
            'month'            => $month,
            'day_of_week'      => $dayOfWeek,
            'day_of_month'     => $dayOfMonth,
            'is_active'        => $isActive,
            'only_when_online' => $onlyWhenOnline,
        ]);

        return ItemResponse::fromBase($r);
    }

    public function execute(string $uuidShort, int $scheduleId): ActionResponse
    {
        $r = $this->requestResponse('POST', '/' . $uuidShort . '/schedules/' . $scheduleId . '/execute');

        return ActionResponse::fromBase($r);
    }

    public function destroy(string $uuidShort, string $scheduleId): ActionResponse
    {
        $r = $this->requestResponse('DELETE', '/' . $uuidShort . '/schedules/' . $scheduleId);

        return ActionResponse::fromBase($r);
    }

    public function createTask(string $uuidShort, string $scheduleId, array $task): ItemResponse
    {
        $r = $this->requestResponse('POST', '/' . $uuidShort . '/schedules/' . $scheduleId . '/tasks', $task);

        return ItemResponse::fromBase($r);
    }

    public function updateTask(string $uuidShort, string $scheduleId, string $taskId, array $task): ItemResponse
    {
        $r = $this->requestResponse('POST', '/' . $uuidShort . '/schedules/' . $scheduleId . '/tasks/' . $taskId, $task);

        return ItemResponse::fromBase($r);
    }

    public function deleteTask(string $uuidShort, string $scheduleId, string $taskId): ActionResponse
    {
        $r = $this->requestResponse('DELETE', '/' . $uuidShort . '/schedules/' . $scheduleId . '/tasks/' . $taskId);

        return ActionResponse::fromBase($r);
    }

}
