// Comment to see notifications
process.env.DISABLE_NOTIFIER = true; 

var elixir = require('laravel-elixir');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */

elixir.config.sourcemaps = false;

elixir(function(mix) {


	/* Common files */
	mix
		.less([
			'common.less',
		], 'resources/assets/css/compiled/common.css')		
		.scripts([
			'moment-with-locales.js',
			'jquery-1.12.1.js',
			'jquery-ui.js',
			'../bootstrap-3.3.6/dist/js/bootstrap.min.js',
			'jquery-validation-1.15.0/dist/jquery.validate.js',
			'jquery-validation-1.15.0/dist/additional-methods.js',
			'jquery.bootstrap-growl.js',
			'jquery.magnific-popup.js',
			'jquery.hideShowPassword.js',
			'jquery.matchHeight.js',
			'bootstrap-table.js',
			'bootstrap-datetimepicker.js',
			'bootstrap-slider.js',
			'summernote.js',
			'alertify.js',
			'select2.full.js',
			'loading.js',
			'tmpl.js',
			'spectrum.js',
			'dropzone.js',
			'common.js'
		], 'resources/assets/js/compiled/common.js');



	/* App */
	mix
		.less([
			'app.less',
		], 'resources/assets/css/compiled/app.css')
		.styles([
			'compiled/common.css',
			'compiled/app.css',
		], 'public/compiled/css/app.css')
		.scripts([
			'compiled/common.js',
			'app.js',
		], 'public/compiled/js/app.js');



	/* Corporate */
	mix
		.less([
			'corporate.less',
		], 'resources/assets/css/compiled/corporate.css')
		.styles([
			'compiled/common.css',
			'compiled/corporate.css',
		], 'public/compiled/css/corporate.css')
		.scripts([
			'compiled/common.js',
			'corporate.js',
		], 'public/compiled/js/corporate.js');


	/* Admin */
	mix
		.less([
			'admin.less',
		], 'resources/assets/css/compiled/admin.css')
		.styles([
			'compiled/common.css',
			'compiled/admin.css',
		], 'public/compiled/css/admin.css')
		.scripts([
			'compiled/common.js',
			'admin.js',
		], 'public/compiled/js/admin.js');


	/* THEMES ------------------------------------------------------------------ */
	/* Bellamar */
	mix
		.less([
			'bellamar/app.less',
		], 'resources/assets/css/compiled/bellamar.css')
		.styles([
			'compiled/common.css',
			'compiled/bellamar.css',
		], 'public/themes/bellamar/compiled/css/app.css');

	/* Elegant */
	mix
		.less([
			'elegant/app.less',
		], 'resources/assets/css/compiled/elegant.css')
		.styles([
			'compiled/common.css',
			'compiled/elegant.css',
		], 'public/themes/elegant/compiled/css/app.css');


});
