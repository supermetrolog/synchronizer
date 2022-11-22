<?php

use PHPUnit\Framework\TestCase;
use Supermetrolog\Synchronizer\lib\repositories\filesystem\file\RelPath;

class RelPathTest extends TestCase
{
    public function testValidPath()
    {
        $relPath = new RelPath("");
        $this->assertEquals("/", $relPath->getPath());
    }
    public function testValidPathWithManySlashes()
    {
        $relPath = new RelPath("fuck/suck////");
        $this->assertEquals("/fuck/suck/", $relPath->getPath());
    }
    public function testValidPathWithoutEndSlash()
    {
        $relPath = new RelPath("fuck/suck");
        $this->assertEquals("/fuck/suck/", $relPath->getPath());
    }
}
