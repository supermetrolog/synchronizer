<?php

namespace Supermetrolog\Synchronizer\interfaces;

interface TargetRepositoryInterface
{
    public function remove(FileInterface $file): bool;
    public function create(FileInterface $file, ?string $content): bool;
    public function update(FileInterface $file, ?string $content): bool;
    public function fileExist(FileInterface $file): bool;
}
