<style type="text/css">
	#edit-form h4 { margin-top: 0px; margin-bottom: 20px; }
	#edit-form .marketplaces-img-item { position: relative; padding: 20px !important; margin: 0px 5px 10px 5px; border: 1px solid #999; background: #fff; cursor: move; }
	#edit-form .marketplaces-img-image { height: 40px; }
	#edit-form .marketplaces-img-delete { position: absolute; top: 3px; right: 3px; color: #999; }
	#edit-form .marketplaces-img-delete:hover { color: #333; }
</style>

{!! Form::model($item, [ 'method'=>$method, 'action'=>$action, 'files'=>true, 'id'=>'edit-form' ]) !!}

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
							{!! Form::label("i18n[name][{$lang_iso}]", Lang::get('admin/geography/countries.name')) !!}
							{!! Form::text("i18n[name][{$lang_iso}]", null, [ 'class'=>'form-control'.(($lang_iso == fallback_lang()) ? ' required' : '') ]) !!}
						</div>
					</div>
				@endforeach
			</div>
		</div>
		<div class="col-xs-12 col-sm-6">
			<div class="hidden-xs" style="height: 57px;"></div>
			<div class="form-group error-container">
				{!! Form::label('code', Lang::get('admin/geography/countries.code')) !!}
				@if ( $item )
					{!! Form::text('code', null, [ 'class'=>'form-control required', 'minlength'=>2, 'maxlength'=>2, 'disabled'=>'disabled' ]) !!}
				@else
					{!! Form::text('code', null, [ 'class'=>'form-control required', 'minlength'=>2, 'maxlength'=>2 ]) !!}
				@endif
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-xs-12 col-sm-6">
			<div class="form-group error-container">
				{!! Form::label('currency', Lang::get('admin/geography/countries.currency')) !!}
				{!! Form::select('currency', [ ''=>'' ]+$currencies, null, [ 'class'=>'form-control required' ]) !!}
			</div>
		</div>
		<div class="col-xs-12 col-sm-6">
			<div class="form-group error-container">
				{!! Form::label('locale', Lang::get('admin/geography/countries.locale')) !!}
				{!! Form::select('locale', [ ''=>'' ]+$locales, null, [ 'class'=>'form-control required' ]) !!}
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-xs-12 col-sm-6">
			<div class="form-group error-container">
				{!! Form::label('pay_methods', Lang::get('admin/geography/countries.payment')) !!}
				<div class="form-inline">
					<div class="checkbox" style="margin-right: 20px;">
						<label>
							{!! Form::checkbox('pay_methods[]', 'stripe', null, [ 'class'=>'' ]) !!}
							{{ Lang::get('account/payment.method.stripe') }}
						</label>
					</div>
					<div class="checkbox">
						<label>
							{!! Form::checkbox('pay_methods[]', 'transfer', null, [ 'class'=>'' ]) !!}
							{{ Lang::get('account/payment.method.transfer') }}
						</label>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xs-12 col-sm-6">
			<div class="form-group error-container">
				{!! Form::label('enabled', Lang::get('admin/geography/countries.enabled')) !!}
				{!! Form::select('enabled', [
					1 => Lang::get('general.yes'),
					0 => Lang::get('general.no'),
				], null, [ 'class'=>'form-control' ]) !!}
			</div>
		</div>
	</div>

	<hr />

	<div class="row">
		<div class="col-xs-12 col-sm-6">
			@if ( @$item->feature_image )
				<a href="#" class="feature-img-delete pull-right" title="Eliminar imagen">
					<span class="glyphicon glyphicon-remove-circle" aria-hidden="true"></span>
				</a>
				<h4>{{ Lang::get('admin/geography/countries.images.feature') }}</h4>
				<p class="feature-img-area">
					<img src="{{ asset("{$item->items_folder}/{$item->feature_image}") }}" class="img-responsive" />
				</p>
			@else
				<h4>{{ Lang::get('admin/geography/countries.images.feature') }}</h4>
			@endif
			<div class="form-group">
				<div class="error-container">
					{!! Form::file('feature_image', [ 'class'=>'form-control', 'accept'=>'image/*' ]) !!}
				</div>
				<div class="help-block">{!! Lang::get('admin/geography/countries.images.feature.help') !!}</div>
			</div>
		</div>
		<div class="col-xs-12 col-sm-6">
			<h4>{{ Lang::get('admin/geography/countries.images.marketplaces.title') }}</h4>
			@if ( @$item->marketplaces_images )
				<ul class="list-inline marketplaces-img-sortable">
					@foreach ($item->marketplaces_images as $image)
						<li class="marketplaces-img-item">
							<a href="#" class="marketplaces-img-delete" title="Eliminar logo">
								<span class="glyphicon glyphicon-remove-circle" aria-hidden="true"></span>
							</a>
							{!! Form::hidden('marketplaces_images[]', $image) !!}
							<img src="{{ asset("{$item->items_folder}/{$image}") }}" class="marketplaces-img-image" />
						</li>
					@endforeach
				</ul>
			@else
				<div class="form-group">{{ Lang::get('admin/geography/countries.images.marketplaces.none') }}</div>
			@endif
			<div class="form-group">
				<div class="error-container">
					{!! Form::label('marketplaces_image', Lang::get('admin/geography/countries.images.marketplaces.new')) !!}
					{!! Form::file('marketplaces_image', [ 'class'=>'form-control', 'accept'=>'image/*' ]) !!}
				</div>
				<div class="help-block">{!! Lang::get('admin/geography/countries.images.marketplaces.help') !!}</div>
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
						url: '{{ action('Admin\Geography\CountriesController@getCheck', 'code') }}',
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
				},
				feature_image: {
					accept: "{{ trim( Lang::get('admin/geography/countries.images.error') ) }}"
				},
				marketplaces_image: {
					accept: "{{ trim( Lang::get('admin/geography/countries.images.error') ) }}"
				}
			}
		});

		form.on('click','.feature-img-delete',function(e){
			e.preventDefault();
			$(this).remove();
			form.find('.feature-img-area').remove();
			form.append('<input type="hidden" name="feature_image_remove" value="1" />');
		});

		form.on('click','.marketplaces-img-delete',function(e){
			e.preventDefault();
			$(this).closest('.marketplaces-img-item').remove();
		});

		form.find('.marketplaces-img-sortable').sortable();

	});
</script>