{!! Form::model($item, [ 'method'=>$method, 'action'=>$action, 'files'=>true, 'id'=>'edit-form' ]) !!}

		<div class="row">
			<div class="col-xs-12 col-sm-6">
				<br />
				<ul class="nav nav-tabs locale-tabs" role="tablist">
					@foreach ($locales as $lang_iso => $lang_name)
						<li role="presentation"><a href="#lang-{{$lang_iso}}" aria-controls="lang-{{$lang_iso}}" role="tab" data-toggle="tab">{{$lang_name}}</a></li>
					@endforeach
				</ul>
			</div>
		</div>

		<div class="row">
			<div class="col-xs-12 col-sm-6">
				<div class="tab-content">
					@foreach ($locales as $lang_iso => $lang_name)
						<div role="tabpanel" class="tab-pane tab-locale" id="lang-{{$lang_iso}}">
							<div class="form-group error-container">
								{!! Form::label("i18n[title][{$lang_iso}]", Lang::get('admin/properties/services.name')) !!}
								{!! Form::text("i18n[title][{$lang_iso}]", null, [ 'class'=>'form-control'.(($lang_iso == fallback_lang()) ? ' required' : '') ]) !!}
							</div>
							<div class="form-group error-container">
								{!! Form::label("i18n[description][{$lang_iso}]", Lang::get('admin/properties/services.description')) !!}
								{!! Form::textarea("i18n[description][{$lang_iso}]", null, [ 'class'=>'form-control', 'rows'=>'4' ]) !!}
							</div>
						</div>
					@endforeach
				</div>
			</div>
			<div class="col-xs-12 col-sm-6">
				<div class="form-group">
					<div class="error-container">
						@if ( empty($item->icon) )
							{!! Form::label('icon', Lang::get('admin/properties/services.icon')) !!}
							{!! Form::file('icon', [ 'class'=>'form-control', 'accept'=>'image/*' ]) !!}
						@else
							<img src="{{ asset("services/{$item->icon}") }}" class="" />
							{!! Form::label('icon', Lang::get('admin/properties/services.icon')) !!}
							{!! Form::file('icon', [ 'class'=>'form-control', 'accept'=>'image/*' ]) !!}
						@endif
					</div>
					<div class="help-block">{{ Lang::get('admin/properties/services.icon.help') }}</div>
				</div>
				<div class="form-group error-container">
					<div class="checkbox">
						<label>
							{!! Form::checkbox('enabled', 1, $item ? null : 1, [ 'class'=>'' ]) !!}
							{{ Lang::get('admin/properties/services.enabled') }}
						</label>
					</div>
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

		form.find('.locale-tabs a').eq(0).trigger('click');

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
			messages: {
				icon: {
					accept: "{{ trim( Lang::get('admin/properties/services.icon.error') ) }}"
				}
			}
		});
	});
</script>