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
        $file = new File($name, $path);
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
        $file = new File($name, $path);
        $this->assertSame($name, $file->getName());
        $this->assertSame(filemtime($fullname), $file->getUpdatedTime());
        $this->assertSame(realpath($fullname), $file->getFullname());
        $this->assertSame(true, $file->isDir());
        $this->assertSame(false, $file->isCurrentDirPointer());
        $this->assertSame(false, $file->isPreventDirPointer());
    }
}
