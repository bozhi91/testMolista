@extends('layouts.account')

@section('account_content')

	<div id="admin-pages">

		@include('common.messages', [ 'dismissible'=>true ])

		<div class="pull-right">
			<a href="{{ action('Account\Site\PagesController@create') }}" class="btn btn-primary">{{ Lang::get('account/site.pages.button.new') }}</a>
		</div>

		<h1 class="page-title">{{ Lang::get('account/site.pages.h1') }}</h1>

		@if ( count($pages) < 1)
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
					@foreach ($pages as $page)
						<tr>
							<td>{{ $page->title }}</td>
							<td>{{ Lang::get("account/site.pages.type.{$page->type}") }}</td>
							<td class="text-right text-nowrap">
								{!! Form::open([ 'method'=>'DELETE', 'class'=>'delete-form', 'action'=>['Account\Site\PagesController@destroy', $page->slug] ]) !!}
									<a href="{{ action('Web\PagesController@show', $page->slug) }}" class="btn btn-warning btn-xs" target="_blank">{{ Lang::get('general.view') }}</a>
									<a href="{{ action('Account\Site\PagesController@edit', $page->slug) }}" class="btn btn-primary btn-xs">{{ Lang::get('general.edit') }}</a>
									<button type="submit" class="btn btn-danger btn-xs">{{ Lang::get('general.delete') }}</button>
								{!! Form::close() !!}
							</td>
						</tr>
					@endforeach
				</tbody>
			</table>
            {!! drawPagination($pages, Input::except('page')) !!}
		@endif

	</div>

	<script type="text/javascript">
		ready_callbacks.push(function() {
			var cont = $('#admin-pages');

			cont.find('form.delete-form').each(function(){
				$(this).validate({
					submitHandler: function(f) {
						alertify.confirm("{{ print_js_string( Lang::get('account/site.pages.delete.warning') ) }}", function (e) {
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