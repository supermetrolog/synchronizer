<?php

namespace Supermetrolog\Synchronizer\lib\repositories\filesystem;

use Supermetrolog\Synchronizer\services\sync\interfaces\FileInterface;

class File implements FileInterface
{
    public const CURRENT_DIRECTORY_FILENAME = ".";
    public const PREVENT_DIRECTORY_FILENAME = "..";
    private string $name;
    private string $path;
    public function __construct(string $name, string $path)
    {
        $this->name = $name;
        $this->path = $path;
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
    public function isDir(): bool
    {
        return is_dir($this->getFullname());
    }

    public function isCurrentDirPointer(): bool
    {
        return $this->getName() == self::CURRENT_DIRECTORY_FILENAME;
    }
    public function isPreventDirPointer(): bool
    {
        return $this->getName() == self::PREVENT_DIRECTORY_FILENAME;
    }
}
