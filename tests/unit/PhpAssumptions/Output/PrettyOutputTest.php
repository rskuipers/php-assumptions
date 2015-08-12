<?php

namespace unit\PhpAssumptions\Output;

use League\CLImate\CLImate;
use PhpAssumptions\Output\PrettyOutput;
use Prophecy\Argument;

class PrettyOutputTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PrettyOutput
     */
    private $output;

    /**
     * @var CLImate
     */
    private $climate;

    public function setUp()
    {
        $this->climate = $this->prophesize(CLImate::class);
        $this->output = new PrettyOutput($this->climate->reveal());
    }

    /**
     * @test
     */
    public function itShouldOutputWhatIsWritten()
    {
        $this->output->write('MyClass.php', 120, 'Weak assumption');

        $this->climate->table([[
            'file' => 'MyClass.php',
            'line' => 120,
            'message' => 'Weak assumption',
        ]])->shouldBeCalled()->willReturn($this->climate);

        $this->climate->br()->shouldBeCalled();
        $this->climate->out('Total warnings: 1')->shouldBeCalled();

        $this->output->flush();
    }
}
