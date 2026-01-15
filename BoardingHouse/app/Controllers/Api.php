<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\OwnerModel;
use App\Models\PhotoModel;
use App\Models\TenantModel;
use App\Models\NotificationModel;
use App\Models\loginModel;
use CodeIgniter\API\ResponseTrait;

class Api extends BaseController
{
    use ResponseTrait;

    public function __construct()
    {
        // Enable CORS for localhost:8080
        header('Access-Control-Allow-Origin: http://localhost:8080');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
    }

    // Authentication Endpoints
    public function login()
    {
        $loginModel = new loginModel();
        $data = $this->request->getJSON(true);

        if (!$data || !isset($data['username']) || !isset($data['password'])) {
            return $this->failValidationErrors('Username and password are required');
        }

        $user = $loginModel->where('username', $data['username'])->first();

        if ($user && $data['password'] === $user['password']) {
            // In production, use password_verify and generate a JWT
            $response = [
                'user_id' => $user['id'],
                'username' => $user['username'],
                'logged_in' => true
            ];
            return $this->respond($response, 200);
        }

        return $this->failUnauthorized('Invalid username or password');
    }

    // User Endpoints
    public function getUsers()
    {
        $userModel = new UserModel();
        $users = $userModel->findAll();
        return $this->respond($users, 200);
    }

    public function getUserCounts()
    {
        $userModel = new UserModel();
        $userCounts = $userModel->select('userType, COUNT(*) as count')
                                ->groupBy('userType')
                                ->findAll();
        return $this->respond($userCounts, 200);
    }

    public function addUser()
    {
        $userModel = new UserModel();
        $data = $this->request->getJSON(true);

        $rules = [
            'fullName' => 'required',
            'username' => 'required',
            'email' => 'required|valid_email',
            'password' => 'required',
            'userType' => 'required'
        ];

        if (!$this->validateData($data, $rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $userData = [
            'fullName' => $data['fullName'],
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => $data['password'], // In production, hash the password
            'userType' => $data['userType']
        ];

        $userModel->insert($userData);
        return $this->respondCreated(['message' => 'User added successfully']);
    }

    public function updateUser($userID)
    {
        $userModel = new UserModel();
        $data = $this->request->getJSON(true);

        $rules = [
            'fullName' => 'required',
            'username' => 'required',
            'email' => 'required|valid_email',
            'password' => 'required',
            'userType' => 'required'
        ];

        if (!$this->validateData($data, $rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $userData = [
            'fullName' => $data['fullName'],
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => $data['password'], // In production, hash the password
            'userType' => $data['userType']
        ];

        if (!$userModel->update($userID, $userData)) {
            return $this->failNotFound('User not found');
        }

        return $this->respond(['message' => 'User updated successfully'], 200);
    }

    public function deleteUser($userID)
    {
        $userModel = new UserModel();
        if (!$userModel->delete($userID)) {
            return $this->failNotFound('User not found');
        }
        return $this->respondDeleted(['message' => 'User deleted successfully']);
    }

    // Owner Endpoints
    public function getOwners()
    {
        $userModel = new UserModel();
        $owners = $userModel->where('userType', 'owner')->findAll();
        return $this->respond($owners, 200);
    }

    public function getOwnerDetails($ownerID)
    {
        $userModel = new UserModel();
        $photoModel = new PhotoModel();

        $ownerInfo = $userModel->db->table('BoardingDetails')
            ->select('BoardingDetails.*, Users.fullName as ownerName')
            ->join('Users', 'Users.userID = BoardingDetails.ownerID')
            ->where('BoardingDetails.ownerID', $ownerID)
            ->get()
            ->getResultArray();

        foreach ($ownerInfo as &$owner) {
            $houseID = $owner['houseID'];
            $owner['photos'] = $photoModel->getPhotosByHouseID($houseID);
        }

        return $this->respond($ownerInfo, 200);
    }

    public function addOwner()
    {
        $ownerModel = new OwnerModel();
        $photoModel = new PhotoModel();
        $data = $this->request->getPost();
        $photos = $this->request->getFileMultiple('photos');

        $rules = [
            'ownerID' => 'required|is_natural_no_zero',
            'name' => 'required|max_length[255]',
            'address' => 'required',
            'NumberOfRooms' => 'required|is_natural_no_zero',
            'pNum' => 'required|max_length[20]',
            'price' => 'required|decimal'
        ];

        if (!empty($photos) && $photos[0]->isValid()) {
            $rules['photos.*'] = 'uploaded[photos]|max_size[photos,2048]|is_image[photos]';
        }

        if (!$this->validateData($data, $rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $ownerData = [
            'ownerID' => $data['ownerID'],
            'name' => $data['name'],
            'address' => $data['address'],
            'NumberOfRooms' => $data['NumberOfRooms'],
            'pNum' => $data['pNum'],
            'price' => $data['price']
        ];

        $houseID = $ownerModel->insert($ownerData, true);

        $uploadPath = FCPATH . 'Uploads';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        if (!empty($photos) && $photos[0]->isValid()) {
            foreach ($photos as $photo) {
                if ($photo->isValid() && !$photo->hasMoved()) {
                    $newName = $photo->getRandomName();
                    $photo->move($uploadPath, $newName);
                    $photoModel->insert([
                        'houseID' => $houseID,
                        'photoUrl' => 'Uploads/' . $newName
                    ]);
                }
            }
        }

        return $this->respondCreated(['message' => 'Owner added successfully', 'houseID' => $houseID]);
    }

    public function updateOwner($houseID)
    {
        $ownerModel = new OwnerModel();
        $photoModel = new PhotoModel();
        $data = $this->request->getPost();
        $photos = $this->request->getFileMultiple('photos');

        $rules = [
            'ownerID' => 'required|is_natural_no_zero',
            'name' => 'required|max_length[255]',
            'address' => 'required',
            'NumberOfRooms' => 'required|is_natural_no_zero',
            'pNum' => 'required|max_length[20]',
            'price' => 'required|decimal'
        ];

        if (!empty($photos) && $photos[0]->isValid()) {
            $rules['photos.*'] = 'uploaded[photos]|max_size[photos,2048]|is_image[photos]';
        }

        if (!$this->validateData($data, $rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $ownerData = [
            'ownerID' => $data['ownerID'],
            'name' => $data['name'],
            'address' => $data['address'],
            'NumberOfRooms' => $data['NumberOfRooms'],
            'pNum' => $data['pNum'],
            'price' => $data['price']
        ];

        if (!$ownerModel->update($houseID, $ownerData)) {
            return $this->failNotFound('Boarding house not found');
        }

        $uploadPath = FCPATH . 'Uploads';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        $deletePhotos = $this->request->getPost('delete_photos') ?? [];
        foreach ($deletePhotos as $photoID) {
            $photo = $photoModel->find($photoID);
            if ($photo) {
                $filePath = FCPATH . $photo['photoUrl'];
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
                $photoModel->delete($photoID);
            }
        }

        if (!empty($photos) && $photos[0]->isValid()) {
            foreach ($photos as $photo) {
                if ($photo->isValid() && !$photo->hasMoved()) {
                    $newName = $photo->getRandomName();
                    $photo->move($uploadPath, $newName);
                    $photoModel->insert([
                        'houseID' => $houseID,
                        'photoUrl' => 'Uploads/' . $newName
                    ]);
                }
            }
        }

        return $this->respond(['message' => 'Owner updated successfully'], 200);
    }

    public function deleteOwner($houseID)
    {
        $ownerModel = new OwnerModel();
        $photoModel = new PhotoModel();

        $photos = $photoModel->where('houseID', $houseID)->findAll();
        foreach ($photos as $photo) {
            $filePath = FCPATH . $photo['photoUrl'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            $photoModel->delete($photo['photoID']);
        }

        if (!$ownerModel->delete($houseID)) {
            return $this->failNotFound('Boarding house not found');
        }

        return $this->respondDeleted(['message' => 'Owner deleted successfully']);
    }

    // Tenant Endpoints
    public function getTenants()
    {
        $userModel = new UserModel();
        $tenants = $userModel->where('userType', 'tenant')->findAll();
        return $this->respond($tenants, 200);
    }

    public function getTenantDetails($tenantID)
    {
        $tenantModel = new TenantModel();
        $userModel = new UserModel();

        $tenantInfo = $tenantModel->db->table('RentalRequests')
            ->select('RentalRequests.*, Users.fullName as tenantName')
            ->join('Users', 'Users.userID = RentalRequests.tenantID')
            ->where('RentalRequests.tenantID', $tenantID)
            ->get()
            ->getResultArray();

        return $this->respond($tenantInfo, 200);
    }

    public function addTenant()
    {
        $tenantModel = new TenantModel();
        $data = $this->request->getJSON(true);

        $rules = [
            'tenantID' => 'required|is_natural_no_zero',
            'houseID' => 'required|is_natural_no_zero',
            'fullName' => 'required|max_length[255]',
            'mobileNumber' => 'required|max_length[20]',
            'email' => 'required|valid_email|max_length[255]',
            'roomPreference' => 'required|in_list[Single Room,Shared Room]'
        ];

        if (!$this->validateData($data, $rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $tenantData = [
            'tenantID' => $data['tenantID'],
            'houseID' => $data['houseID'],
            'fullName' => $data['fullName'],
            'mobileNumber' => $data['mobileNumber'],
            'email' => $data['email'],
            'roomPreference' => $data['roomPreference'],
            'status' => 'pending'
        ];

        $tenantModel->insert($tenantData);
        return $this->respondCreated(['message' => 'Tenant request added successfully']);
    }

    public function updateTenant($requestID)
    {
        $tenantModel = new TenantModel();
        $data = $this->request->getJSON(true);

        $rules = [
            'tenantID' => 'required|is_natural_no_zero',
            'houseID' => 'required|is_natural_no_zero',
            'fullName' => 'required|max_length[255]',
            'mobileNumber' => 'required|max_length[20]',
            'email' => 'required|valid_email|max_length[255]',
            'roomPreference' => 'required|in_list[Single Room,Shared Room]'
        ];

        if (!$this->validateData($data, $rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $tenantData = [
            'tenantID' => $data['tenantID'],
            'houseID' => $data['houseID'],
            'fullName' => $data['fullName'],
            'mobileNumber' => $data['mobileNumber'],
            'email' => $data['email'],
            'roomPreference' => $data['roomPreference'],
            'status' => 'pending'
        ];

        if (!$tenantModel->update($requestID, $tenantData)) {
            return $this->failNotFound('Tenant request not found');
        }

        return $this->respond(['message' => 'Tenant request updated successfully'], 200);
    }

    public function deleteTenant($requestID)
    {
        $tenantModel = new TenantModel();
        if (!$tenantModel->delete($requestID)) {
            return $this->failNotFound('Tenant request not found');
        }
        return $this->respondDeleted(['message' => 'Tenant request deleted successfully']);
    }

    // Notification Endpoints
    public function getNotifications()
    {
        $userModel = new UserModel();
        $tenants = $userModel->where('userType', 'tenant')->findAll();
        return $this->respond($tenants, 200);
    }

    public function getNotificationInfo($tenantID)
    {
        $tenantModel = new TenantModel();
        $userModel = new UserModel();

        $tenantInfo = $tenantModel->db->table('RentalRequests')
            ->select('RentalRequests.*, Users.fullName as tenantName')
            ->join('Users', 'Users.userID = RentalRequests.tenantID')
            ->where('RentalRequests.tenantID', $tenantID)
            ->get()
            ->getResultArray();

        return $this->respond($tenantInfo, 200);
    }

    public function approveRequest($requestID)
    {
        $tenantModel = new TenantModel();
        $notificationModel = new NotificationModel();

        $request = $tenantModel->find($requestID);
        if (!$request) {
            return $this->failNotFound('Rental request not found');
        }

        $tenantModel->update($requestID, ['status' => 'approved']);

        $notificationData = [
            'requestID' => $requestID,
            'userID' => $request['tenantID'],
            'houseID' => $request['houseID'],
            'status' => 'approved'
        ];

        $existingNotification = $notificationModel->where('requestID', $requestID)->first();
        if ($existingNotification) {
            $notificationModel->update($existingNotification['notificationID'], $notificationData);
        } else {
            $notificationModel->insert($notificationData);
        }

        return $this->respond(['message' => 'Rental request approved successfully'], 200);
    }

    public function declineRequest($requestID)
    {
        $tenantModel = new TenantModel();
        $notificationModel = new NotificationModel();

        $request = $tenantModel->find($requestID);
        if (!$request) {
            return $this->failNotFound('Rental request not found');
        }

        $tenantModel->update($requestID, ['status' => 'declined']);

        $notificationData = [
            'requestID' => $requestID,
            'userID' => $request['tenantID'],
            'houseID' => $request['houseID'],
            'status' => 'declined'
        ];

        $existingNotification = $notificationModel->where('requestID', $requestID)->first();
        if ($existingNotification) {
            $notificationModel->update($existingNotification['notificationID'], $notificationData);
        } else {
            $notificationModel->insert($notificationData);
        }

        return $this->respond(['message' => 'Rental request declined successfully'], 200);
    }
}