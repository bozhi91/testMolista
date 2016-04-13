@extends('layouts.admin')

@section('content')

    <div class="container">
        <div class="row">
            <div class="col-sm-3 hidden-xs">
                {!! Form::model(null, [ 'method'=>'get', 'id'=>'list-filters', 'class'=>'list-filters' ]) !!}
                    {!! Form::hidden('limit', Input::get('limit', Config::get('app.pagination_perpage', 10)) ) !!}
                    <h4>{{ Lang::get('general.filters') }}</h4>
                    <p>{!! Form::text('name', Input::get('name'), [ 'class'=>'form-control', 'placeholder'=>Lang::get('admin\config/locales.name') ]) !!}</p>
                    <p>{!! Form::submit( Lang::get('general.filters.apply'), [ 'class'=>'btn btn-default btn-block']) !!}</p>
                {{ Form::close() }}
            </div>
            <div class="col-xs-12 col-sm-9">
                @permission('locale-create')
                    <a href="{{ action('Admin\Config\LocalesController@create') }}" class="btn btn-default pull-right">{{ Lang::get('general.new') }}</a>
                @endpermission

                <h1 class="list-title">{{ Lang::get('admin/menu.locales') }}</h1>

                @if ( count($locales) < 1)
                    <div class="alert alert-info" role="alert">{{ Lang::get('admin/config/locales.empty') }}</div>
                @else
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ Lang::get('admin/config/locales.locale') }}</th>
                                <th>{{ Lang::get('admin/config/locales.name') }}</th>
                                <th class="text-center">{{ Lang::get('admin/config/locales.web') }}</th>
                                <th class="text-center">{{ Lang::get('admin/config/locales.admin') }}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($locales as $locale)
                                <tr>
                                    <td>{{ $locale->id }}</td>
                                    <td>{{ $locale->locale }}</td>
                                    <td>{{ "{$locale->native} ({$locale->name})" }}</td>
                                    <td class="text-center"><span class="glyphicon glyphicon-{{ $locale->web ? 'ok' : 'remove' }}" aria-hidden="true"></span></td>
                                    <td class="text-center"><span class="glyphicon glyphicon-{{ $locale->admin ? 'ok' : 'remove' }}" aria-hidden="true"></span></td>
                                    <td class="text-right">
                                        @permission('locale-edit')
                                            <a href="{{ action('Admin\Config\LocalesController@edit', $locale->id) }}" class="btn btn-xs btn-default">{{ Lang::get('general.edit') }}</a>
                                        @endpermission
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {!! drawPagination($locales, Input::only('limit','name','email','role')) !!}
                @endif
            </div>
        </div>
    </div>

    <script type="text/javascript">
        ready_callbacks.push(function(){
        });
    </script>

@endsection
