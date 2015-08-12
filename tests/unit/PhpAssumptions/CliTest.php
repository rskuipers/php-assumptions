<?php

namespace unit\PhpAssumptions;

use League\CLImate\Argument\Manager;
use League\CLImate\CLImate;
use PhpAssumptions\Cli;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTestCase;

class CliTest extends ProphecyTestCase
{
    /**
     * @var Cli
     */
    private $cli;

    /**
     * @var CLImate
     */
    private $climate;

    public function setUp()
    {
        $argumentManager = $this->prophesize(Manager::class);
        $argumentManager->add(Argument::type('array'))->shouldBeCalled();

        $this->climate = $this->prophesize(CLImate::class);

        $this->climate->arguments = $argumentManager->reveal();
        $this->climate->out(Argument::containingString('PHPAssumptions analyser'))
            ->shouldBeCalled()
            ->willReturn($this->climate);

        $this->climate->br()->shouldBeCalled();

        $this->cli = new Cli($this->climate->reveal());
    }

    /**
     * @test
     */
    public function itShouldShowUsageWithNoArgs()
    {
        $args = ['phpa'];
        $this->climate->usage($args)->shouldBeCalled();
        $this->cli->handle($args);
    }
}
