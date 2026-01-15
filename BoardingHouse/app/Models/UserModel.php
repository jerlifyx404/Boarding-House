<?php

namespace App\Models;
use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'Users';
    protected $primaryKey = 'userID';

    protected $allowedFields = ['userID', 'fullName', 'username', 'email', 'password', 'userType', 'latitude', 'longitude'];
    
}