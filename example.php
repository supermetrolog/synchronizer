<?php

use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;
use League\Flysystem\Ftp\FtpAdapter;
use League\Flysystem\Ftp\FtpConnectionOptions;
use League\Flysystem\UnableToReadFile;

require __DIR__ . '/vendor/autoload.php';

// use Monolog\Handler\StreamHandler;
// use Monolog\Logger;
// use Supermetrolog\Synchronizer\lib\repositories\filesystem\file\AbsPath;
// use Supermetrolog\Synchronizer\lib\repositories\filesystem\Filesystem;
// use Supermetrolog\Synchronizer\lib\repositories\onefile\OneFile;
// use Supermetrolog\Synchronizer\services\sync\Synchronizer;

// $baseRepository = new Filesystem(new AbsPath(__DIR__ . "/example/basefolder"));
// $targetRepository = new Filesystem(new AbsPath(__DIR__ . "/example/targetfolder"));
// $alreadySynchronizedRepository = new OneFile($targetRepository, "sync-file.txt");
// $logger = new Logger("console");
// $stdout = fopen('php://stdout', 'w');
// $logger->pushHandler(new StreamHandler($stdout, Logger::INFO));

// $sync = new Synchronizer($baseRepository, $targetRepository, $alreadySynchronizedRepository, $logger);
// $sync->load();
// $sync->sync();
// fclose($stdout);

// $ftp = new FtpClient();
// $ftp->connect("62.113.107.218");
// $ftp->pasv(true);
// $ftp->login("user_ftptest", "studentjke2h");



// try {
//     $ftp->mkdir("./suck");
//     var_dump($ftp->scanDir(".", true));
//     // $ftp->remove("./fuck");
// } catch (\Throwable $th) {
//     var_dump("ERROR");
//     var_dump(error_get_last());
//     var_dump($th->getMessage());
// }

// $ftp->close();
$adapter = new FtpAdapter(FtpConnectionOptions::fromArray([
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
]));

$filesystem = new Filesystem($adapter);

try {
    $response = $filesystem->listContents(".", Filesystem::LIST_DEEP);
    foreach ($response as $item) {
        var_dump($item);
    }
} catch (FilesystemException | UnableToReadFile $exception) {
    // handle the error
    var_dump($exception->getMessage());
}

// try {
//     $response = $filesystem->fileExists("./test.txt");
//     var_dump($response);
//     $filesystem->adapter->conn;
// } catch (\Throwable $exception) {
//     // handle the error
//     var_dump($exception->getMessage());
// }
