<?php

namespace App\Controllers;

use App\Models\NotificationModel;
use App\Models\TenantModel;
use App\Models\UserModel;
use App\Models\OwnerModel;

class Tenant extends BaseController
{
    public function tenant(): string
    {
        $userModel = new UserModel();
        $data['UserInfo'] = $userModel->where('userType', 'tenant')->findAll();

        return view('Template/sidebar') . view('BoardingHouse/tenant', $data);
    }

    public function ViewTenant(): string
{
    $tenantID = $this->request->getGet('tenantID');

    $tenantModel = new TenantModel();
    $userModel = new UserModel();

    $data['TenantInfo'] = $tenantModel->db->table('RentalRequests')
        ->select('RentalRequests.*, Users.fullName as tenantName')
        ->join('Users', 'Users.userID = RentalRequests.tenantID')
        ->where('RentalRequests.tenantID', $tenantID)
        ->get()
        ->getResultArray();

    $data['tenantID'] = $tenantID;

    return view('Template/sidebar') . view('BoardingHouse/tenantInfo', $data);
}

    public function AddTenant(): string
    {
        $userModel = new UserModel();
        $ownerModel = new OwnerModel();
        $data['tenants'] = $userModel->where('userType', 'tenant')->findAll();
        $data['houses'] = $ownerModel->findAll();

        return view('Template/sidebar') . view('BoardingHouse/AddTenant', $data);
    }

    public function EditTenant($requestID): string
    {
        $tenantModel = new TenantModel();
        $userModel = new UserModel();
        $ownerModel = new OwnerModel();

        $tenantInfo = $tenantModel->find($requestID);

        $data = [
            'page_title' => 'Edit Tenant Request',
            'TenantInfo' => $tenantInfo,
            'tenants' => $userModel->where('userType', 'tenant')->findAll(),
            'houses' => $ownerModel->findAll(),
        ];

        return view('Template/sidebar') . view('BoardingHouse/AddTenant', $data);
    }

    public function insertTenant()
    {
        $data = $this->request->getPost(['tenantID', 'houseID', 'fullName', 'mobileNumber', 'email', 'roomPreference']);

        $rules = [
            'tenantID' => 'required|is_natural_no_zero',
            'houseID' => 'required|is_natural_no_zero',
            'fullName' => 'required|max_length[255]',
            'mobileNumber' => 'required|max_length[20]',
            'email' => 'required|valid_email|max_length[255]',
            'roomPreference' => 'required|in_list[Single Room,Shared Room]',
        ];
        

        if (!$this->validateData($data, $rules)) {
            log_message('error', 'Validation failed: ' . json_encode($this->validator->getErrors()));
            return $this->AddTenant();
        }

        $post = $this->validator->getValidated();

        $data = [
            'tenantID' => $post['tenantID'],
            'houseID' => $post['houseID'],
            'fullName' => $post['fullName'],
            'mobileNumber' => $post['mobileNumber'],
            'email' => $post['email'],
            'roomPreference' => $post['roomPreference'],
            'status' => 'pending',
        ];

        $tenantModel = new TenantModel();
        $tenantModel->insert($data);

        return redirect()->to('/BoardingHouse/ViewTenant?tenantID=' . $post['tenantID'])->with('success', 'Tenant request added successfully');
    }

    public function updateTenant()
    {
        $data = $this->request->getPost(['requestID', 'tenantID', 'houseID', 'fullName', 'mobileNumber', 'email', 'roomPreference']);

        $rules = [
            'requestID' => 'required|is_natural_no_zero',
            'tenantID' => 'required|is_natural_no_zero',
            'houseID' => 'required|is_natural_no_zero',
            'fullName' => 'required|max_length[255]',
            'mobileNumber' => 'required|max_length[20]',
            'email' => 'required|valid_email|max_length[255]',
            'roomPreference' => 'required|in_list[Single Room,Shared Room]',
        ];

        if (!$this->validateData($data, $rules)) {
            log_message('error', 'Validation failed: ' . json_encode($this->validator->getErrors()));
            return $this->EditTenant($data['requestID']);
        }

        $post = $this->validator->getValidated();

        $data = [
            'tenantID' => $post['tenantID'],
            'houseID' => $post['houseID'],
            'fullName' => $post['fullName'],
            'mobileNumber' => $post['mobileNumber'],
            'email' => $post['email'],
            'roomPreference' => $post['roomPreference'],
            'status' => 'pending',
        ];

        $tenantModel = new TenantModel();
        $tenantModel->update($post['requestID'], $data);

        return redirect()->to('/BoardingHouse/ViewTenant?tenantID=' . $post['tenantID'])->with('success', 'Tenant request updated successfully');
    }

    public function DeleteTenant($requestID)
    {
        $tenantModel = new TenantModel();
        $tenantInfo = $tenantModel->find($requestID);

        $tenantModel->delete($requestID);

        return redirect()->to('/BoardingHouse/ViewTenant?tenantID=' . $tenantInfo['tenantID'])->with('success', 'Tenant request deleted successfully');
    }

    
}