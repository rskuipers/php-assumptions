<?php

namespace tests\PhpAssumptions;

use League\CLImate\Argument\Manager;
use League\CLImate\CLImate;
use PhpAssumptions\Cli;
use Prophecy\Argument;

class CliTest extends \PHPUnit_Framework_TestCase
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

        $this->cli = new Cli($this->climate->reveal());
    }

    /**
     * @test
     */
    public function itShouldAnalyseTargetFile()
    {
        $this->itShouldShowAuthor();

        $path = fixture('MyClass.php');

        $this->argumentManager->defined('version')->shouldBeCalled()->willReturn(false);
        $this->argumentManager->get('format')->shouldBeCalled()->willReturn('pretty');
        $this->argumentManager->get('path')->shouldBeCalled()->willReturn($path);
        $this->argumentManager->get('exclude')->shouldBeCalled()->willReturn('');

        $this->climate->table([
            [
                'file' => $path,
                'line' => 9,
                'message' => 'if ($dog !== null) {',
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
        $this->itShouldShowAuthor();

        $files = [fixture('MyClass.php'), fixture('MyOtherClass.php'), fixture('Example.php')];

        $this->argumentManager->defined('version')->shouldBeCalled()->willReturn(false);
        $this->argumentManager->get('format')->shouldBeCalled()->willReturn('pretty');
        $this->argumentManager->get('path')->shouldBeCalled()->willReturn(FIXTURES_DIR);
        $this->argumentManager->get('exclude')->shouldBeCalled()->willReturn('');

        // Assert that all files show up in the table
        $this->climate->table(Argument::that(function ($table) use ($files) {

            foreach ($table as $row) {
                unset($files[array_search($row['file'], $files)]);
            }

            return count($files) === 0;
        }))->shouldBeCalled()->willReturn($this->climate);

        $this->climate->out(Argument::containingString('boolean expressions are assumptions'))->shouldBeCalled();

        $this->cli->handle(['phpa', FIXTURES_DIR]);
    }

    /**
     * @test
     */
    public function itShouldIgnoreExcludeFile()
    {
        $this->itShouldShowAuthor();

        $path = fixture('MyClass.php');

        $this->argumentManager->defined('version')->shouldBeCalled()->willReturn(false);
        $this->argumentManager->get('format')->shouldBeCalled()->willReturn('pretty');
        $this->argumentManager->get('path')->shouldBeCalled()->willReturn($path);
        $this->argumentManager->get('exclude')->shouldBeCalled()->willReturn(fixture('MyClass.php'));

        $this->climate->table()->shouldNotBeCalled();
        $this->climate->out('0 out of 0 boolean expressions are assumptions (0%)')->shouldBeCalled();

        $this->cli->handle(['phpa', $path]);
    }

    /**
     * @test
     */
    public function itShouldIgnoreExcludeFileFromDirectory()
    {
        $this->itShouldShowAuthor();

        $path = fixture('MyClass.php');

        $this->argumentManager->defined('version')->shouldBeCalled()->willReturn(false);
        $this->argumentManager->get('format')->shouldBeCalled()->willReturn('pretty');
        $this->argumentManager->get('path')->shouldBeCalled()->willReturn(FIXTURES_DIR);
        $this->argumentManager->get('exclude')->shouldBeCalled()->willReturn(
            fixture('MyOtherClass.php') . ',' . fixture('Example.php')
        );

        $this->climate->table([
            [
                'file' => $path,
                'line' => 9,
                'message' => 'if ($dog !== null) {',
            ]
        ])->shouldBeCalled()->willReturn($this->climate);
        $this->climate->out('1 out of 2 boolean expressions are assumptions (50%)')->shouldBeCalled();

        $this->cli->handle(['phpa', FIXTURES_DIR]);
    }

    /**
     * @test
     */
    public function itShouldIgnoreExcludeDirectory()
    {
        $this->itShouldShowAuthor();

        $this->argumentManager->defined('version')->shouldBeCalled()->willReturn(false);
        $this->argumentManager->get('format')->shouldBeCalled()->willReturn('pretty');
        $this->argumentManager->get('path')->shouldBeCalled()->willReturn(FIXTURES_DIR);
        $this->argumentManager->get('exclude')->shouldBeCalled()->willReturn(fixture(''));

        // Assert that all files show up in the table
        $this->climate->table()->shouldNotBeCalled();

        $this->climate->out(Argument::containingString('boolean expressions are assumptions'))->shouldBeCalled();

        $this->cli->handle(['phpa', FIXTURES_DIR]);
    }

    /**
     * @test
     */
    public function itShouldAnalyseTargetFileAndOutputXml()
    {
        $this->itShouldShowAuthor();

        $path = fixture('MyClass.php');
        $output = tempnam(sys_get_temp_dir(), 'xml');

        $this->argumentManager->defined('version')->shouldBeCalled()->willReturn(false);
        $this->argumentManager->get('format')->shouldBeCalled()->willReturn('xml');
        $this->argumentManager->get('path')->shouldBeCalled()->willReturn($path);
        $this->argumentManager->get('output')->shouldBeCalled()->willReturn($output);
        $this->argumentManager->get('exclude')->shouldBeCalled()->willReturn('');

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

    /**
     * @test
     */
    public function itShouldShowVersion()
    {
        $this->argumentManager->defined('version')->shouldBeCalled()->willReturn(true);

        $args = ['phpa', '--version'];
        $this->climate->out(Cli::VERSION)->shouldBeCalled();
        $this->cli->handle($args);
    }

    private function itShouldShowAuthor()
    {
        $this->climate->out(Argument::containingString('PHPAssumptions analyser'))
            ->shouldBeCalled()
            ->willReturn($this->climate);

        $this->climate->br()->shouldBeCalled();
    }
}
