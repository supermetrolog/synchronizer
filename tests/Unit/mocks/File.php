<?php

namespace tests\unit\mocks;

use PHPUnit\Framework\TestCase;
use Supermetrolog\Synchronizer\interfaces\FileInterface;

class File extends TestCase
{
    /**
     * @return FileInterface[]
     * @param array<array<string, mixed>> $params
     */
    public static function getMocks(array $params): array
    {
        $self = new self();

        $files = [];

        foreach ($params as $param) {
            $file = $self->getMock($param);
            if ($file !== null) {
                $files[] = $file;
            }
        }

        return $files;
    }
    /**
     * @param array<string, mixed> $params
     */
    private function getMock(?array $params): ?FileInterface
    {
        if ($params === null) {
            return null;
        }
        /** @var \PHPUnit\Framework\MockObject\MockObject $fileMock */
        $fileMock = $this->createMock(FileInterface::class);
        $fileMock->method("isDir")->willReturn($params['isDir']);
        $fileMock->method("getHash")->willReturn($params['hash']);
        $fileMock->method("getParent")->willReturn($this->getMock($params['parent']));
        $fileMock->method("getUniqueName")->willReturn($params['uniqueName']);
        /** @var FileInterface $fileMock */
        return $fileMock;
    }
}
