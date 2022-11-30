<?php

namespace Supermetrolog\Synchronizer\lib\repositories\filesystem;

use Generator;
use LogicException;
use Supermetrolog\Synchronizer\lib\repositories\filesystem\file\AbsPath;
use Supermetrolog\Synchronizer\lib\repositories\filesystem\file\File;
use Supermetrolog\Synchronizer\lib\repositories\filesystem\file\RelPath;
use Supermetrolog\Synchronizer\services\sync\interfaces\StreamInterface;

/**
 * @property resource $lastHandle
 */
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

            $file = $this->createFile($filename, $dirpath, $parent);
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
    private function getFileHash(RelPath $relPath, string $filename): string
    {
        $fullpath = $this->dirpath . $relPath . $filename;
        if (is_dir($fullpath)) {
            throw new LogicException("hash for directory not exist");
        }
        return hash_file("md5", $fullpath);
    }
    private function createFile(string $filename, AbsPath $dirpath, ?File $parent): File
    {
        $relPath = new RelPath(str_replace($this->dirpath, "", $dirpath));
        $fullpath = $this->dirpath . $relPath . $filename;
        $isDir = is_dir($fullpath);
        $hash = "";
        if (!$isDir) {
            $hash = $this->getFileHash($relPath, $filename);
        }
        return new File($filename, $hash, $relPath, $isDir, $parent);
    }
    private function getNextPath(File $file): AbsPath
    {
        $relPath = $file->isDir() ? $file->getUniqueName() : $file->getRelPath();
        return $this->dirpath->addRelativePath($relPath);
    }

    public function __destruct()
    {
        if (is_resource($this->lastHandle))
            closedir($this->lastHandle);
    }
}
