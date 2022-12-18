<?php

namespace tests\services\sync\mocks;

use PHPUnit\Framework\TestCase;
use Supermetrolog\Synchronizer\services\sync\interfaces\AlreadySynchronizedRepositoryInterface;
use Supermetrolog\Synchronizer\services\sync\interfaces\FileInterface;

class AlreadySynchronizedRepo extends TestCase
{
    public static function getEmptyMock(): AlreadySynchronizedRepositoryInterface
    {
        $self = new static();
        return $self->emptyMock();
    }

    public function emptyMock(): AlreadySynchronizedRepositoryInterface
    {
        /** @var \PHPUnit\Framework\MockObject\MockObject $mock */
        $mock = $this->createMock(AlreadySynchronizedRepositoryInterface::class);
        $mock->method('isEmpty')->willReturn(true);
        $mock->method('findFile')->willReturn(null);
        $mock->method('getNotDirtyFiles')->willReturn([]);
        /** @var AlreadySynchronizedRepositoryInterface $mock */
        return $mock;
    }

    public static function getNotEmptyMock(): AlreadySynchronizedRepositoryInterface
    {
        $self = new static;
        return $self->notEmptyMock();
    }

    public function notEmptyMock()
    {
        /** @var \PHPUnit\Framework\MockObject\MockObject $mock */
        $mock = $this->createMock(AlreadySynchronizedRepositoryInterface::class);
        $mock->method('isEmpty')->willReturn(false);
        $mock->method('findFile')->will($this->returnCallback([$this, 'findFile']));
        $mock->method('getNotDirtyFiles')->willReturn(File::getMocks(
            array_filter(self::getFilesParams(), function ($elem) {
                if (array_key_exists('deleted', $elem)) {
                    return $elem;
                }
            })
        ));
        /** @var AlreadySynchronizedRepositoryInterface $mock */
        return $mock;
    }
    public function findFile(FileInterface $findedFile): ?FileInterface
    {
        $files = self::getFiles();
        foreach ($files as $file) {
            if ($file->getUniqueName() === $findedFile->getUniqueName()) {
                return $file;
            }
        }
        return null;
    }
    /** @return FileInterface[] */
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

        $file_1_2_updated = [
            'isDir' => false,
            'hash' => "file_1_2_updated",
            'parent' => $dir_1,
            'uniqueName' => '/dir_1/file_1_2.txt'
        ];
        $file_1_3_deleted = [
            'isDir' => false,
            'deleted' => true,
            'hash' => "file_1_3_deleted",
            'parent' => $dir_1,
            'uniqueName' => '/dir_1/file_1_3_deleted.txt'
        ];

        return [
            $dir_1,
            $dir_2,
            $file_1,
            $file_1_1,
            $file_1_2_updated,
            $file_1_3_deleted
        ];
    }
}
