<?php

return [

	/*
	|--------------------------------------------------------------------------
	| Switch this package on/off. Usefull for testing...
	|--------------------------------------------------------------------------
	*/

	'enabled' => true,

    /*
    |--------------------------------------------------------------------------
    | File path where themes will be located.
    | Can be outside default views path EG: resources/themes
    | Leave it null if you place your themes in the default views folder 
    | (as defined in config\views.php)
    |--------------------------------------------------------------------------
    */

    'themes_path' => null, // eg: realpath(base_path('resources/themes'))

	/*
	|--------------------------------------------------------------------------
	| Set behavior if an asset is not found in a Theme hierarcy.
	| Available options: THROW_EXCEPTION | LOG_ERROR | IGNORE
	|--------------------------------------------------------------------------
	*/

	'asset_not_found' => 'LOG_ERROR',

	/*
	|--------------------------------------------------------------------------
	| Set the Active Theme. Can be set at runtime with:
	|  Themes::set('theme-name');
	|--------------------------------------------------------------------------
	*/

	'active' => 'default',

	/*
	|--------------------------------------------------------------------------
	| Define available themes. Format:
	|
	| 	'theme-name' => [
	| 		'extends'	 	=> 'theme-to-extend',  // optional
	| 		'views-path' 	=> 'path-to-views',    // defaults to: resources/views/theme-name
	| 		'asset-path' 	=> 'path-to-assets',   // defaults to: public/theme-name
	|
	|		// you can add your own custom keys and retrieve them with Theme::config('key');
	| 	],
	|
	|--------------------------------------------------------------------------
	*/

	'themes' => [

		'default' => [
			'title'			=> 'Florecilla',
			'public'		=> true, // true if companies can use it
			'custom'		=> false, // true if custom theme for specific companies
			'extends'	 	=> null,
			'views-path' 	=> '',
			'asset-path' 	=> '',
			'widget-groups'	=> [
									'header' => [
										'accept' => 'menu',
										'max' => 1,
									],
									'home' => [
										'accept' => 'slider',
										'max' => 1,
									],
									'home-footer' => [
										'accept' => 'awesome-link',
									],
									'footer' => [
									],
								],
			'label-palette'	=> [ '#337884', '#30ac4a', '#993556', '#b8a528' ],
			'gmaps-style'	=> '[{"featureType":"water","elementType":"geometry","stylers":[{"color":"#e9e9e9"},{"lightness":17}]},{"featureType":"landscape","elementType":"geometry","stylers":[{"color":"#f5f5f5"},{"lightness":20}]},{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"color":"#ffffff"},{"lightness":17}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"color":"#ffffff"},{"lightness":29},{"weight":0.2}]},{"featureType":"road.arterial","elementType":"geometry","stylers":[{"color":"#ffffff"},{"lightness":18}]},{"featureType":"road.local","elementType":"geometry","stylers":[{"color":"#ffffff"},{"lightness":16}]},{"featureType":"poi","elementType":"geometry","stylers":[{"color":"#f5f5f5"},{"lightness":21}]},{"featureType":"poi.park","elementType":"geometry","stylers":[{"color":"#dedede"},{"lightness":21}]},{"elementType":"labels.text.stroke","stylers":[{"visibility":"on"},{"color":"#ffffff"},{"lightness":16}]},{"elementType":"labels.text.fill","stylers":[{"saturation":36},{"color":"#333333"},{"lightness":40}]},{"elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"transit","elementType":"geometry","stylers":[{"color":"#f2f2f2"},{"lightness":19}]},{"featureType":"administrative","elementType":"geometry.fill","stylers":[{"color":"#fefefe"},{"lightness":20}]},{"featureType":"administrative","elementType":"geometry.stroke","stylers":[{"color":"#fefefe"},{"lightness":17},{"weight":1.2}]}]',
			'gmaps-circle'	=> '#7e1f31',
			'gmaps-marker'	=> 'images/properties/marker.png',
		],

		'corporate' => [
			'title'			=> 'Corporate',
			'public'		=> false,
			'personal'		=> false,
			'extends'	 	=> 'default',
			'views-path' 	=> 'corporate',
			'asset-path' 	=> 'corporate',
		],
		'resellers' => [
			'title'			=> 'Resellers',
			'public'		=> false,
			'personal'		=> false,
			'extends'	 	=> 'corporate',
			'views-path' 	=> 'resellers',
			'asset-path' 	=> 'resellers',
		],

		'bellamar' => [
			'title'			=> 'Bellamar',
			'public'		=> false,
			'custom'		=> true,
			'extends'	 	=> 'default',
			'views-path' 	=> 'themes/bellamar',
			'asset-path' 	=> 'themes/bellamar',
		],

		'elegant' => [
			'title'			=> 'Elegant',
			'public'		=> true,
			'custom'		=> false,
			'extends'	 	=> 'default',
			'views-path' 	=> 'themes/elegant',
			'asset-path' 	=> 'themes/elegant',
			'gmaps-circle'	=> '#acbb00',
		],

		'inmoblue' => [
			'title'			=> 'Inmoblue',
			'public'		=> true,
			'custom'		=> false,
			'extends'	 	=> 'default',
			'views-path' 	=> 'themes/inmoblue',
			'asset-path' 	=> 'themes/inmoblue',
			'gmaps-circle'	=> '#ea1e63',
		],

		'whitey' => [
			'title'			=> 'Whitey',
			'public'		=> true,
			'custom'		=> false,
			'extends'	 	=> 'default',
			'views-path' 	=> 'themes/whitey',
			'asset-path' 	=> 'themes/whitey',
			'gmaps-circle'	=> '#efb817',
		],

		'zoner' => [
			'title'			=> 'Zoner',
			'public'		=> true,
			'custom'		=> false,
			'extends'	 	=> 'default',
			'views-path' 	=> 'themes/zoner',
			'asset-path' 	=> 'themes/zoner',
			'gmaps-circle'	=> '#1396e2',
		],

		'sky' => [
			'title'			=> 'Sky',
			'public'		=> true,
			'custom'		=> false,
			'extends'	 	=> 'default',
			'views-path' 	=> 'themes/sky',
			'asset-path' 	=> 'themes/sky',
			'gmaps-circle'	=> '#1396e2',
		],

		'redly' => [
			'title'			=> 'Redly',
			'public'		=> true,
			'custom'		=> false,
			'extends'	 	=> 'default',
			'views-path' 	=> 'themes/redly',
			'asset-path' 	=> 'themes/redly',
			'gmaps-circle'	=> '#efb817',
		],

		'white-cloud' => [
			'title'			=> 'White Cloud',
			'public'		=> true,
			'custom'		=> false,
			'extends'	 	=> 'default',
			'views-path' 	=> 'themes/white-cloud',
			'asset-path' 	=> 'themes/white-cloud',
			'gmaps-circle'	=> '#efb817',
		],

		// Add your themes here...

		/*--------------[ Example Structre ]-------------

			// Recomended (all defaults) : Assets -> \public\BasicTheme , Views -> \resources\views\BasicTheme

			'BasicTheme',


			// This theme shares the views with BasicTheme but defines its own assets in \public\SomeTheme

			'SomeTheme' => [
				'views-path'	=> 'BasicTheme',
			],


			// This theme extends BasicTheme and ovverides SOME views\assets in its folders

			'AnotherTheme' => [
				'extends'	=> 'BasicTheme',
			],

		------------------------------------------------*/
	],

];
