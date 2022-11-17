<?php


namespace Supermetrolog\Synchronizer\services\sync\interfaces;

use Generator;

interface FileStreamInterface
{
    public function read(): Generator;
    public function readRecursive(): Generator;
}
