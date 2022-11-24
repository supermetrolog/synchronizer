<?php

namespace Supermetrolog\Synchronizer\lib\repositories\ftpfilesystem;

use League\Flysystem\DirectoryAttributes;
use League\Flysystem\FileAttributes;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemAdapter;
use League\Flysystem\PathNormalizer;
use Supermetrolog\Synchronizer\lib\repositories\filesystem\file\AbsPath;
use Supermetrolog\Synchronizer\lib\repositories\filesystem\file\File;
use Supermetrolog\Synchronizer\lib\repositories\filesystem\file\RelPath;
use Supermetrolog\Synchronizer\lib\repositories\onefile\interfaces\RepositoryInterface;
use Supermetrolog\Synchronizer\services\sync\interfaces\FileInterface;
use Supermetrolog\Synchronizer\services\sync\interfaces\TargetRepositoryInterface;

/**
 * @property resource $connection
 */
class FtpFileSystemAdapter extends Filesystem implements TargetRepositoryInterface, RepositoryInterface
{
    private AbsPath $dirpath;
    private $connection;
    /**
     * @param resource $connection
     */
    public function __construct(AbsPath $dirpath, $connection, FilesystemAdapter $adapter, array $config = [], ?PathNormalizer $pathNormalizer = null)
    {
        $this->dirpath = $dirpath;
        $this->connection = $connection;
        parent::__construct($adapter, $config, $pathNormalizer);
    }
    public function remove(FileInterface $file): bool
    {
        if ($file->isDir()) {
            $this->deleteDirectory($this->getFilename($file));
        } else {
            $this->delete($this->getFilename($file));
        }
        return true;
    }
    public function create(FileInterface $file, ?string $content): bool
    {
        if ($file->isDir()) {
            $this->createDirectory($this->getFilename($file));
        } else {
            $this->write($this->getFilename($file), $content ?? "");
        }
        return true;
    }
    public function update(FileInterface $file, ?string $content): bool
    {
        if ($file->isDir()) {
            return false;
        }
        return $this->create($file, $content);
    }
    public function directoryExists(FileInterface $file): bool
    {
        /**@var \FTP\Connection */
        $connection = $this->connection;
        if (@ftp_chdir($connection, $file->getUniqueName()) === true) {
            @ftp_chdir($connection, "/" . $this->dirpath);
            return true;
        }
        return false;
    }
    public function fileExist(FileInterface $file): bool
    {
        if ($file->isDir()) {
            // return $this->find($file) ? true : false;
            return $this->directoryExists($file);
        }
        return $this->fileExists($this->getFilename($file));
    }

    private function getFilename(FileInterface $file): string
    {
        return $this->dirpath . $file->getUniqueName();
    }
    private function find(FileInterface $file): ?FileInterface
    {
        $response = $this->listContents($this->dirpath, Filesystem::LIST_DEEP);
        foreach ($response as $item) {
            if ($item->path() == substr($file->getUniqueName(), 1)) {
                return $this->createFile($item);
            }
        }
        return null;
    }
    public function findByRelativeFullname(string $relativeName): ?FileInterface
    {
        $response = $this->listContents($this->dirpath, Filesystem::LIST_DEEP);
        foreach ($response as $item) {
            if ($item->path() == $relativeName) {
                return $this->createFile($item);
            }
        }
        return null;
    }
    /**
     * @param FileAttributes|DirectoryAttributes $item
     */
    private function createFile($item): File
    {
        $lastSlashPosition = mb_strpos($item->path(), "/");
        $name = "";
        $relPath = "";
        if ($lastSlashPosition === false) {
            $name = $item->path();
        } else {
            $name = mb_strrchr($item->path(), "/");
            $name = substr($name, 1);

            $relPath = substr($item->path(), 0, $lastSlashPosition);
        }
        $hash = "";
        if ($item instanceof FileAttributes) {
            $content = $this->read($this->dirpath . "/" . $item->path());
            $hash = md5($content);
            $isDir = false;
        } elseif ($item instanceof DirectoryAttributes) {
            $isDir = true;
        }
        return new File($name, $hash, new RelPath($relPath), $isDir, null);
    }
    public function createOrUpdate(string $content, string $filename): bool
    {
        $this->write($this->dirpath . "/" . $filename, $content);
        return true;
    }
    public function getContent(FileInterface $file): ?string
    {
        if ($file->isDir()) {
            return null;
        }
        return $this->read($this->getFilename($file));
    }
}
