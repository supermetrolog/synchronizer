<?php

namespace Supermetrolog\Synchronizer\interfaces;

interface FileInterface
{
    public function isDir(): bool;
    public function getHash(): string;
    public function getParent(): ?FileInterface;
    public function getUniqueName(): string;
}
