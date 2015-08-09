<?php

namespace PhpAssumptions;

use PhpAssumptions\Parser\NodeVisitor;
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
     * @var NodeVisitor
     */
    private $nodeVisitor;

    /**
     * @param ParserAbstract $parser
     * @param NodeVisitor $nodeVisitor
     * @param NodeTraverserInterface $nodeTraverser
     */
    public function __construct(ParserAbstract $parser, NodeVisitor $nodeVisitor, NodeTraverserInterface $nodeTraverser)
    {
        $this->parser = $parser;
        $this->nodeVisitor = $nodeVisitor;
        $this->traverser = $nodeTraverser;
        $this->traverser->addVisitor($this->nodeVisitor);
    }

    /**
     * @param array $files
     */
    public function analyse(array $files)
    {
        foreach ($files as $file) {
            $this->nodeVisitor->setCurrentFile($file);
            $statements = $this->parser->parse(file_get_contents($file));
            if (is_array($statements) || $statements instanceof Node) {
                $this->traverser->traverse($statements);
            }
        }
    }
}
