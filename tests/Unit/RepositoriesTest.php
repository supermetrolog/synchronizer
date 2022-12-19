<?php

namespace tests\unit;

use PHPUnit\Framework\TestCase;
use Supermetrolog\Synchronizer\interfaces\AlreadySynchronizedRepositoryInterface;
use Supermetrolog\Synchronizer\interfaces\SourceRepositoryInterface;
use Supermetrolog\Synchronizer\interfaces\TargetRepositoryInterface;
use Supermetrolog\Synchronizer\Repositories;

class RepositoriesTest extends TestCase
{
    public function testConsturctor(): void
    {
        $repositories = new Repositories(
            $this->createMock(AlreadySynchronizedRepositoryInterface::class),
            $this->createMock(SourceRepositoryInterface::class),
            $this->createMock(TargetRepositoryInterface::class)
        );

        $this->assertInstanceOf(AlreadySynchronizedRepositoryInterface::class, $repositories->alreadyRepo);
        $this->assertInstanceOf(SourceRepositoryInterface::class, $repositories->sourceRepo);
        $this->assertInstanceOf(TargetRepositoryInterface::class, $repositories->targetRepo);
    }
}
