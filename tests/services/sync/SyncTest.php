<?php

use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Supermetrolog\Synchronizer\services\sync\interfaces\AlreadySynchronizedRepositoryInterface;
use Supermetrolog\Synchronizer\services\sync\interfaces\SourceRepositoryInterface;
use Supermetrolog\Synchronizer\services\sync\interfaces\TargetRepositoryInterface;
use Supermetrolog\Synchronizer\services\sync\Synchronizer;
use tests\services\sync\mocks\AlreadySynchronizedRepo;
use tests\services\sync\mocks\SourceRepository;
use tests\services\sync\mocks\TargetRepository;

class SyncTest  extends TestCase
{
    private SourceRepositoryInterface $sourceRepo;
    private AlreadySynchronizedRepositoryInterface $alreadyRepoEmpty;
    private AlreadySynchronizedRepositoryInterface $alreadyRepoNotEmpty;
    private TargetRepositoryInterface $targetRepo;
    private LoggerInterface $logger;

    public function setUp(): void
    {
        $this->sourceRepo = SourceRepository::getMock();
        $this->alreadyRepoEmpty = AlreadySynchronizedRepo::getEmptyMock();
        $this->alreadyRepoNotEmpty = AlreadySynchronizedRepo::getNotEmptyMock();
        $this->targetRepo = TargetRepository::getMock();
        $this->logger = $this->createMock(LoggerInterface::class);
    }
    public function testFirstLoad(): void
    {
        $sync = new Synchronizer($this->sourceRepo, $this->targetRepo, $this->alreadyRepoEmpty, $this->logger);
        $sync->load();
        $this->assertTrue($sync->affectedFilesExist());
        $this->assertCount(count(SourceRepository::getFiles()), $sync->getCreatingFiles());
        $this->assertCount(0, $sync->getRemovingFiles());
        $this->assertCount(0, $sync->getChangingFiles());
    }

    public function testSecondLoad(): void
    {
        $sync = new Synchronizer($this->sourceRepo, $this->targetRepo, $this->alreadyRepoNotEmpty, $this->logger);
        $sync->load();
        $this->assertTrue($sync->affectedFilesExist());
        $this->assertCount(5, $sync->getCreatingFiles());
        $this->assertCount(1, $sync->getRemovingFiles());
        $this->assertCount(1, $sync->getChangingFiles());
    }

    public function testFirstSync(): void
    {
        /** @var \PHPUnit\Framework\MockObject\MockObject $targetRepo */
        $targetRepo = $this->targetRepo;
        $files = [];
        $targetRepo->expects($this->exactly(10))->method('create')->will($this->returnCallback(function ($file) use (&$files) {
            $files[] = $file;
            return true;
        }));
        /** @var \PHPUnit\Framework\MockObject\MockObject $alreadyRepo */
        $alreadyRepo = $this->alreadyRepoEmpty;

        $sync = new Synchronizer($this->sourceRepo, $this->targetRepo, $this->alreadyRepoEmpty, $this->logger);
        $sync->load();
        $alreadyRepo->expects($this->once())->method('updateRepository')->with($sync->getCreatingFiles(), [], []);
        $sync->sync();
        $this->assertEquals($files, SourceRepository::getFiles());
        $this->assertEquals($sync->getCreatingFiles(), SourceRepository::getFiles());
    }

    public function testSecondSync(): void
    {
        /** @var \PHPUnit\Framework\MockObject\MockObject $targetRepo */
        $targetRepo = $this->targetRepo;
        $createdFiles = [];
        $targetRepo->expects($this->exactly(5))->method('create')->will($this->returnCallback(function ($file) use (&$createdFiles) {
            $createdFiles[] = $file;
            return true;
        }));
        $changedFiles = [];
        $targetRepo->expects($this->once())->method('update')->will($this->returnCallback(function ($file) use (&$changedFiles) {
            $changedFiles[] = $file;
            return true;
        }));
        $removedFiles = [];
        $targetRepo->expects($this->once())->method('remove')->will($this->returnCallback(function ($file) use (&$removedFiles) {
            $removedFiles[] = $file;
            return true;
        }));
        /** @var \PHPUnit\Framework\MockObject\MockObject $alreadyRepo */
        $alreadyRepo = $this->alreadyRepoNotEmpty;

        $sync = new Synchronizer($this->sourceRepo, $this->targetRepo, $this->alreadyRepoNotEmpty, $this->logger);
        $sync->load();
        $alreadyRepo->expects($this->once())->method('updateRepository')->with($sync->getCreatingFiles(), $sync->getChangingFiles(), $sync->getRemovingFiles());
        $sync->sync();

        $this->assertEquals($createdFiles, $sync->getCreatingFiles());
        $this->assertEquals($changedFiles, $sync->getChangingFiles());
        $this->assertEquals($removedFiles, $sync->getRemovingFiles());
    }
}
