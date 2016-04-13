@extends('layouts.admin')

@section('content')

    <style type="text/css">
        .translations-table { font-size: 12px; }
        .edit-form { position: relative; min-width: 150px; }
        .view-i18n { min-height: 50px; position: relative; z-index: 1; }
        .edit-i18n { height: 100%; width: 100%; position: absolute; top: -3px; left: -3px; z-index: 2; opacity: 0; }
        .edit-i18n:focus { opacity: 1; }
        .copy-from-base { position: absolute; font-size: 10px; color: #ccc; bottom: -8px; right: 0px; }
    </style>

    <div class="container">
        <div class="row">
            <div class="col-sm-3 hidden-xs">
                {!! Form::model(null, [ 'method'=>'get', 'id'=>'list-filters', 'class'=>'list-filters' ]) !!}
                    {!! Form::hidden('limit', Input::get('limit', Config::get('app.pagination_perpage', 10)) ) !!}
                    <h4>{{ Lang::get('admin/config/translations.language.base') }}</h4>
                    <div class="error-container">
                        {!! Form::select('base', [''=>''] + $enabled_languages, Input::get('base'), [ 'class'=>'form-control required' ]) !!}
                    </div>
                    <br />
                    <h4>{{ Lang::get('admin/config/translations.language.chosen') }}</h4>
                    <div class="error-container">
                        <ul class="list-unstyled" style="margin: 0px;">
                            @foreach ($enabled_languages as $iso=>$name)
                                <li>
                                    <label class="normal">
                                        <input type="checkbox" name="langs[]" value="{{ $iso }}" {{ @in_array($iso, Input::get('langs')) ? 'checked="checked"' : '' }} class="required" /> {{ $name }}
                                    </label>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <br />
                    <p>{!! Form::submit( Lang::get('general.filters.apply'), [ 'class'=>'btn btn-default btn-block']) !!}</p>
                    <hr />
                    <h4>{{ Lang::get('general.filters') }}</h4>
                    <p>{!! Form::select('status', [''=>Lang::get('admin/config/translations.translated.all'), 'untranslated'=>Lang::get('admin/config/translations.translated.not'), 'translated'=>Lang::get('admin/config/translations.translated.only') ], Input::get('status'), [ 'class'=>'form-control' ]) !!}</p>
                        <p>{!! Form::select('file', [''=>Lang::get('admin/config/translations.key')] + $keys, Input::get('file'), [ 'class'=>'form-control' ]) !!}</p>
                        <p>{!! Form::text('tag', Input::get('tag'), [ 'class'=>'form-control', 'placeholder'=>Lang::get('admin/config/translations.tag') ]) !!}</p>
                    <p>{!! Form::submit( Lang::get('general.filters.apply'), [ 'class'=>'btn btn-default btn-block']) !!}</p>
                {{ Form::close() }}
            </div>
            <div class="col-xs-12 col-sm-9">
                <h1 class="list-title">{{ Lang::get('admin/menu.translations') }}</h1>
                @if ( !Input::get('base') )
                    <div class="alert alert-warning" style="margin: 38px 0px;">
                        {{ Lang::get('admin/config/translations.warning') }}
                    </div>
                    @if ( $language_stats['total'] )
                        <h3>{{ Lang::get('admin/config/translations.abstract') }}</h3>
                        <div class="dashboard">
                            <div class="dashboard-block">
                                <div class="row">
                                    <div class="col-sm-3">
                                        <div class="panel panel-tile text-center">
                                            <div class="panel-body ">
                                                <div class="number">{{ number_format($language_stats['total'], 0, ',', '.') }}</div>
                                            </div>
                                            <div class="panel-footer text-uppercase">{{ Lang::get('admin/config/translations.abstract.total') }}</div>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="panel panel-tile text-center">
                                            <div class="panel-body ">
                                                <div class="number">{{ number_format($language_stats['last_24'], 0, ',', '.') }}</div>
                                            </div>
                                            <div class="panel-footer text-uppercase">{{ Lang::get('admin/config/translations.abstract.24') }}</div>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="panel panel-tile text-center">
                                            <div class="panel-body ">
                                                <div class="number">{{ number_format($language_stats['last_72'], 0, ',', '.') }}</div>
                                            </div>
                                            <div class="panel-footer text-uppercase">{{ Lang::get('admin/config/translations.abstract.72') }}</div>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="panel panel-tile text-center">
                                            <div class="panel-body ">
                                                <div class="number">{{ number_format($language_stats['last_week'], 0, ',', '.') }}</div>
                                            </div>
                                            <div class="panel-footer text-uppercase">{{ Lang::get('admin/config/translations.abstract.week') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @if (count($language_stats['langs'])>1)
                            <h3>{{ Lang::get('admin/config/translations.percentage') }}</h3>
                        @else
                            <h3>{{ Lang::get('admin/config/translations.percentage.simple') }}</h3>
                        @endif
                        <div id="chart-area" class="chart-area"></div>
                        <script type="text/javascript" src="https://www.google.com/jsapi"></script>
                        <script type="text/javascript">
                            var language_stats = <?= @json_encode($language_stats['langs']) ?>;
                            google.load("visualization", "1", {packages:["gauge"]});
                            google.setOnLoadCallback(drawChart);
                            function drawChart() {
                                var total_stats = 0;
                                var data_stats = [ ['Label', 'Value'] ];
                                $.each(language_stats, function(k,v){
                                    data_stats.push([ v.title, v.percentage ]);
                                    total_stats++;
                                });
                                var data = google.visualization.arrayToDataTable(data_stats);
                                var options = {
                                    //width: 400, 
                                    height: Math.ceil(total_stats/4) * 200,
                                    redFrom: 0, redTo: 75,
                                    yellowFrom: 75, yellowTo: 95,
                                    greenFrom: 95, greenTo: 100,
                                    minorTicks: 5
                                };
                                var chart = new google.visualization.Gauge(document.getElementById('chart-area'));

                                chart.draw(data, options);
                            }
                        </script>
                    @endif
                @elseif ( count($translations) < 1 )
                    <div class="alert alert-info" style="margin-top: 38px;">
                        {{ Lang::get('admin/config/translations.empty') }}
                    </div>
                @else
                    <?php
                        $user_can_translate = Entrust::can('translation-edit');
                    ?>
                    <div id="translations" class="table-responsive">
                        <table class="table table-hover translations-table">
                            <thead>
                                <tr>
                                    <th>{{ Lang::get('admin/config/translations.key') }}</th>
                                    <th>{{ Lang::get('admin/config/translations.tag') }}</th>
                                    <th>{{ $enabled_languages[Input::get('base')] }}</th>
                                    @foreach (Input::get('langs') as $l)
                                        <th>{{ $enabled_languages[$l] }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($translations as $item)
                                    <tr>
                                        <td>{{ $item->file }}</td>
                                        <td>{{ $item->tag }}</td>
                                        <td class="edit-td base-td">
                                            {!! Form::open([ 'action'=>null, 'class'=>'edit-form' ]) !!}
                                                <div class="view-i18n">{!! @$item->i18n[Input::get('base')] !!}</div>
                                                @if ( $user_can_translate )
                                                    <textarea class="edit-i18n" readonly="readonly">{{ @$item->i18n[Input::get('base')] }}</textarea>
                                                @endif
                                            {!! Form::close() !!}
                                        </td>
                                        @foreach (Input::get('langs') as $l)
                                            <td class="edit-td">
                                                {!! Form::open([ 'method'=>'PUT', 'action'=>['Admin\Config\TranslationsController@update', $item->id], 'class'=>'edit-form' ]) !!}
                                                    <input type="hidden" name="locale" value="{{ $l }}">
                                                    <div class="view-i18n">{!! @$item->i18n[$l] !!}</div>
                                                    @if ( $user_can_translate && Auth::user()->canTranslate($l))
                                                        <textarea class="edit-i18n editable" name="value">{{ @$item->i18n[$l] }}</textarea>
                                                        <a href="#" class="copy-from-base">{{ Lang::get('admin/config/translations.copy', [ 'language'=>$enabled_languages[Input::get('base')] ]) }}</a>
                                                    @endif
                                                {!! Form::close() !!}
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {!! drawPagination($translations, Input::only('limit','base','langs','status','file','tag')) !!}
                @endif
            </div>
        </div>
    </div>

    <script type="text/javascript">
        ready_callbacks.push(function() {
            var filters = $('#list-filters');

            filters.validate({
                errorPlacement: function(error, elem) {
                    elem.closest('.error-container').append(error);
                }
            });

            var cont = $('#translations');

            cont.on('change', '.edit-i18n.editable', function(){
                $(this).closest('form').submit();
            });

            cont.find('.edit-form').each(function(){
                var form = $(this);

                form.validate({
                    submitHandler: function(f) {
                        LOADING.show();

                        var textarea = form.find('.edit-i18n');
                        var sample = form.find('.view-i18n');

                        $.ajax(form.attr('action'), {
                            method : 'PUT',
                            dataType: 'json',
                            data: form.serialize(),
                            success: function(data){
                                LOADING.hide();
                                if (data.success){
                                    sample.html( textarea.val() );
                                    alertify.success("{{ print_js_string( Lang::get('admin/config/translations.save.success') ) }}");

                                } else {
                                    textarea.val( sample.html() );
                                    alertify.error("{{ print_js_string( Lang::get('general.messages.error') ) }}");
                                }
                            },
                            error: function(){
                                LOADING.hide();
                                textarea.val( sample.html() );
                                alertify.error("{{ print_js_string( Lang::get('general.messages.error') ) }}");
                            }
                        });

                    }
                });
            });

            cont.on('click', '.copy-from-base', function(e){
                e.preventDefault();

                var base = $(this).closest('tr').find('.base-td .edit-i18n');
                if ( base.length && base.val() ) {
                    $(this).closest('td').find('.edit-i18n').val( base.val() ).trigger('change');
                }
            });

        });
    </script>

@endsection
