<?php

namespace Supermetrolog\Synchronizer\services\sync;

use Supermetrolog\Synchronizer\services\sync\interfaces\FileInterface;
use Supermetrolog\Synchronizer\services\sync\interfaces\FileRepositoryInterface;

/**
 * 
 * @property FileInterface[] $changedFiles;
 */
class Synchronizer
{
    private array $changedFiles;

    private FileRepositoryInterface $baseFileRepository;
    private FileRepositoryInterface $targetFileRepository;
    public function __construct(FileRepositoryInterface $baseFileRepository, FileRepositoryInterface $targetFileRepository)
    {
        $this->changedFiles = [];

        $this->baseFileRepository = $baseFileRepository;
        $this->targetFileRepository = $targetFileRepository;
    }
    public function loadUpdatedData(): void
    {
        $stream = $this->baseFileRepository->createStream();
        foreach ($stream->readRecursive() as $file) {
            if ($file->isCurrentDirPointer() || $file->isPreventDirPointer()) continue;
            if ($targetEntry = $this->targetFileRepository->findFile($file)) {
                if ($targetEntry->getUpdatedTime() > $file->getUpdatedTime()) {
                    $this->changedFiles[] = $file;
                }
            } else {
                $this->changedFiles[] = $file;
            }
        }
    }
    /**
     * @return FileInterface[]
     */
    public function getChangedFiles(): array
    {
        return $this->changedFiles;
    }
    public function changedFilesExists(): bool
    {
        return count($this->changedFiles) != 0;
    }

    public function sync()
    {
        foreach ($this->changedFiles as $file) {
            if ($file->getParent() === null) {
                $this->createFileInTargetRepo($file);
                continue;
            }
            $this->createParentDir($file->getParent());
            $this->createFileInTargetRepo($file);
        }
    }

    private function createParentDir(FileInterface $file)
    {
        if ($file->getParent() === null) {
            $this->createFileInTargetRepo($file);
            return;
        }
        if (!$this->targetFileRepository->findFile($file->getParent())) {
            $this->createParentDir($file->getParent());
        }
    }
    private function createFileInTargetRepo(FileInterface $file): bool
    {
        return $this->targetFileRepository->create($file, $file->getRelativePath());
    }
}
