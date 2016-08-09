@extends('layouts.account')

@section('account_content')

	<style type="text/css">
		#user-signatures {}
		#user-signatures .current-signatures li { margin-bottom: 5px; }
		#user-signatures .current-signatures .btn { white-space: normal; text-align: left; }
	</style>

	<div id="user-signatures">

		@include('common.messages', [ 'dismissible'=>true ])

		<div class="pull-right">
			<a href="{{ action('Account\Profile\SignaturesController@getCreate') }}" class="btn btn-primary btn-signature-create">{{ Lang::get('account/profile.signatures.button.new') }}</a>
		</div>

		<h1 class="page-title">{{ empty($page_title) ? Lang::get('account/profile.signatures.h1') : $page_title }}</h1>

		@if ( $signatures->count() < 1 )
			@yield('signatures_content')
		@else
			<div class="row">
				<div class="col-xs-12 col-sm-4 col-md-3">
					<ul class="list-unstyled current-signatures">
						@foreach ($signatures as $item)
							<li>
								<a href="{{ action('Account\Profile\SignaturesController@getEdit', $item->id) }}" class="btn btn-sm btn-block btn-signature-edit {{ @$signature->id == $item->id ? 'btn-primary' : 'btn-default' }}">
									{{ $item->title }}
								</a>
							</li>
						@endforeach
					</ul>
				</div>
				<div class="col-xs-12 col-sm-8 col-md-9">
					@yield('signatures_content')
				</div>
			</div>
		@endif

	</div>

	<script type="text/javascript">
		ready_callbacks.push(function() {
			var cont = $('#user-signatures');
		});
	</script>

@endsection
