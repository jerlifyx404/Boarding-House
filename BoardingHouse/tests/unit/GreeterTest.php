<?php

use CodeIgniter\Test\CIUnitTestCase;
use App\Controllers\Greeter;
use PHPUnit\Framework\Attributes\DataProvider;


/**
 * @internal
 */
final class GreeterTest extends CIUnitTestCase
{
    // public function testGreetUsingName()
    // {
    //     $greeter = new Greeter;

    //     $greeting = $greeter->greet('Alice');

    //     $this->assertSame('Hello, Jerl!', $greeting);
    // }
    
    // public function testAddition()
    // {
    //     $greeter = new Greeter;

    //     $sum = $greeter->add(3, 5);

    //     $this->assertSame(8, $sum);
    // }

    // public static function additionProvider(): array
    // {
    //     return [
    //         'adding zeros'  => [0, 0, 0],
    //         'zero plus one' => [0, 1, 1],
    //         'one plus zero' => [1, 0, 1],
    //         'one plus one'  => [1, 1, 3],
    //     ];
    // }
    // #[DataProvider('additionProvider')]
    // public function testAdd(int $a, int $b, int $expected): void
    // {
    //     $greeter = new Greeter;

    //     $sum = $greeter->add($a,$b);

    //     $this->assertSame($expected, $sum);
    // }

    public function testSomething(): void
    {
         // Optional: Test anything here, if you want.
         $this->assertTrue(true, 'This should already work.');

         // Stop here and mark this test as incomplete.
        //  $this->markTestIncomplete(
        //      'This test has not been implemented yet.',
        //  );

         $this->markTestSkipped(
            'The PostgreSQL extension is not available',
        );
    }
    
    
}
