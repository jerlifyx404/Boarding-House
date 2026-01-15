<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::homepage');
$routes->get('/login', 'Home::login');
$routes->post('/login', 'Home::processLogin');

$routes->get('/BoardingHouse', 'BoardingHouse::index');

$routes->get('/BoardingHouse/user', 'BoardingHouse::user');
$routes->get('/BoardingHouse/AddUser', 'BoardingHouse::AddUser');
$routes->get('/BoardingHouse/EditUser/(:segment)','BoardingHouse::EditUser/$1');
$routes->get('/BoardingHouse/DeleteUser/(:segment)','BoardingHouse::DeleteUser/$1');

$routes->post('/BoardingHouse/insertUser', 'BoardingHouse::insertUser'); 
$routes->post('/BoardingHouse/updateUser', 'BoardingHouse::updateUser'); 

$routes->get('/BoardingHouse/owner', 'Owner::owner');
$routes->get('/BoardingHouse/ViewOwner', 'Owner::ViewOwner');
// $routes->get('BoardingHouse/ViewOwner/(:segment)', 'Owner::ViewOwner/$1');
$routes->get('/BoardingHouse/AddOwner', 'Owner::AddOwner');
$routes->get('/BoardingHouse/EditOwner/(:segment)', 'Owner::EditOwner/$1');
$routes->get('/BoardingHouse/DeleteOwner/(:segment)','Owner::DeleteOwner/$1');

$routes->post('/BoardingHouse/insertOwner', 'Owner::insertOwner'); 
$routes->post('/BoardingHouse/updateOwner', 'Owner::updateOwner'); 


$routes->get('/BoardingHouse/ViewTenant', 'Tenant::ViewTenant');
$routes->get('/BoardingHouse/tenant', 'Tenant::tenant');
$routes->get('/BoardingHouse/AddTenant', 'Tenant::AddTenant');
$routes->get('/BoardingHouse/EditTenant/(:segment)', 'Tenant::EditTenant/$1');
$routes->get('/BoardingHouse/DeleteTenant/(:segment)', 'Tenant::DeleteTenant/$1');
$routes->post('/BoardingHouse/insertTenant', 'Tenant::insertTenant');
$routes->post('/BoardingHouse/updateTenant', 'Tenant::updateTenant');

// $routes->get('/BoardingHouse/notification', 'Notification::Notification');

// $routes->get('/BoardingHouse/notification', 'Notification::notification');
// $routes->get('/BoardingHouse/ApproveNotification/(:segment)', 'Notification::ApproveNotification/$1');
// $routes->get('/BoardingHouse/DeclineNotification/(:segment)', 'Notification::DeclineNotification/$1');

// $routes->get('/BoardingHouse/notification', 'Tenant::notification');
// $routes->get('/BoardingHouse/approveRequest/(:segment)', 'Tenant::approveRequest/$1');
// $routes->get('/BoardingHouse/declineRequest/(:segment)', 'Tenant::declineRequest/$1');


$routes->get('BoardingHouse/notification', 'Notification::notification');
$routes->get('BoardingHouse/notificationInfo', 'Notification::notificationInfo');
$routes->get('BoardingHouse/approveRequest/(:segment)', 'Notification::approveRequest/$1');
$routes->get('BoardingHouse/declineRequest/(:segment)', 'Notification::declineRequest/$1');



$routes->group('api', function($routes) {
    // Authentication
    $routes->post('login', 'Api::login');

    // Users
    $routes->get('users', 'Api::getUsers');
    $routes->get('user-counts', 'Api::getUserCounts');
    $routes->post('users', 'Api::addUser');
    $routes->put('users/(:num)', 'Api::updateUser/$1');
    $routes->delete('users/(:num)', 'Api::deleteUser/$1');

    // Owners
    $routes->get('owners', 'Api::getOwners');
    $routes->get('owners/(:num)', 'Api::getOwnerDetails/$1');
    $routes->post('owners', 'Api::addOwner');
    $routes->put('owners/(:num)', 'Api::updateOwner/$1');
    $routes->delete('owners/(:num)', 'Api::deleteOwner/$1');

    // Tenants
    $routes->get('tenants', 'Api::getTenants');
    $routes->get('tenants/(:num)', 'Api::getTenantDetails/$1');
    $routes->post('tenants', 'Api::addTenant');
    $routes->put('tenants/(:num)', 'Api::updateTenant/$1');
    $routes->delete('tenants/(:num)', 'Api::deleteTenant/$1');

    // Notifications
    $routes->get('notifications', 'Api::getNotifications');
    $routes->get('notifications/(:num)', 'Api::getNotificationInfo/$1');
    $routes->put('notifications/approve/(:num)', 'Api::approveRequest/$1');
    $routes->put('notifications/decline/(:num)', 'Api::declineRequest/$1');
});

