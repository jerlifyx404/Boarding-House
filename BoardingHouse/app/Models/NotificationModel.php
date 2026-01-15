<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationModel extends Model
{
    protected $table = 'Notifications';

    protected $primaryKey = 'notificationID';

    protected $allowedFields = ['notificationID', 'userID', 'houseID', 'requestID','status'];

    // protected $returnType = 'array';
}