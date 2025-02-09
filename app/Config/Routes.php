<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/pasien', 'PasienController::index');
$routes->get('/pasien/create', 'PasienController::create');
$routes->post('/pasien/store', 'PasienController::store');
$routes->get('/pasien/edit/(:num)', 'PasienController::edit/$1');
$routes->post('/pasien/update/(:num)', 'PasienController::update/$1');
$routes->post('/pasien/delete/(:num)', 'PasienController::delete/$1');

$routes->get('riwayat/(:num)', 'RiwayatController::index/$1');
$routes->get('/riwayat/all', 'RiwayatController::semuaRiwayat');
$routes->get('riwayat/create/(:num)', 'RiwayatController::create/$1');
$routes->post('riwayat/predict', 'RiwayatController::predict');
$routes->post('riwayat/store', 'RiwayatController::store');
$routes->get('riwayat/edit/(:num)', 'RiwayatController::edit/$1');
$routes->post('riwayat/update/(:num)', 'RiwayatController::update/$1');
$routes->post('riwayat/delete/(:num)', 'RiwayatController::delete/$1');

$routes->get('/', 'AuthController::login');
$routes->post('/login', 'AuthController::loginProcess');
$routes->get('/register', 'AuthController::register');
$routes->post('/register', 'AuthController::registerProcess');
$routes->get('/logout', 'AuthController::logout');
