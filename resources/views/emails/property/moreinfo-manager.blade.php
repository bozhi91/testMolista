@extends('layouts.email')

@section('content')

    <h1>{{ Lang::get('web/properties.moreinfo.email.manager.title', [ 'ref'=>$property->ref ]) }}</h1>
    <ul>
    	<li>{{ Lang::get('web/customers.register.name.first') }}: {{ @$data['first_name'] }}</li>
    	<li>{{ Lang::get('web/customers.register.name.last') }}: {{ @$data['last_name'] }}</li>
    	<li>{{ Lang::get('web/customers.register.email') }}: {{ @$data['email'] }}</li>
    	<li>{{ Lang::get('web/customers.register.phone') }}: {{ @$data['phone'] }}</li>
    	<li>{!! @nl2br($data['message']) !!}</li>
    </ul>

@endsection
