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
        $file = new File($name, "", new RelPath(), false, null);
        $this->assertEquals("/test1.txt", $file->getUniqueName());
        $this->assertFalse(false, $file->isDir());
        $this->assertFalse(false, $file->isCurrentDirPointer());
        $this->assertFalse(false, $file->isPreventDirPointer());
    }
    public function testWithDirFile()
    {
        $name = "testfolder";
        $path = __DIR__;
        $path = new AbsPath($path);
        $file = new File($name, "", new RelPath(), true, null);
        $this->assertEquals("/testfolder", $file->getUniqueName());
        $this->assertTrue(true, $file->isDir());
        $this->assertFalse(false, $file->isCurrentDirPointer());
        $this->assertFalse(false, $file->isPreventDirPointer());
    }
    public function testWithEqualPathParent()
    {
        $name = "children";
        $path = __DIR__ . "/testfolder";
        $file = new File($name, "", new RelPath(), true, null);
        $this->expectException(InvalidArgumentException::class);
        new File($name, "", new RelPath(""), true, $file);
    }
    public function testWithDirParent()
    {
        $file = new File("children", "", new RelPath(), true, null);
        $file2 = new File("test3.txt", "", new RelPath(), false, $file);
        $this->assertEquals($file, $file2->getParent());
    }
    public function testWithFileParent()
    {
        $name = "test1.txt";
        $path = __DIR__ . "/testfolder";
        $file = new File($name, "", new RelPath(), false, null);
        $this->expectException(InvalidArgumentException::class);
        new File($name, "", new RelPath(), false, $file);
    }

    public function testGetHash()
    {
        $name = "test1.txt";
        $path = __DIR__ . "/testfolder";
        file_put_contents("$path/$name", "Наглый коричневый лисёнок прыгает вокруг ленивой собаки.");
        $file = new File($name, "bff8b4bc8b5c1c1d5b3211dfb21d1e76", new RelPath(), false, null);
        $this->assertEquals("bff8b4bc8b5c1c1d5b3211dfb21d1e76", $file->getHash());
    }
}
