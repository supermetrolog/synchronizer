<?php

namespace Supermetrolog\Synchronizer\interfaces;

interface AlreadySynchronizedRepositoryInterface
{
    public function isEmpty(): bool;
    public function findFile(FileInterface $file): ?FileInterface;
    public function markFileAsDirty(FileInterface $file): void;
    /**@return FileInterface[] */
    public function getNotDirtyFiles(): array;
    public function updateRepository(array $createdFiles, array $updatedFiles, array $removedFiles): void;
}
