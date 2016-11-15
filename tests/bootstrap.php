<?php

require __DIR__ . '/../vendor/autoload.php';
define('FIXTURES_DIR', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'fixtures');

function fixture($filename)
{
    return FIXTURES_DIR . DIRECTORY_SEPARATOR . $filename;
}
