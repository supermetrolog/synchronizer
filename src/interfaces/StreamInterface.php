<?php

namespace Supermetrolog\Synchronizer\interfaces;

use Generator;

interface StreamInterface
{
    /**
     * @return FileInterface[]
     */
    public function read(): Generator;
}
