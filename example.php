<?php
require __DIR__ . '/vendor/autoload.php';

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Supermetrolog\Synchronizer\lib\repositories\filesystem\file\AbsPath;
use Supermetrolog\Synchronizer\lib\repositories\filesystem\Filesystem;
use Supermetrolog\Synchronizer\lib\repositories\onefile\OneFile;
use Supermetrolog\Synchronizer\services\sync\Synchronizer;

$baseRepository = new Filesystem(new AbsPath(__DIR__ . "/example/basefolder"));
$targetRepository = new Filesystem(new AbsPath(__DIR__ . "/example/targetfolder"));
$alreadySynchronizedRepository = new OneFile($targetRepository, "sync-file.txt");
$logger = new Logger("console");
$stdout = fopen('php://stdout', 'w');
$logger->pushHandler(new StreamHandler($stdout, Logger::INFO));

$sync = new Synchronizer($baseRepository, $targetRepository, $alreadySynchronizedRepository, $logger);
$sync->load();
$sync->sync();
fclose($stdout);
