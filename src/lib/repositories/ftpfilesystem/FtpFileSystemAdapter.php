<?php

namespace Supermetrolog\Synchronizer\lib\repositories\ftpfilesystem;

use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemAdapter;
use League\Flysystem\PathNormalizer;
use Supermetrolog\Synchronizer\lib\repositories\filesystem\file\AbsPath;
use Supermetrolog\Synchronizer\lib\repositories\filesystem\file\File;
use Supermetrolog\Synchronizer\lib\repositories\filesystem\file\RelPath;
use Supermetrolog\Synchronizer\lib\repositories\onefile\interfaces\RepositoryInterface;
use Supermetrolog\Synchronizer\services\sync\interfaces\FileInterface;
use Supermetrolog\Synchronizer\services\sync\interfaces\TargetRepositoryInterface;

class FtpFileSystemAdapter extends Filesystem implements TargetRepositoryInterface, RepositoryInterface
{
    private AbsPath $dirpath;

    public function __construct(AbsPath $dirpath, FilesystemAdapter $adapter, array $config = [], ?PathNormalizer $pathNormalizer = null)
    {
        $this->dirpath = $dirpath;
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
    public function fileExist(FileInterface $file): bool
    {
        return $this->fileExists($this->getFilename($file));
    }

    private function getFilename(FileInterface $file): string
    {
        return $this->dirpath . $file->getUniqueName();
    }

    public function findByRelativeFullname(string $relativeName): ?FileInterface
    {
        $response = $this->listContents($this->dirpath, Filesystem::LIST_DEEP);
        foreach ($response as $item) {
            if ($item->path() == $relativeName) {
                if ($item instanceof \League\Flysystem\FileAttributes) {
                    $chunks = split("/", $item->path());
                    $name = $chunks[count($chunks) - 1];
                    
                    $content = $this->read($this->dirpath . "/" . $item->path());
                    $hash = md5($content);

                    new File($name, $hash, new RelPath($item->))
                } elseif ($item instanceof \League\Flysystem\DirectoryAttributes) {
                    // handle the directory
                }
            }
        }
    }
    public function createOrUpdateFileWithContent(string $content, string $filename, string $relativePath = ""): bool
    {
    }
    public function getContent(FileInterface $file): ?string
    {
        if ($file->isDir()) {
            return null;
        }
        return $this->read($this->getFilename($file));
    }
}
