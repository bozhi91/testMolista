@extends('layouts.email')

@section('content')

	{!! Lang::get('admin/planchange.reject.body', [
		'username' => $owner_name,
		'siteurl' => $site_url,
		'reason' => @nl2p($reason)
	]) !!}

@endsection
