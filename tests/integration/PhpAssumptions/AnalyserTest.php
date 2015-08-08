<?php

namespace integration\PhpAssumptions;

use PhpAssumptions\Analyser;
use PhpAssumptions\NodeVisitor;
use PhpAssumptions\Output\OutputInterface;
use PhpParser\Lexer;
use PhpParser\NodeTraverser;
use PhpParser\Parser;
use PhpParser\PrettyPrinter\Standard;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTestCase;

class AnalyserTest extends ProphecyTestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $output;

    /**
     * @var Analyser
     */
    private $analyser;

    public function setUp()
    {
        $parser = new Parser(new Lexer());
        $this->output = $this->prophesize(OutputInterface::class);
        $nodeVisitor = new NodeVisitor($this->output->reveal(), new Standard());
        $this->analyser = new Analyser($parser, $nodeVisitor, new NodeTraverser());
    }

    /**
     * @test
     */
    public function itShouldDetectWeakAssumptions()
    {
        $fixture = fixture('MyClass.php');
        $assumptions = [9, 10];

        foreach ($assumptions as $assumption) {
            $this->output->write($fixture, $assumption, Argument::type('string'))->shouldBeCalled();
        }

        $this->analyser->analyse([$fixture]);
    }
}
