<?php

namespace Tests\App\Controllers;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\ControllerTestTrait;
use CodeIgniter\HTTP\Request;
use PHPUnit\Framework\Attributes\Depends;
use App\Controllers\BoardingHouseController;

/**
 * @internal
 */
final class BoardingHouseControllerTest extends CIUnitTestCase
{
    use ControllerTestTrait;

    public function testIndexNewBoardingHouse(): void
    {
        $result = $this->controller(\App\Controllers\BoardingHouseController::class)
            ->execute('index');

        $this->assertTrue($result->isOK());
        $response = $result->getJSON();
        $data = json_decode($response, true);

        $this->assertSame('success', $data['status']);
        $this->assertSame('', $data['owner'], 'New boarding house should have no owner');
        $this->assertSame(0, $data['tenant_count'], 'New boarding house should have zero tenants');
    }

    #[Depends('testIndexNewBoardingHouse')]
    public function testSetOwner(): void
    {
        $request = service('request');
        $request->setMethod('post');
        $request->setGlobal('post', ['name' => 'Jane Smith']);

        $result = $this->withRequest($request)
            ->controller(\App\Controllers\BoardingHouseController::class)
            ->execute('setOwner');

        $this->assertTrue($result->isOK());
        $response = $result->getJSON();
        $data = json_decode($response, true);

        $this->assertSame('success', $data['status']);
        $this->assertSame('Owner set successfully', $data['message']);
    }

    #[Depends('testSetOwner')]
    public function testAddTenant(): void
    {
        // Set owner to maintain state
        $request = service('request');
        $request->setMethod('post');
        $request->setGlobal('post', ['name' => 'Jane Smith']);
        $this->withRequest($request)
            ->controller(\App\Controllers\BoardingHouseController::class)
            ->execute('setOwner');

        // Add tenant
        $request->setGlobal('post', ['name' => 'John Doe']);
        $result = $this->withRequest($request)
            ->controller(\App\Controllers\BoardingHouseController::class)
            ->execute('addTenant');

        $this->assertTrue($result->isOK());
        $response = $result->getJSON();
        $data = json_decode($response, true);

        $this->assertSame('success', $data['status']);
        $this->assertSame('Tenant added successfully', $data['message']);

        // Verify tenant was added
        $result = $this->controller(\App\Controllers\BoardingHouseController::class)
            ->execute('index');
        $response = $result->getJSON();
        $data = json_decode($response, true);
        $this->assertSame(1, $data['tenant_count']);
    }

    #[Depends('testAddTenant')]
    public function testRemoveTenant(): void
    {
        // Rebuild state: set owner and add tenant
        $request = service('request');
        $request->setMethod('post');
        $request->setGlobal('post', ['name' => 'Jane Smith']);
        $this->withRequest($request)
            ->controller(\App\Controllers\BoardingHouseController::class)
            ->execute('setOwner');

        $request->setGlobal('post', ['name' => 'John Doe']);
        $this->withRequest($request)
            ->controller(\App\Controllers\BoardingHouseController::class)
            ->execute('addTenant');

        // Remove tenant
        $request->setGlobal('post', ['name' => 'John Doe']);
        $result = $this->withRequest($request)
            ->controller(\App\Controllers\BoardingHouseController::class)
            ->execute('removeTenant');

        $this->assertTrue($result->isOK());
        $response = $result->getJSON();
        $data = json_decode($response, true);

        $this->assertSame('success', $data['status']);
        $this->assertSame('Tenant removed successfully', $data['message']);

        // Verify tenant was removed
        $result = $this->controller(\App\Controllers\BoardingHouseController::class)
            ->execute('index');
        $response = $result->getJSON();
        $data = json_decode($response, true);
        $this->assertSame(0, $data['tenant_count']);
    }
}