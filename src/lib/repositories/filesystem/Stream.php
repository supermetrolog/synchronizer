<?php

namespace Supermetrolog\Synchronizer\lib\repositories\filesystem;

use Generator;
use Supermetrolog\Synchronizer\lib\repositories\filesystem\file\AbsPath;
use Supermetrolog\Synchronizer\lib\repositories\filesystem\file\File;
use Supermetrolog\Synchronizer\lib\repositories\filesystem\file\RelPath;
use Supermetrolog\Synchronizer\services\sync\interfaces\StreamInterface;

class Stream implements StreamInterface
{
    private AbsPath $dirpath;
    private $lastHandle;
    public function __construct(AbsPath $dirpath)
    {
        $this->dirpath = $dirpath;
    }
    /**
     * @return File[]
     */
    public function read(): Generator
    {
        yield from $this->readRecursive($this->dirpath);
    }
    private function readRecursive(AbsPath $dirpath, ?File $parent = null): Generator
    {
        $handle = opendir($dirpath);
        $this->lastHandle = &$handle;
        while ($filename = readdir($handle)) {
            $relativePath = str_replace($this->dirpath, "", $dirpath);
            $file = new File($filename, $this->dirpath, new RelPath($relativePath), $parent);
            if (
                $file->isDir() &&
                !$file->isCurrentDirPointer() &&
                !$file->isPreventDirPointer()
            ) {
                yield from $this->readRecursive($this->getNextPath($file), $file);
            }
            if ($file->isCurrentDirPointer() || $file->isPreventDirPointer()) {
                continue;
            }
            yield $file;
        }
        closedir($handle);
    }
    private function getNextPath(File $file): AbsPath
    {
        return $this->dirpath->addRelativePath($file->getRelFullname());
    }
    public function __destruct()
    {
        if (is_resource($this->lastHandle))
            closedir($this->lastHandle);
    }
}
