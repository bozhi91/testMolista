@extends('layouts.admin')

@section('content')

    <div class="container">
        @include('common.messages', [ 'dismissible'=>true ])

        <h1 class="list-title">{{ Lang::get('admin/geography/countries.create.title') }}</h1>

        @include('admin.geography.countries.form', [ 
            'item' => null,
            'method' => 'POST',
            'action' => 'Admin\Geography\CountriesController@store',
        ])

    </div>
@endsection
