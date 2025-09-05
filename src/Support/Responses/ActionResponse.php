<?php

declare(strict_types=1);

namespace Gigabait93\Support\Responses;

/**
 * Response for actions that do not return an entity.
 */
class ActionResponse extends BaseResponse
{
    public ?string $message = null;

    public static function fromBase(BaseResponse $base): self
    {
        $inst = new static($base->ok, $base->status, $base->headers, $base->data, $base->error, $base->raw, $base->meta, $base->payload);
        $inst->copyMetaFrom($base);
        $msg = $base->error;
        if ($msg === null && is_array($base->data)) {
            $msg = $base->data['message'] ?? ($base->data['meta']['message'] ?? null);
        }
        $inst->message = is_string($msg) ? $msg : null;

        return $inst;
    }

    public function success(): bool
    {
        return $this->ok;
    }

    public function message(): ?string
    {
        return $this->message;
    }
}
