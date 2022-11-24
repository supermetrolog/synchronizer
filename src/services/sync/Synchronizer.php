<?php

namespace Supermetrolog\Synchronizer\services\sync;

use LogicException;
use Psr\Log\LoggerInterface;
use Supermetrolog\Synchronizer\services\sync\interfaces\AlreadySynchronizedRepositoryInterface;
use Supermetrolog\Synchronizer\services\sync\interfaces\BaseRepositoryInterface;
use Supermetrolog\Synchronizer\services\sync\interfaces\FileInterface;
use Supermetrolog\Synchronizer\services\sync\interfaces\TargetRepositoryInterface;

/**
 * @property FileInterface[] $changingFiles
 * @property FileInterface[] $creatingFiles
 * @property FileInterface[] $removingFiles
 * @property array<string, FileInterface[]> $createdFiles
 */

class Synchronizer
{
    private array $changingFiles = [];
    private array $creatingFiles = [];
    private array $removingFiles = [];

    // Нужно, чтобы исключить дублирование создания дирректорий в рекурсии
    private array $createdFiles = [];

    private BaseRepositoryInterface $baseFileRepository;
    private TargetRepositoryInterface $targetFileRepository;
    private AlreadySynchronizedRepositoryInterface $alreadySynchronizedRepository;

    private LoggerInterface $logger;

    public function __construct(BaseRepositoryInterface $baseFileRepository, TargetRepositoryInterface $targetFileRepository, AlreadySynchronizedRepositoryInterface $alreadySynchronizedRepository, LoggerInterface $logger)
    {
        $this->baseFileRepository = $baseFileRepository;
        $this->targetFileRepository = $targetFileRepository;
        $this->alreadySynchronizedRepository = $alreadySynchronizedRepository;
        $this->logger = $logger;
    }
    public function load(): void
    {
        if ($this->alreadySynchronizedRepository->isEmpty()) {
            $this->firstLoadData();
        } else {
            $this->loadData();
        }
    }
    private function firstLoadData(): void
    {
        $stream = $this->baseFileRepository->getStream();
        foreach ($stream->read() as $file) {
            $this->creatingFiles[] = $file;
        }
    }
    private function loadData(): void
    {
        $stream = $this->baseFileRepository->getStream();
        foreach ($stream->read() as $file) {
            $fileInSyncReader = $this->alreadySynchronizedRepository->findFile($file);
            if ($fileInSyncReader === null) {
                $this->creatingFiles[] = $file;
                continue;
            }
            $this->alreadySynchronizedRepository->markFileAsDirty($file);

            if ($file->isDir()) {
                if ($fileInSyncReader->isDir()) {
                    continue;
                }
                $this->removingFiles[] = $file;
                $this->creatingFiles[] = $file;
                continue;
            }
            if ($file->getHash() !== $fileInSyncReader->getHash()) {
                $this->changingFiles[] = $file;
                continue;
            }
        }
        $this->removingFiles = array_merge($this->removingFiles, $this->alreadySynchronizedRepository->getNotDirtyFiles());
    }

    /**
     * @return FileInterface[]
     */
    public function getChangingFiles(): array
    {
        return $this->changingFiles;
    }
    /**
     * @return FileInterface[]
     */
    public function getCreatingFiles(): array
    {
        return $this->creatingFiles;
    }
    /**
     * @return FileInterface[]
     */
    public function getRemovingFiles(): array
    {
        return $this->removingFiles;
    }
    public function affectedFilesExist(): bool
    {
        return count($this->changingFiles) != 0 ||
            count($this->creatingFiles) != 0 ||
            count($this->removingFiles) != 0;
    }

    public function sync()
    {
        $this->removeFiles();
        $this->createFiles();
        $this->changeFiles();
        $this->alreadySynchronizedRepository->updateRepository($this->creatingFiles, $this->changingFiles, $this->removingFiles);
    }
    private function changeFiles(): void
    {
        foreach ($this->changingFiles as $file) {
            $this->updateFile($file);
        }
    }
    private function updateFile(FileInterface $file): void
    {
        if ($file->getParent() === null) {
            $this->updateFileInTargetRepo($file);
            return;
        }
        if (!$this->targetFileRepository->findFile($file->getParent())) {
            $this->updateFile($file->getParent());
        }
        $this->updateFileInTargetRepo($file);
    }
    private function createFiles(): void
    {
        foreach ($this->creatingFiles as $file) {
            $this->createFile($file);
        }
    }
    private function createFile(FileInterface $file): void
    {
        if ($file->getParent() === null) {
            $this->createFileInTargetRepo($file);
            return;
        }
        if (!$this->targetFileRepository->findFile($file->getParent())) {
            $this->createFile($file->getParent());
        }
        $this->createFileInTargetRepo($file);
    }
    private function removeFiles(): void
    {
        foreach ($this->removingFiles as $file) {
            $this->logger->info("----- Removing file: " . $file->getUniqueName());
            if (!$this->targetFileRepository->remove($file)) {
                throw new LogicException("error when removing file");
            }
        }
    }

    private function createFileInTargetRepo(FileInterface $file): void
    {
        if (key_exists($file->getUniqueName(), $this->createdFiles)) return;

        $this->logger->info("----- Creating file: " . $file->getUniqueName());
        if (!$this->targetFileRepository->create($file, $this->baseFileRepository->getContent($file)))
            throw new LogicException("error when create file");

        $this->createdFiles[$file->getUniqueName()] = $file;
    }
    private function updateFileInTargetRepo(FileInterface $file): void
    {
        $this->logger->info("----- Updating file: " . $file->getUniqueName());
        if (!$this->targetFileRepository->update($file, $this->baseFileRepository->getContent($file)))
            throw new LogicException("error when update file");
    }
}
