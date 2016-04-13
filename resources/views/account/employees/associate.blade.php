@extends('layouts.account')

@section('account_content')

	<div id="admin-employees">

		@include('common.messages', [ 'dismissible'=>true ])

		<h1 class="page-title">{{ Lang::get('account/employees.associate.title') }}</h1>

		{!! Form::model(null, [ 'method'=>'POST', 'action'=>[ 'Account\EmployeesController@postAssociate', urlencode($employee->email) ], 'id'=>'associate-form' ]) !!}
			{!! Form::hidden('confirm', 1) !!}

			<div class="alert alert-warning">
				{!! Lang::get('account/employees.associate.warning', [
					'name' => $employee->name,
					'email' => $employee->email,
				]) !!}
				<div class="text-right">
					<a href="{{ action('Account\EmployeesController@create') }}" class="btn btn-sm btn-default">{{ Lang::get('general.back') }}</a>
					{!! Form::submit( Lang::get('general.continue'), [ 'class'=>'btn btn-sm btn-warning']) !!}
				</div>
			</div>

		{!! Form::close() !!}

	</div>

	<script type="text/javascript">
		ready_callbacks.push(function(){
			var form = $('#associate-form');

			// Form validation
			form.validate({
				submitHandler: function(f) {
					LOADING.show();
					f.submit();
				}
			});

		});
	</script>

@endsection