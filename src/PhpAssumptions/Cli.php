<?php

namespace PhpAssumptions;

use League\CLImate\CLImate;
use PhpAssumptions\Output\PrettyOutput;
use PhpAssumptions\Output\XmlOutput;
use PhpAssumptions\Parser\NodeVisitor;
use PhpParser\NodeTraverser;
use PhpParser\ParserFactory;

class Cli
{
    const VERSION = '0.7.1';

    /**
     * @var CLImate
     */
    private $cli;

    private function createParser()
    {
        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        return $parser;
    }

    public function __construct(CLImate $cli)
    {
        $this->cli = $cli;
        $this->cli->arguments->add(
            [
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
            'exclude' => [
                'prefix' => 'e',
                'longPrefix' => 'exclude',
                'description' => 'List of files/directories (separate by ",") to exclude from the analyse',
                'defaultValue' => ''
            ],
            'output' => [
                'prefix' => 'o',
                'longPrefix' => 'output',
                'description' => 'Output file',
                'defaultValue' => 'phpa.xml',
            ],
            ]
        );
        $this->parser = self::createParser();
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

        $excludes = $this->getPathsFromList($this->cli->arguments->get('exclude'));

        $nodeTraverser = new NodeTraverser();

        $analyser = new Analyser(
            $this->parser,
            $nodeTraverser,
            $excludes
        );

        $nodeTraverser->addVisitor(new NodeVisitor($analyser, new Detector()));

        $target = $this->cli->arguments->get('path');
        $targets = $this->getPaths($target);

        $result = $analyser->analyse($targets);

        $output->output($result);
    }

    /**
     * @param string $list
     * @return array
     */
    private function getPathsFromList($list)
    {
        $paths = [];
        if (strlen($list) > 0) {
            $items = explode(',', $list);
            foreach ($items as $item) {
                $paths = array_merge($paths, $this->getPaths($item));
            }
        }

        return $paths;
    }

    /**
     * @param string $fromPath
     * @return array
     */
    private function getPaths($fromPath)
    {
        $paths = [];
        if (is_file($fromPath)) {
            $paths[] = $fromPath;
        } else {
            $directory = new \RecursiveDirectoryIterator($fromPath);
            $iterator = new \RecursiveIteratorIterator($directory);
            $regex = new \RegexIterator($iterator, '/^.+\.php$/i', \RecursiveRegexIterator::GET_MATCH);

            foreach ($regex as $file) {
                $paths[] = $file[0];
            }
        }

        return $paths;
    }
}
