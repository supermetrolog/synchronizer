<?php

use PHPUnit\Framework\TestCase;
use Supermetrolog\Synchronizer\lib\repositories\filesystem\Filesystem;
use Supermetrolog\Synchronizer\services\sync\Synchronizer;

class SynchronizerTest  extends TestCase
{
    public function testLoadUpdatedData()
    {
        $targetDirNameForSynchronize = __DIR__ . "/testfolderwithexistchanges/testfolder";
        $baseDirNameForSynchronize = __DIR__ . "/testfolderwithexistchanges/testbasefolder";
        $baseRepo = new Filesystem($baseDirNameForSynchronize);
        $targetRepo = new Filesystem($targetDirNameForSynchronize);
        $sync = new Synchronizer($baseRepo, $targetRepo);
        $sync->loadUpdatedData();
        $this->assertSame(true, $sync->changedFilesExists());
    }
}
