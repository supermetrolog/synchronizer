<?php


namespace Supermetrolog\Synchronizer\lib\repositories\onefile;

use Supermetrolog\Synchronizer\services\sync\interfaces\FileInterface;

class File implements FileInterface
{
    public string $hash;
    public bool $isDir;
    private FileInterface $file;
    public function __construct(FileInterface $file)
    {
        $this->hash = "";
        $this->isDir = $file->isDir();
        if (!$file->isDir()) {
            $this->hash = $file->getHash();
        }
        $this->file = $file;
    }

    public function getHash(): string
    {
        return $this->hash;
    }
    public function isDir(): bool
    {
        // return $this->isDir;
        return $this->file->isDir();
    }
    public function getName(): string
    {
        return $this->file->getName();
    }
    public function getRelFullname(): string
    {
        return $this->file->getRelFullname();
    }
    public function getRelPath(): string
    {
        return $this->file->getRelPath();
    }
    public function getAbsPath(): string
    {
        return $this->file->getAbsPath();
    }
    public function getParent(): ?self
    {
        return $this->file->getParent();
    }

    public function loadContent(string $content): void
    {
        $this->content = $content;
    }

    public function getContent(): ?string
    {
        return $this->file->getContent();
    }
}
