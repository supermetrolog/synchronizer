<?php


namespace Supermetrolog\Synchronizer\lib\repositories\onefile;

use LogicException;
use Supermetrolog\Synchronizer\lib\repositories\onefile\interfaces\RepositoryInterface;
use Supermetrolog\Synchronizer\services\sync\interfaces\AlreadySynchronizedRepositoryInterface;
use Supermetrolog\Synchronizer\services\sync\interfaces\FileInterface;

/** 
 * @property array<string, FileInterface> $files 
 * @property string[] $dirtyFileKeys 
 * */
class OneFile implements AlreadySynchronizedRepositoryInterface
{
    private string $filename;
    private RepositoryInterface $repo;
    private array $files;
    private array $dirtyFileKeys;
    public function __construct(RepositoryInterface $repo, string $filename)
    {
        $this->filename = $filename;
        $this->repo = $repo;
        $this->files = [];
        $this->loadContent();
    }
    private function loadContent(): void
    {
        $file = $this->repo->findByRelativeFullname($this->filename);
        if ($file !== null) {
            $this->files = unserialize($file->getContent());
        }
    }
    public function findFile(FileInterface $findedFile): ?FileInterface
    {
        if (key_exists($findedFile->getRelFullname(), $this->files)) {
            return $this->files[$findedFile->getRelFullname()];
        }
        return null;
    }
    /**
     * @param FileInterface[] $createdFiles
     * @param FileInterface[] $updatedFiles
     * @param FileInterface[] $removedFiles
     */
    public function updateRepository(array $createdFiles, array $updatedFiles, array $removedFiles): void
    {
        $this->removeFiles($removedFiles);
        $this->createFiles($createdFiles);
        $this->updateFiles($updatedFiles);
        $this->createOrUpdateSyncFile();
    }
    /**
     * @param FileInterface[] $files
     */
    private function removeFiles(array $files): void
    {
        foreach ($files as $file) {
            unset($this->files[$file->getRelFullname()]);
        }
    }
    /**
     * @param FileInterface[] $files
     */
    private function createFiles(array $files): void
    {
        foreach ($files as $file) {
            $this->files[$file->getRelFullname()] = new File($file);
        }
    }
    /**
     * @param FileInterface[] $files
     */
    private function updateFiles(array $files): void
    {
        $this->createFiles($files);
    }
    private function createOrUpdateSyncFile(): void
    {
        if (!$this->repo->createOrUpdateFileWithContent(serialize($this->files), $this->filename))
            throw new LogicException("error when create or update sync file");
    }
    public function markFileAsDirty(FileInterface $file): void
    {
        $this->dirtyFileKeys[$file->getRelFullname()] = $file;
    }
    /**
     * @return FileInterface[]
     */
    public function getNotDirtyFiles(): array
    {
        $result = [];
        foreach ($this->files as $key => $file) {
            if (!key_exists($key, $this->dirtyFileKeys)) {
                $result[] = $file;
            }
        }
        return $result;
    }
    public function isEmpty(): bool
    {
        return count($this->files) == 0;
    }
}
