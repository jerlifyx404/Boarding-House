<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use InvalidArgumentException;

class BoardingHouseController extends BaseController
{
    use ResponseTrait;

    private string $owner = '';
    private array $tenants = [];

    public function index()
    {
        return $this->respond([
            'status' => 'success',
            'owner' => $this->owner,
            'tenant_count' => count($this->tenants)
        ], 200);
    }

    public function setOwner()
    {
        $name = $this->request->getPost('name');

        if (empty($name)) {
            return $this->failValidationErrors('Owner name is required', 400);
        }

        try {
            if (empty($name)) {
                throw new InvalidArgumentException('Owner name cannot be empty');
            }
            $this->owner = $name;
            return $this->respond([
                'status' => 'success',
                'message' => 'Owner set successfully'
            ], 200);
        } catch (InvalidArgumentException $e) {
            return $this->fail($e->getMessage(), 400);
        }
    }

    public function addTenant()
    {
        $name = $this->request->getPost('name');

        if (empty($name)) {
            return $this->failValidationErrors('Tenant name is required', 400);
        }

        try {
            if (empty($name)) {
                throw new InvalidArgumentException('Tenant name cannot be empty');
            }
            $this->tenants[] = $name;
            return $this->respond([
                'status' => 'success',
                'message' => 'Tenant added successfully'
            ], 200);
        } catch (InvalidArgumentException $e) {
            return $this->fail($e->getMessage(), 400);
        }
    }

    public function removeTenant()
    {
        $name = $this->request->getPost('name');

        if (empty($name)) {
            return $this->failValidationErrors('Tenant name is required', 400);
        }

        try {
            $key = array_search($name, $this->tenants, true);
            if ($key === false) {
                throw new InvalidArgumentException('Tenant not found');
            }
            unset($this->tenants[$key]);
            $this->tenants = array_values($this->tenants);
            return $this->respond([
                'status' => 'success',
                'message' => 'Tenant removed successfully'
            ], 200);
        } catch (InvalidArgumentException $e) {
            return $this->fail($e->getMessage(), 400);
        }
    }
}