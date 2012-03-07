<?php

if (file_exists($file = __DIR__.'/autoload.php')) {
    require_once $file;
} elseif (file_exists($file = __DIR__.'/../vendor/.composer/autoload.php')) {
    require_once $file;
}
