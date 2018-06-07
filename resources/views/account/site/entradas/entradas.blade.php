@extends('layouts.account')

@section('account_content')

	<div id="admin-pages">

		@include('common.messages', [ 'dismissible'=>true ])

		<h4>=== List of posts ===</h4>
		<?php App\Http\Controllers\Account\Site\PAgesController::createBlog();?>

		<div class="pull-right">
			<a href="{{ action('Account\Site\PagesController@createNewPost') }}" class="btn btn-primary">
				Nueva Entrada
			</a>
		</div>

		@if ( count($entradas) < 1)
			<div class="alert alert-info">{{ Lang::get('account/site.pages.empty') }}</div>
		@else
			<table class="table table-striped">
				<thead>
					<tr>
						<th>{{ Lang::get('account/site.pages.column.title') }}</th>
						<th>{{ Lang::get('account/site.pages.column.type') }}</th>
						<th></th>
					</tr>
				</thead>

				<tbody>
				@foreach ($entradas as $entrada)
						<tr>
							<td><a href="/">{{ $entrada->title }}</a></td>
							<td>{{ $entrada->created_at }}</td>

							<td class="text-right text-nowrap">
								{!! Form::open([ 'method'=>'POST', 'class'=>'delete-form', 'action'=>['Account\Site\PagesController@deletePost'] ]) !!}

								{{ Form::input('hidden', 'post_id',$entrada->id) }}
								{{ Form::input('hidden', 'action',"edit") }}

								<?php $attribs = array("action"=>"edit","post_id"=>$entrada->id);?>

								<a href="{{ action('Account\Site\PagesController@createNewPost',$attribs) }}" class="btn btn-warning btn-xs" target="_blank">
									{{ Lang::get('general.view') }}
								</a>
								<button type="submit" class="btn btn-danger btn-xs">{{ Lang::get('general.delete') }}</button>
								{!! Form::close() !!}
							</td>

							<!--
								<a href="" class="btn btn-warning btn-xs" target="_blank">{{ Lang::get('general.view') }}</a>
								<a href="" class="btn btn-primary btn-xs">{{ Lang::get('general.edit') }}</a>

							-->
						</tr>
				@endforeach
				</tbody>
			</table>
		@endif
	</div>

	<script type="text/javascript">
		ready_callbacks.push(function() {
			var cont = $('#admin-pages');

			cont.find('form.delete-form').each(function(){
				$(this).validate({
					submitHandler: function(f) {
						SITECOMMON.confirm("{{ print_js_string( "Quiere borrar esta entrada?" ) }}", function (e) {
							if (e) {
								LOADING.show();
								f.submit();
							}
						});
					}
				});
			});

		});
	</script>

@endsection