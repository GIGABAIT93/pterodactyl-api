<?php

declare(strict_types=1);

namespace Gigabait93\Client\Files;

use CURLFile;
use CURLStringFile;
use Gigabait93\Client\Files\Builders\UploadBuilder;
use Gigabait93\Pterodactyl;
use Gigabait93\Support\Builders\ListBuilder;
use Gigabait93\Support\Responses\ActionResponse;
use Gigabait93\Support\Responses\ItemResponse;
use Gigabait93\Support\Responses\ListResponse;
use Gigabait93\Support\SubClient;

class Files extends SubClient
{
    public function __construct(Pterodactyl $ptero)
    {
        parent::__construct($ptero, 'api/client/servers');
    }

    public function list(string $uuidShort, string $path = '/'): ListBuilder
    {
        return (new ListBuilder($this, '/' . $uuidShort . '/files/list', ListResponse::class))
            ->param('directory', $path);
    }

    public function read(string $uuidShort, string $filePath): ItemResponse
    {
        $r = $this->requestResponse('GET', '/' . $uuidShort . '/files/contents', ['file' => '/' . ltrim($filePath, '/')]);
        // Fallback for non-JSON: expose raw body as contents
        $r->data = $r->data ?? ['contents' => $r->raw];

        return ItemResponse::fromBase($r);
    }

    public function download(string $uuidShort, string $filePath): ItemResponse
    {
        $r = $this->requestResponse('GET', '/' . $uuidShort . '/files/download', ['file' => $filePath]);

        return ItemResponse::fromBase($r);
    }

    public function rename(string $uuidShort, string $oldName, string $newName, string $path = '/'): ActionResponse
    {
        $r = $this->requestResponse('PUT', '/' . $uuidShort . '/files/rename', ['root' => $path, 'files' => [['from' => $oldName, 'to' => $newName]]]);

        return ActionResponse::fromBase($r);
    }

    public function copy(string $uuidShort, string $filePath): ActionResponse
    {
        $r = $this->requestResponse('POST', '/' . $uuidShort . '/files/copy', ['location' => $filePath]);

        return ActionResponse::fromBase($r);
    }

    public function write(string $uuidShort, string $filePath, string $content): ActionResponse
    {
        $path    = '/' . $uuidShort . '/files/write?file=' . rawurlencode($filePath);
        $r       = $this->requestResponse('POST', $path, $content);
        $r->data = $r->data ?? ['written' => true];

        return ActionResponse::fromBase($r);
    }

    public function compress(string $uuidShort, array $files, string $filePath = '/'): ActionResponse
    {
        $r = $this->requestResponse('POST', '/' . $uuidShort . '/files/compress', ['root' => $filePath, 'files' => $files]);

        return ActionResponse::fromBase($r);
    }

    public function decompress(string $uuidShort, string $fileName, string $filePath = '/'): ActionResponse
    {
        $r = $this->requestResponse('POST', '/' . $uuidShort . '/files/decompress', ['root' => $filePath, 'file' => $fileName]);

        return ActionResponse::fromBase($r);
    }

    public function destroy(string $uuidShort, array $files, string $filePath): ActionResponse
    {
        $r = $this->requestResponse('POST', '/' . $uuidShort . '/files/delete', ['root' => $filePath, 'files' => $files]);

        return ActionResponse::fromBase($r);
    }

    public function mkdir(string $uuidShort, string $folderName, string $path = '/'): ActionResponse
    {
        $r = $this->requestResponse('POST', '/' . $uuidShort . '/files/create-folder', ['root' => $path, 'name' => $folderName]);

        return ActionResponse::fromBase($r);
    }

    /** Check if a file exists in a directory. */
    public function exists(string $uuidShort, string $path, string $name): bool
    {
        $list = $this->list($uuidShort, $path)->send();
        foreach ($list->data as $it) {
            if (($it['attributes']['name'] ?? null) === $name) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $uuidShort
     * @param array{path?:string,contents?:string,name?:string,mime?:string} $file
     * @param string $directory
     * @param string|null $signedUrl
     * @return ActionResponse
     */
    public function upload(string $uuidShort, array $file, string $directory = '/', ?string $signedUrl = null): ActionResponse
    {
        // 1) Prepare CURL file
        $name = (string)($file['name'] ?? 'file');

        if (isset($file['contents'])) {
            $mime     = (string)($file['mime'] ?? 'application/octet-stream');
            $curlFile = new CURLStringFile((string)$file['contents'], $name, $mime);
        } elseif (isset($file['path'])) {
            $path = (string)$file['path'];
            if (!is_file($path)) {
                return new ActionResponse(false, 0, [], null, "File not found: {$path}", null);
            }
            $mime     = $file['mime'] ?? null;
            $postName = $name ?: basename($path);
            $curlFile = new CURLFile($path, $mime, $postName);
        } else {
            return new ActionResponse(false, 0, [], null, 'Upload item requires path or contents', null);
        }

        // 2) Signed URL (or use external one)
        if (!$signedUrl) {
            $urlResp = $this->uploadUrl($uuidShort, $directory);
            if (!$urlResp->ok) {
                return new ActionResponse(false, $urlResp->status, $urlResp->headers, null, $urlResp->error ?: 'Cannot get upload URL', $urlResp->raw);
            }
            $data      = $urlResp->data             ?? [];
            $signedUrl = $data['attributes']['url'] ?? ($data['url'] ?? null);
            if (!is_string($signedUrl) || $signedUrl === '') {
                return new ActionResponse(false, $urlResp->status, $urlResp->headers, null, 'Upload URL missing in response', $urlResp->raw);
            }
        }

        $ch = curl_init($signedUrl);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS     => [
                'files'     => $curlFile,
                'directory' => $directory,
            ],
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS      => 5,
            CURLOPT_HTTPHEADER     => ['Accept: application/json', 'Expect:'],
        ]);
        $raw  = curl_exec($ch);
        $err  = curl_error($ch) ?: null;
        $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $ok = !$err && ($code === 200 || $code === 204);

        return new ActionResponse($ok, $code, [], ['uploaded' => $ok ? 1 : 0, 'name' => $name], $err, is_string($raw) ? $raw : null);
    }

    /** Signed URL for uploading to the given directory. */
    public function uploadUrl(string $uuidShort, string $directory = '/'): ItemResponse
    {
        $r    = $this->requestResponse('GET', '/' . $uuidShort . '/files/upload', ['directory' => $directory]);
        $data = $r->data;

        if (is_array($data) && isset($data['url']) && is_string($data['url'])) {
            $url = $data['url'];
            if (!str_contains($url, 'directory=')) {
                $sep         = str_contains($url, '?') ? '&' : '?';
                $data['url'] = $url . $sep . 'directory=' . rawurlencode($directory);
            }
        }

        $r->data = $data;

        return ItemResponse::fromBase($r);
    }

    public function makeUpload(string $uuidShort): UploadBuilder
    {
        return new UploadBuilder($this, $uuidShort);
    }
}
