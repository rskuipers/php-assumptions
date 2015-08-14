<?php

namespace unit\PhpAssumptions;

use PhpAssumptions\Analyser;
use PhpAssumptions\Parser\NodeVisitor;
use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\Parser;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTestCase;

class AnalyserTest extends ProphecyTestCase
{
    /**
     * @var Parser
     */
    private $parser;

    /**
     * @var NodeVisitor
     */
    private $nodeVisitor;

    /**
     * @var NodeTraverser
     */
    private $nodeTraverser;

    /**
     * @var Analyser
     */
    private $analyser;

    /**
     * @var Node
     */
    private $node;

    public function setUp()
    {
        $this->node = $this->prophesize(Node::class);
        $this->parser = $this->prophesize(Parser::class);
        $this->nodeVisitor = $this->prophesize(NodeVisitor::class);
        $this->nodeTraverser = $this->prophesize(NodeTraverser::class);
        $this->nodeTraverser->addVisitor($this->nodeVisitor)->shouldBeCalled();
        $this->analyser = new Analyser(
            $this->parser->reveal(),
            $this->nodeVisitor->reveal(),
            $this->nodeTraverser->reveal()
        );
    }

    /**
     * @test
     */
    public function itShouldAnalyseAllFiles()
    {
        $files = [fixture('MyClass.php')];
        $nodes = [$this->node];

        $this->nodeVisitor->setCurrentFile($files[0])->shouldBeCalled();

        $this->parser->parse(Argument::type('string'))->shouldBeCalled()->willReturn($nodes);

        $this->nodeTraverser->traverse($nodes)->shouldBeCalled();

        $this->analyser->analyse($files);
    }
}
