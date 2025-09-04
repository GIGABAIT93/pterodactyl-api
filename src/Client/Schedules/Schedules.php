<?php

declare(strict_types=1);

namespace Gigabait93\Client\Schedules;

use Gigabait93\Pterodactyl;
use Gigabait93\Support\Builders\ListBuilder;
use Gigabait93\Support\Responses\ActionResponse;
use Gigabait93\Support\Responses\ItemResponse;
use Gigabait93\Support\Responses\ListResponse;
use Gigabait93\Support\SubClient;

/**
 * Client API for managing server schedules.
 */
class Schedules extends SubClient
{
    /**
     * @param Pterodactyl $ptero Pterodactyl client instance
     */
    public function __construct(Pterodactyl $ptero)
    {
        parent::__construct($ptero, 'api/client/servers');
    }

    /**
     * List all schedules for a server.
     *
     * @param string $uuidShort Server identifier
     * @return ListBuilder
     */
    public function all(string $uuidShort): ListBuilder
    {
        return new ListBuilder($this, '/' . $uuidShort . '/schedules', ListResponse::class);
    }

    /**
     * Retrieve a single schedule.
     *
     * @param string $uuidShort Server identifier
     * @param string $scheduleId Schedule identifier
     * @return ItemResponse
     */
    public function get(string $uuidShort, string $scheduleId): ItemResponse
    {
        $r = $this->requestResponse('GET', '/' . $uuidShort . '/schedules/' . $scheduleId);

        return ItemResponse::fromBase($r);
    }

    /**
     * Create a new schedule on the server.
     *
     * @param string $uuidShort Server identifier
     * @param string $name Schedule name
     * @param string $minute Cron minute field
     * @param string $hour Cron hour field
     * @param string $month Cron month field
     * @param string $dayOfWeek Cron day of week field
     * @param string $dayOfMonth Cron day of month field
     * @param bool $isActive Whether the schedule is active
     * @param bool $onlyWhenOnline Run only when server online
     * @return ItemResponse
     */
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

    /**
     * Update an existing schedule.
     *
     * @param string $uuidShort Server identifier
     * @param int $scheduleId Schedule identifier
     * @param string $name Schedule name
     * @param string $minute Cron minute field
     * @param string $hour Cron hour field
     * @param string $month Cron month field
     * @param string $dayOfWeek Cron day of week field
     * @param string $dayOfMonth Cron day of month field
     * @param bool $isActive Whether the schedule is active
     * @param bool $onlyWhenOnline Run only when server online
     * @return ItemResponse
     */
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

    /**
     * Execute a schedule immediately.
     *
     * @param string $uuidShort Server identifier
     * @param int $scheduleId Schedule identifier
     * @return ActionResponse
     */
    public function execute(string $uuidShort, int $scheduleId): ActionResponse
    {
        $r = $this->requestResponse('POST', '/' . $uuidShort . '/schedules/' . $scheduleId . '/execute');

        return ActionResponse::fromBase($r);
    }

    /**
     * Delete a schedule.
     *
     * @param string $uuidShort Server identifier
     * @param string $scheduleId Schedule identifier
     * @return ActionResponse
     */
    public function destroy(string $uuidShort, string $scheduleId): ActionResponse
    {
        $r = $this->requestResponse('DELETE', '/' . $uuidShort . '/schedules/' . $scheduleId);

        return ActionResponse::fromBase($r);
    }

    /**
     * Create a task inside a schedule.
     *
     * @param string $uuidShort Server identifier
     * @param string $scheduleId Schedule identifier
     * @param array<string,mixed> $task Task payload
     * @return ItemResponse
     */
    public function createTask(string $uuidShort, string $scheduleId, array $task): ItemResponse
    {
        $r = $this->requestResponse('POST', '/' . $uuidShort . '/schedules/' . $scheduleId . '/tasks', $task);

        return ItemResponse::fromBase($r);
    }

    /**
     * Update a task in a schedule.
     *
     * @param string $uuidShort Server identifier
     * @param string $scheduleId Schedule identifier
     * @param string $taskId Task identifier
     * @param array<string,mixed> $task Task payload
     * @return ItemResponse
     */
    public function updateTask(string $uuidShort, string $scheduleId, string $taskId, array $task): ItemResponse
    {
        $r = $this->requestResponse('POST', '/' . $uuidShort . '/schedules/' . $scheduleId . '/tasks/' . $taskId, $task);

        return ItemResponse::fromBase($r);
    }

    /**
     * Delete a task from a schedule.
     *
     * @param string $uuidShort Server identifier
     * @param string $scheduleId Schedule identifier
     * @param string $taskId Task identifier
     * @return ActionResponse
     */
    public function deleteTask(string $uuidShort, string $scheduleId, string $taskId): ActionResponse
    {
        $r = $this->requestResponse('DELETE', '/' . $uuidShort . '/schedules/' . $scheduleId . '/tasks/' . $taskId);

        return ActionResponse::fromBase($r);
    }
}
