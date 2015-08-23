<?php

namespace Test;

class Example
{
    public function run($bla)
    {
        $test = $bla && $bla === 'test' || $bla === 'ha' ? 'haha' : 'test';
        if ($test && $test > 0) {
            echo '';
        } elseif (!$test) {
            echo '';
        } else {
            echo '';
        }

        $test = true;
        while ($test) {
            $test = false;
        }

        for ($i = 0; $i; $i++) {
            echo '';
        }

        switch ($test) {
            default:
                echo '';
                break;
        }

        if (empty($test)) {
            echo '';
        }

        if (!is_null($test)) {
            echo '';
        }

        if (fixture($test)) {
            echo '';
        }

        if ($test === 'hi') {
            echo '';
        }
    }
}
