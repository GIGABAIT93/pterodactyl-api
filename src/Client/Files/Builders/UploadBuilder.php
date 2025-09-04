<?php

declare(strict_types=1);

namespace Gigabait93\Client\Files\Builders;

use Gigabait93\Client\Files\Files;
use Gigabait93\Support\Responses\ActionResponse;

/**
 * Builder for uploading one or more files to a Pterodactyl server.
 *
 * Usage:
 *   $resp = $files
 *       ->makeUpload($uuidShort)
 *       ->dir('/plugins/')
 *       ->addContents('readme.txt', 'Hello')
 *       ->addFile('/abs/path/to/local.zip', 'archive.zip')
 *       ->send();
 *
 * Notes:
 * - One HTTP request per file (most reliable for one-time signed URLs).
 * - If you pass a signed URL (->signedUrl() or send($signedUrl)),
 *   it will be used only for the FIRST file; the rest will fetch new URLs.
 *
 * @phpstan-type UploadItem array{
 *   name:string,
 *   path?:string,
 *   contents?:string,
 *   mime?:string
 * }
 */
final class UploadBuilder
{
    /** Target remote directory. Used “as-is” (no normalization). */
    private string $directory = '/';

    /**
     * Optional pre-fetched signed URL.
     * Not recommended when uploading more than one file because
     * many panels treat the URL as single-use.
     */
    private ?string $signedUrl = null;

    /**
     * Upload queue.
     *
     * @var array<int, UploadItem>
     */
    private array $items = [];

    /**
     * @param Files $files Files client
     * @param string $uuidShort Server identifier (uuid short)
     */
    public function __construct(
        private readonly Files  $files,
        private readonly string $uuidShort,
    ) {
    }

    /**
     * Set remote directory (kept exactly as provided).
     *
     * @param string $directory
     * @return self
     */
    public function dir(string $directory): self
    {
        $this->directory = $directory === '' ? '/' : $directory;

        return $this;
    }

    /**
     * Set a signed URL manually (optional).
     * Not recommended when uploading multiple files.
     *
     * @param string $url
     * @return self
     */
    public function signedUrl(string $url): self
    {
        $this->signedUrl = $url;

        return $this;
    }

    /**
     * Bulk add items.
     * Each item is either ['path' => '/abs/file', 'name' => 'a.txt', 'mime' => '...']
     * or ['contents' => '...', 'name' => 'b.txt', 'mime' => '...'].
     *
     * @param array<int, array<string,mixed>> $items
     * @return self
     */
    public function addMany(array $items): self
    {
        foreach ($items as $it) {
            if (isset($it['path'])) {
                $this->addFile((string)$it['path'], $it['name'] ?? null, $it['mime'] ?? null);
            } elseif (isset($it['contents'], $it['name'])) {
                $this->addContents((string)$it['name'], (string)$it['contents'], $it['mime'] ?? null);
            }
        }

        return $this;
    }

    /**
     * Queue a local file by filesystem path.
     *
     * @param string $localPath Absolute or relative path to the local file
     * @param string|null $asName Target filename on the server (defaults to basename of $localPath)
     * @param string|null $mime Optional MIME type hint
     * @return self
     */
    public function addFile(string $localPath, ?string $asName = null, ?string $mime = null): self
    {
        $name = $asName ?? basename($localPath);
        $item = ['name' => $name, 'path' => $localPath];
        if ($mime !== null) {
            $item['mime'] = $mime;
        }
        $this->items[] = $item;

        return $this;
    }

    /**
     * Queue an in-memory file (will be sent via CURLStringFile in Files::upload()).
     *
     * @param string $name Target filename on the server
     * @param string $contents Raw contents to upload
     * @param string|null $mime Optional MIME type hint (e.g., "text/plain")
     * @return self
     */
    public function addContents(string $name, string $contents, ?string $mime = null): self
    {
        $item = ['name' => $name, 'contents' => $contents];
        if ($mime !== null) {
            $item['mime'] = $mime;
        }
        $this->items[] = $item;

        return $this;
    }

    /**
     * Clear the upload queue.
     *
     * @return self
     */
    public function clear(): self
    {
        $this->items = [];

        return $this;
    }

    /**
     * Number of files queued.
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->items);
    }

    /**
     * Getter: directory (useful in tests).
     *
     * @return string
     */
    public function getDirectory(): string
    {
        return $this->directory;
    }

    /**
     * Getter: queued items (useful in tests).
     *
     * @return array<int, array{name:string, path?:string, contents?:string, mime?:string}>
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * Send files and verify presence via Files::all().
     * On success: data includes ['verified' => true, 'missing' => []].
     *
     * @param string|null $signedUrl Optional signed URL for the first file only
     * @return ActionResponse
     */
    public function sendAndVerify(?string $signedUrl = null): ActionResponse
    {
        $resp = $this->send($signedUrl);
        if (!$resp->ok) {
            return $resp;
        }

        // Fetch directory listing and verify expected names are present
        $list    = $this->files->all($this->uuidShort, $this->directory)->send();
        $present = [];
        foreach ((array)($list->data ?? []) as $it) {
            $present[] = (string)($it['attributes']['name'] ?? '');
        }

        $missing = [];
        foreach ($this->names() as $n) {
            if ($n !== '' && !in_array($n, $present, true)) {
                $missing[] = $n;
            }
        }

        $ok = empty($missing);

        return new ActionResponse(
            $ok,
            $resp->status,
            $resp->headers,
            ['verified' => $ok, 'missing' => $missing],
            $ok ? null : 'Uploaded but not all files found on panel',
            $resp->raw
        );
    }

    /**
     * Send files (one HTTP request per file).
     *
     * If a signed URL is provided (via ->signedUrl() or send($signedUrl)),
     * it will only be used for the FIRST file. The rest will fetch fresh URLs,
     * which is safer for one-time signed URL setups.
     *
     * @param string|null $signedUrl Optional signed URL to use for the first file only
     * @return ActionResponse
     */
    public function send(?string $signedUrl = null): ActionResponse
    {
        $last   = null;
        $preset = $signedUrl ?? $this->signedUrl;

        foreach ($this->items as $idx => $file) {
            $useUrl = ($idx === 0) ? $preset : null;

            $last = $this->files->upload(
                $this->uuidShort,
                $file,
                $this->directory,
                $useUrl
            );

            if (!$last->ok) {
                return $last;
            }
        }

        return $last ?? new ActionResponse(false, 0, [], null, 'No items to upload', null);
    }

    /**
     * Names of files queued (target names on the server).
     *
     * @return array<int,string>
     */
    public function names(): array
    {
        return array_map(static fn (array $i) => (string)($i['name'] ?? ''), $this->items);
    }
}
