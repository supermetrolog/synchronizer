<?php

use PHPUnit\Framework\TestCase;
use Supermetrolog\Synchronizer\lib\repositories\filesystem\Filesystem;
use Supermetrolog\Synchronizer\lib\repositories\onefile\OneFile;
use Supermetrolog\Synchronizer\services\sync\interfaces\FileInterface;
use Supermetrolog\Synchronizer\services\sync\Synchronizer;
use tests\testhelpers\Directory;

class SynchronizerTest  extends TestCase
{
    private const testFolder = __DIR__ . "/testfolderwithexistchanges";
    private const targetDirNameForSynchronize = self::testFolder . "/testtargetfolder";
    private const baseDirNameForSynchronize = self::testFolder . "/testbasefolder";
    private Synchronizer $sync;
    private Filesystem $baseRepo;
    private Filesystem $targetRepo;
    private OneFile $oneFileRepo;
    public function setUp(): void
    {
        $this->createDataForTestWithExistChanges();
        $this->createData();
    }
    public function tearDown(): void
    {
        $this->removeDataForTestWithExistChanges();
    }
    private function removeDataForTestWithExistChanges(): void
    {
        Directory::rmdir(self::testFolder);
    }
    private function createData(): void
    {
        $this->baseRepo = new Filesystem(self::baseDirNameForSynchronize);
        $this->targetRepo = new Filesystem(self::targetDirNameForSynchronize);
        $this->oneFileRepo = new OneFile($this->targetRepo, "sync-file.txt");
        $this->sync = new Synchronizer($this->baseRepo, $this->targetRepo, $this->oneFileRepo);
    }
    private function createDataForTestWithExistChanges(): void
    {
        $mainFolder = __DIR__ . "/testfolderwithexistchanges";
        mkdir($mainFolder, 0777);
        $baseFolder =  "$mainFolder/testbasefolder";
        mkdir($baseFolder, 0777);
        $targetFolder = "$mainFolder/testtargetfolder";
        mkdir($targetFolder, 0777);

        // create base folder files
        file_put_contents($baseFolder . "/test.txt", 'fuck the police');
        $childrenFolder = "$baseFolder/children";
        mkdir($childrenFolder, 0777);
        file_put_contents("$childrenFolder/test1.txt", "dermo");
        file_put_contents($baseFolder . "/test2.txt", '123suka55');
    }
    public function testLoadUpdatedData()
    {

        $this->sync->load();
        $changingFiles = $this->sync->getChangingFiles();
        $creatingFiles = $this->sync->getCreatingFiles();
        $removingFiles = $this->sync->getRemovingFiles();
        $this->assertSame(true, $this->sync->affectedFilesExist());
        $this->assertEmpty($changingFiles);
        $this->assertEmpty($removingFiles);
        $this->assertNotEmpty($creatingFiles);
        $this->assertCount(4, $creatingFiles);
        foreach ($creatingFiles as $file) {
            $this->assertInstanceOf(FileInterface::class, $file);
        }
        $this->assertSame("test1.txt", $creatingFiles[0]->getName());
        $this->assertSame("children", $creatingFiles[1]->getName());
        $this->assertSame("test.txt", $creatingFiles[2]->getName());
        $this->assertSame("test2.txt", $creatingFiles[3]->getName());
    }

    public function testSync()
    {
        $this->sync->load();
        $this->sync->sync();
        $this->assertTrue(file_exists(self::targetDirNameForSynchronize . "/children"));
        $this->assertTrue(file_exists(self::targetDirNameForSynchronize . "/children/test1.txt"));
        $this->assertTrue(file_exists(self::targetDirNameForSynchronize . "/test.txt"));

        $this->assertEquals("fuck the police", file_get_contents(self::targetDirNameForSynchronize . "/test.txt"));
        $this->assertEquals("dermo", file_get_contents(self::targetDirNameForSynchronize . "/children/test1.txt"));
    }

    public function testDoubleSync()
    {
        $this->sync->load();
        $this->sync->sync();

        $this->assertTrue(file_exists(self::targetDirNameForSynchronize . "/children"));
        $this->assertTrue(file_exists(self::targetDirNameForSynchronize . "/children/test1.txt"));
        $this->assertTrue(file_exists(self::targetDirNameForSynchronize . "/test.txt"));
        $this->assertEquals("fuck the police", file_get_contents(self::targetDirNameForSynchronize . "/test.txt"));
        $this->assertEquals("dermo", file_get_contents(self::targetDirNameForSynchronize . "/children/test1.txt"));

        file_put_contents(self::baseDirNameForSynchronize . "/test.txt", "update");
        $this->assertEquals("update", file_get_contents(self::baseDirNameForSynchronize . "/test.txt"));
        $this->assertEquals("fuck the police", file_get_contents(self::targetDirNameForSynchronize . "/test.txt"));
        $this->createData();
        $this->sync->load();
        $this->sync->sync();
        $this->assertTrue($this->sync->affectedFilesExist());
        $this->assertNotNull($this->sync->getChangingFiles());
        $this->assertCount(1, $this->sync->getChangingFiles());
        $this->assertEquals("test.txt", $this->sync->getChangingFiles()[0]->getName());
        $this->assertEquals("update", file_get_contents(self::targetDirNameForSynchronize . "/test.txt"));
    }
    // public function testRemovingFiles()
    // {
    //     # code...
    // }
}
