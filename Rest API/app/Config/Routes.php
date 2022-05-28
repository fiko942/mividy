<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (file_exists(SYSTEMPATH . 'Config/Routes.php')) {
    require SYSTEMPATH . 'Config/Routes.php';
}

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Api');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(true);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/', 'Home');
$routes->get('/api/index', function() {
    throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
});
$routes->get('/api/login/(:segment)/(:segment)', 'Api::login/$1/$2');
$routes->get('/index.php', function(){
    throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
});
$routes->get('/validate_session/(:segment)', 'Api::validate_session/$1');
$routes->get('/api/ambil_semua_transaksi/(:segment)', 'Api::ambil_semua_transaksi/$1');
$routes->get('/api/ambil_semua_transaksi/(:segment)/(:segment)', 'Api::ambil_semua_transaksi/$1/$2');
$routes->get('/api/logout/(:segment)', 'Api::logout/$1');
$routes->get('/api/tambahkan_data_transaksi/(:segment)/(:segment)/(:segment)/(:segment)', 'Api::tambahkan_data_transaksi/$1/$2/$3/$4');
$routes->get('/api/hapus_transaksi/(:segment)/(:segment)', 'Api::hapus_transaksi/$1/$2');
$routes->get('/api/tambahkan_admin/(:segment)/(:segment)/(:segment)', 'Api::tambahkan_admin/$1/$2/$3');
$routes->get('/api/akun/verifikasi-email/(:segment)', 'Api::verifikasiAkun/$1');
$routes->get('/api/reset-password/(:segment)', 'Api::resetPassword/$1');
$routes->get('/faktur/cetak-faktur/(:any)', 'Api::downloadPdf/$1');
$routes->get('/faktur/index', function(){
    throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
});
$routes->get('/faktur/style', 'Faktur::css');
$routes->get('/faktur/(:segment)', 'Faktur::getFaktur/$1');
$routes->get('/api/cek-keadaan-faktur/(:segment)/(:segment)', 'Api::cekKeadaanFaktur/$1/$2');
$routes->get('/api/minimal-dashboard/(:segment)', 'Api::minimalDashboard/$1');
$routes->get('/api/validasi-id-faktur/(:segment)', 'Api::validasiIdFaktur/$1');
$routes->get('/api/edit-faktur/(:segment)/(:segment)/(:segment)/(:segment)/(:segment)', 'Api::editFakturTransaksi/$1/$2/$3/$4/$5');
$routes->get('/download-client(:any)', 'Home::download');

/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within t
 hat file without
 * needing to reload it.
 */
 if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
