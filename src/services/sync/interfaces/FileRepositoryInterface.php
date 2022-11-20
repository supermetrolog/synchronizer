<?php


namespace Supermetrolog\Synchronizer\services\sync\interfaces;

interface FileRepositoryInterface
{
    public function createStream(): FileStreamInterface;
    public function findFile(FileInterface $file): ?FileInterface;
    public function createFileWithContent(string $content, string $filename, string $relativePath = ""): bool;
    public function getDirpath(): string;
    public function remove(FileInterface $file): bool;
    public function update(FileInterface $file, string $relativePath): bool;
    public function create(FileInterface $file, string $relativePath): bool;
}
