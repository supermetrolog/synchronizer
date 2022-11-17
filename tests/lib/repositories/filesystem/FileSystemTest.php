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

    public function testFindByFullnameValid()
    {
        $baseDir = __DIR__ . "/testfolder";
        $fileForFind = $baseDir . "/children/test3.txt";
        echo $fileForFind . "\n";
        $fs = new Filesystem($baseDir);
        $file = $fs->findByFullname($fileForFind);
        $this->assertNotNull($file);
        $this->assertInstanceOf(File::class, $file);
        $this->assertSame(realpath($fileForFind), $file->getFullname());
        // C:\OpenServer\domains\synchronizer\tests\lib\repositories\filesystem\testfolder/children/test3.txt
        // C:\OpenServer\domains\synchronizer\tests\lib\repositories\filesystem\testfolder\children\test3.txt
    }
}
