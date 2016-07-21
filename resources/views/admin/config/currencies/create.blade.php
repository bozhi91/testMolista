@extends('layouts.admin')

@section('content')

    <div class="container">
        @include('common.messages', [ 'dismissible'=>true ])

        <h1 class="list-title">{{ Lang::get('admin/config/currencies.create.title') }}</h1>

        @include('admin.config.currencies.form', [ 
            'item' => null,
            'method' => 'POST',
            'action' => 'Admin\Config\CurrenciesController@store',
        ])

    </div>
@endsection
