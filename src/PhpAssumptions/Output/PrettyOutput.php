<?php

namespace PhpAssumptions\Output;

use League\CLImate\CLImate;

class PrettyOutput implements OutputInterface
{
    /**
     * @var CLImate
     */
    private $cli;

    /**
     * @var array
     */
    private $table = [];

    /**
     * @param CLImate $cli
     */
    public function __construct(CLImate $cli)
    {
        $this->cli = $cli;
    }

    /**
     * @param string $file
     * @param string $line
     * @param string $message
     */
    public function write($file, $line, $message)
    {
        $this->table[] = [
            'file' => $file,
            'line' => $line,
            'message' => $message,
        ];
    }

    public function flush()
    {
        if (count($this->table) > 0) {
            $this->cli->table($this->table)->br();
        }

        $this->cli->out('Total warnings: ' . count($this->table));
    }
}
