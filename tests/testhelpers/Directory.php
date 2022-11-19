<?php

namespace tests\testhelpers;

class Directory
{
    public static function rmdir(string $path): void
    {
        $files = glob($path . '/*');
        foreach ($files as $file) {
            is_dir($file) ? self::rmdir($file) : unlink($file);
        }
        rmdir($path);

        return;
    }
}
