@extends('layouts.web', [ 
    'header_class'=>'no-bottom-margin',
    'menu_section' => 'home',
])

@section('content')

    <div id="home">

        {!! Form::model(null, [ 'method'=>'get', 'action'=>'Web\PropertiesController@index', 'id'=>'search-form', 'class'=>'search-form' ]) !!}
            <div class="container">
                <div class="custom-tabs">
                    <ul class="nav nav-tabs text-uppercase" role="tablist">
                        <li role="presentation" class="active"><a href="#search-filters" aria-controls="home" role="tab" data-toggle="tab">{{ Lang::get('web/properties.title') }}</a></li>
                    </ul>
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="search-filters">
                            <div class="row main-filters">
                                <div class="col-xs-12 col-sm-10">
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-5">
                                            <div class="row">
                                                <div class="col-xs-12 col-sm-6">
                                                    <div class="form-group error-container">
                                                        {!! Form::label('search[mode]', Lang::get('web/properties.mode')) !!}
                                                        {!! Form::select('search[mode]', $modes, null, [ 'class'=>'form-control required' ]) !!}
                                                    </div>
                                                </div>
                                                <div class="col-xs-12 col-sm-6">
                                                    <div class="form-group error-container">
                                                        {!! Form::label('search[state]', Lang::get('web/properties.state')) !!}
                                                        {!! Form::select('search[state]', [ ''=>Lang::get('web/properties.state.any'), ] + $states->toArray(), null, [ 'class'=>'form-control state-input' ]) !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xs-12 col-sm-7">
                                            <div class="row">
                                                <div class="col-xs-12 col-sm-6">
                                                    <div class="form-group error-container">
                                                        {!! Form::label('search[city]', Lang::get('web/properties.city')) !!}
                                                        {!! Form::select('search[city]', [ 
                                                            ''=>Lang::get('web/properties.city.any'), 
                                                        ], null, [ 'class'=>'form-control city-input' ]) !!}
                                                    </div>
                                                </div>
                                                <div class="col-xs-12 col-sm-6">
                                                    <div class="form-group error-container">
                                                        {!! Form::label('search[type]', Lang::get('web/properties.type')) !!}
                                                        {!! Form::select('search[type]', [ ''=>Lang::get('web/properties.type.any'),  ] + $types, null, [ 'class'=>'form-control' ]) !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-2">
                                    <label>&nbsp;</label>
                                    {!! Form::submit( Lang::get('general.search'), [ 'class'=>'btn btn-yellow btn-block text-uppercase']) !!}
                                </div>
                            </div>
                            <a href="#" class="show-more-filters"><span>{{ Lang::get('web/properties.more.show') }}</span> &raquo;</a>
                            <div class="more-filters-area" style="display: none;">
                                <div class="row">
                                    <div class="col-xs-12 col-sm-9">
                                        <div class="row">
                                            <div class="col-xs-12 col-sm-3">
                                                <div class="form-group error-container">
                                                    {!! Form::label('search[more][rooms]', Lang::get('web/properties.more.rooms')) !!}
                                                    {!! Form::select('search[more][rooms]', [ 
                                                        ''=>Lang::get('web/properties.more.any'), 
                                                    ], null, [ 'class'=>'form-control' ]) !!}
                                                </div>
                                            </div>
                                            <div class="col-xs-12 col-sm-3">
                                                <div class="form-group error-container">
                                                    {!! Form::label('search[more][baths]', Lang::get('web/properties.more.baths')) !!}
                                                    {!! Form::select('search[more][baths]', [ 
                                                        ''=>Lang::get('web/properties.more.any'), 
                                                    ], null, [ 'class'=>'form-control' ]) !!}
                                                </div>
                                            </div>
                                            <div class="col-xs-12 col-sm-3">
                                                <div class="form-group error-container">
                                                    {!! Form::label('search[more][size]', Lang::get('web/properties.more.sqm')) !!}
                                                    {!! Form::select('search[more][size]', [ 
                                                        ''=>Lang::get('web/properties.more.any'), 
                                                    ], null, [ 'class'=>'form-control' ]) !!}
                                                </div>
                                            </div>
                                            <div class="col-xs-12 col-sm-3">
                                                <div class="form-group error-container">
                                                    {!! Form::label('search[more][price]', Lang::get('web/properties.more.price')) !!}
                                                    {!! Form::select('search[more][price]', [ 
                                                        ''=>Lang::get('web/properties.more.any'), 
                                                    ], null, [ 'class'=>'form-control' ]) !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        {!! Form::close() !!}

        <div class="container">
            <div class="properties-list">
                <div class="row">
                    @for ($k=0; $k<=7; $k++)
                        <div class="col-xs-12 col-sm-4" {!! ($k % 3) ? '' : 'style="clear: both;"' !!}>
                            <a href="{{ action('Web\PropertiesController@details', 'slug')}}" class="pill">
                                <div class="image" style="background-image: url('{{ asset('_temp/sample-0'.rand(1,3).'.jpg') }}');">
                                    <img src="{{ asset('_temp/sample-0'.rand(1,3).'.jpg') }}" alt="" title="" />
                                </div>
                                @if ( rand(0,2) )
                                    <div class="labels">
                                        <span class="label label-purple label-big">{{ Lang::get('web/properties.labels.new') }}</span>
                                    </div>
                                @endif
                                <div class="title">Impresionante casa independiente en el Poal</div>
                                <div class="text">
                                    <div class="price">{{ price(495000, [ 'decimals'=>0 ]) }}</div>
                                    <div class="location">Poal, Castelldefels, Barcelona</div>
                                </div>
                            </a>
                        </div>
                    @endfor
                </div>
            </div>
        </div>

    </div>

    <script type="text/javascript">
        ready_callbacks.push(function(){
            var cont = $('#home');
            var form = cont.find('.search-form');

            var state_cities = {};

            cont.on('click', '.show-more-filters', function(e){
                e.preventDefault();
                $(this).hide();
                cont.find('.more-filters-area').slideDown();
            });

            if ( cont.find('.more-filters-area .form-control').filter(function() { return $(this).val(); }).length > 0 ) {
                cont.find('.show-more-filters').trigger('click');
            }

            form.validate({
                ignore: '',
                errorPlacement: function(error, element) {
                    element.closest('.error-container').append(error);
                }
            });

            form.on('change', '.state-input', function(){
                var state_slug = $(this).val();

                var target = form.find('.city-input');

                target.html('<option value="">' + target.find('option[value=""]').eq(0).text() + '</option>');

                if ( !state_slug ) {
                    return;
                }

                if ( state_cities.hasOwnProperty(state_slug) ) {
                    $.each(state_cities[state_slug], function(k,v) {
                        target.append('<option value="' + v.code + '">' + v.label + '</option>');
                    });
                } else {
                    $.ajax({
                        dataType: 'json',
                        url: '{{ action('Ajax\GeographyController@getSuggest', 'city') }}',
                        data: { state_slug: state_slug },
                        success: function(data) {
                            if ( data ) {
                                state_cities[state_slug] = data;
                                $.each(state_cities[state_slug], function(k,v) {
                                    target.append('<option value="' + v.code + '">' + v.label + '</option>');
                                });
                            }
                        }
                    });
                }
            });

        });
    </script>

@endsection
