{!! Form::model($item, [ 'method'=>$method, 'action'=>$action, 'id'=>'edit-form' ]) !!}

	<div class="row">
		<div class="col-xs-12 col-sm-6">
			<ul class="nav nav-tabs locale-tabs text-uppercase" role="tablist">
				<li role="presentation" class="active">
					<a href="#lang-{{fallback_lang()}}" aria-controls="lang-{{fallback_lang()}}" aria-expanded="true" role="tab" data-toggle="tab">{{ fallback_lang() }}</a>
				</li>
				@foreach ($locales as $lang_iso => $lang_name)
					@if ( $lang_iso != fallback_lang() )
						<li role="presentation"><a href="#lang-{{$lang_iso}}" aria-controls="lang-{{$lang_iso}}" role="tab" data-toggle="tab">{{$lang_iso}}</a></li>
					@endif
				@endforeach
			</ul>
			<div class="tab-content">
				@foreach ($locales as $lang_iso => $lang_name)
					<div role="tabpanel" class="tab-pane tab-locale {{ ($lang_iso == fallback_lang()) ? 'active' : '' }}" id="lang-{{$lang_iso}}">
						<div class="form-group error-container">
							{!! Form::label("i18n[title][{$lang_iso}]", Lang::get('admin/config/currencies.title')) !!}
							{!! Form::text("i18n[title][{$lang_iso}]", null, [ 'class'=>'form-control'.(($lang_iso == fallback_lang()) ? ' required' : ''), 'dir'=>lang_dir($lang_iso) ]) !!}
						</div>
					</div>
				@endforeach
			</div>
		</div>
		<div class="col-xs-12 col-sm-6">
			<div class="hidden-xs" style="height: 57px;"></div>
			<div class="form-group error-container">
				{!! Form::label('code', Lang::get('admin/config/currencies.code')) !!}
				@if ( $item )
					{!! Form::text('code', null, [ 'class'=>'form-control required', 'minlength'=>3, 'maxlength'=>3, 'disabled'=>'disabled' ]) !!}
				@else
					{!! Form::text('code', null, [ 'class'=>'form-control required', 'minlength'=>3, 'maxlength'=>3 ]) !!}
				@endif
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-xs-12 col-sm-4">
			<div class="form-group error-container">
				{!! Form::label('decimals', Lang::get('admin/config/currencies.decimals')) !!}
				{!! Form::select('decimals', [ 
					0 => 0,
					1 => 1,
					2 => 2,
					3 => 3,
				 ], null, [ 'class'=>'form-control' ]) !!}
			</div>
		</div>
		<div class="col-xs-12 col-sm-4">
			<div class="form-group error-container">
				{!! Form::label('symbol', Lang::get('admin/config/currencies.symbol')) !!}
				{!! Form::text('symbol', null, [ 'class'=>'form-control required' ]) !!}
			</div>
		</div>
		<div class="col-xs-12 col-sm-4">
			<div class="form-group error-container">
				{!! Form::label('position', Lang::get('admin/config/currencies.position')) !!}
				{!! Form::select('position', [ 
					'before' => Lang::get('admin/config/currencies.position.before'),
					'after' => Lang::get('admin/config/currencies.position.after'),
				 ], null, [ 'class'=>'form-control' ]) !!}
			</div>
		</div>
	</div>

	<div class="row hide">
		<div class="col-xs-12 col-sm-6">
			<div class="form-group error-container">
				{!! Form::label('enabled', Lang::get('admin/config/currencies.enabled')) !!}
				{!! Form::select('enabled', [
					1 => Lang::get('general.yes'),
					0 => Lang::get('general.no'),
				], null, [ 'class'=>'form-control' ]) !!}
			</div>
		</div>
	</div>

	<div class="text-right">
		{!! print_goback_button( Lang::get('general.back'), [ 'class'=>'btn btn-default' ]) !!}
		{!! Form::submit( Lang::get('general.continue'), [ 'class'=>'btn btn-default']) !!}
	</div>

{!! Form::close() !!}

<script type="text/javascript">
	ready_callbacks.push(function(){
		var form = $('#edit-form');

		form.validate({
			ignore: '',
			errorPlacement: function(error, element) {
				element.closest('.error-container').append(error);
			},
			invalidHandler: function(e, validator){
				if ( validator.errorList.length ) {
					var el = $(validator.errorList[0].element);
					if ( el.closest('.tab-locale').length ) {
						form.find('.locale-tabs a[href="#' + el.closest(".tab-pane").attr('id') + '"]').tab('show');
					}
				}
			},
			submitHandler: function(f) {
				LOADING.show();
				f.submit();
			},
			rules: {
				code : {
					remote: {
						url: '{{ action('Admin\Config\CurrenciesController@getCheck', 'code') }}',
						type: 'get',
						data: {
							id: '{{ $item ? $item->id : '' }}',
							code: function() { return form.find('input[name="code"]').val() }
						}
					},
				}
			},
			messages: {
				code : {
					remote: "{{ print_js_string( Lang::get('admin/geography/countries.code.error') ) }}"
				}
			}
		});

	});
</script>