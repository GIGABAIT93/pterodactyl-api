<?php

declare(strict_types=1);

namespace Gigabait93\Support\Responses;

use JsonSerializable;
use Psr\Http\Message\ResponseInterface as HttpResponse;

/**
 * Common response wrapper for Pterodactyl API requests.
 *
 * - $data     → normalized JSON "data" (if present), otherwise whole decoded payload
 * - $meta     → normalized JSON "meta" (if present), otherwise null
 * - $payload  → full decoded JSON (top-level), for fallback access
 * - $errors   → Pterodactyl-style errors array (if any)
 */
class BaseResponse implements JsonSerializable
{
    /** True when HTTP status is 2xx */
    public bool $ok;

    /** HTTP status code */
    public int $status;

    /** Flattened response headers: name => "v1, v2" */
    public array $headers;

    /** Normalized payload "data" (or full payload if "data" is absent) */
    public mixed $data;

    /** Normalized payload "meta" (if present) */
    public mixed $meta;

    /** Short error message (maybe null for ok responses) */
    public ?string $error;

    /** Raw response body (string) */
    public ?string $raw;

    /**
     * Pterodactyl-style errors array (if any), e.g.:
     * [
     *   ["code" => "...", "status" => 422, "detail" => "...", "meta" => ["source" => ...]],
     *   ...
     * ]
     * @var array<int, array{code?:string,status?:int|string,detail?:string,meta?:array}>
     */
    public array $errors = [];

    /** Full decoded JSON payload (top-level), or null if not JSON/decoding failed */
    public ?array $payload = null;

    // Rate limit info (if provided by API)
    public ?int $rateLimit     = null;         // X-RateLimit-Limit
    public ?int $rateRemaining = null;     // X-RateLimit-Remaining
    public ?int $rateReset     = null;         // X-RateLimit-Reset (unix ts or seconds)
    public ?int $retryAfter    = null;        // Retry-After (seconds)

    public function __construct(
        bool    $ok,
        int     $status,
        array   $headers = [],
        mixed   $data = null,
        ?string $error = null,
        ?string $raw = null,
        mixed   $meta = null,
        ?array  $payload = null
    ) {
        $this->ok      = $ok;
        $this->status  = $status;
        $this->headers = $headers;
        $this->data    = $data;
        $this->meta    = $meta;
        $this->error   = $error;
        $this->raw     = $raw;
        $this->payload = $payload;
    }

    /**
     * Copy non-constructor meta from another response instance
     * (e.g., detailed errors and rate-limit fields).
     */
    protected function copyMetaFrom(BaseResponse $base): void
    {
        $this->errors        = $base->errors;
        $this->rateLimit     = $base->rateLimit;
        $this->rateRemaining = $base->rateRemaining;
        $this->rateReset     = $base->rateReset;
        $this->retryAfter    = $base->retryAfter;
    }

    /**
     * Build BaseResponse from PSR-7 HTTP response.
     */
    public static function fromHttp(HttpResponse $resp): self
    {
        $status = $resp->getStatusCode();
        $ok     = $status >= 200 && $status < 300;

        $raw = (string)$resp->getBody();

        $headers = array_map(function ($values) {
            return implode(', ', $values);
        }, $resp->getHeaders());

        $contentType = strtolower($resp->getHeaderLine('Content-Type'));
        $payload     = null;
        $data        = null;
        $meta        = null;
        $errors      = [];

        $shouldTryJson = str_contains($contentType, 'json')
            || (isset($raw[0]) && ($raw[0] === '{' || $raw[0] === '['));

        if ($shouldTryJson) {
            $tmp = json_decode($raw, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($tmp)) {
                $payload = $tmp;
                $data    = $tmp['data'] ?? ($tmp['attributes'] ?? $tmp);
                $meta    = $tmp['meta'] ?? null;

                if (!$ok && isset($tmp['errors']) && is_array($tmp['errors'])) {
                    $errors = $tmp['errors'];
                }
            }
        }

        $shortErr = null;
        if (!$ok) {
            if (is_array($payload) && isset($payload['error']) && is_string($payload['error'])) {
                $shortErr = $payload['error'];
            } else {
                $shortErr = 'HTTP ' . $status;
            }
        }

        $inst         = new self($ok, $status, $headers, $data, $shortErr, $raw, $meta, $payload);
        $inst->errors = $errors;

        $inst->hydrateRateLimitsFromHeaders($headers);

        return $inst;

    }

    /** Serialize only public properties, never methods. */
    public function jsonSerialize(): array
    {
        // Only serialize declared public properties
        return get_object_vars($this);
    }

    // Note: no __debugInfo here to avoid duplicate keys in Symfony VarDumper

    /** Suggest wait time in seconds (HTTP 429 rate limit scenarios). */
    public function retryAfterSeconds(): ?int
    {
        if (is_int($this->retryAfter) && $this->retryAfter > 0) {
            return $this->retryAfter;
        }
        // Some gateways set X-RateLimit-Reset to a unix timestamp
        if (is_int($this->rateReset) && $this->rateReset > 0) {
            $now = time();
            if ($this->rateReset > $now) {
                return $this->rateReset - $now;
            }
        }

        return null;
    }

    protected function hydrateRateLimitsFromHeaders(array $headers): void
    {
        $h = array_change_key_case($headers);

        $this->rateLimit     = self::toIntNullable($h['x-ratelimit-limit'] ?? null);
        $this->rateRemaining = self::toIntNullable($h['x-ratelimit-remaining'] ?? null);
        $this->rateReset     = self::toIntNullable($h['x-ratelimit-reset'] ?? null);
        $retry               = $h['retry-after'] ?? null;
        $this->retryAfter    = self::toIntNullable($retry);
        if ($this->retryAfter === null && is_string($retry)) {
            $ts = strtotime($retry);
            if ($ts !== false) {
                $sec              = $ts - time();
                $this->retryAfter = max($sec, 0);
            }
        }
    }

    public function header(string $name): ?string
    {
        $h   = array_change_key_case($this->headers);
        $key = strtolower($name);

        return $h[$key] ?? null;
    }

    private static function toIntNullable(mixed $v): ?int
    {
        if ($v === null || $v === '') {
            return null;
        }
        if (is_numeric($v)) {
            return (int)$v;
        }

        return null;
    }

    /**
     * Human-friendly explanation based on Pterodactyl error format.
     */
    public function explain(): ?string
    {
        if ($this->ok) {
            return null;
        }

        $parts = [];

        if (!empty($this->errors)) {
            foreach ($this->errors as $err) {
                $seg = [];
                if (!empty($err['code'])) {
                    $seg[] = '[' . $err['code'] . ']';
                }
                if (!empty($err['detail'])) {
                    $seg[] = (string)$err['detail'];
                }
                if (!empty($seg)) {
                    $parts[] = implode(' ', $seg);
                }
                if (!empty($err['meta']) && is_array($err['meta']) && !empty($err['meta']['source'])) {
                    $parts[] = 'Field: ' . json_encode($err['meta']['source']);
                }
            }
        }

        if (empty($parts)) {
            $map = [
                400 => 'Bad request — invalid parameters.',
                401 => 'Unauthorized — check API token.',
                403 => 'Forbidden — insufficient permissions.',
                404 => 'Not found — resource does not exist.',
                409 => 'Conflict — state prevents this action.',
                422 => 'Validation failed — invalid data provided.',
                429 => 'Too many requests.',
                500 => 'Server error — try again later.',
                502 => 'Bad gateway.',
                503 => 'Service unavailable.',
            ];
            $parts[] = $map[$this->status] ?? ('HTTP ' . $this->status);
        }

        return implode(' | ', $parts);
    }
}
