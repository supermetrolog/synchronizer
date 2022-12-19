<?php

namespace Supermetrolog\Synchronizer;

use LogicException;
use Psr\Log\LoggerInterface;
use Supermetrolog\Synchronizer\interfaces\FileInterface;

class Synchronizer
{
    /** @var FileInterface[] $changingFiles */
    private array $changingFiles = [];
    /** @var FileInterface[] $creatingFiles */
    private array $creatingFiles = [];
    /** @var FileInterface[] $removingFiles */
    private array $removingFiles = [];

    /**
     * Нужно, чтобы исключить дублирование создания дирректорий в рекурсии
     * @var FileInterface[] $createdFiles
     */
    private array $createdFiles = [];

    private Repositories $repositories;
    private LoggerInterface $logger;

    public function __construct(
        Repositories $repositories,
        LoggerInterface $logger
    ) {
        $this->repositories = $repositories;
        $this->logger = $logger;
    }
    public function load(): void
    {
        if ($this->repositories->alreadyRepo->isEmpty()) {
            $this->firstLoadData();
        } else {
            $this->loadData();
        }
    }
    private function firstLoadData(): void
    {
        $stream = $this->repositories->sourceRepo->getStream();
        foreach ($stream->read() as $file) {
            if ($file->isDir()) {
                $this->logger->info("----- Processed directory: " . $file->getUniqueName());
            }

            $this->creatingFiles[] = $file;
        }
    }
    private function loadData(): void
    {
        $stream = $this->repositories->sourceRepo->getStream();
        foreach ($stream->read() as $file) {
            if ($file->isDir()) {
                $this->logger->info("----- Processed directory: " . $file->getUniqueName());
            }

            $fileInSyncReader = $this->repositories->alreadyRepo->findFile($file);
            if ($fileInSyncReader === null) {
                $this->creatingFiles[] = $file;
                continue;
            }
            $this->repositories->alreadyRepo->markFileAsDirty($file);

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
        $this->removingFiles = array_merge(
            $this->removingFiles,
            $this->repositories->alreadyRepo->getNotDirtyFiles()
        );
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

    public function sync(): void
    {
        $this->removeFiles();
        $this->createFiles();
        $this->changeFiles();
        $this->repositories->alreadyRepo->updateRepository(
            $this->creatingFiles,
            $this->changingFiles,
            $this->removingFiles
        );
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
        if (!$this->repositories->targetRepo->fileExist($file->getParent())) {
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
        if (!$this->repositories->targetRepo->fileExist($file->getParent())) {
            $this->createFile($file->getParent());
        }
        $this->createFileInTargetRepo($file);
    }
    private function removeFiles(): void
    {
        foreach ($this->removingFiles as $file) {
            $this->logger->info("----- Removing file: " . $file->getUniqueName());
            if (!$this->repositories->targetRepo->remove($file)) {
                throw new LogicException("error when removing file");
            }
        }
    }

    private function createFileInTargetRepo(FileInterface $file): void
    {
        if (key_exists($file->getUniqueName(), $this->createdFiles)) {
            return;
        }

        $this->logger->info("----- Creating file: " . $file->getUniqueName());
        if (!$this->repositories->targetRepo->create($file, $this->repositories->sourceRepo->getContent($file))) {
            throw new LogicException("error when create file");
        }

        $this->createdFiles[$file->getUniqueName()] = $file;
    }
    private function updateFileInTargetRepo(FileInterface $file): void
    {
        $this->logger->info("----- Updating file: " . $file->getUniqueName());
        if (!$this->repositories->targetRepo->update($file, $this->repositories->sourceRepo->getContent($file))) {
            throw new LogicException("error when update file");
        }
    }
}
