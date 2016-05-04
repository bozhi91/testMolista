@extends('layouts.web')

@section('content')

	<div id="customer">
		<div class="container">

			@include('common.messages', [ 'dismissible'=>true ])

		</div>
	</div>

	<script type="text/javascript">
		ready_callbacks.push(function(){
			var cont = $('#customers');
		});
	</script>
@endsection
