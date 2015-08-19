<?php

namespace unit\PhpAssumptions\Parser;

use PhpAssumptions\Analyser;
use PhpAssumptions\Detector;
use PhpAssumptions\Parser\NodeVisitor;
use PhpParser\Node;
use PhpParser\PrettyPrinter\Standard;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTestCase;

class NodeVisitorTest extends ProphecyTestCase
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
     * @var Standard
     */
    private $prettyPrinter;

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
        $this->prettyPrinter = $this->prophesize(Standard::class);
        $this->detector = $this->prophesize(Detector::class);
        $this->node = $this->prophesize(Node::class);
        $this->nodeVisitor = new NodeVisitor(
            $this->analyser->reveal(),
            $this->prettyPrinter->reveal(),
            $this->detector->reveal()
        );
    }

    /**
     * @test
     */
    public function itShouldCallScanAndWriteOnSuccess()
    {
        $this->node->getLine()->shouldBeCalled()->willReturn(120);

        $this->prettyPrinter->prettyPrint([$this->node])->shouldBeCalled()->willReturn('Weak assumption');

        $this->detector->scan($this->node)->shouldBeCalled()->willReturn(true);
        $this->analyser->found(120, 'Weak assumption')->shouldBeCalled();

        $this->nodeVisitor->leaveNode($this->node->reveal());
    }

    /**
     * @test
     */
    public function itShouldCallScanAndNotWriteOnFailure()
    {
        $this->detector->scan($this->node)->shouldBeCalled()->willReturn(false);
        $this->analyser->found(Argument::any(), Argument::any(), Argument::any())->shouldNotBeCalled();
        $this->nodeVisitor->leaveNode($this->node->reveal());
    }
}
