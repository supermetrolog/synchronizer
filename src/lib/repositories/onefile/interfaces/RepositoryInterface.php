<?php

namespace Supermetrolog\Synchronizer\lib\repositories\onefile\interfaces;

use Supermetrolog\Synchronizer\services\sync\interfaces\FileInterface;

interface RepositoryInterface
{
    public function findByRelativeFullname(string $relativeName): ?FileInterface;
    public function createOrUpdateFileWithContent(string $content, string $filename, string $relativePath = ""): bool;
    public function getContent(FileInterface $file): ?string;
}
