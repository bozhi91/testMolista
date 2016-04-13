<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
	/**
	* The application's global HTTP middleware stack.
	*
	* These middleware are run during every request to your application.
	*
	* @var array
	*/
	protected $middleware = [
		\Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
	];

	/**
	* The application's route middleware groups.
	*
	* @var array
	*/
	protected $middlewareGroups = [
		'web' => [
			\App\Http\Middleware\EncryptCookies::class,
			\Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
			\Illuminate\Session\Middleware\StartSession::class,
			\Illuminate\View\Middleware\ShareErrorsFromSession::class,
			\App\Http\Middleware\VerifyCsrfToken::class,
		],

		'api' => [
			'throttle:60,1',
		],
	];

	/**
	* The application's route middleware.
	*
	* These middleware may be assigned to groups or used individually.
	*
	* @var array
	*/
	protected $routeMiddleware = [
		'auth' => \App\Http\Middleware\Authenticate::class,
		'auth.admin' => \App\Http\Middleware\AuthenticateAdmin::class,
		'auth.account' => \App\Http\Middleware\AuthenticateAccount::class,
		'auth.api' => \App\Http\Middleware\AuthenticateApi::class,
		'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
		'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
		'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,

		'site.setup' => \App\Http\Middleware\SiteSetup::class,
		'site.setup.user' => \App\Http\Middleware\SiteSetupUser::class,
		'site.login.roles' => \App\Http\Middleware\SiteLoginRoles::class,

		'property.permission' => \App\Http\Middleware\PropertyPermission::class,

		/* https://github.com/Zizaco/entrust */
		'role' => \Zizaco\Entrust\Middleware\EntrustRole::class,
		'permission' => \Zizaco\Entrust\Middleware\EntrustPermission::class,
		'ability' => \Zizaco\Entrust\Middleware\EntrustAbility::class,

		/* https://github.com/igaster/laravel-theme */
		'setTheme' => \igaster\laravelTheme\Middleware\setTheme::class,
	];

}