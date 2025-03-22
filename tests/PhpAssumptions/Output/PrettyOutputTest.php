<?php

namespace tests\PhpAssumptions\Output;

use League\CLImate\CLImate;
use PhpAssumptions\Output\PrettyOutput;
use PhpAssumptions\Output\Result;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class PrettyOutputTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var PrettyOutput
     */
    private $output;

    /**
     * @var CLImate
     */
    private $climate;

    /**
     * @var Result
     */
    private $result;

    public function setUp(): void
    {
        $this->climate = $this->prophesize(CLImate::class);
        $this->result = $this->prophesize(Result::class);
        $this->output = new PrettyOutput($this->climate->reveal());
    }

    #[Test]
    public function itShouldOutputWhatIsWritten()
    {
        $assumptions = [
            [
                'file' => 'MyClass.php',
                'line' => 120,
                'message' => '$test',
            ]
        ];

        $this->result->getAssumptions()->shouldBeCalled()->willReturn($assumptions);
        $this->result->getAssumptionsCount()->shouldBeCalled()->willReturn(1);
        $this->result->getBoolExpressionsCount()->shouldBeCalled()->willReturn(3);
        $this->result->getPercentage()->shouldBeCalled()->willReturn(33.3333333);

        $this->climate->table($assumptions)->shouldBeCalled()->willReturn($this->climate);
        $this->climate->br()->shouldBeCalled();
        $this->climate->out('1 out of 3 boolean expressions are assumptions (33%)')->shouldBeCalled();

        $this->output->output($this->result->reveal());
    }
}
