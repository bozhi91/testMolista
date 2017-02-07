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

elixir.config.sourcemaps = true;

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
			'jquery.cookie.js',
                        'jquery.pjax.js',
			'bootstrap-table.js',
			'bootstrap-datetimepicker.js',
			'bootstrap-daterangepicker.js',
			'bootstrap-slider.js',
			'summernote.js',
			'alertify.js',
			'select2.full.js',
			'loading.js',
			'tmpl.js',
			'spectrum.js',
			'dropzone.js',
			'common.js'
		], 'resources/assets/js/compiled/common.js')
		;


	/* RTL */
	mix
		.less([
			'../bootstrap-rtl/bootstrap-rtl.less',
		], 'public/compiled/css/rtl.css')
		;


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
		], 'public/compiled/js/app.js')
		.less([
			'app/pdf.less',
		], 'public/compiled/css/pdf.css')
		.scripts([
			'compiled/common.js',
			'app.js',
		], 'resources/assets/js/compiled/app.js')
		;



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
			'jquery.sticky-kit.js',
			'corporate.js',
		], 'public/compiled/js/corporate.js');

	/* 004estate */
	mix
		.less([
			'004estate.less',
		], 'public/compiled/css/004estate.css');


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
		], 'public/themes/bellamar/compiled/css/app.css')
		.less([
			'bellamar/pdf.less',
		], 'public/themes/bellamar/compiled/css/pdf.css')
		;

	/* Elegant */
	mix
		.less([
			'elegant/app.less',
		], 'resources/assets/css/compiled/elegant.css')
		.styles([
			'compiled/common.css',
			'compiled/elegant.css',
		], 'public/themes/elegant/compiled/css/app.css')
		.less([
			'elegant/pdf.less',
		], 'public/themes/elegant/compiled/css/pdf.css')
		;

	/* Inmoblue */
	mix
		.less([
			'inmoblue/app.less',
		], 'resources/assets/css/compiled/inmoblue.css')
		.styles([
			'compiled/common.css',
			'compiled/inmoblue.css',
		], 'public/themes/inmoblue/compiled/css/app.css')
		.less([
			'inmoblue/pdf.less',
		], 'public/themes/inmoblue/compiled/css/pdf.css')
		;

	/* Whitey */
	mix
		.less([
			'whitey/app.less',
		], 'resources/assets/css/compiled/whitey.css')
		.styles([
			'compiled/common.css',
			'compiled/whitey.css',
		], 'public/themes/whitey/compiled/css/app.css')
		.less([
			'whitey/pdf.less',
		], 'public/themes/whitey/compiled/css/pdf.css')
		;

	/* Zoner */
	mix
		.less([
			'zoner/app.less',
		], 'resources/assets/css/compiled/zoner.css')
		.styles([
			'compiled/common.css',
			'compiled/zoner.css',
		], 'public/themes/zoner/compiled/css/app.css')
		.less([
			'zoner/pdf.less',
		], 'public/themes/zoner/compiled/css/pdf.css')
		.scripts([
			'compiled/app.js',
			'themes/zoner/app.js',
		], 'public/themes/zoner/compiled/js/app.js')
		;

	/* Sky */
	mix
		.less([
			'sky/app.less',
		], 'resources/assets/css/compiled/sky.css')
		.styles([
			'compiled/common.css',
			'compiled/sky.css',
		], 'public/themes/sky/compiled/css/app.css')
		.less([
			'sky/pdf.less',
		], 'public/themes/sky/compiled/css/pdf.css')
		;

	/* Redly */
	mix
		.less([
			'redly/app.less',
		], 'resources/assets/css/compiled/redly.css')
		.styles([
			'compiled/common.css',
			'compiled/redly.css',
		], 'public/themes/redly/compiled/css/app.css')
		.less([
			'redly/pdf.less',
		], 'public/themes/redly/compiled/css/pdf.css')
		;

	/* White-Cloud */
	mix
		.less([
			'white-cloud/app.less',
		], 'resources/assets/css/compiled/white-cloud.css')
		.styles([
			'compiled/common.css',
			'compiled/white-cloud.css',
		], 'public/themes/white-cloud/compiled/css/app.css')
		.less([
			'white-cloud/pdf.less',
		], 'public/themes/white-cloud/compiled/css/pdf.css')
		;

	/* Sam */
	mix
		.less([
			'sam/app.less',
		], 'resources/assets/css/compiled/sam.css')
		.styles([
			'compiled/common.css',
			'compiled/sam.css',
		], 'public/themes/sam/compiled/css/app.css')
		.less([
			'white-cloud/pdf.less',
		], 'public/themes/sam/compiled/css/pdf.css')
		;

	/* Kredikasa */
	mix
		.less([
			'kredikasa/app.less',
		], 'resources/assets/css/compiled/kredikasa.css')
		.styles([
			'compiled/common.css',
			'compiled/kredikasa.css',
		], 'public/themes/kredikasa/compiled/css/app.css')
		.less([
			'kredikasa/pdf.less',
		], 'public/themes/kredikasa/compiled/css/pdf.css')
		;

	/* Modular */
	mix
		.less([
			'modular/app.less',
		], 'resources/assets/css/compiled/modular.css')
		.styles([
			'compiled/common.css',
			'compiled/modular.css',
		], 'public/themes/modular/compiled/css/app.css')
		.less([
			'white-cloud/pdf.less',
		], 'public/themes/modular/compiled/css/pdf.css')
		;

	/* Modular */
	mix
		.less([
			'whitey-modular/app.less',
		], 'resources/assets/css/compiled/whitey-modular.css')
		.styles([
			'compiled/common.css',
			'compiled/whitey-modular.css',
		], 'public/themes/whitey-modular/compiled/css/app.css')
		.less([
			'white-cloud/pdf.less',
		], 'public/themes/whitey-modular/compiled/css/pdf.css')
		;

});
