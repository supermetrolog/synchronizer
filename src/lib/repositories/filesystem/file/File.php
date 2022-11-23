<?php

namespace Supermetrolog\Synchronizer\lib\repositories\filesystem\file;

use InvalidArgumentException;
use Supermetrolog\Synchronizer\services\sync\interfaces\FileInterface;

class File implements FileInterface
{

    private const CURRENT_DIR_POINTER = ".";
    private const PREVENT_DIR_POINTER = "..";

    private string $name;
    private string $hash;
    private bool $isDir;
    private ?self $parent;
    private RelPath $relativePath;
    public function __construct(string $name, string $hash, RelPath $relativePath, bool $isDir, ?self $parent)
    {
        $this->name = $name;
        $this->relativePath = $relativePath;
        $this->isDir = $isDir;
        $this->hash = $hash;
        if ($parent && !$parent->isDir()) {
            throw new InvalidArgumentException("parent cannot be file");
        }

        if ($parent && $parent->getUniqueName() == $this->getUniqueName()) {
            throw new InvalidArgumentException("parent directory cannot indicate in equals filepath");
        }
        $this->parent = $parent;
    }
    public function getUniqueName(): string
    {
        return $this->relativePath . $this->name;
    }

    public function isDir(): bool
    {
        return $this->isDir;
    }
    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function getHash(): string
    {
        return $this->hash;
    }

    public function isCurrentDirPointer(): bool
    {
        return $this->name === self::CURRENT_DIR_POINTER;
    }
    public function isPreventDirPointer(): bool
    {
        return $this->name === self::PREVENT_DIR_POINTER;
    }
    public function getRelPath(): RelPath
    {
        return $this->relativePath;
    }
}
