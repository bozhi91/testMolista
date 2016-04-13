@extends('layouts.admin')

@section('content')

    <div class="container">
        @include('common.messages', [ 'dismissible'=>true ])

        <h1 class="list-title">{{ Lang::get('admin/properties/services.create.title') }}</h1>

        @include('admin.properties.services.form', [ 
            'item' => null,
            'method' => 'POST',
            'action' => 'Admin\Properties\ServicesController@store',
        ])

    </div>
@endsection
