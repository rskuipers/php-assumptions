<?php

namespace PhpAssumptions;

use PhpAssumptions\Output\Result;
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
     * @var string
     */
    private $currentFile;

    /**
     * @var Result
     */
    private $result;

    /**
     * @param ParserAbstract $parser
     * @param NodeTraverserInterface $nodeTraverser
     */
    public function __construct(
        ParserAbstract $parser,
        NodeTraverserInterface $nodeTraverser
    ) {
        $this->parser = $parser;
        $this->traverser = $nodeTraverser;
        $this->result = new Result();
    }

    /**
     * @param array $files
     * @return Result
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

        return $this->result;
    }

    /**
     * @param int $line
     * @param string $message
     */
    public function foundAssumption($line, $message)
    {
        $this->result->addAssumption($this->currentFile, $line, $message);
    }

    public function foundBoolExpression()
    {
        $this->result->increaseBoolExpressionsCount();
    }
}
