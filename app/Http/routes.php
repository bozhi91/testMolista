<?php

// Get corporate domain domain -----------------------------------------------------
$domain = parse_url( Config::get('app.application_url', false) );
if ( empty($domain['host']) )
{
	die("Environment variable APP_URL is not defined!");
}


Route::get('hubspot', function()
{
	try
	{
		HubSpot::contacts()->getByEmail('albert+prueba2@incubout.com');
	}
	catch (\Exception $e)
	{
		// Not found, then create the lead
		HubSpot::contacts()->createOrUpdate('albert+prueba2@incubout.com', [
			[
				'property' => 'hs_lead_status',
				'value' => 'NEW'
			],
			[
				'property' => 'lifecyclestage',
				'value' => 'opportunity'
			],
			[
				'property' => 'product',
				'value' => 'Molista'
			],
		]);
	}


	return HubSpot::contacts()->getByEmail('albert+prueba@incubout.com');

});

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
			'hubspot.redirect',
		],
	], function() {
		Route::get('/', 'CorporateController@index');
		Route::controller('demo', 'Corporate\DemoController');
		Route::controller('distribuitors', 'Corporate\DistribuitorController');
		Route::controller('info', 'Corporate\InfoController');
		Route::controller('pricing', 'Corporate\PricingController');
		Route::get('features/{slug?}', 'Corporate\FeaturesController@getIndex');
		// Signup
		Route::controller('signup', 'Corporate\SignupController');
		// Customers area
		Route::controller('customers', 'Corporate\CustomersController');
		// Landing
		Route::controller('starter', 'Corporate\LandingController');
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

	// Thumbnails
	Route::get('sites/{site_id}/properties/{property_id}/{flag}/{image}', 'Web\ThumbnailsController@property');

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
		Route::get('sites/downgrade/{id}', 'Admin\SitesController@getDowngrade');
		Route::get('sites/update-setup/{id}', 'Admin\SitesController@getUpdateSetup');
		Route::post('sites/invoice/{id}', 'Admin\SitesController@postInvoice');
		Route::delete('sites/invoice/{id}', 'Admin\SitesController@deleteInvoice');
		Route::get('sites/comments/{id}', 'Admin\SitesController@comments');
		Route::post('sites/comments/{id}', 'Admin\SitesController@add_comment');
		Route::resource('sites', 'Admin\SitesController');

		Route::get('sites/getMaxLanguages/{id}', 'Admin\SitesController@getMaxLanguages');
        Route::get('sites/verifyPlan/{id}', 'Admin\SitesController@verifyPlan');

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
		// Plan expirations
		Route::group([
			'middleware' => [ 'permission:reports-*' ]
		], function() {
			Route::controller('reports/themes', 'Admin\Reports\ThemesController');
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

	// Feeds
	Route::get('feeds/yaencontre.php', 'Web\FeedsController@yaencontre');
	Route::get('feeds/{code}/{hash}', 'Web\FeedsController@unifiedFeed');

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
	// Info
	Route::controller('info', 'Web\InfoController');
	// Properties
	Route::get('properties', 'Web\PropertiesController@index');
	Route::get('property/{slug}/property-{locale}.pdf', 'Web\PropertiesController@downloads');
	Route::post('property/{slug}', 'Web\PropertiesController@moreinfo');
	Route::post('property/share/{slug}', 'Web\PropertiesController@sharefriend');
	Route::get('property/{slug}/{id?}', 'Web\PropertiesController@details');
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

	// Themes Custom
	Route::controller('custom/servicios', 'Web\Custom\ServiciosController');

	// Account
	Route::group([
		'prefix' => 'account',
		'middleware' => [
			'auth.account',
		],
	], function() {
		Route::get('/', 'Account\ReportsController@getIndex');

		Route::get('profile', 'AccountController@index');
		Route::post('profile', 'AccountController@updateProfile');
		Route::controller('profile/signatures', 'Account\Profile\SignaturesController');
		Route::controller('profile/email-accounts', 'Account\Profile\AccountsController');
		Route::group([
			'middleware' => [
				'role:company',
			],
		], function() {
			Route::controller('profile/plan', 'Account\Profile\PlanController');
			Route::controller('profile/invoices', 'Account\Profile\InvoicesController');
			// Plans & payment
			Route::controller('payment', 'Account\PaymentController');
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
		Route::post('properties/nota/{slug}', 'Account\PropertiesController@postNota');

		Route::group([
			'middleware' => [
				'permission:property-create',
			],
		], function() {
			Route::controller('properties/imports', 'Account\Properties\ImportsController');
			Route::controller('properties/districts', 'Account\Properties\DistrictsController');
		});

		Route::get('properties/leads/{slug}', 'Account\PropertiesController@getLeads');
		Route::get('properties/catch/close/{id}/{client_id?}', 'Account\PropertiesController@getCatchClose');
		Route::post('properties/catch/close/{id}', 'Account\PropertiesController@postCatchClose');
		Route::get('properties/catch/{property_id}/{id?}', 'Account\PropertiesController@getCatch');
		Route::post('properties/catch/{property_id}/{id?}', 'Account\PropertiesController@postCatch');
		Route::post('properties/upload', 'Account\PropertiesController@postUpload');
		Route::get('properties/associate/{slug}', 'Account\PropertiesController@getAssociate');
		Route::get('properties/homeslider/{slug}', 'Account\PropertiesController@getChangeHomeSlider');
		Route::get('properties/highlight/{slug}', 'Account\PropertiesController@getChangeHighlight');
		Route::get('properties/status/{slug}', 'Account\PropertiesController@getChangeStatus');
		Route::get('properties/{slug}/property-{locale}.pdf', 'Account\PropertiesController@download');
		Route::resource('properties', 'Account\PropertiesController');
		Route::get('properties/create/{slug?}', 'Account\PropertiesController@create');
		// Employees
		Route::get('employees/tickets/{email}', 'Account\EmployeesController@getTickets');
		Route::get('employees/associate/{email}', 'Account\EmployeesController@getAssociate');
		Route::post('employees/associate/{email}', 'Account\EmployeesController@postAssociate');
		Route::get('employees/disssociate/{user_id}/{property_id}', 'Account\EmployeesController@getDissociate');
		Route::get('employees/relation/{email}/{property_id}', 'Account\EmployeesController@getChangeRelation');
		Route::resource('employees', 'Account\EmployeesController');
		// Customers
		Route::delete('customers/properties/{slug}', 'Account\CustomersController@deleteRemovePropertyCustomer');
		Route::put('customers/properties/{slug}', 'Account\CustomersController@putUndiscardPropertyCustomer');
		Route::post('customers/properties/{slug}', 'Account\CustomersController@postAddPropertyCustomer');
		Route::get('customers/properties/{slug}', 'Account\CustomersController@getAddPropertyCustomer');
		Route::post('customers/profile/{email}', 'Account\CustomersController@postProfile');
		Route::get('customers/status/{email}', 'Account\CustomersController@getChangeStatus');
		Route::resource('customers', 'Account\CustomersController');

		Route::post('customers/comment/{slug}', 'Account\CustomersController@postComment');
		Route::post('customers/general/{slug}', 'Account\CustomersController@postGeneral');

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
			'middleware' => [
				'role:company',
			],
		], function() {
			// Properties
			Route::controller('reports/properties', 'Account\Reports\PropertiesController');
			// Agents
			Route::controller('reports/agents', 'Account\Reports\AgentsController');
			// Leads
			Route::controller('reports/leads', 'Account\Reports\LeadsController');
			// Referers
			Route::controller('reports/referers', 'Account\Reports\ReferersController');
		});
		// Reports home
		Route::controller('reports', 'Account\ReportsController');

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

			//Blog routes. create,list,update,delete,etc.
            Route::post('storeBlog', 'Account\Site\BlogController@storeBlog')->name("storeBlog");
            Route::post('deletePost', 'Account\Site\BlogController@deletePost')->name("deletePost");
            Route::get('createNewPost', 'Account\Site\BlogController@createNewPost')->name("createNewPost");
            Route::get('createNewBlog', 'Account\Site\BlogController@createNewBlog')->name("createNewBlog");
            Route::get('listPosts', 'Account\Site\BlogController@listPosts')->name("listPosts");
            Route::post('storePost', 'Account\Site\BlogController@storePost')->name("storePost");
            Route::post('updatePost', 'Account\Site\BlogController@updatePost')->name("updatePost");
            Route::get('getSiteById', 'Account\Site\BlogController@getSiteById')->name("getSiteById");


            //Property routes
            Route::post('publishProperty', 'Account\PropertiesController@publishProperty')->name("publishProperty");
            Route::get('viewPropertyInWeb/{id?}', 'Account\PropertiesController@viewPropertyInWeb')->name("viewPropertyInWeb");

            //Sliders
			Route::post('sliders/upload', 'Account\Site\SlidersController@upload');
			Route::resource('sliders', 'Account\Site\SlidersController');

			//get a list of the allowed translations.
            Route::get('configuration/getAllowedTranslations/{id}', 'Admin\SitesController@getAllowedTranslations');

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

