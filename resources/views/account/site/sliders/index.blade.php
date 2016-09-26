@extends('layouts.account')

@section('account_content')

<div id="admin-pages">

	@include('common.messages', [ 'dismissible'=>true ])

	<div class="pull-right">
		<a href="{{ action('Account\Site\SlidersController@create') }}" class="btn btn-primary">
			{{ Lang::get('account/site.sliders.button.new') }}
		</a>
	</div>

	<h1 class="page-title">{{ Lang::get('account/site.sliders.h1') }}</h1>

	@if (count($sliders) < 1)
	<div class="alert alert-info">{{ Lang::get('account/site.sliders.empty') }}</div>
	@else
	<table class="table table-striped">
		<thead>
			<tr>
				<th>{{ Lang::get('account/site.sliders.column.title') }}</th>
				<th>{{ Lang::get('account/site.sliders.column.languages') }}</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			@foreach ($sliders as $slider)
			<tr>
				<td>{{ $slider->name }}</td>
				<td>{{$slider->getLocalesString()}}</td>
				<td class="text-right text-nowrap">
					{!! Form::open([ 'method'=>'DELETE', 'class'=>'delete-form', 'action'=>['Account\Site\SlidersController@destroy', $slider->id] ]) !!}
					<a href="{{ action('Account\Site\SlidersController@edit', $slider->id) }}" class="btn btn-primary btn-xs">{{ Lang::get('general.edit') }}</a>
					<button type="submit" class="btn btn-danger btn-xs">{{ Lang::get('general.delete') }}</button>
					{!! Form::close() !!}
				</td>
			</tr>
			@endforeach
		</tbody>
	</table>
	{!! drawPagination($sliders, Input::except('page')) !!}

	@endif
</div>


<script type="text/javascript">
    ready_callbacks.push(function () {
        var cont = $('#admin-pages');

        cont.find('form.delete-form').each(function () {
            $(this).validate({
                submitHandler: function (f) {
                    SITECOMMON.confirm("{{ print_js_string( Lang::get('account/site.slides.delete.warning') ) }}", function (e) {
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