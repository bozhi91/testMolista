<?php

	return [

		'pagination_perpage' => 10,
		'property_image_maxsize' => env('MAX_PROPERTY_IMAGE_SIZE',2048), //kilobytes
		'slider_image_maxsize' => env('MAX_SLIDER_IMAGE_SIZE',2048), //kilobytes
		
		'application_url' => env('APP_URL'),
		'application_protocol' => env('APP_PROTOCOL'),
		'application_domain' => env('APP_DOMAIN'),

		'google_maps_api_key' => env('GMAPS_API_KEY'),
		'microsoft_primary_account_key' => env('MICROSOFT_PRIMARY_ACCOUNT_KEY'),

		'ticketing_system_url' => env('TICKETING_SYSTEM_URL'),

		'phone_support' => env('WHITELABEL_SUPPORT_PHONE','93 180 70 20'),

		'lat_default' => '40.4636670',
		'lng_default' => '-3.7492200',


		/*
		|--------------------------------------------------------------------------
		| Application Environment
		|--------------------------------------------------------------------------
		|
		| This value determines the "environment" your application is currently
		| running in. This may determine how you prefer to configure various
		| services your application utilizes. Set this in your ".env" file.
		|
		*/

		'env' => env('APP_ENV', 'production'),

		/*
		|--------------------------------------------------------------------------
		| Application Debug Mode
		|--------------------------------------------------------------------------
		|
		| When your application is in debug mode, detailed error messages with
		| stack traces will be shown on every error that occurs within your
		| application. If disabled, a simple generic error page is shown.
		|
		*/

		'debug' => env('APP_DEBUG', false),

		/*
		|--------------------------------------------------------------------------
		| Application URL
		|--------------------------------------------------------------------------
		|
		| This URL is used by the console to properly generate URLs when using
		| the Artisan command line tool. You should set this to the root of
		| your application so that it is used when running Artisan tasks.
		|
		*/

		'url' => env('APP_URL', false), //=> 'http://molista.localhost/',

		/*
		|--------------------------------------------------------------------------
		| Application Timezone
		|--------------------------------------------------------------------------
		|
		| Here you may specify the default timezone for your application, which
		| will be used by the PHP date and date-time functions. We have gone
		| ahead and set this to a sensible default for you out of the box.
		|
		*/

		'timezone' => env('TIMEZONE', 'Europe/Madrid'),

		/*
		|--------------------------------------------------------------------------
		| Application Locale Configuration
		|--------------------------------------------------------------------------
		|
		| The application locale determines the default locale that will be used
		| by the translation service provider. You are free to set this value
		| to any of the locales which will be supported by the application.
		|
		*/

		'locale' => env('LOCALE_DEFAULT','es'),

		/*
		|--------------------------------------------------------------------------
		| Application Fallback Locale
		|--------------------------------------------------------------------------
		|
		| The fallback locale determines the locale to use when the current one
		| is not available. You may change the value to correspond to any of
		| the language folders that are provided through your application.
		|
		*/

		'fallback_locale' => env('LOCALE_FALLBACK_DEFAULT','es'),

		/*
		|--------------------------------------------------------------------------
		| Encryption Key
		|--------------------------------------------------------------------------
		|
		| This key is used by the Illuminate encrypter service and should be set
		| to a random, 32 character string, otherwise these encrypted strings
		| will not be safe. Please do this before deploying an application!
		|
		*/

		'key' => env('APP_KEY'),

		'cipher' => 'AES-256-CBC',

		/*
		|--------------------------------------------------------------------------
		| Logging Configuration
		|--------------------------------------------------------------------------
		|
		| Here you may configure the log settings for your application. Out of
		| the box, Laravel uses the Monolog PHP logging library. This gives
		| you a variety of powerful log handlers / formatters to utilize.
		|
		| Available Settings: "single", "daily", "syslog", "errorlog"
		|
		*/

		'log' => env('APP_LOG', 'daily'),

		/*
		|--------------------------------------------------------------------------
		| Autoloaded Service Providers
		|--------------------------------------------------------------------------
		|
		| The service providers listed here will be automatically loaded on the
		| request to your application. Feel free to add your own services to
		| this array to grant expanded functionality to your applications.
		|
		*/

		'providers' => [

			/*
			* Laravel Framework Service Providers...
			*/
			Illuminate\Auth\AuthServiceProvider::class,
			Illuminate\Broadcasting\BroadcastServiceProvider::class,
			Illuminate\Bus\BusServiceProvider::class,
			Illuminate\Cache\CacheServiceProvider::class,
			Illuminate\Foundation\Providers\ConsoleSupportServiceProvider::class,
			Illuminate\Cookie\CookieServiceProvider::class,
			Illuminate\Database\DatabaseServiceProvider::class,
			Illuminate\Encryption\EncryptionServiceProvider::class,
			Illuminate\Filesystem\FilesystemServiceProvider::class,
			Illuminate\Foundation\Providers\FoundationServiceProvider::class,
			Illuminate\Hashing\HashServiceProvider::class,
			Illuminate\Mail\MailServiceProvider::class,
			Illuminate\Pagination\PaginationServiceProvider::class,
			Illuminate\Pipeline\PipelineServiceProvider::class,
			Illuminate\Queue\QueueServiceProvider::class,
			Illuminate\Redis\RedisServiceProvider::class,
			Illuminate\Auth\Passwords\PasswordResetServiceProvider::class,
			Illuminate\Session\SessionServiceProvider::class,
			Illuminate\Translation\TranslationServiceProvider::class,
			Illuminate\Validation\ValidationServiceProvider::class,
			Illuminate\View\ViewServiceProvider::class,

			/*
			* Application Service Providers...
			*/
			App\Providers\AppServiceProvider::class,
			App\Providers\AuthServiceProvider::class,
			App\Providers\EventServiceProvider::class,
			App\Providers\RouteServiceProvider::class,

			/* https://github.com/Illuminate/HTML */
			Collective\Html\HtmlServiceProvider::class,

			/* https://github.com/barryvdh/laravel-debugbar */
			Barryvdh\Debugbar\ServiceProvider::class,

			/* https://github.com/mcamara/laravel-localization */
			Mcamara\LaravelLocalization\LaravelLocalizationServiceProvider::class,

			/* https://github.com/Zizaco/entrust */
			Zizaco\Entrust\EntrustServiceProvider::class,

			/* https://github.com/igaster/laravel-theme */
			igaster\laravelTheme\themeServiceProvider::class,

			/* https://github.com/dimsav/laravel-translatable */
			Dimsav\Translatable\TranslatableServiceProvider::class,

			/* https://github.com/cviebrock/eloquent-sluggable */
			Cviebrock\EloquentSluggable\SluggableServiceProvider::class,

			/* https://github.com/Intervention/image */
			Intervention\Image\ImageServiceProvider::class,

			/* https://github.com/mewebstudio/Purifier */
			Mews\Purifier\PurifierServiceProvider::class,

			/* https://github.com/rap2hpoutre/laravel-log-viewer */
			Rap2hpoutre\LaravelLogViewer\LaravelLogViewerServiceProvider::class,

			/* https://github.com/owen-it/laravel-auditing */
			OwenIt\Auditing\AuditingServiceProvider::class,

			/* https://github.com/barryvdh/laravel-dompdf */
			Barryvdh\DomPDF\ServiceProvider::class,

			/* https://laravel.com/docs/5.2/billing */
			Laravel\Cashier\CashierServiceProvider::class,

			/* https://github.com/yangqi/Htmldom */
			Yangqi\Htmldom\HtmldomServiceProvider::class,

			/* https://github.com/SimpleSoftwareIO/simple-qrcode */
			SimpleSoftwareIO\QrCode\QrCodeServiceProvider::class,

			/* https://github.com/Torann/laravel-geoip */
			Torann\GeoIP\GeoIPServiceProvider::class,

			/* https://github.com/florianv/laravel-swap */
			Florianv\LaravelSwap\SwapServiceProvider::class,

			/* https://github.com/tremby/laravel-queue-monitor */
			Tremby\QueueMonitor\ServiceProvider::class,

			/* https://github.com/alexpechkarev/google-geocoder */
			Alexpechkarev\GoogleGeocoder\GoogleGeocoderServiceProvider::class,

			/* https://github.com/incubout/heartbeats */
			Incubout\Heartbeats\HeartbeatsServiceProvider::class,

			/* https://github.com/prodeveloper/social-share */
			Chencha\Share\ShareServiceProvider::class,
			
			/* https://github.com/chriskonnertz/open-graph */
			ChrisKonnertz\OpenGraph\OpenGraphServiceProvider::class,
		],

		/*
		|--------------------------------------------------------------------------
		| Class Aliases
		|--------------------------------------------------------------------------
		|
		| This array of class aliases will be registered when this application
		| is started. However, feel free to register as many as you wish as
		| the aliases are "lazy" loaded so they don't hinder performance.
		|
		*/

		'aliases' => [

			'App'       => Illuminate\Support\Facades\App::class,
			'Artisan'   => Illuminate\Support\Facades\Artisan::class,
			'Auth'      => Illuminate\Support\Facades\Auth::class,
			'Blade'     => Illuminate\Support\Facades\Blade::class,
			'Cache'     => Illuminate\Support\Facades\Cache::class,
			'Config'    => Illuminate\Support\Facades\Config::class,
			'Cookie'    => Illuminate\Support\Facades\Cookie::class,
			'Crypt'     => Illuminate\Support\Facades\Crypt::class,
			'DB'        => Illuminate\Support\Facades\DB::class,
			'Eloquent'  => Illuminate\Database\Eloquent\Model::class,
			'Event'     => Illuminate\Support\Facades\Event::class,
			'File'      => Illuminate\Support\Facades\File::class,
			'Gate'      => Illuminate\Support\Facades\Gate::class,
			'Hash'      => Illuminate\Support\Facades\Hash::class,
			'Input'     => Illuminate\Support\Facades\Input::class,
			'Lang'      => Illuminate\Support\Facades\Lang::class,
			'Log'       => Illuminate\Support\Facades\Log::class,
			'Mail'      => Illuminate\Support\Facades\Mail::class,
			'Password'  => Illuminate\Support\Facades\Password::class,
			'Queue'     => Illuminate\Support\Facades\Queue::class,
			'Redirect'  => Illuminate\Support\Facades\Redirect::class,
			'Redis'     => Illuminate\Support\Facades\Redis::class,
			'Request'   => Illuminate\Support\Facades\Request::class,
			'Response'  => Illuminate\Support\Facades\Response::class,
			'Route'     => Illuminate\Support\Facades\Route::class,
			'Schema'    => Illuminate\Support\Facades\Schema::class,
			'Session'   => Illuminate\Support\Facades\Session::class,
			'Storage'   => Illuminate\Support\Facades\Storage::class,
			'URL'       => Illuminate\Support\Facades\URL::class,
			'Validator' => Illuminate\Support\Facades\Validator::class,
			'View'      => Illuminate\Support\Facades\View::class,
			
			/* Own */
			'SiteCustomer' => App\Session\SiteCustomer::class,

			/* https://github.com/Illuminate/HTML */
			'Form' => Collective\Html\FormFacade::class,
			'Html' => Collective\Html\HtmlFacade::class,

			/* https://github.com/barryvdh/laravel-debugbar */
			'Debugbar' => Barryvdh\Debugbar\Facade::class,

			/* https://github.com/mcamara/laravel-localization */
			'LaravelLocalization' => Mcamara\LaravelLocalization\Facades\LaravelLocalization::class,

			/* https://github.com/Zizaco/entrust */
			'Entrust'   => Zizaco\Entrust\EntrustFacade::class,

			/* https://github.com/igaster/laravel-theme */
			'Theme' => igaster\laravelTheme\Facades\Theme::class,

			/* https://github.com/Intervention/image */
			'Image' => Intervention\Image\Facades\Image::class,

			/* https://github.com/mewebstudio/Purifier */
			'Purifier' => Mews\Purifier\Facades\Purifier::class,

			/* https://github.com/barryvdh/laravel-dompdf */
			'PDF' => Barryvdh\DomPDF\Facade::class,

			/* https://github.com/yangqi/Htmldom */
			'Htmldom' => Yangqi\Htmldom\Htmldom::class,

			/* https://github.com/SimpleSoftwareIO/simple-qrcode */
			'QrCode' => SimpleSoftwareIO\QrCode\Facades\QrCode::class,

			/* https://github.com/Torann/laravel-geoip */
			'GeoIP' => Torann\GeoIP\GeoIPFacade::class,

			/* https://github.com/florianv/laravel-swap */
			'Swap' => Florianv\LaravelSwap\Facades\Swap::class,
			
			/* https://github.com/prodeveloper/social-share */
			'Share'	=> Chencha\Share\ShareFacade::class,
			
			/* https://github.com/chriskonnertz/open-graph */
			'OpenGraph' => \ChrisKonnertz\OpenGraph\OpenGraph::class,
		],

	];
