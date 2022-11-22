<?php

namespace Supermetrolog\Synchronizer\lib\repositories\filesystem;

use InvalidArgumentException;
use Supermetrolog\Synchronizer\lib\repositories\filesystem\file\AbsPath;
use Supermetrolog\Synchronizer\lib\repositories\onefile\interfaces\RepositoryInterface;
use Supermetrolog\Synchronizer\services\sync\interfaces\BaseRepositoryInterface;
use Supermetrolog\Synchronizer\services\sync\interfaces\FileInterface;
use Supermetrolog\Synchronizer\services\sync\interfaces\StreamInterface;
use Supermetrolog\Synchronizer\services\sync\interfaces\TargetRepositoryInterface;

class Filesystem implements BaseRepositoryInterface, TargetRepositoryInterface, RepositoryInterface
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
    public function getStream(): StreamInterface
    {
        return new Stream($this->dirpath);
    }
    public function findFile(FileInterface $findedFile): ?FileInterface
    {
        $stream = $this->getStream();
        foreach ($stream->read() as $file) {
            if ($file->getRelFullname() === $findedFile->getRelFullname()) {
                return $file;
            }
        }
        return null;
    }
    public function findByRelativeFullname(string $relativeName): ?FileInterface
    {
        $relativeName = "/$relativeName";
        $relativeName = str_replace("//", '/', $relativeName);
        $stream = $this->getStream();

        foreach ($stream->read() as $file) {
            if ($file->getRelFullname() == $relativeName) return $file;
        }
        return null;
    }
    public function getDirpath(): string
    {
        return $this->dirpath;
    }
    public function createOrUpdateFileWithContent(string $content, string $filename, string $relativePath = ""): bool
    {
        return $this->createFileWithContent($content, $filename, $relativePath);
    }

    public function create(FileInterface $file): bool
    {
        $fullname = $this->dirpath . $file->getRelFullname();
        if ($file->isDir()) {
            if (file_exists($fullname)) return true;
            return mkdir($fullname, 0777);
        }
        $result = file_put_contents($fullname, $file->getContent());
        if ($result === false) {
            return false;
        }
        return true;
    }
    public function update(FileInterface $file): bool
    {
        if ($file->isDir()) {
            return false;
        }
        $filename = $this->dirpath . $file->getRelFullname();
        $result = file_put_contents($filename, $file->getContent());
        if ($result === false) {
            return false;
        }
        return true;
    }
    public function remove(FileInterface $file): bool
    {
        $filename = $this->dirpath . $file->getRelFullname();
        if ($filename == "C:/OpenServer/domains/synchronizer/tests/services/sycn/testfolderwithexistchanges/testtargetfolder/children") {
            var_dump($file);
        }
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
