<?php


namespace Supermetrolog\Synchronizer\services\sync\interfaces;

interface TargetRepositoryInterface
{
    public function remove(FileInterface $file): bool;
    public function create(FileInterface $file): bool;
    public function update(FileInterface $file): bool;
    public function findFile(FileInterface $file): ?FileInterface;
}
