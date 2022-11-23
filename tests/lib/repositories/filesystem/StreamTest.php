<?php

use PHPUnit\Framework\TestCase;
use Supermetrolog\Synchronizer\lib\repositories\filesystem\file\AbsPath;
use Supermetrolog\Synchronizer\lib\repositories\filesystem\file\File;
use Supermetrolog\Synchronizer\lib\repositories\filesystem\Stream;

class StreamTest  extends TestCase
{
    public function testRead()
    {
        $testDir = new AbsPath(__DIR__ . "/testfolder");
        $stream = new Stream($testDir);
        /**@var Fileinterface[] */
        $files = [];
        foreach ($stream->read() as  $file) {
            $files[] = $file;
            // var_dump($file);
            $this->assertInstanceOf(File::class, $file);
        }
        $this->assertCount(4, $files);
        $this->assertEquals("/children/test3.txt", $files[0]->getUniqueName());
        $this->assertEquals("/children", $files[1]->getUniqueName());
        $this->assertEquals("/test1.txt", $files[2]->getUniqueName());
        $this->assertEquals("/test2.txt", $files[3]->getUniqueName());
    }
}
