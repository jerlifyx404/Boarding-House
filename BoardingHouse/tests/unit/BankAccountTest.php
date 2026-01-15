<?php

use CodeIgniter\Test\CIUnitTestCase;
use App\Controllers\BankAccount;
use PHPUnit\Framework\Attributes\Depends;


/**
 * @internal
 */
final class BankAccountTest extends CIUnitTestCase
{
    public function testOpenNewAccount(): BankAccount
    {
        $bankAccount = new BankAccount();

        $result = $bankAccount->getBalance();

        $this->assertSame(0.0, $result);
        return $bankAccount;

    }

    #[Depends('testOpenNewAccount')]
    public function testDeposit(BankAccount $bankAccount): BankAccount
    {

        $bankAccount->deposit(200.0);
        $result = $bankAccount->getBalance();


        $this->assertSame(200.0, $result);
        return $bankAccount;
    }

    #[Depends('testDeposit')]
    public function testWithdraw(BankAccount $bankAccount): void
    {

        $bankAccount->withdraw(300.0);
        $result = $bankAccount->getBalance();


        $this->assertSame(100.0, $result);
        
    }
    
}
