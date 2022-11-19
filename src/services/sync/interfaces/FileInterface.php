<?php


namespace Supermetrolog\Synchronizer\services\sync\interfaces;

interface FileInterface
{
    public function getName(): string;
    public function getUpdatedTime(): int;
    public function getFullname(): string;
    public function isDir(): bool;
    public function isCurrentDirPointer(): bool;
    public function isPreventDirPointer(): bool;
    public function getParent(): ?FileInterface;
    public function getRelativePath(): string;
    public function getContent(): ?string;
}
