<?php

namespace Supermetrolog\Synchronizer\interfaces;

use Generator;

interface StreamInterface
{
    /**
     * @return Generator<FileInterface>
     */
    public function read(): Generator;
}
