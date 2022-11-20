<?php

namespace Supermetrolog\Synchronizer\lib\repositories\ftpfilesystem;

use Supermetrolog\Synchronizer\lib\repositories\ftpfilesystem\interfaces\FTPClientInterface;
use Supermetrolog\Synchronizer\services\sync\interfaces\FileRepositoryInterface;

class FTPFileSystem implements FileRepositoryInterface
{
    private FTPClientInterface $client;
    public function __construct(FTPClientInterface $client)
    {
        $this->client = $client;
    }
}
