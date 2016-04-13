@extends('layouts.admin')

@section('content')

<div class="container">

	<div class="alert alert-danger {{ empty($dismissible) ? '' : 'alert-dismissible' }}" role="alert">
		@if ( !empty($dismissible) )
			<button type="button" class="close" data-dismiss="alert" aria-label="{{ Lang::get('general.messages.close') }}"><span aria-hidden="true">&times;</span></button>
		@endif
		@if ( empty($error) )
			{!! Lang::get('general.messages.error') !!}
		@else
			{!! $error !!}
		@endif
	</div>

</div>

<script type="text/javascript">
	ready_callbacks.push(function(){
	});
</script>

@endsection