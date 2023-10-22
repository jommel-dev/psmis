<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (file_exists(SYSTEMPATH . 'Config/Routes.php'))
{
	require SYSTEMPATH . 'Config/Routes.php';
}

/**
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
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
$routes->get('/', 'Home::index');

$routes->group('psmis/api/v1', function($routes){
	$routes->group('auth', function($routes){
		$routes->post('login', 'Auth::login');
		$routes->post('getVersion', 'Auth::validateAppVersion');
		$routes->post('firstLoginChange', 'Auth::firstLoginChangePassword');
	}); 

	// User Management
	$routes->group('users', function($routes){
		$routes->post('register', 'Users::registerUser');
		$routes->get('getAllUserList', 'Users::getAllUserList');
		$routes->post('changePassword', 'Users::ChangePassword');
		$routes->post('getUserById', 'Users::getUserDetails');
	});

	// Miscelenious
	$routes->group('misc', function($routes){
		$routes->get('userTypes', 'Misc::getUserTypes');
		$routes->get('getBranches', 'Misc::getBranches');
		$routes->get('getCategories', 'Misc::getCategories');
		$routes->get('getUnits', 'Misc::getUnits');
		$routes->get('getAddress/(:any)', 'Misc::getAddress/$1');

		// Set Daily Series of Reference Numbers
		$routes->post('set/user/reference', 'Misc::setDailySeries');
		$routes->post('set/reference/list', 'Misc::getDateSeriesList');
		$routes->post('get/user/reference', 'Misc::getDailySeries');

		$routes->post('dashboard', 'Dashboard::getDashboard');
		$routes->post('monthly/result', 'Dashboard::getMonthlyResult');
		$routes->post('weekly/result', 'Dashboard::getWeeklyResult');
	});

	// Applicant Module
	$routes->group('application', function($routes){
		$routes->post('register', 'Client::registerClient');
		$routes->get('clientList', 'Client::getAllClientList');
		$routes->post('get/patient/list', 'Client::getClientPatientList');
	});

	// Loans Module
	$routes->group('loan', function($routes){
		$routes->post('add/new', 'Client::registerPet');
		$routes->post('add/schedule', 'Client::addSchedule');
		$routes->post('add/checkup', 'Client::addCheckup');
		$routes->post('details', 'Client::getPatientDetails');
		$routes->post('get/schedule', 'Client::getPatientSchedule');
		$routes->post('get/checkup', 'Client::getPatientCheckups');
		$routes->post('get/wellness', 'Client::getPatientWellnes');
		$routes->get('details/schedule/(:num)', 'Client::getPatientScheduleDetail/$1');
	});

	// Invoice Group
	$routes->group('invoice', function($routes){
		$routes->post('create', 'InvoiceController::createInvoice');
		$routes->post('update', 'InvoiceController::updateInvoice');
		$routes->post('create/booking', 'InvoiceController::createBookingInvoice');
		$routes->post('fetch/byId', 'InvoiceController::getInvoiceDetails');
		$routes->get('fetch/list', 'InvoiceController::fetchInvoice');
	});

	// Generate PDF's and Reports
	$routes->group('generate', function($routes){
		$routes->post('invoice', 'Generate::createInvoice');
		$routes->get('print/invoice/(:any)', 'Generate::generateInvoicePdf/$1');
	});

});


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
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php'))
{
	require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}

