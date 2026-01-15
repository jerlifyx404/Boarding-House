<?php

namespace App\Controllers;

use App\Models\loginModel;

class Home extends BaseController
{
    public function homepage(): string
    {
        return view('Template/homepage');
    }

    public function login(): string
    {
        // Pass any error messages and CSRF token to the view
        $data = [
            'error' => session()->getFlashdata('error')
        ];
        return view('Template/login', $data);
    }

    public function processLogin()
    {
        $loginModel = new loginModel(); // Instantiate model directly
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        // Find user by username
        $user = $loginModel->where('username', $username)->first();

        if ($user && $password === $user['password']) {
            // Password matches (Note: Use password_hash and password_verify in production)
            // Store user data in session
            session()->set([
                'user_id' => $user['id'],
                'username' => $user['username'],
                'logged_in' => true
            ]);
            return redirect()->to('/BoardingHouse'); // Redirect to homepage or dashboard
        } else {
            // Set error message and redirect back to login
            session()->setFlashdata('error', 'Invalid username or password!');
            return redirect()->back();
        }
    }
}