<?php

namespace App\Controllers;

// use App\Models\OwnerModel;
use App\Models\UserModel;

class BoardingHouse extends BaseController
{
    public function index(): string
    {
        $UserModel = new UserModel();
        // Fetch the count of users grouped by userType for the graph
        $userCounts = $UserModel->select('userType, COUNT(*) as count')
                                ->groupBy('userType')
                                ->findAll();
        
        $data['userCounts'] = $userCounts;
        return 
         view('Template/header', $data) .
         view('Template/sidebar') .
         view('Template/footer');
    }
    public function user(): string
    {
        $UserModel = new UserModel();
        $data['UserInfo'] = $UserModel->findAll();
        
        return 
         view('Template/sidebar') .
         view('BoardingHouse/user', $data);
    }
    public function AddUser(): string
    {
        return 
         view('Template/sidebar') .
         view('BoardingHouse/AddUser');
    }

    public function EditUser($userID): string
    {
        $UserModel = new UserModel();
        $UserInfo = $UserModel->find($userID);

        $data = [
            'page_title' => 'Edit User',
            'UserInfo' => $UserInfo,
        ];

        return 
         view('Template/sidebar') .
         view('BoardingHouse/AddUser', $data);
    }

    public function insertUser()
    {
        $data = $this->request->getPost(['txtUserID', 'txtFullName', 'txtUsername', 'txtEmail', 'txtPassword', 'txtUserType']);

        // Checks whether the submitted data passed the validation rules.
        if (! $this->validateData($data, [
            // 'txtUserID' => 'required',
            'txtFullName'  => 'required',
            'txtUsername'  => 'required',
            'txtEmail'  => 'required',
            'txtPassword'  => 'required',
            'txtUserType'  => 'required',
        ])) {
            // The validation fails, so returns the form.
            return $this->AddUser();
        }

        // Gets the validated data.
        $post = $this->validator->getValidated();

        $data = [
            // 'userID' => $post['txtUserID'],
            'fullName' => $post['txtFullName'],
            'username' => $post['txtUsername'],
            'email' => $post['txtEmail'],
            'password' => hash('sha256', $post['txtPassword']), // Hash the password
            'userType' => $post['txtUserType'],
        ];
        
        // Inserts data and returns inserted row's primary key
        $UserModel = new UserModel();
        $UserModel->insert($data);

        return redirect()->to('/BoardingHouse/user');
    }

    public function updateUser()
    {
        $data = $this->request->getPost(['txtUserID', 'txtFullName', 'txtUsername', 'txtEmail', 'txtPassword', 'txtUserType']);

        // Checks whether the submitted data passed the validation rules.
        if (! $this->validateData($data, [
            'txtUserID' => 'required',
            'txtFullName'  => 'required',
            'txtUsername'  => 'required',
            'txtEmail'  => 'required',
            'txtPassword'  => 'required',
            'txtUserType'  => 'required',
        ])) {
            // The validation fails, so returns the form.
            return $this->AddUser();
        }

        // Gets the validated data.
        $post = $this->validator->getValidated();

        $data = [
            // 'userID' => $post['txtUserID'],
            'fullName' => $post['txtFullName'],
            'username' => $post['txtUsername'],
            'email' => $post['txtEmail'],
            'password' => hash('sha256', $post['txtPassword']), // Hash the password
            'userType' => $post['txtUserType'],
        ];
        
        // Updates data
        $UserModel = new UserModel();
        $UserModel->update($post['txtUserID'], $data);

        return redirect()->to('/BoardingHouse/user');
    }

    public function DeleteUser($userID)
    {
        $UserModel = new UserModel();
        $UserModel->delete($userID);
        return redirect()->to('/BoardingHouse/user');
    }
}