<?php


namespace Supermetrolog\Synchronizer\services\sync\interfaces;

use Generator;

interface FileStreamInterface
{
    /**
     * @return FileInterface[]
     */
    public function read(): Generator;
    /**
     * @return FileInterface[]
     */
    public function readRecursive(): Generator;
}
