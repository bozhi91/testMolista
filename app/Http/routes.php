<?php

// Get corporate domain domain -----------------------------------------------------
$domain = parse_url( Config::get('app.application_url', false) );
if ( empty($domain['host']) )
{
	die("Environment variable APP_URL is not defined!");
}

// Corporate domain routes ---------------------------------------------------------
Route::group([
	'domain' => $domain['host'],
	'prefix' => LaravelLocalization::setLocale(),
	'middleware' => [
		'web', 
		'site.login.roles:admin|translator',
		'setTheme:corporate',
	],
], function() {

	// Corporate web
	Route::get('/', 'CorporateController@index');
	Route::controller('info', 'Corporate\InfoController');
	Route::controller('features', 'Corporate\FeaturesController');

	// Admin
	Route::group([
		'prefix' => 'admin',
		'middleware' => [
			'auth.admin',
			'role:admin|translator',
			'setTheme:admin',
		],
	], function() {
		Route::get('/', 'AdminController@index');

		// Sites
		Route::resource('sites', 'Admin\SitesController');
		// Users
		Route::resource('users', 'Admin\UsersController');
		// Marketplaces
		Route::get('marketplaces/check/{type}', 'Admin\MarketplacesController@getCheck');
		Route::resource('marketplaces', 'Admin\MarketplacesController');
		// Properties
		Route::get('properties/check/{type}', 'Admin\Properties\ServicesController@getCheck');
		Route::resource('properties/services', 'Admin\Properties\ServicesController');
		Route::resource('properties', 'Admin\Properties\BaseController');
		// Configuration
		Route::resource('config/locales', 'Admin\Config\LocalesController');
		Route::resource('config/translations', 'Admin\Config\TranslationsController');
		// Utils
		Route::controller('utils/user', 'Admin\Utils\UserController');
		Route::controller('utils/locale', 'Admin\Utils\LocaleController');
		Route::controller('utils/parser', 'Admin\Utils\ParserController');
		// Error log
		//Route::get('errorlog', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');
		Route::get('errorlog', [ 
			'middleware' => ['role:admin'], 
			'uses' => '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index' 
		]);

	});

	// Auth
	Route::auth();
});



// Other domains -------------------------------------------------------------------
Route::group([
	'prefix' => LaravelLocalization::setLocale(),
	'middleware' => [ 
		'web',
		'site.login.roles:company|employee',
		'site.setup',
		'site.setup.user',
	],
], function() {
	// Web
	Route::get('/', 'WebController@index');
	// Properties
	Route::get('properties', 'Web\PropertiesController@index');
	Route::post('property/{slug}', 'Web\PropertiesController@moreinfo');
	Route::get('property/{slug}', 'Web\PropertiesController@details');
	// Pages
	Route::post('pages/{slug}', 'Web\PagesController@post');
	Route::get('pages/{slug}', 'Web\PagesController@show');

	// Thumbnails
	Route::get('sites/{site_id}/properties/{property_id}/{flag}/{image}', 'Web\ThumbnailsController@property');

	// Auth
	Route::auth();

	// Autologin
	Route::get('account/autologin/{id}/{hash}', 'Auth\AuthController@autologin');

	// User
	Route::controller('customers', 'Web\CustomersController');

	// Feeds
	Route::controller('feeds', 'Web\FeedsController');

	// Account
	Route::group([
		'prefix' => 'account',
		'middleware' => [
			'auth.account',
		],
	], function() {
		Route::get('/', 'AccountController@index');
		Route::post('/', 'AccountController@updateProfile');
		// Properties
		Route::group([
			'middleware' => [
				'permission:property-view',
			],
		], function() {
			Route::controller('properties/documents', 'Account\Properties\DocumentsController');
		});
		Route::get('properties/leads/{slug}', 'Account\PropertiesController@getLeads');
		Route::get('properties/catch/close/{id}', 'Account\PropertiesController@getCatchClose');
		Route::post('properties/catch/close/{id}', 'Account\PropertiesController@postCatchClose');
		Route::get('properties/catch/{property_id}/{id?}', 'Account\PropertiesController@getCatch');
		Route::post('properties/catch/{property_id}/{id?}', 'Account\PropertiesController@postCatch');
		Route::post('properties/upload', 'Account\PropertiesController@postUpload');
		Route::get('properties/associate/{slug}', 'Account\PropertiesController@getAssociate');
		Route::get('properties/highlight/{slug}', 'Account\PropertiesController@getChangeHighlight');
		Route::get('properties/status/{slug}', 'Account\PropertiesController@getChangeStatus');
		Route::resource('properties', 'Account\PropertiesController');
		// Employees
		Route::get('employees/tickets/{email}', 'Account\EmployeesController@getTickets');
		Route::get('employees/associate/{email}', 'Account\EmployeesController@getAssociate');
		Route::post('employees/associate/{email}', 'Account\EmployeesController@postAssociate');
		Route::get('employees/disssociate/{user_id}/{property_id}', 'Account\EmployeesController@getDissociate');
		Route::resource('employees', 'Account\EmployeesController');
		// Customers
		Route::post('customers/properties/{slug}', 'Account\CustomersController@postAddPropertyCustomer');
		Route::get('customers/properties/{slug}', 'Account\CustomersController@getAddPropertyCustomer');
		Route::post('customers/profile/{email}', 'Account\CustomersController@postProfile');
		Route::resource('customers', 'Account\CustomersController');
		// Marketplaces
		Route::group([
			'middleware' => [
				'role:company',
			],
		], function() {
			Route::controller('marketplaces', 'Account\MarketplacesController');
		});
		// Tickets
		Route::controller('tickets', 'Account\TicketsController');
		// Reports
		Route::group([
			'prefix' => 'reports',
			'middleware' => [
				'role:company',
			],
		], function() {
			// Properties
			Route::controller('properties', 'Account\Reports\PropertiesController');
			// Agents
			Route::controller('agents', 'Account\Reports\AgentsController');
			// Leads
			Route::controller('leads', 'Account\Reports\LeadsController');
		});
		// Site configuration
		Route::group([
			'prefix' => 'site',
		], function() {
			// Configuration
			Route::controller('configuration', 'Account\Site\ConfigurationController');
			// Menus
			Route::post('menus/item/{slug}', 'Account\Site\MenusController@postItem');
			Route::resource('menus', 'Account\Site\MenusController');
			// Widgets
			Route::controller('widgets', 'Account\Site\WidgetsController');
			// Pages
			Route::resource('pages', 'Account\Site\PagesController');
		});
	});
});


// Ajax ----------------------------------------------------------------------------
Route::group([
	'namespace' => 'Ajax',
	'prefix' => 'ajax',
], function() {
	Route::controller('autotranslate', 'AutotranslateController');
	Route::controller('site', 'SiteController');
	Route::controller('user', 'UserController');
	Route::controller('geography', 'GeographyController');
});
