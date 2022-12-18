<?php


namespace Supermetrolog\Synchronizer\services\sync\interfaces;

interface FileInterface
{
    public function isDir(): bool;
    public function getHash(): string;
    public function getParent(): ?FileInterface;
    public function getUniqueName(): string;
}
