<?php

namespace PhpAssumptions;

use PhpAssumptions\Output\Result;
use PhpParser\Node;
use PhpParser\NodeTraverserInterface;
use PhpParser\Parser\Multiple;
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
     * @var string
     */
    private $currentFilePath;

    /**
     * @var array
     */
    private $currentFile = [];

    /**
     * @var Result
     */
    private $result;

    /**
     * @var array|\string[]
     */
    private $excludes = [];

    /**
     * @param ParserAbstract|Multiple $parser
     * @param NodeTraverserInterface  $nodeTraverser
     * @param string[]                $excludes
     */
    public function __construct(
        Multiple $parser,
        NodeTraverserInterface $nodeTraverser,
        $excludes = []
    ) {
        $this->parser = $parser;
        $this->traverser = $nodeTraverser;
        $this->result = new Result();
        $this->excludes = $excludes;
    }

    /**
     * @param array $files
     * @return Result
     */
    public function analyse(array $files)
    {
        foreach ($files as $file) {
            if (in_array($file, $this->excludes, true)) {
                continue;
            }
            $this->currentFilePath = $file;
            $this->currentFile = [];
            $statements = $this->parser->parse(file_get_contents($file));
            if (is_array($statements) || $statements instanceof Node) {
                $this->traverser->traverse($statements);
            }
        }

        return $this->result;
    }

    /**
     * @param int $line
     */
    public function foundAssumption($line)
    {
        $this->result->addAssumption($this->currentFilePath, $line, $this->readLine($line));
    }

    public function foundBoolExpression()
    {
        $this->result->increaseBoolExpressionsCount();
    }

    private function readLine($line)
    {
        if (count($this->currentFile) === 0) {
            $this->currentFile = file($this->currentFilePath);
        }

        return trim($this->currentFile[$line - 1]);
    }
}
