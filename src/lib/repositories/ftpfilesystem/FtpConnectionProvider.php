<?php

namespace Supermetrolog\Synchronizer\lib\repositories\ftpfilesystem;

use League\Flysystem\Ftp\ConnectionProvider;
use League\Flysystem\Ftp\FtpConnectionException;
use League\Flysystem\Ftp\FtpConnectionOptions;
use League\Flysystem\Ftp\FtpConnectionProvider as FtpFtpConnectionProvider;
use League\Flysystem\Ftp\UnableToAuthenticate;
use League\Flysystem\Ftp\UnableToConnectToFtpHost;
use League\Flysystem\Ftp\UnableToEnableUtf8Mode;
use League\Flysystem\Ftp\UnableToMakeConnectionPassive;
use League\Flysystem\Ftp\UnableToSetFtpOption;

class FtpConnectionProvider extends FtpFtpConnectionProvider implements ConnectionProvider
{
    public $connection;


    public function __construct(FtpConnectionOptions $options)
    {
        $this->connection = $this->createConnection($options);
    }

    /**
     * @return resource
     *
     * @throws FtpConnectionException
     */
    public function createConnection(FtpConnectionOptions $options)
    {
        if (is_resource($this->connection)) {
            return $this->connection;
        }
        $connection = $this->createConnectionResource(
            $options->host(),
            $options->port(),
            $options->timeout(),
            $options->ssl()
        );

        try {
            $this->authenticate($options, $connection);
            $this->enableUtf8Mode($options, $connection);
            $this->ignorePassiveAddress($options, $connection);
            $this->makeConnectionPassive($options, $connection);
        } catch (FtpConnectionException $exception) {
            /**@var \FTP\Connection $connection */
            ftp_close($connection);
            throw $exception;
        }
        return $connection;
    }
    /**
     * @return resource
     */
    public function getConnection()
    {
        return $this->connection;
    }
    /**
     * @return resource
     */
    private function createConnectionResource(string $host, int $port, int $timeout, bool $ssl)
    {
        $connection = $ssl ? @ftp_ssl_connect($host, $port, $timeout) : @ftp_connect($host, $port, $timeout);

        if ($connection === false) {
            throw UnableToConnectToFtpHost::forHost($host, $port, $ssl);
        }

        return $connection;
    }

    /**
     * @param resource $connection
     */
    private function authenticate(FtpConnectionOptions $options, $connection): void
    {
        /**@var \FTP\Connection $connection */
        if (!@ftp_login($connection, $options->username(), $options->password())) {
            throw new UnableToAuthenticate();
        }
    }

    /**
     * @param resource $connection
     */
    private function enableUtf8Mode(FtpConnectionOptions $options, $connection): void
    {
        if (!$options->utf8()) {
            return;
        }

        /**@var \FTP\Connection $connection */
        $response = ftp_raw($connection, "OPTS UTF8 ON");

        if (!in_array(substr($response[0], 0, 3), ['200', '202'])) {
            throw new UnableToEnableUtf8Mode(
                'Could not set UTF-8 mode for connection: ' . $options->host() . '::' . $options->port()
            );
        }
    }

    /**
     * @param resource $connection
     */
    private function ignorePassiveAddress(FtpConnectionOptions $options, $connection): void
    {
        $ignorePassiveAddress = $options->ignorePassiveAddress();

        if (!is_bool($ignorePassiveAddress) || !defined('FTP_USEPASVADDRESS')) {
            return;
        }

        /**@var \FTP\Connection $connection */
        if (!ftp_set_option($connection, FTP_USEPASVADDRESS, !$ignorePassiveAddress)) {
            throw UnableToSetFtpOption::whileSettingOption('FTP_USEPASVADDRESS');
        }
    }

    /**
     * @param resource $connection
     */
    private function makeConnectionPassive(FtpConnectionOptions $options, $connection): void
    {
        /**@var \FTP\Connection $connection */
        if (!ftp_pasv($connection, $options->passive())) {
            throw new UnableToMakeConnectionPassive(
                'Could not set passive mode for connection: ' . $options->host() . '::' . $options->port()
            );
        }
    }
}
