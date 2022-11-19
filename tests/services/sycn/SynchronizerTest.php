<?php

use PHPUnit\Framework\TestCase;
use Supermetrolog\Synchronizer\lib\repositories\filesystem\Filesystem;
use Supermetrolog\Synchronizer\services\sync\interfaces\FileInterface;
use Supermetrolog\Synchronizer\services\sync\Synchronizer;
use tests\testhelpers\Directory;

class SynchronizerTest  extends TestCase
{

    public function setUp(): void
    {
        $this->createDataForTestWithExistChanges();
    }
    public function tearDown(): void
    {
        $this->removeDataForTestWithExistChanges();
    }
    private function removeDataForTestWithExistChanges(): void
    {
        Directory::rmdir(__DIR__ . "/testfolderwithexistchanges");
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

        //create target folder files
        file_put_contents("$targetFolder/test.txt", "fuck the shit");
    }
    public function testLoadUpdatedData()
    {
        $targetDirNameForSynchronize = __DIR__ . "/testfolderwithexistchanges/testtargetfolder";
        $baseDirNameForSynchronize = __DIR__ . "/testfolderwithexistchanges/testbasefolder";
        $baseRepo = new Filesystem($baseDirNameForSynchronize);
        $targetRepo = new Filesystem($targetDirNameForSynchronize);
        $sync = new Synchronizer($baseRepo, $targetRepo);
        $sync->loadUpdatedData();
        $chandedFiles = $sync->getChangedFiles();
        $this->assertSame(true, $sync->changedFilesExists());
        $this->assertNotEmpty($chandedFiles);
        $this->assertCount(3, $chandedFiles);
        foreach ($chandedFiles as $file) {
            $this->assertInstanceOf(FileInterface::class, $file);
        }
        $this->assertSame("test1.txt", $chandedFiles[0]->getName());
        $this->assertSame("children", $chandedFiles[1]->getName());
        $this->assertSame("test.txt", $chandedFiles[2]->getName());
    }

    public function testSync()
    {
        $targetDirNameForSynchronize = __DIR__ . "/testfolderwithexistchanges/testtargetfolder";
        $baseDirNameForSynchronize = __DIR__ . "/testfolderwithexistchanges/testbasefolder";
        $baseRepo = new Filesystem($baseDirNameForSynchronize);
        $targetRepo = new Filesystem($targetDirNameForSynchronize);
        $sync = new Synchronizer($baseRepo, $targetRepo);
        $sync->loadUpdatedData();
        $sync->sync();
        $this->assertTrue(file_exists("$targetDirNameForSynchronize/children"));
        $this->assertTrue(file_exists("$targetDirNameForSynchronize/children/test1.txt"));
        $this->assertTrue(file_exists("$targetDirNameForSynchronize/test.txt"));

        $this->assertEquals("fuck the police", file_get_contents("$targetDirNameForSynchronize/test.txt"));
        $this->assertEquals("dermo", file_get_contents("$targetDirNameForSynchronize/children/test1.txt"));
    }
}
