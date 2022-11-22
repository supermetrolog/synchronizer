<?php

use PHPUnit\Framework\TestCase;
use Supermetrolog\Synchronizer\lib\repositories\filesystem\file\AbsPath;
use Supermetrolog\Synchronizer\lib\repositories\filesystem\file\RelPath;

class AbsPathTest extends TestCase
{
    public function testValidPath()
    {
        $absPath = new AbsPath(__DIR__);
        $this->assertEquals(str_replace("\\", "/", __DIR__), $absPath->getPath());
    }
    public function testValidPathWithSlashInEnd()
    {
        $absPath = new AbsPath(__DIR__ . "/");
        $this->assertEquals(str_replace("\\", "/", __DIR__), $absPath->getPath());
    }
    public function testInvalidEmptyPath()
    {
        $this->expectException(InvalidArgumentException::class);
        new AbsPath("");
    }

    public function testAddRelativePath()
    {
        $absPath = new AbsPath(__DIR__);
        $newPath = $absPath->addRelativePath(new RelPath("fuck/the/police"));
        $this->assertEquals(str_replace("\\", "/", __DIR__) . "/fuck/the/police", $newPath->getPath());
    }
}
