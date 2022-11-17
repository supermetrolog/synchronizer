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
    public function findByFullname(string $fullname): ?FileInterface
    {
        $fullname = realpath($fullname);
        $stream = $this->createStream();

        foreach ($stream->readRecursive() as $file) {
            if ($file->getFullname() == $fullname) {
                return $file;
            }
        }

        return null;
    }
}
