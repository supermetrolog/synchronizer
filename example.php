<?php
require __DIR__ . '/vendor/autoload.php';

use Supermetrolog\Synchronizer\lib\repositories\filesystem\file\AbsPath;
use Supermetrolog\Synchronizer\lib\repositories\filesystem\Filesystem;
use Supermetrolog\Synchronizer\lib\repositories\onefile\OneFile;
use Supermetrolog\Synchronizer\services\sync\Synchronizer;

$baseRepository = new Filesystem(new AbsPath(__DIR__ . "/example/basefolder"));
$targetRepository = new Filesystem(new AbsPath(__DIR__ . "/example/targetfolder"));
$alreadySynchronizedRepository = new OneFile($targetRepository, "sync-file.txt");

$sync = new Synchronizer($baseRepository, $targetRepository, $alreadySynchronizedRepository);
$sync->load();
$sync->sync();
