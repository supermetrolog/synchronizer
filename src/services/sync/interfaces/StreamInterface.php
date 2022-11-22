<?php


namespace Supermetrolog\Synchronizer\services\sync\interfaces;

use Generator;

interface StreamInterface
{
    /**
     * @return FileInterface[]
     */
    public function read(): Generator;
}
