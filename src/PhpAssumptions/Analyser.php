<?php

namespace PhpAssumptions;

use PhpAssumptions\Output\OutputInterface;
use PhpParser\Lexer;
use PhpParser\NodeTraverser;
use PhpParser\Parser;

class Analyser
{
    /**
     * @var NodeTraverser
     */
    private $traverser;

    /**
     * @var NodeVisitor
     */
    private $nodeVisitor;

    /**
     * @param OutputInterface $output
     */
    public function __construct(OutputInterface $output)
    {
        $this->nodeVisitor = new NodeVisitor($output);

        $this->traverser = new NodeTraverser();
        $this->traverser->addVisitor($this->nodeVisitor);
    }

    /**
     * @param array $files
     */
    public function analyse(array $files)
    {
        $parser = new Parser(new Lexer());

        foreach ($files as $file) {
            $this->nodeVisitor->setCurrentFile($file);
            $statements = $parser->parse(file_get_contents($file));
            $this->traverser->traverse($statements);
        }
    }
}
