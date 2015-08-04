<?php

namespace PhpAssumptions\Output;

interface OutputInterface
{
    /**
     * @param string $file
     * @param string $line
     * @param string $message
     */
    public function write($file, $line, $message);

    public function flush();
}
