<?php

use PHPUnit\Framework\TestCase;
use Supermetrolog\Synchronizer\lib\repositories\filesystem\file\AbsPath;
use Supermetrolog\Synchronizer\lib\repositories\filesystem\file\File;
use Supermetrolog\Synchronizer\lib\repositories\filesystem\file\RelPath;
use Supermetrolog\Synchronizer\lib\repositories\filesystem\Filesystem;
use Supermetrolog\Synchronizer\lib\repositories\onefile\OneFile;
use tests\testhelpers\Directory;

class OneFileTest  extends TestCase
{
    private const FILE_NAME = "sync-file.cache";
    private const TEST_FOLDER = __DIR__ . "/testfolder";
    private AbsPath $testFolderPath;
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
        $this->testFolderPath = new AbsPath(self::TEST_FOLDER);
        $filesystemRepo = new Filesystem(new AbsPath(self::TEST_FOLDER));
        $this->oneFileRepo = new OneFile($filesystemRepo, self::FILE_NAME);
    }
    public function createFilesForTestWithExistFile(): void
    {
        $testFolder = self::TEST_FOLDER;
        mkdir($testFolder, 0777);
        mkdir($testFolder . "/fuck", 0777);

        file_put_contents(self::TEST_FOLDER . "/test1.txt", "fuck");
        file_put_contents(self::TEST_FOLDER . "/test2.txt", "suck");
    }
    public function testUpdateRepository()
    {
        $this->assertTrue($this->oneFileRepo->isEmpty());
        $hash1 = hash_file("md5", $this->testFolderPath . "/test1.txt");
        $hash2 = hash_file("md5", $this->testFolderPath . "/test2.txt");
        $file1 = new File("test1.txt", $hash1, new RelPath(), false, null);
        $file2 = new File("test2.txt", $hash2, new RelPath(), false, null);
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

        $filesystemRepo = new Filesystem($this->testFolderPath);
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
        $hash1 = hash_file("md5", $this->testFolderPath . "/test1.txt");
        $hash2 = hash_file("md5", $this->testFolderPath . "/test2.txt");
        $file1 = new File("test1.txt", $hash1, new RelPath(), false, null);
        $file2 = new File("test2.txt", $hash2, new RelPath(), false, null);
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

    public function testFileMethodsAfterUnserialize()
    {
        $this->assertTrue($this->oneFileRepo->isEmpty());
        $hash1 = hash_file("md5", $this->testFolderPath . "/test1.txt");
        $file1 = new File("test1.txt", $hash1, new RelPath(), false, null);
        $file2 = new File("fuck", "", new RelPath(), true, null);
        $creatingFiles = [
            $file1,
            $file2,
        ];
        $this->oneFileRepo->updateRepository($creatingFiles, [], []);

        $filesystemRepo = new Filesystem($this->testFolderPath);
        $newOneFileRepo = new OneFile($filesystemRepo, self::FILE_NAME);

        $findedFile1 = $newOneFileRepo->findFile($file1);
        $findedFile2 = $newOneFileRepo->findFile($file2);
        $this->assertFalse($findedFile1->isDir());
        $this->assertTrue($findedFile2->isDir());
    }
}
