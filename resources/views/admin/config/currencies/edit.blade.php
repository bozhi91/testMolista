@extends('layouts.admin')

@section('content')

    <div class="container">
        @include('common.messages', [ 'dismissible'=>true ])

        <h1 class="list-title">{{ Lang::get('admin/config/currencies.edit.title') }}</h1>

        @include('admin.config/currencies.form', [ 
            'item' => $currency,
            'method' => 'PATCH',
            'action' => [ 'Admin\Config\CurrenciesController@update', $currency->id ],
        ])

    </div>

@endsection
