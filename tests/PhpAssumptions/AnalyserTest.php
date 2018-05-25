<?php

namespace tests\PhpAssumptions;

use PhpAssumptions\Analyser;
use PhpAssumptions\Output\OutputInterface;
use PhpParser\Parser;
use PhpParser\Parser\Multiple;
use PhpParser\Node;
use PhpParser\NodeTraverser;
use Prophecy\Argument;

class AnalyserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Parser
     */
    private $parser;

    /**
     * @var OutputInterface
     */
    private $output;

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
        $this->parser = $this->prophesize(Multiple::class);
        $this->output = $this->prophesize(OutputInterface::class);
        $this->nodeTraverser = $this->prophesize(NodeTraverser::class);
        $this->analyser = new Analyser(
            $this->parser->reveal(),
            $this->nodeTraverser->reveal(),
            [fixture('MyOtherClass.php')]
        );
    }

    /**
     * @test
     */
    public function itShouldAnalyseAllFiles()
    {
        $files = [fixture('MyClass.php')];
        $nodes = [$this->node];

        $this->parser->parse(Argument::type('string'))->shouldBeCalled()->willReturn($nodes);

        $this->nodeTraverser->traverse($nodes)->shouldBeCalled();

        $this->analyser->analyse($files);
    }

    /**
     * @test
     */
    public function itShouldIgnoreExcludeFiles()
    {
        $files = [fixture('MyClass.php'), fixture('MyOtherClass.php')];
        $nodes = [$this->node];

        $this->parser->parse(Argument::type('string'))->shouldBeCalled()->willReturn($nodes);

        $this->nodeTraverser->traverse($nodes)->shouldBeCalled();

        $this->analyser->analyse($files);
    }
}
