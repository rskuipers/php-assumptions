<?php

namespace tests\PhpAssumptions;

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

    /**
     * @var Manager
     */
    private $argumentManager;

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

        $this->climate->table([
            [
                'file' => $path,
                'line' => 9,
                'message' => '$dog !== null;',
            ]
        ])->shouldBeCalled()->willReturn($this->climate);
        $this->climate->out('1 out of 2 boolean expressions are assumptions (50%)')->shouldBeCalled();

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

        $this->climate->out('2 out of 3 boolean expressions are assumptions (67%)')->shouldBeCalled();

        $this->cli->handle(['phpa', FIXTURES_DIR]);
    }

    /**
     * @test
     */
    public function itShouldAnalyseTargetFileAndOutputXml()
    {
        $path = fixture('MyClass.php');
        $output = tempnam(sys_get_temp_dir(), 'xml');

        $this->argumentManager->get('format')->shouldBeCalled()->willReturn('xml');
        $this->argumentManager->get('path')->shouldBeCalled()->willReturn($path);
        $this->argumentManager->get('output')->shouldBeCalled()->willReturn($output);

        $this->climate->out('Written 1 assumption(s) to file ' . $output)->shouldBeCalled();

        $this->cli->handle(['phpa', $path]);
        $this->assertTrue(is_file($output));
    }

    /**
     * @test
     */
    public function itShouldShowUsageWithNoArgs()
    {
        $this->argumentManager->parse(Argument::type('array'))->willThrow(\Exception::class);

        $args = ['phpa'];
        $this->climate->usage($args)->shouldBeCalled();
        $this->cli->handle($args);
    }
}
