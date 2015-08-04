<?php

namespace PhpAssumptions\Output;

use PhpAssumptions\Cli;

class PrettyOutput implements OutputInterface
{
    private $buffer = [];

    /**
     * @param string $file
     * @param string $line
     * @param string $message
     */
    public function write($file, $line, $message)
    {
        $this->buffer[] = [
            'file' => $file,
            'line' => $line,
            'message' => $message
        ];
    }

    public function flush()
    {
        printf('PHPAssumptions analyser v%s by @rskuipers' . PHP_EOL . PHP_EOL, Cli::VERSION);

        foreach ($this->buffer as $warning) {
            echo $warning['file'] . ':' . $warning['line'] . ': ' . $warning['message'] . PHP_EOL;
        }

        echo PHP_EOL;

        echo 'Total warnings: ' . count($this->buffer) . PHP_EOL;
    }
}
