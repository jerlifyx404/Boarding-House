<?php

namespace App\Models;
use CodeIgniter\Model;

class TenantModel extends Model
{
    protected $table = 'RentalRequests';

    protected $primaryKey = 'requestID';

    protected $allowedFields = ['requestID', 'tenantID', 'houseID', 'fullName', 'mobileNumber', 'email', 'roomPreference', 'status'];
}

