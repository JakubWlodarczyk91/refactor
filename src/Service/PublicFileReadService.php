<?php

namespace App\Service;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\Filesystem\Filesystem;

class PublicFileReadService
{
    public function __construct(private Filesystem $filesystem, private string $projectDir)
    {}
    public function read(string $name): array
    {
        $filePath = $this->projectDir . '/public/'. $name;
        if (!$this->filesystem->exists($filePath)) {
            throw new FileNotFoundException('File not found.');
        }

        $fileContent = file_get_contents($filePath);
        $lines = explode(PHP_EOL, $fileContent);
        $data = [];

        foreach ($lines as $line) {
            $decoded = json_decode($line, true);
            if ($decoded !== null) {
                $data[] = $decoded;
            }
        }

        return $data;
    }
}