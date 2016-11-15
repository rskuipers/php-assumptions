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
     * @param CLImate $cli
     */
    public function __construct(CLImate $cli)
    {
        $this->cli = $cli;
    }

    /**
     * @param Result $result
     */
    public function output(Result $result)
    {
        if ($result->getAssumptionsCount() > 0) {
            $this->cli->table($result->getAssumptions())->br();
        }

        $this->cli->out(
            sprintf(
                '%d out of %d boolean expressions are assumptions (%d%%)',
                $result->getAssumptionsCount(),
                $result->getBoolExpressionsCount(),
                $result->getPercentage()
            )
        );
    }
}
