<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Rute global untuk halaman login utama
$routes->get('/', 'AuthController::login');
$routes->post('/login', 'AuthController::loginProcess');
$routes->get('/register', 'AuthController::register');
$routes->post('/register', 'AuthController::registerProcess');
$routes->get('/logout', 'AuthController::logout');
$routes->get('profile/edit', 'AuthController::editProfile', ['filter' => 'auth']);
$routes->post('profile/update', 'AuthController::updateProfile', ['filter' => 'auth']);

// Export Riwayat terbaru semua pasien
$routes->get('riwayat-terbaru/all/pdf', 'ExportController::exportLatestRiwayatAllPasienToPdf');
$routes->get('riwayat-terbaru/all/excel', 'ExportController::exportLatestRiwayatAllPasienToExcel');

// Export Riwayat historis semua pasien
$routes->get('riwayat-historis/all/pdf', 'ExportController::exportAllHistoricalRiwayatToPdf');
$routes->get('riwayat-historis/all/excel', 'ExportController::exportAllHistoricalRiwayatToExcel');



// Rute khusus petugas
$routes->group('petugas', ['filter' => 'role:petugas'], function ($routes) {
    $routes->get('/', 'DashboardController::index'); // Menjadikan dashboard sebagai halaman default petugas
    $routes->get('dashboard', 'DashboardController::index');

    $routes->get('pasien', 'PasienController::index');
    $routes->get('pasien/create', 'PasienController::create');
    $routes->post('pasien/store', 'PasienController::store');
    $routes->get('pasien/edit/(:num)', 'PasienController::edit/$1');
    $routes->post('pasien/update/(:num)', 'PasienController::update/$1');
    $routes->post('pasien/delete/(:num)', 'PasienController::delete/$1');

    $routes->get('riwayat/(:num)', 'RiwayatController::index/$1');
    $routes->get('riwayat/all', 'RiwayatController::semuaRiwayat');
    $routes->get('riwayat/create/(:num)', 'RiwayatController::create/$1');
    $routes->post('riwayat/predict', 'RiwayatController::predict');
    $routes->post('riwayat/store', 'RiwayatController::store');
    $routes->get('riwayat/edit/(:num)', 'RiwayatController::edit/$1');
    $routes->post('riwayat/update/(:num)', 'RiwayatController::update/$1');
    $routes->post('riwayat/delete/(:num)', 'RiwayatController::delete/$1');

    $routes->get('riwayat/pdf/(:num)', 'ExportController::exportPasienRiwayatToPdf/$1');
    $routes->get('riwayat/excel/(:num)', 'ExportController::exportPasienRiwayatToExcel/$1');


    $routes->get('import', 'PasienController::import');
    $routes->post('import/upload', 'PasienController::upload');
});

// Rute khusus admin
$routes->group('admin', ['filter' => 'role:admin'], function ($routes) {
    $routes->get('/', 'AdminController::dashboard'); // Menjadikan dashboard sebagai halaman default petugas
    $routes->get('dashboard', 'AdminController::dashboard');

    $routes->get('pasien', 'AdminController::index');
    $routes->get('pasien/create', 'AdminController::create');
    $routes->post('pasien/store', 'AdminController::store');
    $routes->get('pasien/edit/(:num)', 'AdminController::edit/$1');
    $routes->post('pasien/update/(:num)', 'AdminController::update/$1');
    $routes->post('pasien/delete/(:num)', 'AdminController::delete/$1');

    $routes->get('petugas', 'AdminController::indexPetugas');
    $routes->get('petugas/create', 'AdminController::createPetugas');
    $routes->post('petugas/store', 'AdminController::storePetugas');
    $routes->get('petugas/edit/(:num)', 'AdminController::editPetugas/$1');
    $routes->post('petugas/update/(:num)', 'AdminController::updatePetugas/$1');
    $routes->post('petugas/delete/(:num)', 'AdminController::deletePetugas/$1');

    $routes->get('import', 'AdminController::import');
    $routes->post('import/upload', 'AdminController::upload');
});
