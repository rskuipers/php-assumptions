<?php

namespace tests\fixtures;

class MyClass
{
    public function run($dog, $cat)
    {
        if ($dog !== null) {
            $dog->woof();
        }

        if ($cat instanceof MyOtherClass) {
            $cat->run($dog);
        }
    }
}
