<?php

namespace unit\PhpAssumptions\Output;

use League\CLImate\CLImate;
use PhpAssumptions\Output\PrettyOutput;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTestCase;

class PrettyOutputTest extends ProphecyTestCase
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
