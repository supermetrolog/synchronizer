<?php
require __DIR__ . '/vendor/autoload.php';

use Supermetrolog\Synchronizer\services\sync\Synchronizer;

$sync = new Synchronizer();
$sync->run();
