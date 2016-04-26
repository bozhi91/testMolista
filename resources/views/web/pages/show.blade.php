@extends('layouts.web')

@section('content')

	<div id="pages">

		<div class="container">
			<div class="row">
				<div class="cols-xs-12">
					<h1>{{ $page->title }}</h1>
					<div class="body">
						{!! $page->body !!}
					</div>
				</div>
			</div>
		</div>

	</div>

	<script type="text/javascript">
		ready_callbacks.push(function(){
			var cont = $('#pages');
		});
	</script>

@endsection
