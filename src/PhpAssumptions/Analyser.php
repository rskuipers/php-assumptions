<?php

namespace PhpAssumptions;

use PhpAssumptions\Output\OutputInterface;
use PhpParser\Node;
use PhpParser\NodeTraverserInterface;
use PhpParser\ParserAbstract;

class Analyser
{
    /**
     * @var ParserAbstract
     */
    private $parser;

    /**
     * @var NodeTraverserInterface
     */
    private $traverser;

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @var string
     */
    private $currentFile;

    /**
     * @param ParserAbstract $parser
     * @param NodeTraverserInterface $nodeTraverser
     * @param OutputInterface $output
     */
    public function __construct(
        ParserAbstract $parser,
        NodeTraverserInterface $nodeTraverser,
        OutputInterface $output
    ) {
        $this->parser = $parser;
        $this->traverser = $nodeTraverser;
        $this->output = $output;
    }

    /**
     * @param array $files
     */
    public function analyse(array $files)
    {
        foreach ($files as $file) {
            $this->currentFile = $file;
            $statements = $this->parser->parse(file_get_contents($file));
            if (is_array($statements) || $statements instanceof Node) {
                $this->traverser->traverse($statements);
            }
        }
    }

    /**
     * @param int $line
     * @param string $message
     */
    public function found($line, $message)
    {
        $this->output->write($this->currentFile, $line, $message);
    }
}
