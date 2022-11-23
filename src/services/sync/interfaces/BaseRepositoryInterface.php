<?php


namespace Supermetrolog\Synchronizer\services\sync\interfaces;

interface BaseRepositoryInterface
{
    public function getStream(): StreamInterface;
    public function getContent(FileInterface $file): ?string;
}
