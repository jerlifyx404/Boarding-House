<?php

namespace App\Controllers;

use InvalidArgumentException;


class BankAccount extends BaseController
{
    private float $balance = 0;
    public function deposit($amount): void
    {
        $this->balance += $amount;
    }
    public function withdraw($amount): void
    {
        if($amount > $this->balance){
            throw new InvalidArgumentException('Insufficient Funds');
        }
        $this->balance -= $amount;
    }
    public function getBalance(): float
    {
        return $this->balance;
    }
}