@extends('layouts.web')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                {{ Lang::get('errors.503.body') }}
            </div>
        </div>
    </div>
@endsection
