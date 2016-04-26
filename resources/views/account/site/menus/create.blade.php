@extends('account.site.menus.index', [ 'tab_current'=>'create' ])

@section('account_site_menus_content')

	{!! Form::model(null, [ 'method'=>'POST', 'action'=>'Account\Site\MenusController@store', 'id'=>'create-form' ]) !!}
		<div class="form-horizontal">
			<div class="form-group">
				{!! Form::label('title', Lang::get('account/site.menus.create.name'), [ 'class'=>'col-md-4 control-label' ]) !!}
				<div class="col-md-6 error-container">
					{!! Form::text('title', null, [ 'class'=>'form-control required' ]) !!}
				</div>
			</div>
		</div>
		<hr />
		{!! Lang::get('account/site.menus.create.intro') !!}
		<hr />
		<div class="text-right">
			{!! Form::submit(Lang::get('account/site.menus.create.button'), [ 'class'=>'btn btn-primary']) !!}
		</div>
	{!! Form::close() !!}

	<script type="text/javascript">
		ready_callbacks.push(function(){
			var form = $('#create-form');

			form.validate({
				ignore: '',
				errorPlacement: function(error, element) {
					element.closest('.error-container').append(error);
				},
				submitHandler: function(f) {
					LOADING.show();
					f.submit();
				}
			});

		});
	</script>

@endsection