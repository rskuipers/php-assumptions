<?php

namespace PhpAssumptions\Output;

interface OutputInterface
{
    /**
     * @param Result $result
     */
    public function output(Result $result);
}
