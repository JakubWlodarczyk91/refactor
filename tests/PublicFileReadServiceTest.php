<?php

namespace App\Tests\Service;

use App\Service\PublicFileReadService;
use phpmock\phpunit\PHPMock;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\Filesystem\Filesystem;

class PublicFileReadServiceTest extends TestCase
{
    use PHPMock;

    private Filesystem $filesystemMock;
    private string $projectDir;
    private PublicFileReadService $service;

    protected function setUp(): void
    {
        $this->filesystemMock = $this->createMock(Filesystem::class);
        $this->projectDir = '/path/to/project';
        $this->service = new PublicFileReadService($this->filesystemMock, $this->projectDir);
    }

    public function testReadFileNotFound()
    {
        $fileName = 'nonexistent-file.json';
        $filePath = $this->projectDir . '/public/' . $fileName;

        $this->filesystemMock->method('exists')->with($filePath)->willReturn(false);

        $this->expectException(FileNotFoundException::class);
        $this->expectExceptionMessage('File not found.');

        $this->service->read($fileName);
    }

    public function testReadSuccessful()
    {
        $fileName = 'data.json';
        $filePath = $this->projectDir . '/public/' . $fileName;
        $fileContent = '{"key1":"value1"}' . PHP_EOL . '{"key2":"value2"}';

        $this->filesystemMock->method('exists')->with($filePath)->willReturn(true);

        $fileGetContentsMock = $this->getFunctionMock('App\Service', 'file_get_contents');
        $fileGetContentsMock->expects($this->once())->with($filePath)->willReturn($fileContent);

        $result = $this->service->read($fileName);

        $expected = [
            ['key1' => 'value1'],
            ['key2' => 'value2']
        ];
        $this->assertEquals($expected, $result);
    }
}