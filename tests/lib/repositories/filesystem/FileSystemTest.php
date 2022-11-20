<?php

use PHPUnit\Framework\TestCase;
use Supermetrolog\Synchronizer\lib\repositories\filesystem\File;
use Supermetrolog\Synchronizer\lib\repositories\filesystem\Filesystem;
use Supermetrolog\Synchronizer\lib\repositories\filesystem\Stream;

class FileSystemTest  extends TestCase
{
    public function testWithNotExistPath()
    {
        $this->expectException(InvalidArgumentException::class);
        new Filesystem("fuck");
    }

    public function testWithNotFolderPath()
    {
        $this->expectException(InvalidArgumentException::class);
        new Filesystem(__DIR__ . "/SynchronizerTest.php");
    }

    public function testCreateStream()
    {
        $fs = new Filesystem(__DIR__ . "/testfolder");
        $stream = $fs->createStream();
        $this->assertInstanceOf(Stream::class, $stream);
    }

    public function testFindFile()
    {
        $baseDir = __DIR__ . "/testfolder";
        $fileForFindRelPath = "/children/test3.txt";
        $fs = new Filesystem($baseDir);
        $fileForFind = new File("test3.txt", $baseDir . $fileForFindRelPath, $fileForFindRelPath, null);
        $file = $fs->findFile($fileForFind);
        $this->assertEquals("test3.txt", $file->getName());
        $this->assertNotNull($file);
        $this->assertInstanceOf(File::class, $file);
        $this->assertSame(realpath($baseDir = __DIR__ . "/testfolder/" . $fileForFindRelPath), $file->getFullname());
    }
    public function testFindByRelativeFullname()
    {
        $baseDir = __DIR__ . "/testfolder";
        $fileForFindRelPath = "/children/test3.txt";
        $fs = new Filesystem($baseDir);
        $file = $fs->findByRelativeFullname($fileForFindRelPath);
        $this->assertNotNull($file);
        $this->assertEquals("test3.txt", $file->getName());
    }
    public function testFindDir()
    {
        $baseDir = __DIR__ . "/testfolder";
        $fileForFindRelPath = "/children";
        $fs = new Filesystem($baseDir);
        $fileForFind = new File("children", $baseDir . $fileForFindRelPath, $fileForFindRelPath, null);
        $file = $fs->findFile($fileForFind);
        $this->assertEquals("children", $file->getName());
        $this->assertNotNull($file);
        $this->assertInstanceOf(File::class, $file);
        $this->assertSame(realpath($baseDir = __DIR__ . "/testfolder/" . $fileForFindRelPath), $file->getFullname());
    }
    public function testCreate()
    {
        $baseDir = __DIR__ . "/testfolder";
        $fs = new Filesystem($baseDir);
        $fileForCreate = new File("fuck.txt", $baseDir, "", null);
        $fileForCreate->loadContent("fuck the police");
        $fs->create($fileForCreate, $fileForCreate->getRelativePath());
        $this->assertTrue(file_exists("$baseDir/fuck.txt"));
        unlink("$baseDir/fuck.txt");
    }
    public function testCreateAlreadyExistFolder()
    {
        $baseDir = __DIR__ . "/testfolder";
        $fs = new Filesystem($baseDir);
        $fileForCreate = new File("children", $baseDir, "", null);
        $fs->create($fileForCreate, $fileForCreate->getRelativePath());
        $this->assertTrue(file_exists("$baseDir/children"));
    }

    public function testCreateFileWithContent()
    {
        $baseDir = __DIR__ . "/testfolder";
        $fs = new Filesystem($baseDir);
        $fs->createFileWithContent("fuck the police", "suka.txt");
        $this->assertTrue(file_exists("$baseDir/suka.txt"));
        $this->assertEquals("fuck the police", file_get_contents("$baseDir/suka.txt"));
        unlink("$baseDir/suka.txt");
    }
    public function testCreateFileWithContentInChildrenDirectory()
    {
        $baseDir = __DIR__ . "/testfolder";
        $fs = new Filesystem($baseDir);
        $fs->createFileWithContent("fuck the police", "suka.txt", "/children");
        $this->assertTrue(file_exists("$baseDir/children/suka.txt"));
        $this->assertEquals("fuck the police", file_get_contents("$baseDir/children/suka.txt"));
        unlink("$baseDir/children/suka.txt");
    }
}
