<!DOCTYPE html>
<html lang="{{ LaravelLocalization::getCurrentLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @if ( !empty($seo_title) )
        <title>{{ $seo_title }}</title>
    @else
        <title>Molista</title>
    @endif

    @if ( !empty($seo_description) )
        <meta name="description" content="{{ $seo_description }}" />
    @endif

    <link href="{{ Theme::url('/compiled/css/corporate.css').'?v='.env('CSS_VERSION') }}" rel="stylesheet" type='text/css' />

    <link id="page_favicon" href="{{ asset('favicon.ico') }}" rel="icon" type="image/x-icon" />

    <script type="text/javascript">
        var ready_callbacks = [];
    </script>

</head>

<body id="{{ @$body_id }}">

<div id="sticky-wrapper">

    @include('common.header')

    @yield('content')

</div>

@include('common.footer')

<script src="{{ Theme::url('/compiled/js/corporate.js').'?v='.env('JS_VERSION') }}"></script>
<script src="{{ Theme::url('/js/jquery.validate/messages_' . LaravelLocalization::getCurrentLocale() . '.min.js') }}"></script>
<script src="{{ Theme::url('/js/alertify/messages_' . LaravelLocalization::getCurrentLocale() . '.js') }}"></script>

</body>
</html>
