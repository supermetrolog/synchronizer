<?php

require __DIR__ . '/vendor/autoload.php';

use League\Flysystem\Ftp\FtpConnectionOptions;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Supermetrolog\Synchronizer\lib\repositories\filesystem\Filesystem;
use Supermetrolog\Synchronizer\lib\repositories\ftpfilesystem\FtpFilesystem;
use Supermetrolog\Synchronizer\lib\repositories\onefile\OneFile;
use Supermetrolog\Synchronizer\services\sync\Synchronizer;

$baseRepository = Filesystem::getInstance(__DIR__ . "/example/basefolder");

$targetRepository = FtpFilesystem::getInstance(".", FtpConnectionOptions::fromArray(
    [
        'host' => '62.113.107.218', // required
        'root' => '/', // required
        'username' => 'user_ftptest', // required
        'password' => 'studentjke2h', // required
        'port' => 21,
        'ssl' => false,
        'timeout' => 90,
        'utf8' => false,
        'passive' => true,
        'transferMode' => FTP_BINARY,
        'systemType' => "unix", // 'windows' or 'unix'
        'ignorePassiveAddress' => null, // true or false
        'timestampsOnUnixListingsEnabled' => false, // true or false
        'recurseManually' => true // true
    ]
));
$alreadySynchronizedRepository = new OneFile($targetRepository, "sync-file.txt");

$logger = new Logger("console");
$stdout = fopen('php://stdout', 'w');
$logger->pushHandler(new StreamHandler($stdout, Logger::INFO));

$sync = new Synchronizer($baseRepository, $targetRepository, $alreadySynchronizedRepository, $logger);
$sync->load();
$sync->sync();

fclose($stdout);
