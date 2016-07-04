@extends('layouts.corporate')

@section('content')

	<style type="text/css">
		#legal-notice { padding-bottom: 50px; font-size: 16px; }
		#legal-notice h1 { padding-bottom: 50px; }
		#legal-notice h2 { margin-top: 0px; padding-top: 40px; }
	</style>

	<div id="legal-notice">
		<div class="container">

			{!! Lang::get('corporate/info.legal') !!}
			
		</div>
	</div>

	<script type="text/javascript">
		ready_callbacks.push(function(){
			var cont = $('#legal-notice');
		});
	</script>

@endsection
