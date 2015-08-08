<?php

namespace spec\PhpAssumptions;

use PhpAssumptions\Cli;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @mixin Cli
 */
class CliSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(Cli::class);
    }
}
