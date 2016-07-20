@extends('layouts.admin')

@section('content')

    <div class="container">
        @include('common.messages', [ 'dismissible'=>true ])

        <h1 class="list-title">{{ Lang::get('admin/geography/countries.edit.title') }}</h1>

        @include('admin.geography.countries.form', [ 
            'item' => $country,
            'method' => 'PATCH',
            'action' => [ 'Admin\Geography\CountriesController@update', $country->id ],
        ])

    </div>

@endsection
