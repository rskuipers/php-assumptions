<?php

namespace PhpAssumptions;

use PhpAssumptions\Output\PrettyOutput;
use PhpAssumptions\Parser\NodeVisitor;
use PhpParser\Lexer;
use PhpParser\NodeTraverser;
use PhpParser\Parser;
use PhpParser\PrettyPrinter\Standard;

class Cli
{
    const VERSION = '0.1.0';

    /**
     * @param array $args
     */
    public function handle(array $args)
    {
        if (count($args) === 0) {
            $this->showHelp();
            return;
        }

        $output = new PrettyOutput();
        $analyser = new Analyser(
            new Parser(new Lexer()),
            new NodeVisitor($output, new Standard(), new Detector()),
            new NodeTraverser()
        );

        $target = $args[0];
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
        echo 'Usage: phpa <file/directory>' . PHP_EOL;
    }
}
