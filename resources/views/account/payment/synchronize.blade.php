@extends('layouts.account', [
	'hide_pending_request_warning' => 1,
])

@section('account_content')

	<div id="plan-upgrade">
			@if ($data["pending_request"]["payment_method"]=='stripe' )
					holaaa
			@else
				adios
			@endif
	</div>

@endsection
