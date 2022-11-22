<?php

use PHPUnit\Framework\TestCase;
use Supermetrolog\Synchronizer\lib\repositories\filesystem\file\AbsPath;
use Supermetrolog\Synchronizer\lib\repositories\filesystem\file\File;
use Supermetrolog\Synchronizer\lib\repositories\filesystem\Stream;

class StreamTest  extends TestCase
{
    public function testRead()
    {
        $streamDir = new AbsPath(__DIR__ . "/testfolder");
        $stream = new Stream($streamDir);
        $files = [];
        foreach ($stream->read() as  $file) {
            $files[] = $file;
            // var_dump($file);
            $this->assertInstanceOf(File::class, $file);
        }
        $this->assertCount(4, $files);
        $this->assertEquals("test3.txt", $files[0]->getName());
        $this->assertEquals("children", $files[1]->getName());
        $this->assertEquals("test1.txt", $files[2]->getName());
        $this->assertEquals("test2.txt", $files[3]->getName());

        $this->assertEquals($streamDir->getPath(), $files[1]->getAbsPath());
        $this->assertEquals("/", $files[1]->getRelPath());

        $this->assertEquals($streamDir->getPath(), $files[0]->getAbsPath());
        $this->assertEquals("/children/", $files[0]->getRelPath());
    }
}
