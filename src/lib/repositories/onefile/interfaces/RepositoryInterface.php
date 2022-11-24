<?php

namespace Supermetrolog\Synchronizer\lib\repositories\onefile\interfaces;

use Supermetrolog\Synchronizer\services\sync\interfaces\FileInterface;

interface RepositoryInterface
{
    public function findByRelativeFullname(string $relativeName): ?FileInterface;
    public function createOrUpdate(string $content, string $filename): bool;
    public function getContent(FileInterface $file): ?string;
}
