<?php

namespace Supermetrolog\Synchronizer\lib\repositories\filesystem;

use Generator;
use Supermetrolog\Synchronizer\services\sync\interfaces\FileStreamInterface;

class Stream implements FileStreamInterface
{
    private string $dirpath;
    public function __construct(string $dirpath)
    {
        $this->dirpath = $dirpath;
    }

    public function read(): Generator
    {
        $handle = opendir($this->dirpath);
        while ($filename = readdir($handle)) {
            yield new File($filename, $this->dirpath);
        }
        closedir($handle);
    }
    public function readRecursive(): Generator
    {
        yield from $this->_readRecursive($this->dirpath);
    }
    private function _readRecursive($dirpath): Generator
    {
        $handle = opendir($dirpath);
        while ($filename = readdir($handle)) {
            $file = new File($filename, $dirpath);
            if (
                $file->isDir() &&
                !$file->isCurrentDirPointer() &&
                !$file->isPreventDirPointer()
            ) {
                yield from $this->_readRecursive($file->getFullname());
            }
            yield $file;
        }
        closedir($handle);
    }
}
