<?php

namespace App\Models;

use CodeIgniter\Model;

class loginModel extends Model
{
    protected $table = 'admin';

    protected $primaryKey = 'id';

    protected $allowedFields = ['id', 'username', 'password'];

    // protected $returnType = 'array';
}