<?php

use PHPUnit\Framework\TestCase;
use Supermetrolog\Synchronizer\lib\ftp\FTPClient;

class FTPClientTest  extends TestCase
{
    public function testValidate()
    {
        $this->expectException(InvalidArgumentException::class);
        $client = new FTPClient("sdawdaw");
    }
}
