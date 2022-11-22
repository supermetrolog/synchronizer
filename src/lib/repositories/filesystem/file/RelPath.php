<?php

namespace Supermetrolog\Synchronizer\lib\repositories\filesystem\file;


class RelPath extends Path
{
    public function __construct(string $path)
    {
        $path = preg_replace('!\\\+!', "/", $path);
        $path = preg_replace('!/+!', "/", $path);
        if (mb_strlen($path) === 0) {
            $this->path = "/";
            return;
        }
        if ($path[0] !== "/") {
            $path = "/$path";
        }
        if (mb_strlen($path) !== 1 && $path[mb_strlen($path) - 1] !== "/") {
            $path = "$path/";
        }
        $this->path = $path;
    }
}
