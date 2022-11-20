<?php

use PHPUnit\Framework\TestCase;
use Supermetrolog\Synchronizer\lib\repositories\filesystem\File;

class FileTest extends TestCase
{
    public function testWithFileFile()
    {
        $name = "test1.txt";
        $path = __DIR__ . "/testfolder";
        $fullname = $path . "/$name";
        $file = new File($name, $path, "", null);
        $this->assertSame($name, $file->getName());
        $this->assertSame(filemtime($fullname), $file->getUpdatedTime());
        $this->assertSame(realpath($fullname), $file->getFullname());
        $this->assertSame(false, $file->isDir());
        $this->assertSame(false, $file->isCurrentDirPointer());
        $this->assertSame(false, $file->isPreventDirPointer());
    }
    public function testWithDirFile()
    {
        $name = "testfolder";
        $path = __DIR__;
        $fullname = $path . "/$name";
        $file = new File($name, $path, "", null);
        $this->assertSame($name, $file->getName());
        $this->assertSame(filemtime($fullname), $file->getUpdatedTime());
        $this->assertSame(realpath($fullname), $file->getFullname());
        $this->assertSame(true, $file->isDir());
        $this->assertSame(false, $file->isCurrentDirPointer());
        $this->assertSame(false, $file->isPreventDirPointer());
    }
    public function testWithEqualPathParent()
    {
        $name = "children";
        $path = __DIR__ . "/testfolder";
        $file = new File($name, $path, "", null);
        $this->expectException(InvalidArgumentException::class);
        new File($name, $path, "", $file);
    }
    public function testWithDirParent()
    {
        $file = new File("children", __DIR__ . "/testfolder", "", null);
        $file2 = new File("test3.txt", __DIR__ . "/testfolder/children", "", $file);
        $this->assertEquals($file, $file2->getParent());
    }
    public function testWithFileParent()
    {
        $name = "test1.txt";
        $path = __DIR__ . "/testfolder";
        $file = new File($name, $path, "", null);
        $this->expectException(InvalidArgumentException::class);
        new File($name, $path, "", $file);
    }

    public function testGetHash()
    {
        $name = "test1.txt";
        $path = __DIR__ . "/testfolder";
        file_put_contents("$path/$name", "Наглый коричневый лисёнок прыгает вокруг ленивой собаки.");
        $file = new File($name, $path, "", null);
        $this->assertEquals("bff8b4bc8b5c1c1d5b3211dfb21d1e76", $file->getHash());
    }
}
