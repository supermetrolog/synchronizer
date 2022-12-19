<?php

namespace tests\unit\mocks;

use Generator;
use PHPUnit\Framework\TestCase;
use Supermetrolog\Synchronizer\interfaces\SourceRepositoryInterface;
use Supermetrolog\Synchronizer\interfaces\StreamInterface;

class SourceRepository extends TestCase
{
    public static function getMock(): SourceRepositoryInterface
    {
        $self = new static();

        /** @var StreamInterface $streamMock */
        $streamMock = $self->getStreamMock();
        /** @var SourceRepositoryInterface $sourceRepoMock */
        $sourceRepoMock = $self->getSourceRepoMock($streamMock);
        return $sourceRepoMock;
    }
    public function getSourceRepoMock(StreamInterface $streamMock)
    {
        /** @var \PHPUnit\Framework\MockObject\MockObject $sourceRepoMock */
        $sourceRepoMock = $this->createMock(SourceRepositoryInterface::class);
        $sourceRepoMock->method("getStream")->willReturn($streamMock);
        return $sourceRepoMock;
    }
    public function getStreamMock()
    {
        /** @var \PHPUnit\Framework\MockObject\MockObject $streamMock */
        $streamMock = $this->createMock(StreamInterface::class);
        $streamMock->method("read")->will($this->returnCallback([$this, 'streamReadCallback']));
        return $streamMock;
    }

    public function streamReadCallback(): Generator
    {
        $files = self::getFiles();
        foreach ($files as $file) {
            yield $file;
        }
    }

    public static function getFiles(): array
    {
        return File::getMocks(self::getFilesParams());
    }

    public static function getFilesParams(): array
    {
        $dir_1 = [
            'isDir' => true,
            'hash' => "",
            'parent' => null,
            'uniqueName' => '/dir_1'
        ];
        $dir_2 = [
            'isDir' => true,
            'hash' => "",
            'parent' => null,
            'uniqueName' => '/dir_2'
        ];
        $file_1 = [
            'isDir' => false,
            'hash' => "file_1_1",
            'parent' => null,
            'uniqueName' => '/file_1.txt'
        ];
        $file_1_1 = [
            'isDir' => false,
            'hash' => "file_1_1",
            'parent' => $dir_1,
            'uniqueName' => '/dir_1/file_1_1.txt'
        ];

        $file_1_2 = [
            'isDir' => false,
            'hash' => "file_1_2",
            'parent' => $dir_1,
            'uniqueName' => '/dir_1/file_1_2.txt'
        ];
        $file_1_3 = [
            'isDir' => false,
            'hash' => "file_1_3",
            'parent' => $dir_1,
            'uniqueName' => '/dir_1/file_1_3.jpg'
        ];
        $dir_1_1 = [
            'isDir' => true,
            'hash' => "",
            'parent' => $dir_1,
            'uniqueName' => '/dir_1/dir_1_1'
        ];
        $dir_1_1_1 = [
            'isDir' => true,
            'hash' => "",
            'parent' => $dir_1_1,
            'uniqueName' => '/dir_1/dir_1_1/dir_1_1_1'
        ];
        $file_1_1_1_1 = [
            'isDir' => false,
            'hash' => "file_1_1_1_1",
            'parent' => $dir_1_1_1,
            'uniqueName' => '/dir_1/dir_1_1/dir_1_1_1/file_1_1_1_1.jpg'
        ];
        $file_1_1_1_2 = [
            'isDir' => false,
            'hash' => "file_1_1_1_2",
            'parent' => $dir_1_1_1,
            'uniqueName' => '/dir_1/dir_1_1/dir_1_1_1/file_1_1_1_2.txt'
        ];
        return [
            $dir_1,
            $dir_2,
            $file_1,
            $file_1_1,
            $file_1_2,
            $file_1_3,
            $dir_1_1,
            $dir_1_1_1,
            $file_1_1_1_1,
            $file_1_1_1_2,
        ];
    }
}
