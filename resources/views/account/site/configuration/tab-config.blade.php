<div class="row">
	<div class="col-xs-12 col-sm-6">
		<div class="form-group error-container">
			{!! Form::label('site_currency', Lang::get('account/site.configuration.currency')) !!}
			{!! Form::select('site_currency', $currencies, null, [ 'class'=>'currency-select form-control required' ]) !!}
		</div>
	</div>
</div>
<div class="row">
	<div class="col-xs-12 col-sm-6">
		<div class="form-group">
			<div class="error-container">
				@if ( @$site->logo )
					<a href="{{ asset("sites/{$site->id}/{$site->logo}") }}" target="_blank"><span class="glyphicon glyphicon-picture" aria-hidden="true"></span></a>
					<label>{{ Lang::get('account/site.configuration.logo') }}</label>
					{!! Form::file('logo', [ 'class'=>'form-control', 'accept'=>'image/*' ]) !!}
				@else
					<label>{{ Lang::get('account/site.configuration.logo') }}</label>
					{!! Form::file('logo', [ 'class'=>'form-control required', 'accept'=>'image/*' ]) !!}
				@endif
			</div>
			<div class="help-block">
				{!! Lang::get('account/site.configuration.logo.helper', [ 'IMAGE_MAXSIZE'=>Config::get('app.property_image_maxsize', 2048) ]) !!}
			</div>
		</div>
	</div>
	<div class="col-xs-12 col-sm-6">
		<div class="form-group">
			<div class="error-container">
				<label>{{ Lang::get('account/site.configuration.favicon') }}</label>
				{!! Form::file('favicon', [ 'class'=>'form-control', 'accept'=> 'image/x-icon,image/vnd.microsoft.icon' ]) !!}
			</div>
			<div class="help-block">
				{!! Lang::get('account/site.configuration.favicon.helper', [ 'IMAGE_MAXSIZE'=>Config::get('app.property_image_maxsize', 2048) ]) !!}
			</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-xs-12 col-sm-6">
		<div class="form-group error-container">
			{!! Form::label('timezone', Lang::get('account/site.configuration.timezone')) !!}
			{!! Form::select('timezone', [ ''=>'' ]+$timezones, null, [ 'class'=>'form-control required' ]) !!}
		</div>
	</div>
	<div class="col-xs-12 col-sm-6">
		<div class="form-group error-container">
			{!! Form::label('customer_register', Lang::get('account/site.configuration.client.register')) !!}
			{!! Form::select('customer_register', [
				'1' => Lang::get('general.yes'),
				'0' => Lang::get('general.no'),
			], null, [ 'class'=>'form-control' ]) !!}
		</div>
	</div>
</div>
<div class="row">
	<div class="col-xs-12 col-sm-6">
		<div class="form-group">
			<div class="error-container">
				{!! Form::label('ga_account', Lang::get('account/site.configuration.ga.account')) !!}
				{!! Form::text('ga_account', null, [ 'class'=>'form-control' ]) !!}
			</div>
			<div class="help-block">{{ Lang::get('account/site.configuration.ga.account.helper') }}</div>
		</div>
	</div>
	<div class="col-xs-12 col-sm-6">
		<div class="form-group">
			{!! Form::label('hide_molista', Lang::get('account/site.configuration.hide.molista', [ 'webname'=>env('WHITELABEL_WEBNAME','Molista') ])) !!}
			<div class="error-container">
				@if ( $current_site->can_hide_molista )
					{!! Form::select('hide_molista', [
						'1' => Lang::get('general.yes'),
						'0' => Lang::get('general.no'),
					], null, [ 'class'=>'form-control' ]) !!}
				@elseif ( $current_site->hide_molista )
					{!! Form::select('hide_molista', [
						'1' => Lang::get('general.yes'),
					], null, [ 'class'=>'form-control' ]) !!}
				@else
					{!! Form::select('hide_molista', [
						'0' => Lang::get('general.no'),
					], null, [ 'class'=>'form-control' ]) !!}
				@endif
			</div>
			@if ( !$current_site->can_hide_molista )
				<div class="help-block">{{ Lang::get('account/site.configuration.hide.molista.helper') }}</div>
			@endif
		</div>
	</div>
</div>
<div class="row">
	<div class="col-xs-12 col-sm-6">
		<div class="form-group error-container">
			{!! Form::label('home_highlights', Lang::get('account/site.configuration.home.highlights.label')) !!}
			{!! Form::select('home_highlights', [
				3 => Lang::get('account/site.configuration.home.highlights.group.3'),
				6 => Lang::get('account/site.configuration.home.highlights.group.6'),
				9 => Lang::get('account/site.configuration.home.highlights.group.9'),
				0 => Lang::get('account/site.configuration.home.highlights.group.all'),
			], null, [ 'class'=>'form-control' ]) !!}
		</div>
	</div>
	<div class="col-xs-12 col-sm-6">
	</div>
</div>

<div class="{{ ( $max_languages == 1 ) ? 'hide' : '' }}"
	<hr />
	<div class="error-container">
		<label>{{ Lang::get('account/site.configuration.languages') }}</label>
		<div class="row">
			<div class="col-xs-12 col-sm-2">
				<div class="form-group">
					<div class="checkbox">
						<label class="normal">
							{!! Form::checkbox('locales_array[]', fallback_lang(), null, [ 'class'=>'required locale-input', 'readonly'=>'readonly', 'title'=>Lang::get('account/site.configuration.languages.error') ]) !!}
							{{ fallback_lang_text() }}
						</label>
					</div>
				</div>
			</div>
			@foreach (LaravelLocalization::getSupportedLocales() as $lang_iso => $lang_def)
				@if ( $lang_iso != fallback_lang() )
					<div class="col-xs-12 col-sm-2">
						<div class="form-group">
							<div class="checkbox">
								<label class="normal">
									{!! Form::checkbox('locales_array[]', $lang_iso, null, [ 'class'=>'required locale-input', 'title'=>Lang::get('account/site.configuration.languages.error') ]) !!}
									{{ $lang_def['native'] }}
								</label>
							</div>
						</div>
					</div>
				@endif
			@endforeach
		</div>
	</div>
</div>
