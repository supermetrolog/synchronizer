<?php

namespace Supermetrolog\Synchronizer\lib\repositories\filesystem;

use Generator;
use Supermetrolog\Synchronizer\services\sync\interfaces\FileStreamInterface;

class Stream implements FileStreamInterface
{
    private string $dirpath;
    private $handlerBuffer;
    public function __construct(string $dirpath)
    {
        $this->dirpath = $dirpath;
    }

    public function read(): Generator
    {
        $handle = opendir($this->dirpath);
        while ($filename = readdir($handle)) {
            yield new File($filename, $this->dirpath, "", null);
        }
        closedir($handle);
    }
    /**
     * @return File[]
     */
    public function readRecursive(): Generator
    {
        yield from $this->_readRecursive($this->dirpath);
    }
    private function _readRecursive(string $dirpath, ?File $parent = null): Generator
    {
        $handle = opendir($dirpath);
        $this->handlerBuffer = &$handle;
        while ($filename = readdir($handle)) {
            $relativePath = str_replace($this->dirpath, "", $dirpath);
            $file = new File($filename, $dirpath, $relativePath, $parent);
            if (
                $file->isDir() &&
                !$file->isCurrentDirPointer() &&
                !$file->isPreventDirPointer()
            ) {
                yield from $this->_readRecursive($file->getFullname(), $file);
            }
            yield $file;
        }
        closedir($handle);
    }

    public function __destruct()
    {
        if (is_resource($this->handlerBuffer))
            closedir($this->handlerBuffer);
    }
}
