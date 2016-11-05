<?php

namespace tests\PhpAssumptions\Parser;

use PhpAssumptions\Analyser;
use PhpAssumptions\Detector;
use PhpAssumptions\Parser\NodeVisitor;
use PhpParser\Node;
use PhpParser\PrettyPrinter\Standard;
use Prophecy\Argument;

class NodeVisitorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var NodeVisitor
     */
    private $nodeVisitor;

    /**
     * @var Analyser
     */
    private $analyser;

    /**
     * @var Detector
     */
    private $detector;

    /**
     * @var Node
     */
    private $node;

    public function setUp()
    {
        $this->analyser = $this->prophesize(Analyser::class);
        $this->detector = $this->prophesize(Detector::class);
        $this->node = $this->prophesize(Node::class);
        $this->nodeVisitor = new NodeVisitor(
            $this->analyser->reveal(),
            $this->detector->reveal()
        );
    }

    /**
     * @test
     */
    public function itShouldCallScanAndWriteOnSuccess()
    {
        $this->node->getLine()->shouldBeCalled()->willReturn(120);

        $this->detector->scan($this->node)->shouldBeCalled()->willReturn(true);
        $this->detector->isBoolExpression($this->node)->shouldBeCalled()->willReturn(true);

        $this->analyser->foundAssumption(120)->shouldBeCalled();
        $this->analyser->foundBoolExpression()->shouldBeCalled();

        $this->nodeVisitor->enterNode($this->node->reveal());
    }

    /**
     * @test
     */
    public function itShouldCallScanAndNotWriteOnFailure()
    {
        $this->detector->scan($this->node)->shouldBeCalled()->willReturn(false);
        $this->detector->isBoolExpression($this->node)->shouldBeCalled()->willReturn(true);
        $this->analyser->foundAssumption(Argument::any(), Argument::any())->shouldNotBeCalled();
        $this->analyser->foundBoolExpression()->shouldBeCalled();
        $this->nodeVisitor->enterNode($this->node->reveal());
    }
}
