<?php


namespace Supermetrolog\Synchronizer\services\sync\interfaces;

interface BaseRepositoryInterface
{
    public function getStream(): StreamInterface;
}
