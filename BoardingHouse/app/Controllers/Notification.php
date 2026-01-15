<?php

namespace App\Controllers;

use App\Models\NotificationModel;
use App\Models\TenantModel;
use App\Models\UserModel;

class Notification extends BaseController
{
    public function notification(): string
    {
        $userModel = new UserModel();
        $data['UserInfo'] = $userModel->where('userType', 'tenant')->findAll();

        return view('Template/sidebar') . view('BoardingHouse/notification', $data);
    }

    public function notificationInfo(): string
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

        return view('Template/sidebar') . view('BoardingHouse/notificationInfo', $data);
    }

    public function approveRequest($requestID)
    {
        $tenantModel = new TenantModel();
        $notificationModel = new NotificationModel();

        // Fetch the rental request to get tenantID and houseID
        $request = $tenantModel->find($requestID);
        if (!$request) {
            return redirect()->to('/BoardingHouse/notification')->with('error', 'Rental request not found');
        }

        // Update RentalRequests status
        $tenantModel->update($requestID, ['status' => 'approved']);

        // Update or create notification
        $notificationData = [
            'requestID' => $requestID,
            'userID' => $request['tenantID'],
            'houseID' => $request['houseID'],
            'status' => 'approved',
        ];

        // Check if a notification already exists for this request
        $existingNotification = $notificationModel->where('requestID', $requestID)->first();
        if ($existingNotification) {
            $notificationModel->update($existingNotification['notificationID'], $notificationData);
        } else {
            $notificationModel->insert($notificationData);
        }

        return redirect()->to('/BoardingHouse/notificationInfo?tenantID=' . $request['tenantID'])->with('success', 'Rental request approved successfully');
    }

    public function declineRequest($requestID)
    {
        $tenantModel = new TenantModel();
        $notificationModel = new NotificationModel();

        // Fetch the rental request to get tenantID and houseID
        $request = $tenantModel->find($requestID);
        if (!$request) {
            return redirect()->to('/BoardingHouse/notification')->with('error', 'Rental request not found');
        }

        // Update RentalRequests status
        $tenantModel->update($requestID, ['status' => 'declined']);

        // Update or create notification
        $notificationData = [
            'requestID' => $requestID,
            'userID' => $request['tenantID'],
            'houseID' => $request['houseID'],
            'status' => 'declined',
        ];

        // Check if a notification already exists for this request
        $existingNotification = $notificationModel->where('requestID', $requestID)->first();
        if ($existingNotification) {
            $notificationModel->update($existingNotification['notificationID'], $notificationData);
        } else {
            $notificationModel->insert($notificationData);
        }

        return redirect()->to('/BoardingHouse/notificationInfo?tenantID=' . $request['tenantID'])->with('success', 'Rental request declined successfully');
    }
}