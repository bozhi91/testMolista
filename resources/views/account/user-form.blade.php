<div class="row">
	<div class="col-xs-12 col-sm-6">
		<div class="form-group error-container">
			{!! Form::label('name', Lang::get('account/profile.name')) !!}
			{!! Form::text('name', null, [ 'class'=>'form-control required']) !!}
		</div>
	</div>
	<div class="col-xs-12 col-sm-6">
		<div class="form-group error-container">
			{!! Form::label('email', Lang::get('account/profile.email')) !!}
			{!! Form::email('email', null, [ 'class'=>'form-control required email' ]); !!}
		</div>
	</div>
</div>
<div class="row">
	<div class="col-xs-12 col-sm-6">
		<div class="form-group error-container">
			{!! Form::label('locale', Lang::get('account/profile.locale')) !!}
			{!! Form::select('locale', $site_setup['locales_select'], null, [ 'class'=>'form-control required' ]) !!}
		</div>
	</div>
	<div class="col-xs-12 col-sm-6">
		<div class="form-group">
			<div class="error-container">
				{!! Form::label('password', Lang::get('account/profile.password')) !!}
				<div class="input-group">
					{!! Form::password('password', [ 'class'=>'form-control', 'minlength'=>6 ]) !!}
					<div class="input-group-addon"><span class="glyphicon glyphicon-eye-open show-hide-password" style="cursor: pointer;" aria-hidden="true"></span></div>
				</div>
			</div>
			<div class="help-block">{!! Lang::get('account/profile.password.helper') !!}</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-xs-12 col-sm-6">
		<div class="form-group error-container">
			{!! Form::label('phone', Lang::get('account/profile.phone')) !!}
			{!! Form::text('phone', null, [ 'class'=>'form-control' ]) !!}
		</div>
	</div>
	<div class="col-xs-12 col-sm-6">
		<div class="form-group error-container">
			{!! Form::label('linkedin', Lang::get('account/profile.linkedin')) !!}
			{!! Form::text('linkedin', null, [ 'class'=>'form-control url' ]) !!}
		</div>
	</div>
</div>
<div class="row">
	<div class="col-xs-12 col-sm-6">
		<div class="form-group">
			<div class="error-container">
				@if ( @$user_image )
					<a href="{{ asset($user_image) }}" class="user-image-link pull-right" target="_blank"><span class="glyphicon glyphicon-picture" aria-hidden="true"></span></a>
				@endif
				{!! Form::label('image', Lang::get('account/profile.image')) !!}
				{!! Form::file('image', [ 'class'=>'form-control', 'accept'=>'image/jpeg,image/jpg,image/gif,image/png' ]) !!}
			</div>
			<div class="help-block">{!! Lang::get('account/profile.image.helper') !!}</div>
		</div>
	</div>
	<div class="col-xs-12 col-sm-6">
		<div class="form-group error-container">
			{!! Form::label('signature', Lang::get('account/profile.signature')) !!}
			{!! Form::select('signature', [ 
				0 =>Lang::get('general.no'),
				1 => Lang::get('general.yes'), 
			], null, [ 'class'=>'form-control' ]) !!}
		</div>
	</div>
</div>
