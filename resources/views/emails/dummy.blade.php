@extends('layouts.email')

@section('content')
    {!! empty($content) ? '' : $content !!}
@endsection
