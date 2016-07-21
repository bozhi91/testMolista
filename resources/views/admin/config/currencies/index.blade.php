@extends('layouts.admin')

@section('content')

    <div class="container">
        <div class="row">
            <div class="col-sm-3 hidden-xs">
                {!! Form::model(null, [ 'method'=>'get', 'id'=>'list-filters', 'class'=>'list-filters' ]) !!}
                    {!! Form::hidden('limit', Input::get('limit', Config::get('app.pagination_perpage', 10)) ) !!}
                    <h4>{{ Lang::get('general.filters') }}</h4>
                    <p>{!! Form::text('title', Input::get('title'), [ 'class'=>'form-control', 'placeholder'=>Lang::get('admin/config/currencies.title') ]) !!}</p>
                    <p>{!! Form::submit( Lang::get('general.filters.apply'), [ 'class'=>'btn btn-default btn-block']) !!}</p>
                {{ Form::close() }}
            </div>
            <div class="col-xs-12 col-sm-9">

                <h1 class="list-title">{{ Lang::get('admin/menu.currencies') }}</h1>

                @if ( $currencies->count() < 1)
                    <div class="alert alert-info" role="alert">{{ Lang::get('admin/config/currencies.empty') }}</div>
                @else
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>{{ Lang::get('admin/config/currencies.code') }}</th>
                                <th>{{ Lang::get('admin/config/currencies.title') }}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($currencies as $currency)
                                <tr>
                                    <td>{{ $currency->code }}</td>
                                    <td>{{ $currency->title }}</td>
                                    <td class="text-right">
                                        @permission('currency-edit')
                                            <a href="{{ action('Admin\Config\CurrenciesController@edit', $currency->id) }}" class="btn btn-xs btn-default">{{ Lang::get('general.edit') }}</a>
                                        @endpermission
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {!! drawPagination($currencies, Input::except('page')) !!}
                @endif
            </div>
        </div>
    </div>

    <script type="text/javascript">
        ready_callbacks.push(function(){
        });
    </script>

@endsection
