<?php

namespace tests\fixtures;

class MyOtherClass
{
    public function run($cat)
    {
        if ($cat !== null) {
            $cat->meow();
        }
    }
}
