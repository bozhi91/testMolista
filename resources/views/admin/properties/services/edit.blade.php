@extends('layouts.admin')

@section('content')

    <div class="container">
        @include('common.messages', [ 'dismissible'=>true ])

        <h1 class="list-title">{{ Lang::get('admin/properties/services.edit.title') }}</h1>

        @include('admin.properties.services.form', [ 
            'item' => $service,
            'method' => 'PATCH',
            'action' => [ 'Admin\Properties\ServicesController@update', $service->id ],
        ])

    </div>

@endsection
