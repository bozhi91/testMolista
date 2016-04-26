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
		// Properties
		Route::resource('properties/services', 'Admin\Properties\ServicesController');
		Route::resource('properties', 'Admin\Properties\BaseController');
		// Configuration
		Route::resource('config/locales', 'Admin\Config\LocalesController');
		Route::resource('config/translations', 'Admin\Config\TranslationsController');
		// Utils
		Route::controller('utils/user', 'Admin\Utils\UserController');
		Route::controller('utils/locale', 'Admin\Utils\LocaleController');
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
	Route::get('property/{slug}', 'Web\PropertiesController@details');
	// Pages
	Route::post('pages/{slug}', 'Web\PagesController@post');
	Route::get('pages/{slug}', 'Web\PagesController@show');

	// Auth
	Route::auth();

	// Autologin
	Route::get('account/autologin/{id}/{hash}', 'Auth\AuthController@autologin');

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
		Route::get('properties/associate/{slug}', 'Account\PropertiesController@getAssociate');
		Route::resource('properties', 'Account\PropertiesController');
		// Employees
		Route::get('employees/associate/{email}', 'Account\EmployeesController@getAssociate');
		Route::post('employees/associate/{email}', 'Account\EmployeesController@postAssociate');
		Route::get('employees/disssociate/{user_id}/{property_id}', 'Account\EmployeesController@getDissociate');
		Route::resource('employees', 'Account\EmployeesController');
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
