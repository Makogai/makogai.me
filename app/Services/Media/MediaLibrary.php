<?php

namespace App\Services\Media;

use App\Models\Media;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class MediaLibrary
{
    public function storeImage(
        UploadedFile $file,
        int|string|null $userId = null,
        string $collection = 'uploads',
        ?string $alt = null,
        array $meta = [],
    ): Media {
        $disk = 'public';
        $directory = "media/{$collection}";

        $path = $file->storePublicly($directory, ['disk' => $disk]);

        [$width, $height] = $this->dimensions($file);

        return Media::query()->create([
            'user_id' => $userId ? (int) $userId : null,
            'disk' => $disk,
            'path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getClientMimeType(),
            'size_bytes' => $file->getSize() ?: 0,
            'width' => $width,
            'height' => $height,
            'alt' => $alt,
            'meta' => [
                ...$meta,
                'collection' => $collection,
                'ext' => $file->getClientOriginalExtension(),
                'name' => Str::limit($file->getClientOriginalName(), 140, '…'),
            ],
        ]);
    }

    /**
     * @return array{0: int|null, 1: int|null}
     */
    protected function dimensions(UploadedFile $file): array
    {
        $mime = (string) $file->getClientMimeType();

        if (! str_starts_with($mime, 'image/') || $mime === 'image/svg+xml') {
            return [null, null];
        }

        $path = $file->getRealPath();

        if (! $path) {
            return [null, null];
        }

        $info = @getimagesize($path);

        if (! is_array($info)) {
            return [null, null];
        }

        return [(int) ($info[0] ?? 0) ?: null, (int) ($info[1] ?? 0) ?: null];
    }
}
