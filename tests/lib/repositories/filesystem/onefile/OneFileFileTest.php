<?php

use PHPUnit\Framework\TestCase;
use Supermetrolog\Synchronizer\lib\repositories\filesystem\file\AbsPath;
use Supermetrolog\Synchronizer\lib\repositories\filesystem\file\File as FileSystemFile;
use Supermetrolog\Synchronizer\lib\repositories\filesystem\file\RelPath;
use Supermetrolog\Synchronizer\lib\repositories\filesystem\Filesystem;
use Supermetrolog\Synchronizer\lib\repositories\onefile\File;
use tests\testhelpers\Directory;

class OneFileFileTest extends TestCase
{
    private const TEST_FOLDER = __DIR__ . "/testfolder";
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
    }
    public function createFilesForTestWithExistFile(): void
    {
        $testFolder = self::TEST_FOLDER;
        mkdir($testFolder, 0777);


        mkdir($testFolder . "/children", 0777);
    }
    public function testIsDir()
    {
        $fileSystemFile = new FileSystemFile("children", new AbsPath(self::TEST_FOLDER), new RelPath(""), null);
        $file = new File($fileSystemFile);
        $serializedFile = serialize($file);
        $file = unserialize($serializedFile);
        $this->assertTrue($file->isDir());
    }
}
