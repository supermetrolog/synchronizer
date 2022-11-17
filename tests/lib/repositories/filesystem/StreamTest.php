<?php

use PHPUnit\Framework\TestCase;
use Supermetrolog\Synchronizer\lib\repositories\filesystem\File;
use Supermetrolog\Synchronizer\lib\repositories\filesystem\Stream;

class StreamTest  extends TestCase
{
    public function testRead()
    {
        $stream = new Stream(__DIR__ . "/testfolder");
        $files = [];
        foreach ($stream->read() as  $file) {
            $files[] = $file;
            $this->assertInstanceOf(File::class, $file);
        }
        $this->assertCount(5, $files);
        $this->assertSame(".", $files[0]->getName());
        $this->assertSame("..", $files[1]->getName());
        $this->assertSame("children", $files[2]->getName());
        $this->assertSame("test1.txt", $files[3]->getName());
        $this->assertSame("test2.txt", $files[4]->getName());
    }
    public function testReadRecursive()
    {
        $stream = new Stream(__DIR__ . "/testfolder");
        $files = [];
        foreach ($stream->readRecursive() as  $file) {
            $files[] = $file;
            // var_dump($file);
            $this->assertInstanceOf(File::class, $file);
        }
        $this->assertCount(8, $files);
        $this->assertSame(".", $files[0]->getName());
        $this->assertSame("..", $files[1]->getName());
        $this->assertSame(".", $files[2]->getName());
        $this->assertSame("..", $files[3]->getName());
        $this->assertSame("test3.txt", $files[4]->getName());
        $this->assertSame("children", $files[5]->getName());
        $this->assertSame("test1.txt", $files[6]->getName());
        $this->assertSame("test2.txt", $files[7]->getName());
    }
}
