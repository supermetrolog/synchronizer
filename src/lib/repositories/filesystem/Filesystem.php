<?php

namespace Supermetrolog\Synchronizer\lib\repositories\filesystem;

use InvalidArgumentException;
use Supermetrolog\Synchronizer\services\sync\interfaces\FileInterface;
use Supermetrolog\Synchronizer\services\sync\interfaces\FileRepositoryInterface;
use Supermetrolog\Synchronizer\services\sync\interfaces\FileStreamInterface;

class Filesystem implements FileRepositoryInterface
{
    private string $dirpath;
    public function __construct(string $dirpath)
    {
        $dirpath = realpath($dirpath);
        if (!$dirpath)
            throw new InvalidArgumentException("invalid base directory path");
        if (!file_exists($dirpath))
            throw new InvalidArgumentException("base directory with path: $dirpath not exist");
        if (!is_dir($dirpath))
            throw new InvalidArgumentException("base dir path is not directory");
        $this->dirpath = $dirpath;
    }
    public function createStream(): FileStreamInterface
    {
        return new Stream($this->dirpath);
    }
    public function findFile(FileInterface $findedFile): ?FileInterface
    {
        $fullname = realpath($this->dirpath . $findedFile->getRelativePath());
        if (!$fullname) return null;
        $stream = $this->createStream();

        foreach ($stream->readRecursive() as $file) {
            if ($file->getFullname() == $fullname && $file->getName() == $findedFile->getName()) {
                return $file;
            }
        }
        return null;
    }
    public function getDirpath(): string
    {
        return $this->dirpath;
    }
    public function create(FileInterface $file, string $relativePath): bool
    {
        $fullname = $this->dirpath . $relativePath . "/" . $file->getName();
        if ($file->isDir()) {
            if (file_exists($fullname)) return true;
            return mkdir($fullname, 0777);
        }
        $result = file_put_contents($fullname, $file->getContent());
        if ($result !== false) {
            return true;
        }
        return false;
    }
}
