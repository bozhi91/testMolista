@extends('layouts.admin')

@section('content')

    <div class="container">
        <div class="row">
            <div class="col-sm-3 hidden-xs">
                {!! Form::model(null, [ 'method'=>'get', 'id'=>'list-filters', 'class'=>'list-filters' ]) !!}
                    {!! Form::hidden('limit', Input::get('limit', Config::get('app.pagination_perpage', 10)) ) !!}
                    <h4>{{ Lang::get('general.filters') }}</h4>
                    <p>{!! Form::text('name', Input::get('name'), [ 'class'=>'form-control', 'placeholder'=>Lang::get('admin/geography/countries.name') ]) !!}</p>
                    <p>{!! Form::submit( Lang::get('general.filters.apply'), [ 'class'=>'btn btn-default btn-block']) !!}</p>
                {{ Form::close() }}
            </div>
            <div class="col-xs-12 col-sm-9">

                <h1 class="list-title">{{ Lang::get('admin/menu.geography.countries') }}</h1>

                @if ( $countries->count() < 1)
                    <div class="alert alert-info" role="alert">{{ Lang::get('admin/geography/countries.empty') }}</div>
                @else
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ Lang::get('admin/geography/countries.name') }}</th>
                                <th>{{ Lang::get('admin/geography/countries.currency') }}</th>
                                <th class="text-center">{{ Lang::get('admin/geography/countries.enabled') }}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($countries as $country)
                                <tr>
                                    <td>{{ $country->code }}</td>
                                    <td>{{ $country->name }}</td>
                                    <td>{{ $country->currency }}</td>
                                    <td class="text-center"><span class="glyphicon glyphicon-{{ $country->enabled ? 'ok' : 'remove' }}" aria-hidden="true"></span></td>
                                    <td class="text-right">
                                        @permission('geography-edit')
                                            <a href="{{ action('Admin\Geography\CountriesController@edit', $country->id) }}" class="btn btn-xs btn-default">{{ Lang::get('general.edit') }}</a>
                                        @endpermission
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {!! drawPagination($countries, Input::only('limit','name')) !!}
                @endif
            </div>
        </div>
    </div>

    <script type="text/javascript">
        ready_callbacks.push(function(){
        });
    </script>

@endsection
