@extends('layouts.account')

@section('account_content')

	<style type="text/css">
		#site-countries .list-group-item { background: transparent; border: none; }
		#site-countries label.disabled { color: #999; cursor: default; }
	</style>

	<div id="site-countries">

	 	@include('common.messages', [ 'dismissible'=>true ])

		<h1 class="page-title">{{ Lang::get('account/countries.h1') }}</h1>

		{!! Form::open([ 'method'=>'POST', 'action'=>[ 'Account\Site\CountriesController@postIndex' ], 'id'=>'countries-form' ]) !!}

			<div class="row">
				<div class="col-xs-12 col-sm-6">
					<div class="form-group error-container">
						{!! Form::label('limit_countries', Lang::get('account/countries.limit')) !!}
						{!! Form::select('limit_countries', [ 
							'0' => Lang::get('general.no'),
							'1' => Lang::get('general.yes'),
						], (empty($current_site->country_ids) ? 0 : 1), [ 'class'=>'limit-countries-select form-control' ]) !!}
					</div>
				</div>
				<div class="col-xs-12 col-sm-6">
					<div class="form-group error-container">
						{!! Form::label('country_id', Lang::get('account/countries.default')) !!}
						{!! Form::select('country_id', $countries, $current_site->country_id, [ 'class'=>'form-control required' ]) !!}
					</div>
				</div>
			</div>

			<div class="text-right">
				{!! Form::button(Lang::get('general.continue'), [ 'type'=>'submit', 'class'=>'btn btn-primary']) !!}
			</div>

			<hr />

			<div class="countries-area hide">
				<ul class="list-group row">
					@foreach ($countries as $country_id => $country_title)
					     <li class="list-group-item col-xs-12 col-sm-6 col-lg-4">
							<div class="checkbox">
								<label class="text-ellipsis">
									<input type="checkbox" name="country_ids[]" value="{{ $country_id }}" id="country-input-{{ $country_id }}" />
									{{ $country_title }}
								</label>
							</div>
					     </li>
					@endforeach
				</ul>
				<div class="text-right">
					{!! Form::button(Lang::get('general.continue'), [ 'type'=>'submit', 'class'=>'btn btn-primary']) !!}
				</div>
			</div>

		{!! Form::close() !!}

	</div>

	<script type="text/javascript">
		ready_callbacks.push(function(){
			var cont = $('#site-countries');
			var form = $('#countries-form');

			var country_ids = {!! json_encode($current_site->country_ids) !!};

			form.validate({
				submitHandler: function(f) {
					LOADING.show();
					f.submit();
				}
			});

			var str = $('<ul class="list-group row"></ul>');
			$.each(country_ids, function(k,id){
				form.find('#country-input-'+id).prop('checked',true).closest('.list-group-item').appendTo(str);
			});
			form.find('.countries-area').prepend(str).removeClass('hide');

			form.find('.limit-countries-select').on('change', function(){
				if ( $(this).val() == 0 ) {
					form.find('.countries-area input').attr('disabled','disabled').closest('label').addClass('disabled');
				} else {
					form.find('.countries-area input').removeAttr('disabled').closest('label').removeClass('disabled');
				}
			}).trigger('change');

		});
	</script>

@endsection