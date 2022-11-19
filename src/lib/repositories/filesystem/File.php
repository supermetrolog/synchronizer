<?php

namespace Supermetrolog\Synchronizer\lib\repositories\filesystem;

use InvalidArgumentException;
use Supermetrolog\Synchronizer\services\sync\interfaces\FileInterface;

class File implements FileInterface
{
    public const CURRENT_DIRECTORY_FILENAME = ".";
    public const PREVENT_DIRECTORY_FILENAME = "..";
    private string $name;
    private string $path;
    private string $relativePath;
    private ?self $parent;
    private $content = null;
    public function __construct(string $name, string $path, string $relativePath, ?self $parent)
    {
        $this->name = $name;
        $this->path = $path;
        $this->relativePath = $relativePath;
        if ($parent && !$parent->isDir()) {
            throw new InvalidArgumentException("parent cannot be file");
        }

        if ($parent && $parent->getNotPrecessedFullname() == $this->getNotPrecessedFullname()) {
            throw new InvalidArgumentException("parent directory cannot indicate in equals filepath");
        }
        $this->parent = $parent;
    }
    private function getNotPrecessedFullname(): string
    {
        return $this->path . "/" . $this->name;
    }

    public function getName(): string
    {
        return $this->name;
    }
    public function getUpdatedTime(): int
    {
        return filemtime($this->getFullname());
    }
    public function getFullname(): string
    {
        return realpath($this->path . "/" . $this->name);
    }
    public function getRelativePath(): string
    {
        return $this->relativePath;
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
}
