<?php

namespace integration\PhpAssumptions;

use League\CLImate\Argument\Manager;
use League\CLImate\CLImate;
use PhpAssumptions\Cli;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTestCase;

class CliTest extends ProphecyTestCase
{
    /**
     * @var Manager
     */
    private $argumentManager;

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
        $this->argumentManager = $this->prophesize(Manager::class);
        $this->argumentManager->add(Argument::type('array'))->shouldBeCalled();
        $this->argumentManager->parse(Argument::type('array'))->shouldBeCalled();

        $this->climate = $this->prophesize(CLImate::class);

        $this->climate->arguments = $this->argumentManager->reveal();
        $this->climate->out(Argument::containingString('PHPAssumptions analyser'))
            ->shouldBeCalled()
            ->willReturn($this->climate);

        $this->climate->br()->shouldBeCalled();

        $this->cli = new Cli($this->climate->reveal());
    }

    /**
     * @test
     */
    public function itShouldAnalyseTargetFile()
    {
        $path = fixture('MyClass.php');

        $this->argumentManager->get('format')->shouldBeCalled()->willReturn('pretty');
        $this->argumentManager->get('path')->shouldBeCalled()->willReturn($path);

        $this->climate->table([[
            'file' => $path,
            'line' => 9,
            'message' => '$dog !== null;'
        ]])->shouldBeCalled()->willReturn($this->climate);

        $this->climate->out('Total warnings: 1')->shouldBeCalled();

        $this->cli->handle(['phpa', $path]);
    }

    /**
     * @test
     */
    public function itShouldAnalyseTargetDirectory()
    {
        $pathMyClass = fixture('MyClass.php');
        $pathMyOtherClass = fixture('MyOtherClass.php');

        $this->argumentManager->get('format')->shouldBeCalled()->willReturn('pretty');
        $this->argumentManager->get('path')->shouldBeCalled()->willReturn(FIXTURES_DIR);

        $this->climate->table([
            [
                'file' => $pathMyClass,
                'line' => 9,
                'message' => '$dog !== null;'
            ],
            [
                'file' => $pathMyOtherClass,
                'line' => 9,
                'message' => '$cat !== null;'
            ]
        ])->shouldBeCalled()->willReturn($this->climate);

        $this->climate->out('Total warnings: 2')->shouldBeCalled();

        $this->cli->handle(['phpa', FIXTURES_DIR]);
    }
}
