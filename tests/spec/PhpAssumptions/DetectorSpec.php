<?php

namespace spec\PhpAssumptions;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class DetectorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('PhpAssumptions\Detector');
    }
}
