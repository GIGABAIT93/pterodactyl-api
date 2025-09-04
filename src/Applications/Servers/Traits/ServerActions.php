<?php

declare(strict_types=1);

namespace Gigabait93\Applications\Servers\Traits;

use Gigabait93\Support\Responses\ActionResponse;
use Gigabait93\Support\Responses\ItemResponse;
use Gigabait93\Support\SubClient;

/**
 * Shared server actions for Application API.
 *
 * Provides protected low-level helpers (do*) that accept an explicit server ID.
 * Public wrappers in concrete classes (e.g., Servers, ServersItemBuilder)
 * can forward either an explicit ID or the current builder's ID.
 */
trait ServerActions
{
    /** Implement in consumer to return underlying SubClient bound to servers endpoint. */
    abstract protected function serverClient(): SubClient;

    protected function doUpdateServerDetails(int|string $id, array $params): ItemResponse
    {
        $r = $this->serverClient()->requestResponse('PATCH', '/' . $id . '/details', $params);

        return ItemResponse::fromBase($r);
    }

    protected function doBuildServer(int|string $id, array $params): ItemResponse
    {
        $r = $this->serverClient()->requestResponse('PATCH', '/' . $id . '/build', $params);

        return ItemResponse::fromBase($r);
    }

    protected function doStartupServer(int|string $id, array $params): ItemResponse
    {
        $r = $this->serverClient()->requestResponse('PATCH', '/' . $id . '/startup', $params);

        return ItemResponse::fromBase($r);
    }

    protected function doSuspendServer(int|string $id): ActionResponse
    {
        $r = $this->serverClient()->requestResponse('POST', '/' . $id . '/suspend');

        return ActionResponse::fromBase($r);
    }

    protected function doUnsuspendServer(int|string $id): ActionResponse
    {
        $r = $this->serverClient()->requestResponse('POST', '/' . $id . '/unsuspend');

        return ActionResponse::fromBase($r);
    }

    protected function doReinstallServer(int|string $id): ActionResponse
    {
        $r = $this->serverClient()->requestResponse('POST', '/' . $id . '/reinstall');

        return ActionResponse::fromBase($r);
    }

    protected function doDestroyServer(int|string $id): ActionResponse
    {
        $r = $this->serverClient()->requestResponse('DELETE', '/' . $id);

        return ActionResponse::fromBase($r);
    }

    protected function doForceDeleteServer(int|string $id): ActionResponse
    {
        $r = $this->serverClient()->requestResponse('DELETE', '/' . $id . '/force');

        return ActionResponse::fromBase($r);
    }
}
