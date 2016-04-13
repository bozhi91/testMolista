@extends('layouts.admin')

@section('content')

    <div class="container">
        @include('common.messages', [ 'dismissible'=>true ])

        <h1 class="list-title">{{ Lang::get('admin/config/locales.create.title') }}</h1>

        @include('admin.config.locales.form', [ 
            'item' => null,
            'method' => 'POST',
            'action' => 'Admin\Config\LocalesController@store',
        ])

    </div>
@endsection
