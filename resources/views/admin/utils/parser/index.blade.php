@extends('layouts.admin')

@section('content')

	<div class="container">

		<div class="row">

			<div class="col-sm-3 hidden-xs">
				{!! Form::model(null, [ 'method'=>'get', 'id'=>'list-filters', 'class'=>'list-filters' ]) !!}
					{!! Form::hidden('limit', Input::get('limit')) !!}
					<h4>{{ Lang::get('general.filters') }}</h4>
					<p>{!! Form::text('query', Input::get('query'), [ 'class'=>'form-control', 'placeholder'=>'Query' ]) !!}</p>
					<p>{!! Form::submit( Lang::get('general.filters.apply'), [ 'class'=>'btn btn-default btn-block']) !!}</p>
				{{ Form::close() }}
			</div>

			<div class="col-xs-12 col-sm-9">

				<h1 class="list-title">Parse requests</h1>

				@if ( count($requests) < 1)
					<div class="alert alert-info" role="alert">No requests found</div>

				@else
					<table class="table table-striped">
						<thead>
							<tr>
								<th>#</th>
								<th>Service</th>
								<th>Request</th>
								<th class="text-center">Ready</th>
								<th class="text-right"></th>
							</tr>
						</thead>
						<tbody>
							@foreach ($requests as $req)
								<tr>
									<td>{{ $req->id }}</td>
									<td>{{ $req->service_title }}</td>
									<td>{{ $req->query }}</td>
									<td class="text-center"><span class="glyphicon glyphicon-{{ $req->ready ? 'ok' : 'remove' }}" aria-hidden="true"></span></td>
									<td class="text-right">
										<a href="{{ action('Admin\Utils\ParserController@getDownload',$req->id) }}" class="btn btn-xs btn-default {{ $req->ready ? '' : 'disabled' }}" target="_blank">Download CSV</a>
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
					{!! drawPagination($requests, Input::only('query','limit')) !!}

				@endif

			</div>

		</div>

	</div>

	<script type="text/javascript">
		ready_callbacks.push(function(){
		});
	</script>

@endsection
