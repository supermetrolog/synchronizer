<?php


namespace Supermetrolog\Synchronizer\services\sync\interfaces;

interface SourceRepositoryInterface
{
    public function getStream(): StreamInterface;
    public function getContent(FileInterface $file): ?string;
}
