<?php

namespace tests\unit\mocks;

use PHPUnit\Framework\TestCase;
use Supermetrolog\Synchronizer\interfaces\FileInterface;
use Supermetrolog\Synchronizer\interfaces\TargetRepositoryInterface;

class TargetRepository extends TestCase
{
    public static function getMock(): TargetRepositoryInterface
    {
        $self = new self();
        return $self->mock();
    }

    public function mock(): TargetRepositoryInterface
    {
        /** @var \PHPUnit\Framework\MockObject\MockObject $mock */
        $mock = $this->createMock(TargetRepositoryInterface::class);
        $mock->method('remove')->willReturn(true);
        $mock->method('create')->willReturn(true);
        $mock->method('update')->willReturn(true);
        $mock->method('fileExist')->will($this->returnCallback([$this, 'fileExist']));
        /** @var TargetRepositoryInterface $mock */
        return $mock;
    }

    public function fileExist(FileInterface $findedFile): bool
    {
        $files = self::getFiles();
        foreach ($files as $file) {
            if ($file->getUniqueName() === $findedFile->getUniqueName()) {
                return true;
            }
        }
        return false;
    }
    /** @return FileInterface[] */
    public static function getFiles(): array
    {
        return File::getMocks(self::getFilesParams());
    }
    /**
     * @return array<array<string, mixed>>
     */
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

        return [
            $dir_1,
            $dir_2,
            $file_1,
            $file_1_1,
            $file_1_2_updated,
        ];
    }
}
