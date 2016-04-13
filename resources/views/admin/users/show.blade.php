@extends('layouts.admin')

@section('content')

    {!! Form::open([ 'action'=>'Admin\UsersController@store', 'class'=>'container' ]) !!}
        {!! Form::hidden('confirm', $user->id) !!}
        <h1 class="list-title">{{ Lang::get('admin/users.login.title') }}</h1>
        {!! Lang::get('admin/users.login.body', [ 'name'=>$user->name ]) !!}
        <div class="text-right">
            <a href="javascript:history.back();" class="btn btn-default">{{ Lang::get('general.back') }}</a>
            {!! Form::submit( Lang::get('general.continue'), [ 'class'=>'btn btn-default']) !!}
        </div>
    {!! Form::close() !!}

@endsection
