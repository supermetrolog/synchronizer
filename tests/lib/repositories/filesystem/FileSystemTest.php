<?php

use PHPUnit\Framework\TestCase;
use Supermetrolog\Synchronizer\lib\repositories\filesystem\file\AbsPath;
use Supermetrolog\Synchronizer\lib\repositories\filesystem\file\File;
use Supermetrolog\Synchronizer\lib\repositories\filesystem\file\RelPath;
use Supermetrolog\Synchronizer\lib\repositories\filesystem\Filesystem;
use Supermetrolog\Synchronizer\lib\repositories\filesystem\Stream;

class FileSystemTest  extends TestCase
{
    public function testWithNotExistPath()
    {
        $this->expectException(InvalidArgumentException::class);
        new Filesystem(new AbsPath("fuck"));
    }

    public function testWithNotFolderPath()
    {
        $this->expectException(InvalidArgumentException::class);
        new Filesystem(new AbsPath(__DIR__ . "/SynchronizerTest.php"));
    }

    public function testGetStream()
    {
        $fs = new Filesystem(new AbsPath(__DIR__ . "/testfolder"));
        $stream = $fs->getStream();
        $this->assertInstanceOf(Stream::class, $stream);
    }

    public function testFindFile()
    {
        $baseDir = new AbsPath(__DIR__ . "/testfolder");
        $fileForFindRelPath = new RelPath("/children//");
        $fs = new Filesystem($baseDir);
        $fileForFind = new File("test3.txt", $baseDir->addRelativePath($fileForFindRelPath), $fileForFindRelPath, null);
        $file = $fs->findFile($fileForFind);
        $this->assertEquals("test3.txt", $file->getName());
        $this->assertNotNull($file);
        $this->assertInstanceOf(File::class, $file);
        $this->assertSame($fileForFindRelPath->getPath() . "test3.txt", $file->getRelFullname());
    }
    public function testFindByRelativeFullname()
    {
        $baseDir = __DIR__ . "/testfolder";
        $fileForFindRelPath = "/children/test3.txt";
        $fs = new Filesystem(new AbsPath($baseDir));
        $file = $fs->findByRelativeFullname($fileForFindRelPath);
        $this->assertNotNull($file);
        $this->assertEquals("test3.txt", $file->getName());
    }
    public function testFindDir()
    {
        $baseDir = new AbsPath(__DIR__ . "/testfolder");
        $fileForFindRelPath = new RelPath("");
        $fs = new Filesystem($baseDir);
        $fileForFind = new File("children", $baseDir->addRelativePath($fileForFindRelPath), $fileForFindRelPath, null);
        $file = $fs->findFile($fileForFind);
        $this->assertEquals("children", $file->getName());
        $this->assertNotNull($file);
        $this->assertInstanceOf(File::class, $file);
        $this->assertSame($fileForFindRelPath->getPath() . "children", $file->getRelFullname());
    }
    public function testCreate()
    {
        $baseDir = new AbsPath(__DIR__ . "/testfolder");
        $fs = new Filesystem($baseDir);
        $fileForCreate = new File("fuck.txt", $baseDir, new RelPath(""), null);
        $fileForCreate->loadContent("fuck the police");
        $fs->create($fileForCreate, $fileForCreate->getRelPath());
        $this->assertTrue(file_exists("$baseDir/fuck.txt"));
        unlink("$baseDir/fuck.txt");
    }
    public function testCreateAlreadyExistFolder()
    {
        $baseDir = new AbsPath(__DIR__ . "/testfolder");
        $fs = new Filesystem($baseDir);
        $fileForCreate = new File("children", $baseDir, new RelPath(""), null);
        $fs->create($fileForCreate, $fileForCreate->getRelPath());
        $this->assertTrue(file_exists("$baseDir/children"));
    }

    public function testCreateFileWithContent()
    {
        $baseDir = __DIR__ . "/testfolder";
        $fs = new Filesystem(new AbsPath($baseDir));
        $fs->createFileWithContent("fuck the police", "suka.txt");
        $this->assertTrue(file_exists("$baseDir/suka.txt"));
        $this->assertEquals("fuck the police", file_get_contents("$baseDir/suka.txt"));
        unlink("$baseDir/suka.txt");
    }
    public function testCreateFileWithContentInChildrenDirectory()
    {
        $baseDir = __DIR__ . "/testfolder";
        $fs = new Filesystem(new AbsPath($baseDir));
        $fs->createFileWithContent("fuck the police", "suka.txt", "/children");
        $this->assertTrue(file_exists("$baseDir/children/suka.txt"));
        $this->assertEquals("fuck the police", file_get_contents("$baseDir/children/suka.txt"));
        unlink("$baseDir/children/suka.txt");
    }
}
