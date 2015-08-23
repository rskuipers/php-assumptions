<?php

namespace PhpAssumptions;

use League\CLImate\CLImate;
use PhpAssumptions\Output\PrettyOutput;
use PhpAssumptions\Output\XmlOutput;
use PhpAssumptions\Parser\NodeVisitor;
use PhpParser\Lexer;
use PhpParser\NodeTraverser;
use PhpParser\Parser;

class Cli
{
    const VERSION = '0.3.0';

    /**
     * @var CLImate
     */
    private $cli;

    public function __construct(CLImate $cli)
    {
        $this->cli = $cli;
        $this->cli->arguments->add([
            'path' => [
                'description' => 'The path to analyse',
                'required' => true,
            ],
            'format' => [
                'prefix' => 'f',
                'longPrefix' => 'format',
                'description' => 'Format (pretty, xml)',
                'defaultValue' => 'pretty',
            ],
            'output' => [
                'prefix' => 'o',
                'longPrefix' => 'output',
                'description' => 'Output file',
                'defaultValue' => 'phpa.xml',
            ],
        ]);
    }

    /**
     * @param array $args
     */
    public function handle(array $args)
    {
        $this->cli->out(sprintf('PHPAssumptions analyser v%s by @rskuipers', Cli::VERSION))->br();

        try {
            $this->cli->arguments->parse($args);
        } catch (\Exception $e) {
            $this->cli->usage($args);
            return;
        }

        switch ($this->cli->arguments->get('format')) {
            case 'xml':
                $output = new XmlOutput($this->cli, $this->cli->arguments->get('output'));
                break;
            default:
                $output = new PrettyOutput($this->cli);
                break;
        }

        $nodeTraverser = new NodeTraverser();

        $analyser = new Analyser(
            new Parser(new Lexer()),
            $nodeTraverser
        );

        $nodeTraverser->addVisitor(new NodeVisitor($analyser, new Detector()));

        $target = $this->cli->arguments->get('path');
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

        $result = $analyser->analyse($targets);

        $output->output($result);
    }
}
