<?php

namespace Supermetrolog\Synchronizer\interfaces;

interface SourceRepositoryInterface
{
    public function getStream(): StreamInterface;
    public function getContent(FileInterface $file): ?string;
}
