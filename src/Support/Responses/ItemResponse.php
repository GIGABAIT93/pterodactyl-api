<?php

declare(strict_types=1);

namespace Gigabait93\Support\Responses;

/**
 * Response containing a single data object.
 */
class ItemResponse extends BaseResponse
{
    public ?int $id = null;

    public static function fromBase(BaseResponse $base): self
    {
        $inst = new static($base->ok, $base->status, $base->headers, $base->data, $base->error, $base->raw, $base->meta, $base->payload,);
        $inst->copyMetaFrom($base);
        $inst->id = isset($base->data['id']) ? (int)$base->data['id'] : null;

        return $inst;
    }
}
