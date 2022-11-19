<?php


namespace Supermetrolog\Synchronizer\services\sync\interfaces;

interface FileRepositoryInterface
{
    public function createStream(): FileStreamInterface;
    public function findFile(FileInterface $file): ?FileInterface;
    public function create(FileInterface $file, string $relativePath): bool;
    public function getDirpath(): string;
}
