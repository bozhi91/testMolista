@extends('layouts.account')

@section('account_content')

	<div id="admin-employees">

		<h1>{{ Lang::get('account/employees.delete') }}</h1>

		{!! Form::open([ 'method'=>'DELETE', 'class'=>'delete-form', 'action'=>['Account\EmployeesController@destroy', urlencode($employee->email)] ]) !!}
			{!! Form::hidden('confirm',1) !!}

			<ul>
				<li>{{ $employee->name }}</li>
				<li>{{ $employee->email }}</li>
			</ul>

			<p>&nbsp;</p>

			<div class="form-inline">
				<div class="form-group">
					<label class="control-label">{{ Lang::get('account/employees.delete.reassign') }}</label>
					&nbsp;
					{!! Form::select('reassign_to', [ ''=>'' ]+$employees, null, [ 'class'=>'form-control' ]) !!}
				</div>
			</div>
			<p>&nbsp;</p>

			<div class="alert alert-danger text-center">
				<h4 style="margin: 0px;">{{ Lang::get('account/employees.delete.intro') }}</h4>
			</div>

			<div class="text-right">
				{!! print_goback_button( Lang::get('general.back'), [ 'class'=>'btn btn-default' ]) !!}
				{!! Form::submit( Lang::get('general.delete'), [ 'class'=>'btn btn-danger']) !!}
			</div>

		{!! Form::close() !!}

	</div>

	<script type="text/javascript">
		ready_callbacks.push(function(){
			var form = $('#delete-form');

			// Form validation
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