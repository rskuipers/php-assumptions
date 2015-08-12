<?php

namespace spec\PhpAssumptions;

use League\CLImate\Argument\Manager;
use League\CLImate\CLImate;
use PhpAssumptions\Cli;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @mixin Cli
 */
class CliSpec extends ObjectBehavior
{
    public function let(CLImate $cli, Manager $argumentManager)
    {
        $argumentManager->add(Argument::type('array'))->shouldBeCalled();
        $cli->arguments = $argumentManager;
        $this->beConstructedWith($cli);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(Cli::class);
    }
}
