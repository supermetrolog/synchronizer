<?php

use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;
use League\Flysystem\Ftp\FtpAdapter;
use League\Flysystem\Ftp\FtpConnectionOptions;
use League\Flysystem\UnableToReadFile;

require __DIR__ . '/vendor/autoload.php';

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Supermetrolog\Synchronizer\lib\repositories\filesystem\file\AbsPath;
use Supermetrolog\Synchronizer\lib\repositories\filesystem\Filesystem as BaseFilesystem;
use Supermetrolog\Synchronizer\lib\repositories\ftpfilesystem\FtpConnectionProvider;
use Supermetrolog\Synchronizer\lib\repositories\ftpfilesystem\FtpFileSystemAdapter;
use Supermetrolog\Synchronizer\lib\repositories\onefile\OneFile;
use Supermetrolog\Synchronizer\services\sync\Synchronizer;

$baseRepository = new BaseFilesystem(new AbsPath(__DIR__ . "/example/basefolder"));
// $targetRepository = new BaseFilesystem(new AbsPath(__DIR__ . "/example/targetfolder"));
$connOptions = FtpConnectionOptions::fromArray(
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
);
$connProvider = new FtpConnectionProvider();
$connProvider->createConnection($connOptions);
$adapter = new FtpAdapter($connOptions, $connProvider);
$targetRepository = new FtpFileSystemAdapter(new AbsPath("."), $connProvider->getConnection(), $adapter);
$alreadySynchronizedRepository = new OneFile($targetRepository, "sync-file.txt");
$logger = new Logger("console");
$stdout = fopen('php://stdout', 'w');
$logger->pushHandler(new StreamHandler($stdout, Logger::INFO));

$sync = new Synchronizer($baseRepository, $targetRepository, $alreadySynchronizedRepository, $logger);
$sync->load();
// $sync->sync();
try {
    $sync->sync();
} catch (\Throwable $th) {
    throw $th;
    // print_r($th->getMessage());
    // print_r($sync->getCreatingFiles());
    // print_r($sync->getChangingFiles());
}
fclose($stdout);

/**@var \FTP\Connection */
// $conn = $connProvider->createConnection(FtpConnectionOptions::fromArray(
//     [
//         'host' => '62.113.107.218', // required
//         'root' => '/', // required
//         'username' => 'user_ftptest', // required
//         'password' => 'studentjke2h', // required
//         'port' => 21,
//         'ssl' => false,
//         'timeout' => 90,
//         'utf8' => false,
//         'passive' => true,
//         'transferMode' => FTP_BINARY,
//         'systemType' => "unix", // 'windows' or 'unix'
//         'ignorePassiveAddress' => null, // true or false
//         'timestampsOnUnixListingsEnabled' => false, // true or false
//         'recurseManually' => true // true
//     ]
// ));
// var_dump(ftp_pwd($conn));
// var_dump(@ftp_chdir($conn, "/test1/parser"));
// var_dump(ftp_pwd($conn));
// var_dump(@ftp_chdir($conn, "/."));
// var_dump(ftp_pwd($conn));
// var_dump(ftp_pwd($connProvider->getConnection()));

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
// $adapter = new FtpAdapter(FtpConnectionOptions::fromArray([
//     'host' => '62.113.107.218', // required
//     'root' => '/', // required
//     'username' => 'user_ftptest', // required
//     'password' => 'studentjke2h', // required
//     'port' => 21,
//     'ssl' => false,
//     'timeout' => 90,
//     'utf8' => false,
//     'passive' => true,
//     'transferMode' => FTP_BINARY,
//     'systemType' => "unix", // 'windows' or 'unix'
//     'ignorePassiveAddress' => null, // true or false
//     'timestampsOnUnixListingsEnabled' => false, // true or false
//     'recurseManually' => true // true
// ]));

// $filesystem = new Filesystem($adapter);

// var_dump(ftp_pwd($filesystem->read(".")));
// try {
//     $response = $filesystem->listContents(".", Filesystem::LIST_DEEP);
//     foreach ($response as $item) {
//         var_dump($item);
//     }
// } catch (FilesystemException | UnableToReadFile $exception) {
//     // handle the error
//     var_dump($exception->getMessage());
// }

// try {
//     $response = $filesystem->fileExists("./test1");
//     var_dump($response);
// } catch (\Throwable $exception) {
//     // handle the error
//     var_dump($exception->getMessage());
// }

// $path = "suck/govno/asda.txt";
// $name = mb_strrchr($path, "/");
// $name = substr($name, 1);

// $lastSlashPos = strrpos($path, "/");
// $rel = substr($path, 0, $lastSlashPos);
// var_dump($name, $rel);
