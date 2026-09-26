<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class ItemImageStorage
{
    private const MAX_BYTES = 5 * 1024 * 1024;

    private const MIME_TYPES = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];

    public function __construct(private readonly string $directory)
    {
    }

    public function store(UploadedFile $upload): string
    {
        if (!$upload->isValid()) {
            throw new \InvalidArgumentException('Das Bild konnte nicht hochgeladen werden.');
        }

        if ($upload->getSize() > self::MAX_BYTES) {
            throw new \InvalidArgumentException('Das Bild darf höchstens 5 MB groß sein.');
        }

        $mimeType = (new \finfo(FILEINFO_MIME_TYPE))->file($upload->getPathname());
        if (!is_string($mimeType) || !isset(self::MIME_TYPES[$mimeType])) {
            throw new \InvalidArgumentException('Erlaubte Bildformate sind JPEG, PNG und WebP.');
        }

        if (!@getimagesize($upload->getPathname())) {
            throw new \InvalidArgumentException('Die hochgeladene Datei ist kein gültiges Bild.');
        }

        if (!is_dir($this->directory) && !mkdir($this->directory, 0770, true) && !is_dir($this->directory)) {
            throw new \RuntimeException('Das Bildverzeichnis konnte nicht angelegt werden.');
        }

        $filename = bin2hex(random_bytes(16)).'.'.self::MIME_TYPES[$mimeType];
        $upload->move($this->directory, $filename);

        return $filename;
    }

    public function path(string $filename): string
    {
        if (basename($filename) !== $filename) {
            throw new \InvalidArgumentException('Ungültiger Bildname.');
        }

        return $this->directory.'/'.$filename;
    }

    public function remove(?string $filename): void
    {
        if ($filename === null) {
            return;
        }

        $path = $this->path($filename);
        if (is_file($path) && !unlink($path)) {
            throw new \RuntimeException('Das alte Produktbild konnte nicht entfernt werden.');
        }
    }
}
