<?php

use PHPUnit\Framework\TestCase;
use Supermetrolog\Synchronizer\lib\repositories\filesystem\File;
use Supermetrolog\Synchronizer\lib\repositories\filesystem\Filesystem;
use Supermetrolog\Synchronizer\lib\repositories\onefile\OneFile;
use tests\testhelpers\Directory;

class OneFileTest  extends TestCase
{
    private const FILE_NAME = "sync-file.txt";
    private const TEST_FOLDER = __DIR__ . "/testfolder";
    private OneFile $oneFileRepo;
    public function setUp(): void
    {
        $this->createFilesForTestWithExistFile();
        $this->createData();
    }
    public function tearDown(): void
    {
        Directory::rmdir(self::TEST_FOLDER);
    }
    public function createData()
    {
        $filesystemRepo = new Filesystem(self::TEST_FOLDER);
        $this->oneFileRepo = new OneFile($filesystemRepo, self::FILE_NAME);
    }
    public function createFilesForTestWithExistFile(): void
    {
        $testFolder = self::TEST_FOLDER;
        mkdir($testFolder, 0777);

        file_put_contents(self::TEST_FOLDER . "/test1.txt", "fuck");
        file_put_contents(self::TEST_FOLDER . "/test2.txt", "suck");
    }
    public function testUpdateRepository()
    {
        $this->assertTrue($this->oneFileRepo->isEmpty());
        $file1 = new File("test1.txt", self::TEST_FOLDER, "", null);
        $file2 = new File("test2.txt", self::TEST_FOLDER, "", null);
        $creatingFiles = [
            $file1,
            $file2,
        ];
        $this->oneFileRepo->updateRepository($creatingFiles, [], []);
        $this->assertFalse($this->oneFileRepo->isEmpty());
        $this->assertTrue(file_exists(self::TEST_FOLDER . "/" . self::FILE_NAME));
        $findedFile1 = $this->oneFileRepo->findFile($file1);
        $findedFile2 = $this->oneFileRepo->findFile($file2);
        $this->assertNotNull($findedFile1);
        $this->assertNotNull($findedFile2);
        $this->assertEquals($file1->getHash(), $findedFile1->getHash());
        $this->assertEquals($file2->getHash(), $findedFile2->getHash());

        $filesystemRepo = new Filesystem(self::TEST_FOLDER);
        $newOneFileRepo = new OneFile($filesystemRepo, self::FILE_NAME);
        $findedFile1 = $newOneFileRepo->findFile($file1);
        $findedFile2 = $newOneFileRepo->findFile($file2);
        $this->assertNotNull($findedFile1);
        $this->assertNotNull($findedFile2);
        $this->assertEquals($file1->getHash(), $findedFile1->getHash());
        $this->assertEquals($file2->getHash(), $findedFile2->getHash());
    }

    public function testMarkFileAsDirty()
    {
        $file1 = new File("test1.txt", self::TEST_FOLDER, "", null);
        $file2 = new File("test2.txt", self::TEST_FOLDER, "", null);
        $creatingFiles = [
            $file1,
            $file2,
        ];
        $this->oneFileRepo->updateRepository($creatingFiles, [], []);
        $this->oneFileRepo->markFileAsDirty($file1);
        $this->assertNotEmpty($this->oneFileRepo->getNotDirtyFiles());
        $this->assertCount(1, $this->oneFileRepo->getNotDirtyFiles());
        $this->assertEquals($file2->getHash(), $this->oneFileRepo->getNotDirtyFiles()[0]->getHash());
    }
}
