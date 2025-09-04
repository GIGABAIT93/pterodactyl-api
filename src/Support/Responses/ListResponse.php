<?php

declare(strict_types=1);

namespace Gigabait93\Support\Responses;

/**
 * Response containing a collection of items with pagination data.
 */
class ListResponse extends BaseResponse
{
    public array $pagination;

    /** Build a ListResponse from a BaseResponse and normalize pagination. */
    public static function fromBase(BaseResponse $base): self
    {
        $inst = new static($base->ok, $base->status, $base->headers, $base->data, $base->error, $base->raw, $base->meta, $base->payload,);
        $inst->copyMetaFrom($base);
        $inst->pagination = is_array($base->meta) ? ($base->meta['pagination'] ?? []) : [];

        return $inst;
    }
}
