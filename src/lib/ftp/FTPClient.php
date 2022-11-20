<?php

namespace Supermetrolog\Synchronizer\lib\ftp;

use InvalidArgumentException;
use Supermetrolog\Synchronizer\lib\repositories\ftpfilesystem\interfaces\FTPClientInterface;

class FTPClient implements FTPClientInterface
{
    protected int $port;
    protected string $host;
    protected string $username;
    protected string $password;

    public function __construct(string $host, int $port = 22, string $username = "", string $password = "")
    {
        $this->port = $port;
        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
        $this->validate();
    }

    private function validate(): void
    {
        if (!filter_var($this->host, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            throw new InvalidArgumentException("invalid host");
        }
    }
    public function connect(): bool
    {
        return true;
    }

    public function disconnect(): bool
    {
        return true;
    }

    public function command(string $command, array &$rows): bool
    {
        return true;
    }
}
