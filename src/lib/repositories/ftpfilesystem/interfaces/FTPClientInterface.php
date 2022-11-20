<?php

namespace Supermetrolog\Synchronizer\lib\repositories\ftpfilesystem\interfaces;


interface FTPClientInterface
{
    public function connect(): bool;
    public function disconnect(): bool;
    public function command(string $command, array &$rows): bool;
}
