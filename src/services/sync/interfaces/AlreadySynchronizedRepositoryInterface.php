<?php

namespace Supermetrolog\Synchronizer\services\sync\interfaces;

interface AlreadySynchronizedRepositoryInterface
{
    public function updateRepository(array $createdFiles, array $updatedFiles, array $removedFiles): void;
    public function findFile(FileInterface $file): ?FileInterface;
    public function markFileAsDirty(FileInterface $file): void;
    /**@return FileInterface[] */
    public function getNotDirtyFiles(): array;
    public function isEmpty(): bool;
}
