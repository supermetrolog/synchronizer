<?php


namespace Supermetrolog\Synchronizer\services\sync\interfaces;

interface FileInterface
{
    public function isDir(): bool;
    public function getHash(): string;
    public function getParent(): ?FileInterface;
    public function getRelPath(): string;
    public function getAbsPath(): string;
    public function getName(): string;
    public function getRelFullname(): string;
    public function getContent(): ?string;
}
