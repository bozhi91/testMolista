@extends('layouts.admin')

@section('content')

	<div class="container">
		<div class="row">
			<div class="col-xs-12">

				@if (session('status'))
					<div class="alert alert-success alert-dismissible">
						<button type="button" class="close" data-dismiss="alert" aria-label="{{ Lang::get('general.messages.close') }}"><span aria-hidden="true">&times;</span></button>
						{{ session('status') }}
					</div>
				@endif

				<h1>Admin home</h1>

			</div>
		</div>
	</div>

	<script type="text/javascript">
		ready_callbacks.push(function(){
		});
	</script>

@endsection
