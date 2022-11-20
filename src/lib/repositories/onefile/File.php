<?php


namespace Supermetrolog\Synchronizer\lib\repositories\onefile;

use Supermetrolog\Synchronizer\lib\repositories\filesystem\File as FilesystemFile;
use Supermetrolog\Synchronizer\services\sync\interfaces\FileInterface;

class File extends FilesystemFile
{
    public string $hash;
    public function __construct(FileInterface $file)
    {
        $this->hash = "";
        if (!$file->isDir()) {
            $this->hash = $file->getHash();
        }

        parent::__construct($file->getName(), $file->getPath(), $file->getRelativePath(), $file->getParent());
    }

    public function getHash(): string
    {
        return $this->hash;
    }
}
