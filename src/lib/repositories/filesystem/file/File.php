<?php

namespace Supermetrolog\Synchronizer\lib\repositories\filesystem\file;

use InvalidArgumentException;
use LogicException;
use Supermetrolog\Synchronizer\services\sync\interfaces\FileInterface;

class File implements FileInterface
{
    public const CURRENT_DIRECTORY_FILENAME = ".";
    public const PREVENT_DIRECTORY_FILENAME = "..";
    private string $name;
    private AbsPath $path;
    private RelPath $relativePath;
    private ?self $parent;
    private $content = null;
    public function __construct(string $name, AbsPath $path, RelPath $relativePath, ?self $parent)
    {
        $this->name = $name;
        $this->path = $path;
        $this->relativePath = $relativePath;
        if ($parent && !$parent->isDir()) {
            throw new InvalidArgumentException("parent cannot be file");
        }

        if ($parent && $parent->getFullname() == $this->getFullname()) {
            throw new InvalidArgumentException("parent directory cannot indicate in equals filepath");
        }
        $this->parent = $parent;
    }
    public function getName(): string
    {
        return $this->name;
    }

    private function getFullname(): string
    {
        return $this->path . $this->relativePath . $this->name;
    }
    public function getRelFullname(): string
    {
        return $this->relativePath . $this->name;
    }
    public function getRelPath(): string
    {
        return $this->relativePath;
    }
    public function getAbsPath(): string
    {
        return $this->path;
    }
    public function isDir(): bool
    {
        return is_dir($this->getFullname());
    }
    public function getParent(): ?self
    {
        return $this->parent;
    }
    public function isCurrentDirPointer(): bool
    {
        return $this->getName() == self::CURRENT_DIRECTORY_FILENAME;
    }
    public function isPreventDirPointer(): bool
    {
        return $this->getName() == self::PREVENT_DIRECTORY_FILENAME;
    }

    public function loadContent(string $content): void
    {
        $this->content = $content;
    }

    public function getContent(): ?string
    {
        if ($this->content !== null) return $this->content;
        return file_get_contents($this->getFullname());
    }

    public function getHash(): string
    {
        if ($this->isDir()) throw new LogicException("directory have not hash");
        return hash_file('md5', $this->getFullname());
    }
}
