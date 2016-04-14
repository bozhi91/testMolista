<div class="row">
	<div class="col-xs-12 col-sm-6">
		<div class="form-group error-container">
			{!! Form::label('name]', Lang::get('admin/users.name')) !!}
			{!! Form::text('name',null, [ 'class'=>'form-control required' ]) !!}
		</div>
		<div class="form-group error-container">
			{!! Form::label('email', Lang::get('admin/users.email')) !!}
			{!! Form::email('email', null, [ 'class'=>'form-control required email' ]) !!}
		</div>
		<div class="form-group error-container">
			{!! Form::label('locale]', Lang::get('admin/users.locale')) !!}
			{!! Form::select('locale', $locales, null, [ 'class'=>'form-control required' ]) !!}
		</div>
	</div>
	<div class="col-xs-12 col-sm-6">
		@if ( empty($user) )
			<div class="form-group error-container">
				{!! Form::label('password', Lang::get('admin/users.password')) !!}
				<div class="input-group">
					{!! Form::password('password', [ 'class'=>'form-control', 'minlength'=>6 ]) !!}
					<div class="input-group-addon"><span class="glyphicon glyphicon-eye-open show-hide-password" style="cursor: pointer;" aria-hidden="true"></span></div>
				</div>
			</div>
		@else
			<div class="form-group error-container">
				{!! Form::label(null, Lang::get('admin/users.registered')) !!}
				{!! Form::text(null, $user->created_at->format('d/m/Y'), [ 'class'=>'form-control', 'readonly'=>'readonly' ]) !!}
			</div>
		@endif
		<div class="row">
			<div class="col-xs-12 col-sm-6">
				<div class="form-group error-container">
					{!! Form::label('roles[]', Lang::get('admin/users.role')) !!}
					<ul class="list-unstyled">
						@foreach ($roles as $role)
							<li class="{{ (!empty($user) && !$user->hasRole($role->name)) ? 'hide' : '' }}">
								<label class="normal">
									<input type="radio" name="roles[]" value="{{$role->name}}" {{ empty($user) ? false : $user->hasRole($role->name) ? 'checked="checked"' : '' }} class="role-radio" />
									{{ $role->display_name }}
								</label>
							</li>
						@endforeach
					</ul>
				</div>
			</div>
			<div class="col-xs-12 col-sm-6" >
				<div class="form-group error-container">
					<div class="role-related-area role-related-company role-related-employee hide">
						@if ( !empty($user) && count($user->sites) > 0 )
							{!! Form::label(null, Lang::get('admin/users.sites')) !!}
							<ul class="list-unstyled">
								<li>{!! $user->sites->implode('title', '</li><li>') !!}</li>
							</ul>
						@endif
					</div>
					<div class="role-related-area role-related-translator hide">
						{!! Form::label('locales[]', Lang::get('admin/users.translation.locales')) !!}
						<ul class="list-unstyled">
							<li>
								<label class="normal">
									{!! Form::checkbox('locales[]', 'all', empty($user) ? false : $user->canTranslate(), [ 'class'=>'locale-checkbox' ]) !!}
									{{ Lang::get('admin/users.translation.all') }}
								</label>
							</li>
							@foreach ($translation_locales as $locale)
								<li>
									<label class="normal">
										{!! Form::checkbox('locales[]', $locale->id, empty($user) ? false : $user->canTranslate($locale->locale), [ 'class'=>'locale-checkbox' ]) !!}
										{{ $locale->native }}
									</label>
								</li>
							@endforeach
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	ready_callbacks.push(function(){
		var form = $('#user-form');

		form.validate({
			ignore: '',
			errorPlacement: function(error, element) {
				element.closest('.error-container').append(error);
			},
			rules: {
				email: {
					remote: {
						url: '{{ action('Admin\Utils\UserController@getCheck', 'email') }}',
						type: 'get',
						data: {
							exclude: {{ empty($user) ? 0 : $user->id }}
						}
					}
				},
				'roles[]': {
					require_from_group: [1, '.role-radio']
				}
			},
			messages: {
				email: {
					remote: "{{ trim( Lang::get('admin/users.email.used') ) }}"
				},
				'roles[]': {
					require_from_group: "{{ trim( Lang::get('admin/users.roles.required') ) }}"
				}
			}
		});

		form.on('click', '.role-radio', function(){
			form.find('.role-related-area').addClass('hide');
			form.find('.role-related-'+this.value).removeClass('hide');
		});
		form.find('.role-radio:checked').trigger('click');

		form.on('change', '.locale-checkbox', function(){
			if ( $(this).val() == 'all' ) {
				if ( $(this).is(':checked') ) {
					form.find('.locale-checkbox').not(this).prop('checked', true);
				} else {
					form.find('.locale-checkbox').not(this).prop('checked', false);
				}
			} else {
				if ( $(this).not(':checked') ) {
					form.find('.locale-checkbox[value="all"]').prop('checked', false);
				}
			}
		});

		form.on('click', '.show-hide-password', function(e){
			e.preventDefault();
			form.find('input[name="password"]').togglePassword();
		});
	});
</script>
