<?php

namespace spec\PhpAssumptions\Output;

use PhpAssumptions\Output\OutputInterface;
use PhpAssumptions\Output\PrettyOutput;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @mixin PrettyOutput
 */
class PrettyOutputSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(PrettyOutput::class);
        $this->shouldHaveType(OutputInterface::class);
    }
}
