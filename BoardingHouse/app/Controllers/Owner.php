<?php

namespace App\Controllers;

use App\Models\OwnerModel;
use App\Models\UserModel;
use App\Models\PhotoModel;

class Owner extends BaseController
{
    public function owner(): string
    {
        $userModel = new UserModel();
        $data['UserInfo'] = $userModel->where('userType', 'owner')->findAll();

        return view('Template/sidebar') . view('BoardingHouse/owner', $data);
    }

    public function ViewOwner(): string
    {
        $ownerID = $this->request->getGet('ownerID');

        $userModel = new UserModel();
        $photoModel = new PhotoModel();

        $data['OwnerInfo'] = $userModel->db->table('BoardingDetails')
            ->select('BoardingDetails.*, Users.fullName as ownerName')
            ->join('Users', 'Users.userID = BoardingDetails.ownerID')
            ->where('BoardingDetails.ownerID', $ownerID)
            ->get()
            ->getResultArray();

        foreach ($data['OwnerInfo'] as &$owner) {
            $houseID = $owner['houseID'];
            $photos = $photoModel->getPhotosByHouseID($houseID);
            log_message('debug', "Photos for houseID {$houseID}: " . json_encode($photos));
            $owner['photos'] = $photos;
        }

        $data['ownerID'] = $ownerID;

        return view('Template/sidebar') . view('BoardingHouse/ownerInfo', $data);
    }

    public function AddOwner(): string
    {
        $userModel = new UserModel();
        $data['owners'] = $userModel->where('userType', 'owner')->findAll();

        return view('Template/sidebar') . view('BoardingHouse/AddOwner', $data);
    }

    public function EditOwner($houseID): string
    {
        $ownerModel = new OwnerModel();
        $userModel = new UserModel();
        $photoModel = new PhotoModel();

        $ownerInfo = $ownerModel->find($houseID);

        $data = [
            'page_title' => 'Edit Owner',
            'OwnerInfo' => $ownerInfo,
            'owners' => $userModel->where('userType', 'owner')->findAll(),
            'photos' => $photoModel->where('houseID', $houseID)->findAll(),
        ];

        return view('Template/sidebar') . view('BoardingHouse/AddOwner', $data);
    }

    public function insertOwner()
    {
        $data = $this->request->getPost(['ownerID', 'txtName', 'txtAddress', 'txtNumberOfRooms', 'txtPhoneNum', 'txtPrice']);

        $rules = [
            'ownerID' => 'required|is_natural_no_zero',
            'txtName' => 'required|max_length[255]',
            'txtAddress' => 'required',
            'txtNumberOfRooms' => 'required|is_natural_no_zero',
            'txtPhoneNum' => 'required|max_length[20]',
            'txtPrice' => 'required|decimal',
        ];

        $photos = $this->request->getFileMultiple('photos');
        if (!empty($photos) && $photos[0]->isValid()) {
            $rules['photos.*'] = 'uploaded[photos]|max_size[photos,2048]|is_image[photos]';
        }

        if (!$this->validateData($data, $rules)) {
            log_message('error', 'Validation failed: ' . json_encode($this->validator->getErrors()));
            return $this->AddOwner();
        }

        $post = $this->validator->getValidated();

        $data = [
            'ownerID' => $post['ownerID'],
            'name' => $post['txtName'],
            'address' => $post['txtAddress'],
            'NumberOfRooms' => $post['txtNumberOfRooms'],
            'pNum' => $post['txtPhoneNum'],
            'price' => $post['txtPrice'],
        ];

        $ownerModel = new OwnerModel();
        $houseID = $ownerModel->insert($data, true);
        log_message('debug', "Inserted boarding house with houseID: {$houseID}");

        // Unified upload path
        $uploadPath = '/var/www/html/Uploads';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
            log_message('debug', "Created uploads directory: {$uploadPath}");
        }

        $photoModel = new PhotoModel();
        if (!empty($photos) && $photos[0]->isValid()) {
            $uploadedCount = 0;
            log_message('debug', 'Total photos detected: ' . count($photos));
            foreach ($photos as $index => $photo) {
                log_message('debug', "Processing photo {$index}: " . $photo->getName());
                if ($photo->isValid() && !$photo->hasMoved()) {
                    $newName = $photo->getRandomName();
                    $photo->move($uploadPath, $newName);
                    $photoData = [
                        'houseID' => $houseID,
                        'photoUrl' => '/Uploads/' . $newName, // Updated to /Uploads/
                    ];
                    $photoModel->insert($photoData);
                    $uploadedCount++;
                    log_message('debug', "Photo {$index} uploaded successfully: " . json_encode($photoData));
                } else {
                    log_message('error', "Photo {$index} upload failed: " . $photo->getErrorString());
                }
            }
            log_message('debug', "Total photos uploaded: {$uploadedCount}");
        } else {
            log_message('debug', 'No valid photos uploaded');
        }

        return redirect()->to('/BoardingHouse/ViewOwner?ownerID=' . $post['ownerID'])->with('success', 'Boarding house added successfully');
    }

    public function updateOwner()
    {
        $data = $this->request->getPost(['txtHouseID', 'ownerID', 'txtName', 'txtAddress', 'txtNumberOfRooms', 'txtPhoneNum', 'txtPrice']);

        $rules = [
            'txtHouseID' => 'required|is_natural_no_zero',
            'ownerID' => 'required|is_natural_no_zero',
            'txtName' => 'required|max_length[255]',
            'txtAddress' => 'required',
            'txtNumberOfRooms' => 'required|is_natural_no_zero',
            'txtPhoneNum' => 'required|max_length[20]',
            'txtPrice' => 'required|decimal',
        ];

        $photos = $this->request->getFileMultiple('photos');
        if (!empty($photos) && $photos[0]->isValid()) {
            $rules['photos.*'] = 'uploaded[photos]|max_size[photos,2048]|is_image[photos]';
        }

        if (!$this->validateData($data, $rules)) {
            log_message('error', 'Validation failed: ' . json_encode($this->validator->getErrors()));
            return $this->EditOwner($data['txtHouseID']);
        }

        $post = $this->validator->getValidated();

        $data = [
            'ownerID' => $post['ownerID'],
            'name' => $post['txtName'],
            'address' => $post['txtAddress'],
            'NumberOfRooms' => $post['txtNumberOfRooms'],
            'pNum' => $post['txtPhoneNum'],
            'price' => $post['txtPrice'],
        ];

        $ownerModel = new OwnerModel();
        $ownerModel->update($post['txtHouseID'], $data);

        // Unified upload path
        $uploadPath = '/var/www/html/Uploads';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
            log_message('debug', "Created uploads directory: {$uploadPath}");
        }

        $photoModel = new PhotoModel();

        // Handle photo deletions
        $deletePhotos = $this->request->getPost('delete_photos') ?? [];
        foreach ($deletePhotos as $photoID) {
            $photo = $photoModel->find($photoID);
            if ($photo) {
                $filePath = '/var/www/html/' . $photo['photoUrl'];
                if (file_exists($filePath)) {
                    unlink($filePath);
                    log_message('debug', "Deleted photo file: {$filePath}");
                }
                $photoModel->delete($photoID);
                log_message('debug', "Deleted photo ID: {$photoID}");
            }
        }

        // Handle new photo uploads
        if (!empty($photos) && $photos[0]->isValid()) {
            $uploadedCount = 0;
            log_message('debug', 'Total photos detected: ' . count($photos));
            foreach ($photos as $index => $photo) {
                log_message('debug', "Processing photo {$index}: " . $photo->getName());
                if ($photo->isValid() && !$photo->hasMoved()) {
                    $newName = $photo->getRandomName();
                    $photo->move($uploadPath, $newName);
                    $photoData = [
                        'houseID' => $post['txtHouseID'],
                        'photoUrl' => '/Uploads/' . $newName, // Updated to /Uploads/
                    ];
                    $photoModel->insert($photoData);
                    $uploadedCount++;
                    log_message('debug', "Photo {$index} uploaded successfully: " . json_encode($photoData));
                } else {
                    log_message('error', "Photo {$index} upload failed: " . $photo->getErrorString());
                }
            }
            log_message('debug', "Total photos uploaded: {$uploadedCount}");
        } else {
            log_message('debug', 'No new photos uploaded');
        }

        return redirect()->to('/BoardingHouse/ViewOwner?ownerID=' . $post['ownerID'])->with('success', 'Boarding house updated successfully');
    }

    public function DeleteOwner($houseID)
    {
        $ownerModel = new OwnerModel();
        $photoModel = new PhotoModel();
        $ownerInfo = $ownerModel->find($houseID);

        $photos = $photoModel->where('houseID', $houseID)->findAll();
        foreach ($photos as $photo) {
            $filePath = '/var/www/html/' . $photo['photoUrl'];
            if (file_exists($filePath)) {
                unlink($filePath);
                log_message('debug', "Deleted photo file: {$filePath}");
            }
            $photoModel->delete($photo['photoID']);
        }

        $ownerModel->delete($houseID);
        return redirect()->to('/BoardingHouse/ViewOwner?ownerID=' . $ownerInfo['ownerID'])->with('success', 'Boarding house deleted successfully');
    }
}