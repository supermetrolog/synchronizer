<?php


namespace Supermetrolog\Synchronizer\services\sync\interfaces;

interface FileRepositoryInterface
{
    public function createStream(): FileStreamInterface;
    public function findByFullname(string $fullname): ?FileInterface;
}
