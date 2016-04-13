@extends('layouts.admin')

@section('content')

    <div class="container">
        @include('common.messages', [ 'dismissible'=>true ])

        <h1 class="list-title">{{ Lang::get('admin/config/locales.edit.title') }}</h1>

        @include('admin.config.locales.form', [ 
            'item' => $locale,
            'method' => 'PATCH',
            'action' => [ 'Admin\Config\LocalesController@update', $locale->id ],
        ])

    </div>

@endsection
