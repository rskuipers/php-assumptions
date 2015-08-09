<?php

namespace fixtures;

class MyClass
{
    public function run($dog)
    {
        if ($dog !== null) {
            $dog->woof();
        }
    }
}
