<?php

namespace tests\services\sync\mocks;

use PHPUnit\Framework\TestCase;
use Supermetrolog\Synchronizer\services\sync\interfaces\FileInterface;

class File extends TestCase
{
    public static function getMocks(array $params)
    {
        $self = new static();

        $files = [];

        foreach ($params as $param) {
            $files[] = $self->getMock($param);
        }

        return $files;
    }

    private function getMock(?array $params)
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
        return $fileMock;
    }
}
