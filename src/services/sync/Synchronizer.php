<?php

namespace Supermetrolog\Synchronizer\services\sync;

use Supermetrolog\Synchronizer\services\sync\interfaces\FileRepositoryInterface;

class Synchronizer
{
    private array $changedFiles;

    private FileRepositoryInterface $baseFileRepository;
    private FileRepositoryInterface $targetFileRepository;
    public function __construct(FileRepositoryInterface $baseFileRepository, FileRepositoryInterface $targetFileRepository)
    {
        $this->changedFiles = [];

        $this->baseFileRepository = $baseFileRepository;
        $this->targetFileRepository = $targetFileRepository;
    }
    public function loadUpdatedData(): void
    {
        $stream = $this->baseFileRepository->createStream();
        foreach ($stream->read() as $file) {
            if ($file->getName() == "." || $file->getName() == "..") continue;
            if ($targetEntry = $this->targetFileRepository->findByFullname($file->getFullname())) {
                if ($targetEntry->getUpdatedTime() > $file->getUpdatedTime()) {
                    $this->changedFiles[] = $file;
                }
            } else {
                $this->changedFiles[] = $file;
            }
        }
    }
    // public function loadUpdatedData(): void
    // {
    //     $handle = opendir($this->baseDirPath);
    //     while ($entry = readdir($handle)) {

    //         $baseEntry = $this->baseDirPath . "/$entry";
    //         $targetEntry = $this->targetDirPath . "/$entry";

    //         if ($entry == "." || $entry == "..") continue;
    //         if (file_exists($targetEntry)) {
    //             if (filemtime($targetEntry) > filemtime($baseEntry)) {
    //                 $this->changedFiles[] = $baseEntry;
    //             }
    //         } else {
    //             $this->changedFiles[] = $baseEntry;
    //         }
    //     }
    //     closedir($handle);
    // }

    public function changedFilesExists(): bool
    {
        return count($this->changedFiles) != 0;
    }
}
