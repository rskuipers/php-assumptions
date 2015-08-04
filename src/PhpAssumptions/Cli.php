<?php

namespace PhpAssumptions;

use PhpAssumptions\Output\PrettyOutput;

class Cli
{
    const VERSION = '0.1.0';

    /**
     * @param array $args
     */
    public function handle(array $args)
    {
        if (count($args) === 1) {
            $this->showHelp();
            return;
        }

        $output = new PrettyOutput();
        $analyser = new Analyser($output);

        $target = $args[1];
        $targets = [];

        if (is_file($target)) {
            $targets[] = $target;
        } else {
            $directory = new \RecursiveDirectoryIterator($target);
            $iterator = new \RecursiveIteratorIterator($directory);
            $regex = new \RegexIterator($iterator, '/^.+\.php$/i', \RecursiveRegexIterator::GET_MATCH);

            foreach ($regex as $file) {
                $targets[] = $file[0];
            }
        }

        $analyser->analyse($targets);

        $output->flush();
    }

    private function showHelp()
    {
        echo 'Usage: phpassumptions <file/directory>' . PHP_EOL;
    }
}
