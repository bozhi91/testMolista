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
		'site.login.roles:admin|translator|franchisee',
		'setTheme:corporate',
	],
], function() {

	// Corporate web
	Route::group([
		'middleware' => [
			'geolocation',
			'currency.corporate',
		],
	], function() {
		Route::get('/', 'CorporateController@index');
		Route::controller('demo', 'Corporate\DemoController');
		Route::controller('info', 'Corporate\InfoController');
		Route::controller('pricing', 'Corporate\PricingController');
		Route::get('features/{slug?}', 'Corporate\FeaturesController@getIndex');
		// Signup
		Route::controller('signup', 'Corporate\SignupController');
		// Customers area
		Route::controller('customers', 'Corporate\CustomersController');
	});

	// Resellers
	Route::controller('resellers/auth', 'Resellers\AuthController');
	Route::group([
		'prefix' => 'resellers',
			'middleware' => [
				'auth.reseller',
				'setTheme:resellers',
			],
	], function() {
		Route::controller('/', 'ResellersController');
	});

	// Admin
	Route::group([
		'prefix' => 'admin',
		'middleware' => [
			'auth.admin',
			'role:admin|translator|franchisee',
			'setTheme:admin',
		],
	], function() {
		Route::get('/', 'AdminController@index');

		// Sites
		Route::controller('sites/payments', 'Admin\Sites\PaymentsController');
		Route::get('sites/invoice/{id}/{file?}', 'Admin\SitesController@getInvoice');
		Route::post('sites/invoice/{id}', 'Admin\SitesController@postInvoice');
		Route::delete('sites/invoice/{id}', 'Admin\SitesController@deleteInvoice');
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
		// Resellers
		Route::get('resellers/validate/{type}', 'Admin\ResellersController@getValidate');
		Route::controller('resellers/payments', 'Admin\Resellers\PaymentsController');
		Route::resource('resellers', 'Admin\ResellersController');
		// Configuration
		Route::resource('config/locales', 'Admin\Config\LocalesController');
		Route::resource('config/translations', 'Admin\Config\TranslationsController');
		Route::get('config/plans/check/{type}', 'Admin\Config\PlansController@getCheck');
		Route::resource('config/plans', 'Admin\Config\PlansController');
		Route::get('config/currencies/check/{type}', 'Admin\Config\CurrenciesController@getCheck');
		Route::resource('config/currencies', 'Admin\Config\CurrenciesController');
		// Utils
		Route::controller('utils/user', 'Admin\Utils\UserController');
		Route::controller('utils/locale', 'Admin\Utils\LocaleController');
		Route::controller('utils/parser', 'Admin\Utils\ParserController');
		// Plan change requests
		Route::group([
			'middleware' => [ 'permission:planchange-aproove' ]
		], function() {
			Route::controller('planchange', 'Admin\PlanchangeController');
		});
		// Plan expirations
		Route::group([
			'middleware' => [ 'permission:expirations-*' ]
		], function() {
			Route::controller('expirations', 'Admin\ExpirationsController');
		});
		Route::group([
			'middleware' => [
				'permission:geography-*',
			],
		], function() {
			Route::get('geography/countries/check/{type}', 'Admin\Geography\CountriesController@getCheck');
			Route::resource('geography/countries', 'Admin\Geography\CountriesController');
		});
		// Error log
		Route::get('errorlog', [
			'middleware' => ['role:admin'],
			'uses' => '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index'
		]);

		// Queue monitor
		Route::get('queue-monitor', function () {
		    return Response::view('queue-monitor::status-page');
		});

	});

	// Auth
	Route::auth();
});

// Queue monitor service
Route::get('queue-monitor.json', function () {
	$response = Response::view('queue-monitor::status-json');
	$response->header('Content-Type', 'application/json');
	return $response;
});

// Other domains -------------------------------------------------------------------
Route::group([
	'prefix' => LaravelLocalization::setLocale(),
	'middleware' => [
		'web',
		'site.login.roles:company|employee',
		'site.setup',
		'site.autologin',
		'site.setup.user',
		'currency.site',
	],
], function() {
	// Web
	Route::get('/', 'WebController@index');
	// Properties
	Route::get('properties', 'Web\PropertiesController@index');
	Route::get('property/{slug}/property-{locale}.pdf', 'Web\PropertiesController@downloads');
	Route::post('property/{slug}', 'Web\PropertiesController@moreinfo');
	Route::get('property/{slug}', 'Web\PropertiesController@details');
	// Pages
	Route::post('pages/{slug}', 'Web\PagesController@post');
	Route::get('pages/{slug}', 'Web\PagesController@show');

	// Thumbnails
	Route::get('sites/{site_id}/properties/{property_id}/{flag}/{image}', 'Web\ThumbnailsController@property');

	// Auth
	Route::auth();

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
		Route::controller('profile/signatures', 'Account\Profile\SignaturesController');
		Route::controller('profile/email-accounts', 'Account\Profile\AccountsController');
		Route::group([
			'middleware' => [
				'role:company',
			],
		], function() {
			// Plans & payment
			Route::controller('payment', 'Account\PaymentController');
			// Invoices
			Route::controller('invoices', 'Account\InvoicesController');
		});

		// Properties
		Route::group([
			'middleware' => [
				'permission:property-view',
			],
		], function() {
			Route::controller('properties/documents', 'Account\Properties\DocumentsController');
		});

		Route::post('properties/comment/{slug}', 'Account\PropertiesController@postComment');

		Route::group([
			'middleware' => [
				'permission:property-create',
			],
		], function() {
			Route::controller('properties/imports', 'Account\Properties\ImportsController');
		});

		Route::get('properties/leads/{slug}', 'Account\PropertiesController@getLeads');
		Route::get('properties/catch/close/{id}', 'Account\PropertiesController@getCatchClose');
		Route::post('properties/catch/close/{id}', 'Account\PropertiesController@postCatchClose');
		Route::get('properties/catch/{property_id}/{id?}', 'Account\PropertiesController@getCatch');
		Route::post('properties/catch/{property_id}/{id?}', 'Account\PropertiesController@postCatch');
		Route::post('properties/upload', 'Account\PropertiesController@postUpload');
		Route::get('properties/associate/{slug}', 'Account\PropertiesController@getAssociate');
		Route::get('properties/homeslider/{slug}', 'Account\PropertiesController@getChangeHomeSlider');
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
		Route::delete('customers/properties/{slug}', 'Account\CustomersController@deleteRemovePropertyCustomer');
		Route::put('customers/properties/{slug}', 'Account\CustomersController@putUndiscardPropertyCustomer');
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
		// Calendar
		Route::controller('calendar', 'Account\Calendar\BaseController');
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
			// Domain name
			Route::controller('domain', 'Account\Site\DomainNameController');
			// Price ranges
			Route::controller('priceranges', 'Account\Site\PriceRangesController');
			// Countries
			Route::controller('countries', 'Account\Site\CountriesController');
			// Menus
			Route::post('menus/item/{slug}', 'Account\Site\MenusController@postItem');
			Route::resource('menus', 'Account\Site\MenusController');
			// Widgets
			Route::controller('widgets', 'Account\Site\WidgetsController');
			// Pages
			Route::resource('pages', 'Account\Site\PagesController');
			//Sliders
			Route::post('sliders/upload', 'Account\Site\SlidersController@upload');
			Route::resource('sliders', 'Account\Site\SlidersController');
		});
		Route::controller('visits', 'Account\Visits\AjaxController');
	});
});

// Stripe --------------------------------------------------------------------------
Route::post('stripe/webhook','Stripe\WebhookController@handleWebhook');
Route::controller('stripe', 'StripeController');


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
