<?php

use PHPUnit\Framework\TestCase;
use Supermetrolog\Synchronizer\lib\repositories\filesystem\file\AbsPath;
use Supermetrolog\Synchronizer\lib\repositories\filesystem\file\File;
use Supermetrolog\Synchronizer\lib\repositories\filesystem\file\RelPath;

class FileTest extends TestCase
{
    public function testWithFileFile()
    {
        $name = "test1.txt";
        $path = __DIR__ . "/testfolder";
        $file = new File($name, new AbsPath($path), new RelPath(""), null);
        $this->assertEquals($name, $file->getName());
        $this->assertFalse(false, $file->isDir());
        $this->assertFalse(false, $file->isCurrentDirPointer());
        $this->assertFalse(false, $file->isPreventDirPointer());
    }
    public function testWithDirFile()
    {
        $name = "testfolder";
        $path = __DIR__;
        $path = new AbsPath($path);
        $file = new File($name, $path, new RelPath(""), null);
        $this->assertEquals($name, $file->getName());
        $this->assertTrue(true, $file->isDir());
        $this->assertFalse(false, $file->isCurrentDirPointer());
        $this->assertFalse(false, $file->isPreventDirPointer());
    }
    public function testWithEqualPathParent()
    {
        $name = "children";
        $path = __DIR__ . "/testfolder";
        $file = new File($name, new AbsPath($path), new RelPath(""), null);
        $this->expectException(InvalidArgumentException::class);
        new File($name, new AbsPath($path), new RelPath(""), $file);
    }
    public function testWithDirParent()
    {
        $file = new File("children", new AbsPath(__DIR__ . "/testfolder"), new RelPath(""), null);
        $file2 = new File("test3.txt", new AbsPath(__DIR__ . "/testfolder/children"), new RelPath(""), $file);
        $this->assertEquals($file, $file2->getParent());
    }
    public function testWithFileParent()
    {
        $name = "test1.txt";
        $path = __DIR__ . "/testfolder";
        $file = new File($name, new AbsPath($path), new RelPath(""), null);
        $this->expectException(InvalidArgumentException::class);
        new File($name, new AbsPath($path), new RelPath(""), $file);
    }

    public function testGetHash()
    {
        $name = "test1.txt";
        $path = __DIR__ . "/testfolder";
        file_put_contents("$path/$name", "Наглый коричневый лисёнок прыгает вокруг ленивой собаки.");
        $file = new File($name, new AbsPath($path), new RelPath(""), null);
        $this->assertEquals("bff8b4bc8b5c1c1d5b3211dfb21d1e76", $file->getHash());
    }
}
