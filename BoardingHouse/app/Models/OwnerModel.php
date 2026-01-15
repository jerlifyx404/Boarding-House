<?php

namespace App\Models;
use CodeIgniter\Model;

class OwnerModel extends Model
{
    protected $table = 'BoardingDetails';
    protected $primaryKey = 'houseID';
    protected $foreignKey = 'ownerID';

    protected $allowedFields = ['houseID', 'ownerID', 'name', 'address', 'NumberOfRooms', 'pNum', 'price' ];
    
}