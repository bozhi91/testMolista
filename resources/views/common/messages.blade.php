@if (session('success'))
	<div class="alert alert-success {{ empty($dismissible) ? '' : 'alert-dismissible' }}" role="alert">
		@if ( !empty($dismissible) )
			<button type="button" class="close" data-dismiss="alert" aria-label="{{ Lang::get('general.messages.close') }}"><span aria-hidden="true">&times;</span></button>
		@endif
		{!! session('success') !!}
	</div>
@elseif (session('error'))
	<div class="alert alert-danger {{ empty($dismissible) ? '' : 'alert-dismissible' }}" role="alert">
		@if ( !empty($dismissible) )
			<button type="button" class="close" data-dismiss="alert" aria-label="{{ Lang::get('general.messages.close') }}"><span aria-hidden="true">&times;</span></button>
		@endif
		{!! session('error') !!}
	</div>
@elseif ($errors->any())
	<div class="alert alert-danger {{ empty($dismissible) ? '' : 'alert-dismissible' }}" role="alert">
		@if ( !empty($dismissible) )
			<button type="button" class="close" data-dismiss="alert" aria-label="{{ Lang::get('general.messages.close') }}"><span aria-hidden="true">&times;</span></button>
		@endif
		{!! Lang::get('general.messages.error') !!}
		<ul>
			@foreach ($errors->all() as $error)
				<li>{{ $error }}</li>
			@endforeach
		</ul>
	</div>
@endif
