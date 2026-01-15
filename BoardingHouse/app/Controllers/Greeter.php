<?php

namespace App\Controllers;


class Greeter extends BaseController
{
    public function greet(string $name): string
    {
        return 'Hello, ' . $name . '!';
    }
    public function add($a, $b): int
    {
        return $a + $b;
    }
}