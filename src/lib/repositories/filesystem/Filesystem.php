<?php

namespace Supermetrolog\Synchronizer\lib\repositories\filesystem;

use InvalidArgumentException;
use Supermetrolog\Synchronizer\lib\repositories\filesystem\file\AbsPath;
use Supermetrolog\Synchronizer\lib\repositories\onefile\interfaces\RepositoryInterface;
use Supermetrolog\Synchronizer\services\sync\interfaces\FileInterface;
use Supermetrolog\Synchronizer\services\sync\interfaces\SourceRepositoryInterface;
use Supermetrolog\Synchronizer\services\sync\interfaces\StreamInterface;
use Supermetrolog\Synchronizer\services\sync\interfaces\TargetRepositoryInterface;

class Filesystem implements SourceRepositoryInterface, TargetRepositoryInterface, RepositoryInterface
{
    private AbsPath $dirpath;
    public function __construct(AbsPath $dirpath)
    {
        if (!$dirpath)
            throw new InvalidArgumentException("invalid base directory path");
        if (!file_exists($dirpath))
            throw new InvalidArgumentException("base directory with path: $dirpath not exist");
        if (!is_dir($dirpath))
            throw new InvalidArgumentException("base dir path is not directory");
        $this->dirpath = $dirpath;
    }
    public static function getInstance(string $dirpath): self
    {
        return new self(new AbsPath($dirpath));
    }
    public function getStream(): StreamInterface
    {
        return new Stream($this->dirpath);
    }
    public function findFile(FileInterface $findedFile): ?FileInterface
    {
        $stream = $this->getStream();
        foreach ($stream->read() as $file) {
            if ($file->getUniqueName() === $findedFile->getUniqueName()) {
                return $file;
            }
        }
        return null;
    }
    public function fileExist(FileInterface $file): bool
    {
        return $this->findFile($file) === null ? false : true;
    }
    public function findByRelativeFullname(string $relativeName): ?FileInterface
    {
        $relativeName = "/$relativeName";
        $relativeName = str_replace("//", '/', $relativeName);
        $stream = $this->getStream();

        foreach ($stream->read() as $file) {
            if ($file->getUniqueName() == $relativeName) return $file;
        }
        return null;
    }
    public function getDirpath(): string
    {
        return $this->dirpath;
    }
    public function createOrUpdate(string $content, string $filename, string $relativePath = ""): bool
    {
        return $this->createFileWithContent($content, $filename, $relativePath);
    }
    public function getContent(FileInterface $file): ?string
    {
        $filename = $this->dirpath . $file->getUniqueName();
        if (!file_exists($filename)) {
            return null;
        }
        if (is_dir($filename)) {
            return null;
        }
        return file_get_contents($this->dirpath . $file->getUniqueName());
    }
    public function create(FileInterface $file, ?string $content = null): bool
    {
        $filename = $this->dirpath . $file->getUniqueName();
        if ($file->isDir()) {
            if (file_exists($filename)) return true;
            try {
                return mkdir($filename, 0777);
            } catch (\Throwable $th) {
                throw $th;
            }
        }
        $result = file_put_contents($filename, $content);
        if ($result === false) {
            return false;
        }
        return true;
    }
    public function update(FileInterface $file, ?string $content = null): bool
    {
        if ($file->isDir()) {
            return false;
        }
        $filename = $this->dirpath . $file->getUniqueName();
        $result = file_put_contents($filename, $content);
        if ($result === false) {
            return false;
        }
        return true;
    }
    public function remove(FileInterface $file): bool
    {
        $filename = $this->dirpath . $file->getUniqueName();
        if ($file->isDir()) {
            return $this->removeDirRecursive($filename);
        }

        return unlink($filename);
    }

    private function removeDirRecursive(string $path): bool
    {
        $files = glob($path . '/*');
        foreach ($files as $file) {
            is_dir($file) ? $this->removeDirRecursive($file) : unlink($file);
        }
        return rmdir($path);
    }
    public function createFileWithContent(string $content, string $filename, string $relativePath = ""): bool
    {
        $fullname = $this->dirpath . $relativePath . "/" . $filename;
        $result = file_put_contents($fullname, $content);
        if ($result === false) {
            return false;
        }
        return true;
    }
}
