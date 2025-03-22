<?php

namespace tests\PhpAssumptions;

use PhpAssumptions\Analyser;
use PhpAssumptions\Output\OutputInterface;
use PhpParser\Parser;
use PhpParser\ParserAbstract;
use PhpParser\Node;
use PhpParser\NodeTraverser;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

class AnalyserTest extends TestCase
{
    use ProphecyTrait;

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

    public function setUp(): void
    {
        $this->node = $this->prophesize(Node::class);
        $this->parser = $this->prophesize(ParserAbstract::class);
        $this->output = $this->prophesize(OutputInterface::class);
        $this->nodeTraverser = $this->prophesize(NodeTraverser::class);
        $this->analyser = new Analyser(
            $this->parser->reveal(),
            $this->nodeTraverser->reveal(),
            [fixture('MyOtherClass.php')]
        );
    }

    #[Test]
    public function itShouldAnalyseAllFiles()
    {
        $files = [fixture('MyClass.php')];
        $nodes = [$this->node];

        $this->parser->parse(Argument::type('string'))->shouldBeCalled()->willReturn($nodes);

        $this->nodeTraverser->traverse($nodes)->shouldBeCalled();

        $this->analyser->analyse($files);
    }

    #[Test]
    public function itShouldIgnoreExcludeFiles()
    {
        $files = [fixture('MyClass.php'), fixture('MyOtherClass.php')];
        $nodes = [$this->node];

        $this->parser->parse(Argument::type('string'))->shouldBeCalled()->willReturn($nodes);

        $this->nodeTraverser->traverse($nodes)->shouldBeCalled();

        $this->analyser->analyse($files);
    }
}
