<?php

namespace App\Models;

use CodeIgniter\Model;

class PhotoModel extends Model
{
    protected $table = 'Photos';
    protected $primaryKey = 'photoID';
    protected $allowedFields = ['houseID', 'photoUrl'];

    // Get all photos for a specific boarding house
    public function getPhotosByHouseID($houseID)
    {
        $photos = $this->where('houseID', $houseID)->findAll();
        log_message('debug', "Fetched photos for houseID {$houseID}: " . json_encode($photos));
        return $photos;
    }
}